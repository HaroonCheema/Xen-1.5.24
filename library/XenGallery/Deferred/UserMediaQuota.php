<?php

class XenGallery_Deferred_UserMediaQuota extends XenForo_Deferred_Abstract
{
	public function execute(array $deferred, array $data, $targetRunTime, &$status)
	{
		$data = array_merge(array(
			'position' => 0,
			'batch' => 100,
			'resetField' => false
		), $data);
		$data['batch'] = max(1, $data['batch']);

		$db = XenForo_Application::getDb();

		$userIds = $db->fetchCol($db->limit('
			SELECT user_id
			FROM xf_user
			WHERE user_id > ?
				AND (xengallery_media_quota > 0 OR xengallery_media_count > 0)
			ORDER BY user_id
		', $data['batch']), $data['position']);

		if (sizeof($userIds) == 0)
		{
			if ($data['resetField'])
			{
				// Used to reset the field to INT in the event that users have followed the advice to change it to BIGINT.
				try
				{
					$db->query("
						ALTER TABLE xf_user
						CHANGE COLUMN xengallery_media_quota xengallery_media_quota INT(10) UNSIGNED NOT NULL DEFAULT 0
					");
				}
				catch (Zend_Db_Exception $e) {}
			}
			return true;
		}

		/* @var $mediaModel XenGallery_Model_Media */
		$mediaModel = XenForo_Model::create('XenGallery_Model_Media');

		$mediaModel->rebuildUserMediaQuota($userIds);

		$data['position'] = end($userIds);

		$actionPhrase = new XenForo_Phrase('rebuilding');
		$typePhrase = new XenForo_Phrase('xengallery_rebuilding_user_media_quotas');
		$status = sprintf('%s... %s (%s)', $actionPhrase, $typePhrase, XenForo_Locale::numberFormat($data['position']));

		return $data;

	}

	public function canCancel()
	{
		return true;
	}
}