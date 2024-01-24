<?php

class FS_ThreadCount_ControllerPublic_Post extends XFCP_FS_ThreadCount_ControllerPublic_Post {

    public function actionDelete() {

        
         $hardDelete = $this->_input->filterSingle('hard_delete', XenForo_Input::UINT);
        if ($this->isConfirmedPost() && $hardDelete) {

         
            $postId = $this->_input->filterSingle('post_id', XenForo_Input::UINT);

            $ftpHelper = $this->getHelper('ForumThreadPost');

            list($post, $thread, $forum) = $ftpHelper->assertPostValidAndViewable($postId);

            if ($post['post_id'] == $thread['first_post_id']) {


                $db = XenForo_Application::getDb();
                $db->query('
                                       UPDATE xf_user
                                       SET thread_count = thread_count - 1
                                       WHERE user_id = ?
                               ', $thread['user_id']);

                return parent::actionDelete();
            }
        }


        return parent::actionDelete();
    }

}
