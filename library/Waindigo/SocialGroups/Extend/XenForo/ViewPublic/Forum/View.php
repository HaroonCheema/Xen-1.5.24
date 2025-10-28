<?php

/**
 *
 * @see XenForo_ViewPublic_Forum_View
 */
class Waindigo_SocialGroups_Extend_XenForo_ViewPublic_Forum_View extends XFCP_Waindigo_SocialGroups_Extend_XenForo_ViewPublic_Forum_View
{

    /**
     *
     * @see XenForo_ViewPublic_Forum_View::renderHtml()
     */
    public function renderHtml()
    {
        parent::renderHtml();

        if (Waindigo_SocialGroups_SocialForum::hasInstance()) {
            $xenOptions = XenForo_Application::get('options');
            if (!$xenOptions->waindigo_socialGroups_showChildNodesInSocialForums) {
                $this->_params['renderedNodes'] = array();
            }
        }
    }
}