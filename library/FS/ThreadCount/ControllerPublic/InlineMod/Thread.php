<?php

class FS_ThreadCount_ControllerPublic_InlineMod_Thread extends XFCP_FS_ThreadCount_ControllerPublic_InlineMod_Thread {

    public function actionDelete() {

        $hardDelete = $this->_input->filterSingle('hard_delete', XenForo_Input::STRING);

        if ($this->isConfirmedPost() && $hardDelete) {

            $threadIds = $this->getInlineModIds(false);

            foreach ($threadIds as $threadId) {

                $thread = $this->getModelFromCache('XenForo_Model_Thread')->getThreadById($threadId);

                $db = XenForo_Application::getDb();
                $db->query('
                                       UPDATE xf_user
                                       SET thread_count = thread_count - 1
                                       WHERE user_id = ?
                               ', $thread['user_id']);
            }
        }

        return parent::actionDelete();
    }


}
