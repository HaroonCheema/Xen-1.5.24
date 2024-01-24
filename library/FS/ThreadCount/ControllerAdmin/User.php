<?php

class FS_ThreadCount_ControllerAdmin_User extends XFCP_FS_ThreadCount_ControllerAdmin_User {

    
    public function actionSave()
    {
        $parent = parent::actionSave();
        
        $userId = $this->_input->filterSingle('user_id', XenForo_Input::UINT);
        $threadCount = $this->_input->filterSingle('thread_count', XenForo_Input::UINT);

        $db = XenForo_Application::getDb();
        
        $sql = 'UPDATE xf_user SET thread_count = ? WHERE user_id = ?';
        $db->query($sql, [$threadCount, $userId]);
        
        return $parent;
    }

}
