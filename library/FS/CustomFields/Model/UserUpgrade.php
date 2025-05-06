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
		$baseTable = 'user_upgrade_active';
		$orderBy = 'user_upgrade_active.start_date DESC';

		$whereClause = $this->prepareUserUpgradeRecordConditions($conditions, $baseTable, $fetchOptions);
		$orderClause = $this->prepareUserUpgradeOrderOptions($fetchOptions, $baseTable, $orderBy);
		$sqlClauses = $this->prepareUserUpgradeRecordFetchOptions($fetchOptions, $baseTable);

		return $this->fetchAllKeyed(
			'
	SELECT ' . $baseTable . '.*,
		user.*,

		shipping_name.field_value AS shipping_name,
		shipping_country.field_value AS shipping_country,
		shipping_state.field_value AS shipping_state,
		shipping_city.field_value AS shipping_city,
		shipping_postal.field_value AS shipping_postal,
		shipping_street_address.field_value AS shipping_street_address,
		shipping_street_address_2.field_value AS shipping_street_address_2,
		t_shirt_size.field_value AS t_shirt_size,
		receive_swag.field_value AS receive_swag

		' . $sqlClauses['selectFields'] . '
	FROM xf_' . $baseTable . ' AS ' . $baseTable . '
	LEFT JOIN xf_user AS user ON (' . $baseTable . '.user_id = user.user_id)

	LEFT JOIN xf_user_field_value AS shipping_name ON (
		shipping_name.user_id = user.user_id AND shipping_name.field_id = "shipping_name"
	)
	LEFT JOIN xf_user_field_value AS shipping_country ON (
		shipping_country.user_id = user.user_id AND shipping_country.field_id = "shipping_country"
	)
	LEFT JOIN xf_user_field_value AS shipping_state ON (
		shipping_state.user_id = user.user_id AND shipping_state.field_id = "shipping_state"
	)
	LEFT JOIN xf_user_field_value AS shipping_city ON (
		shipping_city.user_id = user.user_id AND shipping_city.field_id = "shipping_city"
	)
	LEFT JOIN xf_user_field_value AS shipping_postal ON (
		shipping_postal.user_id = user.user_id AND shipping_postal.field_id = "shipping_postal"
	)
	LEFT JOIN xf_user_field_value AS shipping_street_address ON (
		shipping_street_address.user_id = user.user_id AND shipping_street_address.field_id = "shipping_street_address"
	)
	LEFT JOIN xf_user_field_value AS shipping_street_address_2 ON (
		shipping_street_address_2.user_id = user.user_id AND shipping_street_address_2.field_id = "shipping_street_address_2"
	)
	LEFT JOIN xf_user_field_value AS t_shirt_size ON (
		t_shirt_size.user_id = user.user_id AND t_shirt_size.field_id = "t_shirt_size"
	)
	LEFT JOIN xf_user_field_value AS receive_swag ON (
		receive_swag.user_id = user.user_id AND receive_swag.field_id = "receive_swag"
	)

	' . $sqlClauses['joinTables'] . '
	WHERE ' . $whereClause . '
	' . $orderClause . '
	',
			'user_upgrade_record_id'
		);
	}


	// public function getUserUpgradeRecordsExport(array $conditions = array(), array $fetchOptions = array())
	// {
	// 	$baseTable = 'user_upgrade_active';

	// 	$orderBy = 'user_upgrade_active.start_date DESC';

	// 	$whereClause = $this->prepareUserUpgradeRecordConditions($conditions, $baseTable, $fetchOptions);
	// 	$orderClause = $this->prepareUserUpgradeOrderOptions($fetchOptions, $baseTable, $orderBy);
	// 	$sqlClauses = $this->prepareUserUpgradeRecordFetchOptions($fetchOptions, $baseTable);

	// 	return $this->fetchAllKeyed(
	// 		'
	// 			SELECT ' . $baseTable . '.*,
	// 				user.*
	// 			' . $sqlClauses['selectFields'] . '
	// 			FROM xf_' . $baseTable . ' AS ' . $baseTable . '
	// 			LEFT JOIN xf_user AS user ON (' . $baseTable . '.user_id = user.user_id)
	// 			' . $sqlClauses['joinTables'] . '
	// 			WHERE ' . $whereClause . '
	// 			' . $orderClause . '
	// 		',
	// 		'user_upgrade_record_id'
	// 	);
	// }
}
