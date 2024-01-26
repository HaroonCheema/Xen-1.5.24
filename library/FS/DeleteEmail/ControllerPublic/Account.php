<?php

class FS_DeleteEmail_ControllerPublic_Account extends XFCP_FS_DeleteEmail_ControllerPublic_Account
{

    /**
     * Database object
     *
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_db = null;

    // public function actionContactDetails()
    // {
    //     $visitor = XenForo_Visitor::getInstance();

    //     $auth = $this->_getUserModel()->getUserAuthenticationObjectByUserId($visitor['user_id']);
    //     if (!$auth) {
    //         return $this->responseNoPermission();
    //     }

    //     // echo "<pre>";
    //     // var_dump($visitor['deleted_by']);
    //     // exit;
    //     // // echo "hello world";
    //     // // exit;

    //     $response = parent::actionContactDetails();

    //     return $response;
    // }

    public function actionDeleteEmail()
    {

        // if ($this->isUpdate() && $this->isChanged('navigation_id'))
        // {

        $visitor = XenForo_Visitor::getInstance();

        // echo "<pre>";
        // var_dump($visitor);
        // exit;

        if ($this->isConfirmedPost()) {
            $userId = $visitor['user_id'];
            $email = '';

            $db = XenForo_Application::getDb();

            $sql = 'UPDATE xf_user SET email = ? WHERE user_id = ?';
            $db->query($sql, [$email, $userId]);

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

    // public function actionDeleteEmail()
    // {
    // 	$visitor = \XF::visitor();
    // 	$auth = $visitor->Auth->getAuthenticationHandler();
    // 	if (!$auth) {
    // 		return $this->noPermission();
    // 	}

    // 	if ($this->isPost()) {
    // 		$visitor->fastUpdate('email', '');

    // 		return $this->redirect($this->buildLink('account/account-details'));
    // 	}

    // 	$viewpParams = [
    // 		'confirmUrl' => $this->buildLink('account/delete-email', $visitor),
    // 		'contentTitle' => $visitor->email,
    // 	];

    // 	return $this->view('XF\Account', 'fs_email_delete_confirm', $viewpParams);
    // }

    // public function actionEmail()
    // {
    //     $visitor = XenForo_Visitor::getInstance();

    // 	$auth = $visitor->Auth->getAuthenticationHandler();
    // 	if (!$auth) {
    // 		return $this->noPermission();
    // 	}

    // 	if ($visitor['deleted_by'] == 1) {
    // 		throw $this->exception(
    // 			$this->error(\XF::phrase('your_email_may_not_be_changed_at_this_time'))
    // 		);
    // 	}
    // 	return parent::actionEmail();
    // }
}
