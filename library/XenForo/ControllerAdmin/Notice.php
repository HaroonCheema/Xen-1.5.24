<?php

class XenForo_ControllerAdmin_Notice extends XenForo_ControllerAdmin_Abstract
{
	protected function _preDispatch($action)
	{
		$this->assertAdminPermission('notice');
	}

	public function actionIndex()
	{
		$noticeModel = $this->_getNoticeModel();
		$optionModel = $this->getModelFromCache('XenForo_Model_Option');

		$viewParams = array(
			'noticeTypes' => array(
				'block' => new XenForo_Phrase('block'),
				'floating' => new XenForo_Phrase('floating'),
				'bottom_fixer' => new XenForo_Phrase('fixed')
			),

			'groupedNotices' => $noticeModel->getAllNoticesGrouped(),
			'totalNotices' => $noticeModel->countNotices(),

			'options' => $optionModel->prepareOptions($optionModel->getOptionsByIds(array('enableNotices'))),
			'canEditOptionDefinition' => $optionModel->canEditOptionAndGroupDefinitions()
		);

		return $this->responseView('XenForo_ViewAdmin_Notice_List', 'notice_list', $viewParams);
	}

	protected function _getNoticeAddEditResponse(array $notice)
	{
		$viewParams = array(
			'notice' => $notice,

			'userCriteria' => XenForo_Helper_Criteria::prepareCriteriaForSelection($notice['user_criteria']),
			'userCriteriaData' => XenForo_Helper_Criteria::getDataForUserCriteriaSelection(),

			'pageCriteria' => XenForo_Helper_Criteria::prepareCriteriaForSelection($notice['page_criteria']),
			'pageCriteriaData' => XenForo_Helper_Criteria::getDataForPageCriteriaSelection(),

			'showInactiveCriteria' => true
		);

		return $this->responseView('XenForo_ViewAdmin_Notice_Edit', 'notice_edit', $viewParams);
	}

	public function actionAdd()
	{
		return $this->_getNoticeAddEditResponse($this->_getNoticeModel()->getDefaultNotice());
	}

	public function actionEdit()
	{
		$noticeId = $this->_input->filterSingle('notice_id', XenForo_Input::UINT);
		$notice = $this->_getNoticeOrError($noticeId);

		return $this->_getNoticeAddEditResponse($notice);
	}

	public function actionSave()
	{
		$this->_assertPostOnly();

		$noticeId = $this->_input->filterSingle('notice_id', XenForo_Input::UINT);

		$data = $this->_input->filter(array(
			'title' => XenForo_Input::STRING,
			'message' => XenForo_Input::STRING,
			'dismissible' => XenForo_Input::UINT,
			'active' => XenForo_Input::UINT,
			'wrap' => XenForo_Input::UINT,
			'display_order' => XenForo_Input::UINT,
			'user_criteria' => XenForo_Input::ARRAY_SIMPLE,
			'page_criteria' => XenForo_Input::ARRAY_SIMPLE,
			'display_image' => XenForo_Input::STRING,
			'image_url' => XenForo_Input::STRING,
			'visibility' => XenForo_Input::STRING,
			'notice_type' => XenForo_Input::STRING,
			'display_style' => XenForo_Input::STRING,
			'css_class' => XenForo_Input::STRING,
			'display_duration' => XenForo_Input::UINT,
			'delay_duration' => XenForo_Input::UINT,
			'auto_dismiss' => XenForo_Input::BOOLEAN
		));

		$dw = XenForo_DataWriter::create('XenForo_DataWriter_Notice');
		if ($noticeId)
		{
			$dw->setExistingData($noticeId);
		}
		$dw->bulkSet($data);
		$dw->save();

		$noticeId = $dw->get('notice_id');

		return $this->responseRedirect(
			XenForo_ControllerResponse_Redirect::SUCCESS,
			XenForo_Link::buildAdminLink('notices') . $this->getLastHash($noticeId)
		);
	}

	public function actionReset()
	{
		$noticeId = $this->_input->filterSingle('notice_id', XenForo_Input::UINT);
		$notice = $this->_getNoticeOrError($noticeId);

		if ($this->isConfirmedPost())
		{
			$this->_getNoticeModel()->resetNotice($notice['notice_id']);

			return $this->responseRedirect(
				XenForo_ControllerResponse_Redirect::SUCCESS,
				XenForo_Link::buildAdminLink('notices') . $this->getLastHash($notice['notice_id'])
			);
		}
		else
		{
			$viewParams = array('notice' => $notice);

			return $this->responseView('XenForo_ViewAdmin_Notice_Reset', 'notice_reset', $viewParams);
		}
	}

	public function actionDelete()
	{
		$noticeId = $this->_input->filterSingle('notice_id', XenForo_Input::UINT);

		if ($this->isConfirmedPost())
		{
			return $this->_deleteData(
				'XenForo_DataWriter_Notice', 'notice_id',
				XenForo_Link::buildAdminLink('notices')
			);
		}
		else
		{
			$viewParams = array('notice' => $this->_getNoticeOrError($noticeId));

			return $this->responseView('XenForo_ViewAdmin_Notice_Delete', 'notice_delete', $viewParams);
		}
	}

	/**
	 * Selectively enables or disables specified notices
	 *
	 * @return XenForo_ControllerResponse_Abstract
	 */
	public function actionToggle()
	{
		return $this->_getToggleResponse(
			$this->_getNoticeModel()->getAllNotices(),
			'XenForo_DataWriter_Notice',
			'notices');
	}

	/**
	 * Gets a valid notice or throws an exception.
	 *
	 * @param integer $noticeId
	 *
	 * @return array
	 */
	protected function _getNoticeOrError($noticeId)
	{
		$noticeModel = $this->_getNoticeModel();

		$notice = $noticeModel->getNoticeById($noticeId);
		if (!$notice)
		{
			throw $this->responseException($this->responseError(new XenForo_Phrase('requested_notice_not_found'), 404));
		}

		return $noticeModel->prepareNotice($notice);
	}

	/**
	 * @return XenForo_Model_Notice
	 */
	protected function _getNoticeModel()
	{
		return $this->getModelFromCache('XenForo_Model_Notice');
	}
}