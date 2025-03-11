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
		$customFieldCheckbox = $this->_input->filterSingle('is_checked', XenForo_Input::ARRAY_SIMPLE);

		$customFieldsShown = array_keys($customFields);

		foreach ($customFields as $key => &$value) {
			if (!isset($customFieldCheckbox[$key])) {
				$value = "";
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

		return $this->responseRedirect(
			XenForo_ControllerResponse_Redirect::SUCCESS,
			XenForo_Link::buildPublicLink('address/'),
			null
		);
	}

	protected function _getFieldModel()
	{
		return $this->getModelFromCache('XenForo_Model_UserField');
	}
}
