<?php

class FS_CustomFields_ControllerPublic_Address extends XenForo_ControllerPublic_Abstract
{
	public function actionIndex()
	{
		$visitor = XenForo_Visitor::getInstance();

		if (!$visitor->canEditProfile()) {
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

		if (empty($customFields['receive_swag'])) {
			return $this->responseError(new XenForo_Phrase('please_enter_value_for_required_field_x', array('field' => $showAddressSave['receive_swag']['title'])));
		}

		$customFieldsShown = array_keys($customFields);
		$showAddressSaveKeys = array_keys($showAddressSave);

		foreach ($customFields as $key => &$value) {
			if (!isset($showAddressSave[$key])) {
				$value = "";
			}
		}

		if (strtolower($customFields['receive_swag']) != "no") {
			foreach ($showAddressSaveKeys as $key) {

				if (empty($customFields[$key])) {
					// return $this->responseError(new XenForo_Phrase('please_enter_value_for_required_field_x', array('field' => $showAddressSave[$key]['title'])));

					return $this->responseError(new XenForo_Phrase('fs_enter_complete_all_fields'));
				}
			}
		}

		$writer = XenForo_DataWriter::create('XenForo_DataWriter_User');
		$writer->setExistingData(XenForo_Visitor::getUserId());
		$writer->setCustomFields($customFields, $customFieldsShown);

		$writer->preSave();

		if ($dwErrors = $writer->getErrors()) {
			return $this->responseError($dwErrors);
		}

		$writer->save();

		$options = XenForo_Application::getOptions();
		$db = XenForo_Application::getDb();

		$userId = $visitor['user_id'];

		$saveAddressField = $options->fs_save_address_fields;


		if ($saveAddressField) {

			$saveAddressValue = isset($customFields[$saveAddressField]) ? $customFields[$saveAddressField] : "";

			$sql = 'UPDATE xf_user SET fs_save_address = ? WHERE user_id = ?';
			$db->query($sql, [$saveAddressValue, $userId]);
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
