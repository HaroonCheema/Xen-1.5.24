<?php

class FS_ThreadCount_ControllerPublic_Forum extends XFCP_FS_ThreadCount_ControllerPublic_Forum {

    public function actionAddThread() {

        $response = parent::actionAddThread();

        $redirectTarget = ($response->redirectTarget);

        
        $urlParts = explode('.', $url = trim(str_replace('index.php', '', $redirectTarget), '/'));
        $threadId = $urlParts['1'];

        if($threadId){

                $thread = $this->getModelFromCache('XenForo_Model_Thread')->getThreadById($threadId);


                if(isset($thread['user_id'])){

                $db = XenForo_Application::getDb();
                $db->query('
                                        UPDATE xf_user
                                        SET thread_count = thread_count + 1
                                        WHERE user_id = ?
                                ', $thread['user_id']);

                }
        
        }
        return $response;
    }

    protected function _getInlineModThreadModel() {
        return $this->getModelFromCache('XenForo_Model_Thread');
    }

}
