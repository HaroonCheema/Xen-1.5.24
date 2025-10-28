<?php

/**
 *
 * @see XenForo_ControllerPublic_Post
 */
class Waindigo_SocialGroups_Extend_XenForo_ControllerPublic_Post extends XFCP_Waindigo_SocialGroups_Extend_XenForo_ControllerPublic_Post
{

    public function getHelper($class)
    {
        if (XenForo_Application::$versionId < 1020000 && $class == "ForumThreadPost") {
            $class = 'Waindigo_SocialGroups_ControllerHelper_SocialForumThreadPost';
        }

        return parent::getHelper($class);
    }
}