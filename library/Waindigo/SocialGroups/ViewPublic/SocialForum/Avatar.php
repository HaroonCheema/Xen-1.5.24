<?php

class Waindigo_SocialGroups_ViewPublic_SocialForum_Avatar extends XenForo_ViewPublic_Base
{

    public function renderHtml()
    {
        $this->_params['urls'] = Waindigo_SocialGroups_Template_Helper_SocialForum::getAvatarUrls(
            $this->_params['socialForum']);
    }
}