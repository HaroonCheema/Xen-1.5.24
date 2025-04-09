<?php

class FS_CustomFields_DataWriter_User extends XFCP_FS_CustomFields_DataWriter_User
{

    protected function _getFields()
    {
        $fields = parent::_getFields();

        $fields['xf_user']['fs_save_address'] = array('type' => self::TYPE_STRING, 'default' => '', 'maxLength' => 10);

        $fields['xf_user']['upgrade_after_addon'] = array('type' => self::TYPE_UINT, 'default' => 0);
        $fields['xf_user']['amplify_order_submitted'] = array('type' => self::TYPE_UINT, 'default' => 0);

        $fields['xf_user']['amplifier_order_id'] = array('type' => self::TYPE_STRING, 'default' => '', 'maxLength' => 150);

        return $fields;
    }
    
    public function setAddressCustomFields(array $fieldValues, array $fieldsShown = null)
	{
		if ($fieldsShown === null)
		{
			// not passed - assume keys are all there
			$fieldsShown = array_keys($fieldValues);
		}

		$fieldModel = $this->_getFieldModel();
		$fields = $this->_getUserFieldDefinitions();

		if ($this->get('user_id') && !$this->_importMode)
		{
			$existingValues = $fieldModel->getUserFieldValues($this->get('user_id'));
		}
		else
		{
			$existingValues = array();
		}

		$finalValues = array();

		foreach ($fieldsShown AS $fieldId)
		{
			if (!isset($fields[$fieldId]))
			{
				continue;
			}

			$field = $fields[$fieldId];
			$multiChoice = ($field['field_type'] == 'checkbox' || $field['field_type'] == 'multiselect');

			if ($multiChoice)
			{
				// multi selection - array
				$value = array();
				if (isset($fieldValues[$fieldId]))
				{
					if (is_string($fieldValues[$fieldId]))
					{
						$value = array($fieldValues[$fieldId]);
					}
					else if (is_array($fieldValues[$fieldId]))
					{
						$value = $fieldValues[$fieldId];
					}
				}
			}
			else
			{
				// single selection - string
				if (isset($fieldValues[$fieldId]))
				{
					if (is_array($fieldValues[$fieldId]))
					{
						$value = count($fieldValues[$fieldId]) ? strval(reset($fieldValues[$fieldId])) : '';
					}
					else
					{
						$value = strval($fieldValues[$fieldId]);
					}
				}
				else
				{
					$value = '';
				}
			}

			$existingValue = (isset($existingValues[$fieldId]) ? $existingValues[$fieldId] : null);



			if ($value !== $existingValue)
			{
				$finalValues[$fieldId] = $value;
			}
		}

		$this->_updateCustomFields = $finalValues + $this->_updateCustomFields;
		$this->set('custom_fields', $finalValues + $existingValues);
	}
    
    
}
