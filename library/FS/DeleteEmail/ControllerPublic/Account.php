<?php

class FS_DeleteEmail_ControllerPublic_Account extends XFCP_FS_DeleteEmail_ControllerPublic_Account
{
    public function actionContactDetailsSave()
    {
        $this->_assertPostOnly();

        $settings = $this->_input->filter(array(
            'email' => XenForo_Input::STRING,
        ));

        $visitor = XenForo_Visitor::getInstance();

        $auth = $this->_getUserModel()->getUserAuthenticationObjectByUserId($visitor['user_id']);
        if (!$auth) {
            return $this->responseNoPermission();
        }

        if ($settings['email'] != $visitor['email'] && $visitor['deleted_by'] == 1) {

            return $this->responseError(new XenForo_Phrase('your_email_may_not_be_changed_at_this_time'));
        }

        return parent::actionContactDetailsSave();
    }

    public function actionDeleteEmail()
    {
        $visitor = XenForo_Visitor::getInstance();

        if (!$visitor['email']) {
            return $this->responseNoPermission();
        }

        if ($this->isConfirmedPost()) {
            $userId = $visitor['user_id'];

            $db = XenForo_Application::getDb();

            $sql = 'UPDATE xf_user SET email = ? WHERE user_id = ?';
            $db->query($sql, ['', $userId]);

            $sql1 = 'UPDATE xf_user SET deleted_by = ? WHERE user_id = ?';
            $db->query($sql1, [0, $userId]);

            return $this->responseRedirect(
                XenForo_ControllerResponse_Redirect::SUCCESS,
                XenForo_Link::buildPublicLink('account/contact-details')
            );
        }

        $viewParams = array(

            'email' => $visitor->email,
        );

        return $this->responseView(
            'FS_DeleteEmail_ControllerPublic_Account_Delete_Email',
            'fs_delete_email_delete',
            $viewParams
        );
    }
}
