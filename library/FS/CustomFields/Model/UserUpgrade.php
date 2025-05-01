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


	/**
	 * Gets the specified user upgrade records. Queries active and expired records.
	 *
	 * @param array $conditions
	 * @param array $fetchOptions
	 *
	 * @return array [user upgrade record id]
	 */
	public function getUserUpgradeRecordsExport(array $conditions = array(), array $fetchOptions = array())
	{
		$baseTable = (empty($conditions['active']) ? 'user_upgrade_expired' : 'user_upgrade_active');

		if (empty($conditions['active'])) {
			$orderBy = 'user_upgrade_expired.end_date DESC';
		} else {
			$orderBy = 'user_upgrade_active.start_date DESC';
		}

		$whereClause = $this->prepareUserUpgradeRecordConditions($conditions, $baseTable, $fetchOptions);
		$orderClause = $this->prepareUserUpgradeOrderOptions($fetchOptions, $baseTable, $orderBy);
		$sqlClauses = $this->prepareUserUpgradeRecordFetchOptions($fetchOptions, $baseTable);

		return $this->fetchAllKeyed(
			'
				SELECT ' . $baseTable . '.*,
					user.*
				' . $sqlClauses['selectFields'] . '
				FROM xf_' . $baseTable . ' AS ' . $baseTable . '
				LEFT JOIN xf_user AS user ON (' . $baseTable . '.user_id = user.user_id)
				' . $sqlClauses['joinTables'] . '
				WHERE ' . $whereClause . '
				' . $orderClause . '
			',
			'user_upgrade_record_id'
		);
	}
}
