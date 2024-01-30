<?php

class FS_DeleteEmail_ControllerAdmin_User extends XFCP_FS_DeleteEmail_ControllerAdmin_User
{
    public function actionSave()
    {
        $parent = parent::actionSave();

        $userId = $this->_input->filterSingle('user_id', XenForo_Input::UINT);

        $user = $this->_getUserOrError($userId);

        $value = 0;

        if (!$user['email']) {
            $value = 1;
        }

        $db = XenForo_Application::getDb();

        $sql = 'UPDATE xf_user SET deleted_by = ? WHERE user_id = ?';
        $db->query($sql, [$value, $userId]);

        return $parent;
    }
}
