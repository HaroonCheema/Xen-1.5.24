<?php

class Waindigo_SocialGroups_Listener_TemplateCreate extends Waindigo_Listener_TemplateCreate
{

    protected function _getTemplates()
    {
        return array(
            'forum_view',
            'thread_view',
        );
    }

    public static function templateCreate(&$templateName, array &$params, XenForo_Template_Abstract $template)
    {
        $templateCreate = new Waindigo_SocialGroups_Listener_TemplateCreate($templateName, $params, $template);
        list ($templateName, $params) = $templateCreate->run();
    }

    protected function _forumView()
    {
        $this->_preloadTemplate('waindigo_social_forum_tools_socialgroups');
    }

    protected function _threadView()
    {
        $this->_preloadTemplate('waindigo_message_user_info_socialgroups');
    }
}