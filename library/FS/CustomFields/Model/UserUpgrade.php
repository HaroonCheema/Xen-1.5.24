<?php

class FS_CustomFields_Model_UserUpgrade extends XFCP_FS_CustomFields_Model_UserUpgrade
{

	/**
	 * Upgrades the user with the specified upgrade.
	 *
	 * @param integer $userId
	 * @param array $upgrade Info about upgrade to apply
	 * @param boolean $allowInsertUnpurchasable Allow insert of a new upgrade even if not purchasable
	 * @param integer|null $endDate Forces a specific end date; if null, don't overwrite
	 *
	 * @return integer|false User upgrade record ID
	 */
	public function upgradeUser($userId, array $upgrade, $allowInsertUnpurchasable = false, $endDate = null)
	{
		$parent = parent::upgradeUser($userId,  $upgrade, $allowInsertUnpurchasable, $endDate);

		$active = $this->getActiveUserUpgradeRecord($userId, $upgrade['user_upgrade_id']);

		if ($active || ($upgrade['can_purchase'] && $allowInsertUnpurchasable)) {
			$db = XenForo_Application::getDb();

			$sql = 'UPDATE xf_user SET upgrade_after_addon = ? WHERE user_id = ?';
			$db->query($sql, [1, $userId]);
		}

		return $parent;
	}
}
