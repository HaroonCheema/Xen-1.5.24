<?php

class FS_ThreadCount_ControllerPublic_Member extends XFCP_FS_ThreadCount_ControllerPublic_Member {

    protected function _getNotableMembers($type, $limit)
    {   
            if(!$type)
            {
                $type = 'threads';
            }
            
            $userModel = $this->_getUserModel();

            $notableCriteria = array(
                    'is_banned' => 0
            );
            $typeMap = array(
                    'messages' => 'message_count',
                    'likes' => 'like_count',
                    'threads' => 'thread_count'
            );
            if (XenForo_Application::getOptions()->enableTrophies)
            {
                    $typeMap['points'] = 'trophy_points';
            }

            if (!isset($typeMap[$type]))
            {
                    return false;
            }

            $field = $typeMap[$type];

            $notableCriteria[$field] = array('>', 0);

            return array($userModel->getUsers($notableCriteria, array(
                    'join' => XenForo_Model_User::FETCH_USER_FULL,
                    'limit' => $limit,
                    'order' => $field,
                    'direction' => 'desc'
            )), $typeMap[$type]);
    }

}
