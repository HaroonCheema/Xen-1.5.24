<?php

class FS_CustomFields_Model_UserField extends XFCP_FS_CustomFields_Model_UserField
{
    
    public function prepareUserFieldConditions(array $conditions, array &$fetchOptions)
	{
		$db = $this->_getDb();
		$sqlConditions = array();

		if (!empty($conditions['display_group']))
		{
			$sqlConditions[] = 'user_field.display_group = ' . $db->quote($conditions['display_group']);
		}

		if (!empty($conditions['profileView']))
		{
			$sqlConditions[] = 'user_field.display_group <> \'preferences\' AND user_field.viewable_profile = 1';
		}

		if (!empty($conditions['messageView']))
		{
			$sqlConditions[] = 'user_field.display_group <> \'preferences\' AND user_field.viewable_message = 1';
		}

		if (!empty($conditions['registration']))
		{
			$sqlConditions[] = 'user_field.required = 1 OR user_field.show_registration = 1';
		}

		if (isset($conditions['moderator_editable']))
		{
			$sqlConditions[] = 'user_field.moderator_editable = ' . ($conditions['moderator_editable'] ? 1 : 0);
		}

	   	if (!empty($conditions['showaddress']))
		{
			$sqlConditions[] = 'user_field.show_address = 1';
		}
	   

		if (!empty($conditions['adminQuickSearch']))
		{
			$searchStringSql = 'CONVERT(user_field.field_id USING utf8) LIKE ' . XenForo_Db::quoteLike($conditions['adminQuickSearch']['searchText'], 'lr');

			if (!empty($conditions['adminQuickSearch']['phraseMatches']))
			{
				$sqlConditions[] = '(' . $searchStringSql . ' OR CONVERT(user_field.field_id USING utf8) IN (' . $db->quote($conditions['adminQuickSearch']['phraseMatches']) . '))';
			}
			else
			{
				$sqlConditions[] = $searchStringSql;
			}
		}

		return $this->getConditionsForClause($sqlConditions);
	}
    

    
}