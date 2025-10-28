<?php

/**
 *
 * @see XenForo_ControllerPublic_Thread
 */
class Waindigo_SocialGroups_Extend_XenForo_ControllerPublic_Thread extends XFCP_Waindigo_SocialGroups_Extend_XenForo_ControllerPublic_Thread
{

    /**
     *
     * @see XenForo_ControllerPublic_Thread::_postDispatch()
     */
    protected function _postDispatch($controllerResponse, $controllerName, $action)
    {
        if (isset($controllerResponse->params['thread'])) {
            $this->_overrideSocialForumStyle($controllerResponse->params['thread']);
        }
        
        parent::_postDispatch($controllerResponse, $controllerName, $action);
    }

    /**
     *
     * @see XenForo_ControllerPublic_Thread::actionIndex()
     */
    public function actionIndex()
    {
        $response = parent::actionIndex();
        
        if ($response instanceof XenForo_ControllerResponse_View) {
            $thread = $response->params['thread'];
            $this->_overrideSocialForumStyle($thread);
            if (Waindigo_SocialGroups_SocialForum::hasInstance()) {
                $response->params['socialForum'] = Waindigo_SocialGroups_SocialForum::getInstance()->toArray();
                $response->params['forum']['social_forum_id'] = $response->params['socialForum']['social_forum_id'];
                $response->params['forum']['social_forum_title'] = $response->params['socialForum']['social_forum_title'];
                $this->_getForumModel()->markSocialForumReadIfNeeded($response->params['socialForum']);
                
                if ($response->params['socialForum']['social_forum_type'] == 'resource') {
                    $response->params['thread']['discussion_type'] = 'resource';
                }
            }
        }
        
        return $response;
    }

    /**
     *
     * @param array $thread
     */
    protected function _overrideSocialForumStyle(array $thread)
    {
        if (isset($thread['social_forum_id']) && $thread['social_forum_id'] && isset($thread['social_forum_style_id'])) {
            $xenOptions = XenForo_Application::get('options');
            if ($thread['social_forum_style_id'] && $xenOptions->waindigo_socialGroups_allowStyleOverride) {
                $this->setViewStateChange('styleId', $thread['social_forum_style_id']);
            }
        }
    }

    /**
     *
     * @param string $class
     */
    public function getHelper($class)
    {
        if (XenForo_Application::$versionId < 1020000 && $class == "ForumThreadPost") {
            $class = 'Waindigo_SocialGroups_ControllerHelper_SocialForumThreadPost';
        }
        
        return parent::getHelper($class);
    }

    /**
     *
     * @return Waindigo_SocialGroups_Model_SocialForum
     */
    protected function _getForumModel()
    {
        return Waindigo_SocialGroups_SocialForum::getSocialForumModel();
    }
}