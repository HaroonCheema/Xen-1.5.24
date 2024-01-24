<?php

/**
 * Handles user upgrade processing with PayPal.
 *
 * @package XenForo_UserUpgrade
 */
class XenForo_UserUpgradeProcessor_PayPal
{
	/**
	 * @var Zend_Controller_Request_Http
	 */
	protected $_request;

	/**
	 * @var XenForo_Input
	 */
	protected $_input;

	/**
	 * List of filtered input for handling a callback.
	 *
	 * @var array
	 */
	protected $_filtered = null;

	/**
	 * Info about the upgrade being processed.
	 *
	 * @var array|false
	 */
	protected $_upgrade = false;

	/**
	 * Info about the user the upgrade is for.
	 *
	 * @var array|false
	 */
	protected $_user = false;

	/**
	 * The upgrade record ID inserted/updated.
	 *
	 * @var integer|null
	 */
	protected $_upgradeRecordId = null;

	/**
	 * The upgrade record being processed.
	 *
	 * @var array|false
	 */
	protected $_upgradeRecord = false;

	/**
	 * @var XenForo_Model_UserUpgrade
	 */
	protected $_upgradeModel = null;

	/**
	 * @param string $url
	 * @return Zend_Http_Client
	 */
	public static function getHttpClient($url)
	{
		$transports = stream_get_transports();
		if (in_array('tlsv1.2', $transports))
		{
			// we know PHP supports TLS 1.2
			$adapterOptions = array('adapter' => 'Zend_Http_Client_Adapter_Socket');
		}
		else if (function_exists('curl_getinfo'))
		{
			// ...if it doesn't, hopefully cURL supports it
			$version = curl_version();
			if ($version['version_number'] >= 0x072200 && !preg_match('#OpenSSL/(0|1\.0\.0)#i', $version['ssl_version']))
			{
				// >= 7.34.0 (which adds this SSL version type) and not using OpenSSL 0.9.x/1.0.0 which doesn't support TLS 1.2
				$sslVersion = 6;
			}
			else
			{
				$sslVersion = 1; // push for TLSv1. PHP doesn't have the constant until 5.5, though cURL supports it since 7.9.2
			}

			// ...if it doesn't, hopefully cURL supports it
			$adapterOptions = array(
				'adapter' => 'Zend_Http_Client_Adapter_Curl',
				'curloptions' => array(
					CURLOPT_SSL_VERIFYPEER => false, // add this as we might find broken certificate stores
					CURLOPT_SSLVERSION => $sslVersion
				)
			);
		}
		else
		{
			// ...if not, TLS 1.2 is unlikely to be available but we need to test anyway
			$adapterOptions = array();
		}

		return new Zend_Http_Client($url, $adapterOptions);
	}

	public static function runTlsTest(&$error = null)
	{
		try
		{
			$client = self::getHttpClient('https://tlstest.paypal.com/');
			$body = $client->request('GET')->getBody();
			if ($body == 'PayPal_Connection_OK')
			{
				return true;
			}
			else
			{
				$error = 'Unexpected output.';
				return false;
			}
		}
		catch (Exception $e)
		{
			$error = $e->getMessage();
			return false;
		}
	}

	/**
	 * Initializes handling for processing a request callback.
	 *
	 * @param Zend_Controller_Request_Http $request
	 */
	public function initCallbackHandling(Zend_Controller_Request_Http $request)
	{
		$this->_request = $request;
		$this->_input = new XenForo_Input($request);

		$this->_filtered = $this->_input->filter(array(
			'test_ipn' => XenForo_Input::UINT,
			'business' => XenForo_Input::STRING,
			'receiver_email' => XenForo_Input::STRING,
			'txn_type' => XenForo_Input::STRING,
			'txn_id' => XenForo_Input::STRING,
			'parent_txn_id' => XenForo_Input::STRING,
			'mc_currency' => XenForo_Input::STRING,
			'mc_gross' => XenForo_Input::UNUM,
			'tax' => XenForo_Input::UNUM,
			'payment_status' => XenForo_Input::STRING,
			'custom' => XenForo_Input::STRING,
			'subscr_id' => XenForo_Input::STRING
		));

		$this->_upgradeModel =  XenForo_Model::create('XenForo_Model_UserUpgrade');
	}

	/**
	 * Validates the callback request is valid. If failure happens, the response should
	 * tell the processor to retry.
	 *
	 * @param string $errorString Output error string
	 *
	 * @return boolean
	 */
	public function validateRequest(&$errorString)
	{
		try
		{
			if ($this->_filtered['test_ipn'] && XenForo_Application::debugMode())
			{
				$validator = self::getHttpClient('https://www.sandbox.paypal.com/cgi-bin/webscr');
			}
			else
			{
				$validator = self::getHttpClient('https://www.paypal.com/cgi-bin/webscr');
			}
			$validator->setParameterPost('cmd', '_notify-validate');
			$validator->setParameterPost($_POST);
			$validatorResponse = $validator->request('POST');

			if (!$validatorResponse || $validatorResponse->getBody() != 'VERIFIED' || $validatorResponse->getStatus() != 200)
			{
				$host = XenForo_Model_Ip::getHost($this->_request->getClientIp(false));
				if (preg_match('#(^|\.)paypal.com$#i', $host))
				{
					$errorString = 'Request not validated';
				}
				else
				{
					$errorString = array(false, 'Request not validated (from unknown source)');
				}
				return false;
			}
		}
		catch (Zend_Http_Client_Exception $e)
		{
			$errorString = 'Connection to PayPal failed';
			XenForo_Error::logException($e, false, 'Connection to PayPal failed: ');
			return false;
		}

		$business = strtolower($this->_filtered['business']);
		$receiverEmail = strtolower($this->_filtered['receiver_email']);

		$options = XenForo_Application::get('options');
		$accounts = preg_split('#\r?\n#', $options->payPalAlternateAccounts, -1, PREG_SPLIT_NO_EMPTY);
		$accounts[] = $options->payPalPrimaryAccount;

		$matched = false;
		foreach ($accounts AS $account)
		{
			$account = trim(strtolower($account));
			if ($account && ($business == $account || $receiverEmail == $account))
			{
				$matched = true;
				break;
			}
		}

		if (!$matched)
		{
			$errorString = 'Invalid business or receiver_email';
			return false;
		}

		return true;
	}

	/**
	 * Validates pre-conditions on the callback. These represent things that likely wouldn't get fixed
	 * (and generally shouldn't happen), so retries are not necessary.
	 *
	 * @param string $errorString
	 *
	 * @return boolean
	 */
	public function validatePreConditions(&$errorString)
	{
		$itemParts = explode(',', $this->_filtered['custom'], 4);
		if (count($itemParts) != 4)
		{
			$errorString = 'Invalid item (custom)';
			return false;
		}

		list($userId, $userUpgradeId, $validationType, $validation) = $itemParts;
		// $validationType allows validation method changes

		$user = XenForo_Model::create('XenForo_Model_User')->getFullUserById($userId);
		if (!$user)
		{
			$errorString = 'Invalid user';
			return false;
		}
		$this->_user = $user;

		$tokenParts = explode(',', $validation);
		if (count($tokenParts) != 3 || sha1($tokenParts[1] . $user['csrf_token']) != $tokenParts[2])
		{
			$errorString = 'Invalid validation';
			return false;
		}

		$upgrade = $this->_upgradeModel->getUserUpgradeById($userUpgradeId);
		if (!$upgrade)
		{
			$errorString = 'Invalid user upgrade';
			return false;
		}
		$this->_upgrade = $upgrade;

		if (!$this->_filtered['txn_id'])
		{
			switch ($this->_filtered['txn_type'])
			{
				case 'subscr_signup':
					$errorString = array('info', 'Subscription started. Action only taken on payment.');
					break;

				case 'subscr_cancel':
					$errorString = array('info', 'Subscription cancelled by user. No action to take, upgrade will end naturally.');
					break;

				case 'subscr_eot':
					$errorString = array('info', 'Subscription term ended. No direct action to take.');
					break;

				case 'subscr_failed':
					$errorString = array('info', 'Subscription payment failed. No action to take, upgrade will end naturally.');
					break;

				default:
					$errorString = array('info', 'No txn_id. No action to take.');
			}
			return false;
		}

		$transaction = $this->_upgradeModel->getProcessedTransactionLog($this->_filtered['txn_id']);
		if ($transaction)
		{
			$errorString = array('info', 'Transaction already processed. Skipping.');
			return false;
		}

		$upgradeRecord = $this->_upgradeModel->getActiveUserUpgradeRecord($this->_user['user_id'], $this->_upgrade['user_upgrade_id']);
		if ($upgradeRecord)
		{
			$this->_upgradeRecordId = $upgradeRecord['user_upgrade_record_id'];
			$this->_upgradeRecord = $upgradeRecord;
		}

		if (!$upgradeRecord && $this->_filtered['subscr_id'])
		{
			// do we have a log from a previous part of this subscription to work with?
			$parentLogs = $this->_upgradeModel->getLogsBySubscriberId($this->_filtered['subscr_id']);
			foreach (array_reverse($parentLogs) AS $parentLog)
			{
				if ($parentLog['user_upgrade_record_id'])
				{
					$upgradeRecord = $this->_upgradeModel->getExpiredUserUpgradeRecordById($parentLog['user_upgrade_record_id']);
					if ($upgradeRecord)
					{
						$this->_upgradeRecordId = $upgradeRecord['user_upgrade_record_id'];
						$this->_upgradeRecord = $upgradeRecord;
						break;
					}
				}
			}
		}

		if (!$upgradeRecord && $this->_filtered['parent_txn_id'])
		{
			// do we have a log from a previous part of this transaction to work with?
			$parentLogs = $this->_upgradeModel->getLogsByTransactionId($this->_filtered['parent_txn_id']);
			foreach (array_reverse($parentLogs) AS $parentLog)
			{
				if ($parentLog['user_upgrade_record_id'])
				{
					$upgradeRecord = $this->_upgradeModel->getExpiredUserUpgradeRecordById($parentLog['user_upgrade_record_id']);
					if ($upgradeRecord)
					{
						$this->_upgradeRecordId = $upgradeRecord['user_upgrade_record_id'];
						$this->_upgradeRecord = $upgradeRecord;
						break;
					}
				}
			}
		}

		switch ($this->_filtered['txn_type'])
		{
			case 'web_accept':
			case 'subscr_payment':
				$paymentAmountPassed = (
					round($this->_filtered['mc_gross'], 2) - round($this->_filtered['tax'], 2) == round($upgrade['cost_amount'], 2)
					&& strtolower($this->_filtered['mc_currency']) == $upgrade['cost_currency']
				);

				if (!$paymentAmountPassed && $upgradeRecord && $upgradeRecord['extra'])
				{
					$extra = unserialize($upgradeRecord['extra']);
					$cost = $extra['cost_amount'];
					$currency = $extra['cost_currency'];

					$paymentAmountPassed = (
						round($this->_filtered['mc_gross'], 2) - round($this->_filtered['tax'], 2) == round($cost, 2)
						&& strtolower($this->_filtered['mc_currency']) == $currency
					);
					if ($paymentAmountPassed)
					{
						// the upgrade's cost has changed, but we need to continue as if it hasn't
						$this->_upgrade['cost_amount'] = round($this->_filtered['mc_gross'], 2);
						$this->_upgrade['cost_currency'] = strtolower($this->_filtered['mc_currency']);
					}
				}

				if (!$paymentAmountPassed)
				{
					$errorString = 'Invalid payment amount';
					return false;
				}
		}

		return true;
	}

	/**
	 * Once all conditions are validated, process the transaction.
	 *
	 * @return array [0] => log type (payment, cancel, info), [1] => log message
	 */
	public function processTransaction()
	{
		switch ($this->_filtered['txn_type'])
		{
			case 'web_accept':
			case 'subscr_payment':
				if ($this->_filtered['payment_status'] == 'Completed')
				{
					$allowInsertUnpurchasable = (
						!empty($this->_filtered['parent_txn_id']) || !empty($this->_filtered['subsrc_id'])
					);

					$this->_upgradeRecordId = $this->_upgradeModel->upgradeUser(
						$this->_user['user_id'], $this->_upgrade, $allowInsertUnpurchasable
					);

					return array('payment', 'Payment received, upgraded/extended');
				}
				break;
		}

		if ($this->_filtered['payment_status'] == 'Refunded' || $this->_filtered['payment_status'] == 'Reversed')
		{
			if ($this->_upgradeRecord)
			{
				$this->_upgradeModel->downgradeUserUpgrade($this->_upgradeRecord, false);

				return array('cancel', 'Payment refunded/reversed, downgraded');
			}
		}
		else if ($this->_filtered['payment_status'] == 'Canceled_Reversal' && $this->_upgradeRecord)
		{
			$this->_upgradeRecordId = $this->_upgradeModel->upgradeUser(
				$this->_user['user_id'], $this->_upgrade, true,
				isset($this->_upgradeRecord['original_end_date'])
					? $this->_upgradeRecord['original_end_date']
					: $this->_upgradeRecord['end_date']
			);
			return array('payment', 'Reversal cancelled, upgrade reactivated');
		}

		return array('info', 'OK, no action');
	}

	/**
	 * Get details for use in the log.
	 *
	 * @return array
	 */
	public function getLogDetails()
	{
		$details = $_POST;
		$details['_callbackIp'] = (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : false);

		return $details;
	}

	/**
	 * Gets the transaction ID.
	 *
	 * @return string
	 */
	public function getTransactionId()
	{
		return $this->_filtered['txn_id'];
	}

	/**
	 * Gets the subscriber ID.
	 *
	 * @return string
	 */
	public function getSubscriberId()
	{
		return $this->_filtered['subscr_id'];
	}

	/**
	 * Gets the ID of the processor.
	 *
	 * @return string
	 */
	public function getProcessorId()
	{
		return 'paypal';
	}

	/**
	 * Gets the ID of the upgrade record changed.
	 *
	 * @return integer
	 */
	public function getUpgradeRecordId()
	{
		return intval($this->_upgradeRecordId);
	}

	/**
	 * Logs the request.
	 *
	 * @param string $type Log type (info, payment, cancel, error)
	 * @param string $message Log message
	 * @param array $extra Extra details to log (not including output from getLogDetails)
	 */
	public function log($type, $message, array $extra)
	{
		$upgradeRecordId = $this->getUpgradeRecordId();
		$processor = $this->getProcessorId();
		$transactionId = $this->getTransactionId();
		$subscriberId = $this->getSubscriberId();
		$details = $this->getLogDetails() + $extra;

		$this->_upgradeModel->logProcessorCallback(
			$upgradeRecordId, $processor, $transactionId, $type, $message, $details, $subscriberId
		);
	}
}