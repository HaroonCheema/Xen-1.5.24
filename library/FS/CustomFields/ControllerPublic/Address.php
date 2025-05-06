<?php

class FS_CustomFields_ControllerPublic_Address extends XenForo_ControllerPublic_Abstract
{
	public function actionIndex()
	{
		$visitor = XenForo_Visitor::getInstance();

		if (!$visitor->canEditProfile()) {
			return $this->responseNoPermission();
		}

		$userId = $visitor['user_id'];

		$options = XenForo_Application::getOptions();

		$userUpgradeIds = $options->fs_address_upgrade_ids;

		if (empty($userUpgradeIds)) {
			return;
		}

		$db = XenForo_Application::getDb();

		$upgradeIds = array_filter(array_map('trim', explode(',', $userUpgradeIds)));

		$upgradeIdsList = implode(',', array_map('intval', $upgradeIds)); // Convert array to a safe string

		$active = $db->fetchRow("
    SELECT user_upgrade_active.*,
           user.*
    FROM xf_user_upgrade_active AS user_upgrade_active
    INNER JOIN xf_user AS user ON
        (user.user_id = user_upgrade_active.user_id)
    WHERE user_upgrade_active.user_id = ?
        AND user_upgrade_active.user_upgrade_id IN ($upgradeIdsList)
", $userId);

		if (!$active) {
			return $this->responseNoPermission();
		}

		$customFields = $this->_getFieldModel()->getUserFields(
			array(),
			array('valueUserId' => $visitor['user_id'])
		);

		$viewParams = [
			'customFields' => $this->_getFieldModel()->prepareUserFields($customFields, true),
		];

		return $this->responseView('FS_CustomFields_ViewPublic_Address', 'fs_address', $viewParams);
	}

	public function actionSave()
	{
		$visitor = XenForo_Visitor::getInstance();

		if (!$visitor->canEditProfile()) {
			return $this->responseNoPermission();
		}

		$customFields = $this->_input->filterSingle('custom_fields', XenForo_Input::ARRAY_SIMPLE);

		$showAddressSave = $this->_getFieldModel()->prepareUserFields(
			$this->_getFieldModel()->getUserFields(array('showaddress' => true)),
			true
			// array(),
			// false
		);

		$options = XenForo_Application::getOptions();
		$saveAddressField = $options->fs_save_address_fields;

		if (empty($saveAddressField)) {
			return $this->responseNoPermission();
		}

		if (empty($customFields[$saveAddressField])) {
			return $this->responseError(new XenForo_Phrase('please_enter_value_for_required_field_x', array('field' => $showAddressSave[$saveAddressField]['title'])));
		}
		$showAddressSaveKeys = array_keys($showAddressSave);
		if ($customFields[$saveAddressField] == $options->fs_receive_swag_no) {
			$customFieldsnew = [$options->fs_save_address_fields => $options->fs_receive_swag_no];
			$customFields = $customFieldsnew;
			$showAddressSaveKeys = [];
			$showAddressSaveKeys[] = $options->fs_save_address_fields;
		}

		$customFieldsShown = array_keys($customFields);


		foreach ($customFields as $key => &$value) {
			if (!isset($showAddressSave[$key])) {
				$value = "";
			}
		}

		foreach ($showAddressSaveKeys as $key) {
			if ($key == "shipping_street_address_2") {
				continue;
			}

			if (empty($customFields[$key])) {
				return $this->responseError(new XenForo_Phrase('fs_enter_complete_all_fields'));
			}
		}

		$writer = XenForo_DataWriter::create('XenForo_DataWriter_User');
		$writer->setExistingData(XenForo_Visitor::getUserId());

		$writer->setAddressCustomFields($customFields, $customFieldsShown);

		$writer->preSave();

		if ($dwErrors = $writer->getErrors()) {
			return $this->responseError($dwErrors);
		}

		$writer->save();
		$db = XenForo_Application::getDb();

		$userId = $visitor['user_id'];

		if ($saveAddressField) {

			$saveAddressValue = isset($customFields[$saveAddressField]) ? $customFields[$saveAddressField] : "";

			$sql = 'UPDATE xf_user SET fs_save_address = ? WHERE user_id = ?';
			$db->query($sql, [$saveAddressValue, $userId]);
		}

		$apiKey = $options->fs_amplifier_api_key;

		$isAplicable = isset($customFields[$saveAddressField]) ? strtolower($customFields[$saveAddressField]) : "";

		if ($visitor['upgrade_after_addon'] == 1 && $visitor['amplify_order_submitted'] == 0 && $apiKey && $isAplicable == "yes") {

			$orderSource = $options->fs_amplifier_order_source;

			$encodedApiKey = base64_encode($apiKey);

			$customFields = $this->getModelFromCache('XenForo_Model_UserField')->getUserFields(
				array(),
				array('valueUserId' => $userId)
			);

			$visitorCustomFields = $this->getModelFromCache('XenForo_Model_UserField')->prepareUserFields($customFields, true);

			$time = time();

			$date = new DateTime("@$time");
			$date->setTimezone(new DateTimeZone('America/Chicago'));

			$formatted = $date->format('Y-m-d\TH:i:s');

			$skuItemSize = [
				"ADVR24S00" => "Extra Small",
				"ADVR24S01" => "Small",
				"ADVR24S02" => "Medium",
				"ADVR24S03" => "Large",
				"ADVR24S04" => "Extra Large",
				"ADVR24S05" => "Extra Extra Large",
				"ADVR24S06" => "Extra Extra Extra Large",
				"ADVR24S07" => "Extra Extra Extra Extra Large",
			];

			$orderData = [
				"order_source_code" => $orderSource,
				"order_id" => "order-" . time(),
				"order_date" => $formatted,
				"shipping_method" => "Standard Shipping",
				"billing_info" => [
					"name" => $visitorCustomFields['shipping_name']['field_value'],
					'company_name' => 'Amplifier',
					"address1" => $visitorCustomFields['shipping_street_address']['field_value'],
					"address2" => isset($visitorCustomFields['shipping_street_address_2']['field_value']) ? $visitorCustomFields['shipping_street_address_2']['field_value'] : "",
					"city" => $visitorCustomFields['shipping_city']['field_value'],
					"state" => $visitorCustomFields['shipping_state']['field_value'],
					"postal_code" => $visitorCustomFields['shipping_postal']['field_value'],
					"country_code" => $visitorCustomFields['shipping_country']['field_value']
				],
				"shipping_info" => [
					"name" => $visitorCustomFields['shipping_name']['field_value'],
					'company_name' => 'Amplifier',
					"address1" => $visitorCustomFields['shipping_street_address']['field_value'],
					"address2" => isset($visitorCustomFields['shipping_street_address_2']['field_value']) ? $visitorCustomFields['shipping_street_address_2']['field_value'] : "",
					"city" => $visitorCustomFields['shipping_city']['field_value'],
					"state" => $visitorCustomFields['shipping_state']['field_value'],
					"postal_code" => $visitorCustomFields['shipping_postal']['field_value'],
					"country_code" => $visitorCustomFields['shipping_country']['field_value']
				],
				"line_items" => [
					[
						"sku" => $visitorCustomFields['t_shirt_size']['field_value'],
						"description" => "T-shirt - " . isset($skuItemSize[$visitorCustomFields['t_shirt_size']['field_value']]) ? $skuItemSize[$visitorCustomFields['t_shirt_size']['field_value']] : "",
						"quantity" => 1
					],
					[
						"sku" => $options->fs_additions_sku,
						"description" => $options->fs_additions_sku_des,
						"quantity" => 1
					]

				]
			];

			try {
				$ch = curl_init('https://api.amplifier.com/orders');
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_POST, true);

				curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($orderData));

				curl_setopt($ch, CURLOPT_HTTPHEADER, [
					'Authorization: Basic ' . $encodedApiKey,
					'Content-Type: application/json'
				]);

				$response = curl_exec($ch);
				$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

				if ($httpCode == 500) {
					$errorText = "Amplifier API Internal Server Error";

					throw new Exception($errorText);
				}

				if (curl_errno($ch)) {
					throw new Exception('cURL error: ' . curl_error($ch));
				}

				$decodedResponse = json_decode($response, true);

				if ($httpCode == 200) {
					if (isset($decodedResponse['id'])) {
						$amplifierOrderId = $decodedResponse['id'];

						$sql = 'UPDATE xf_user SET amplify_order_submitted = ?, amplifier_order_id = ? WHERE user_id = ?';
						$db->query($sql, [1, $amplifierOrderId, $userId]);
					}
				} else {
					if (isset($decodedResponse['errors']) && is_array($decodedResponse['errors'])) {
						$error = [];
						foreach ($decodedResponse['errors'] as $err) {
							// $key = $err['name'] ?? 'general';
							$message = $err['message'] ?? 'Unknown error';
							$error[] = htmlspecialchars($message, ENT_QUOTES, 'UTF-8', false);
						}

						$message = $error;

						foreach ($message as &$m) {
							if (is_string($m)) {
								$m = htmlspecialchars($m, ENT_QUOTES, 'UTF-8', false);
							}
						}

						$controllerResponse = new XenForo_ControllerResponse_Error();
						$controllerResponse->errorText = $message;

						return $controllerResponse;
					} elseif (isset($decodedResponse['message'])) {
						$errorText = $decodedResponse['message'];
					} else {
						$errorText = 'Unknown error occurred.';
					}

					throw new Exception($errorText);
				}

				curl_close($ch);
			} catch (Exception $e) {

				$message = $e->getMessage();

				$message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8', false);

				$controllerResponse = new XenForo_ControllerResponse_Error();
				$controllerResponse->errorText = $message;

				return $controllerResponse;

				// echo "Exception caught: " . $e->getMessage();
			}
		}

		return $this->responseRedirect(
			XenForo_ControllerResponse_Redirect::SUCCESS,
			XenForo_Link::buildPublicLink('index'),
			null
		);
	}

	protected function _getFieldModel()
	{
		return $this->getModelFromCache('XenForo_Model_UserField');
	}
}
