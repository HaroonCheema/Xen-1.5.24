<?php

class Waindigo_SocialGroups_ViewPublic_Watched_SocialForums extends XenForo_ViewPublic_Base
{

    /**
     * Help render the HTML output.
     *
     * @return mixed
     */
    public function renderHtml()
    {
        foreach ($this->_params['socialForums'] as &$forum) {
            $forum['urls'] = Waindigo_SocialGroups_Template_Helper_SocialForum::getAvatarUrls($forum);
        }
    }
}
