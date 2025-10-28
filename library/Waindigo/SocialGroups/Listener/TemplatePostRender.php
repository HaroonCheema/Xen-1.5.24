<?php

class Waindigo_SocialGroups_Listener_TemplatePostRender extends Waindigo_Listener_TemplatePostRender
{

    protected function _getTemplates()
    {
        return array(
            'forum_view',
            'PAGE_CONTAINER',
            'post_field_edit',
            'resource_category_edit',
            'thread_field_edit',
            'thread_prefix_edit',
            'tools_rebuild',
            'waindigo_social_forum_container_socialgroups',
        );
    }

    public static function templatePostRender($templateName, &$content, array &$containerData,
        XenForo_Template_Abstract $template)
    {
        $templatePostRender = new Waindigo_SocialGroups_Listener_TemplatePostRender($templateName, $content,
            $containerData, $template);
        list ($content, $containerData) = $templatePostRender->run();
    }

    protected function _forumView()
    {
        if (Waindigo_SocialGroups_SocialForum::hasInstance()) {
            $socialForum = Waindigo_SocialGroups_SocialForum::getInstance();
            $viewParams = $this->_fetchViewParams();
            $viewParams['socialForum'] = $socialForum->toArray();
            $pattern = '#<div class="linkGroup SelectionCountContainer">#';
            $replacement = '$0' . $this->_render('waindigo_social_forum_tools_socialgroups', $viewParams);
            $this->_patternReplace($pattern, $replacement);
        }
    }

    protected function _threadPrefixEdit()
    {
        $viewParams = $this->_fetchViewParams();

        foreach ($viewParams['nodes'] as $nodeId => $node) {
            if ($node['node_type_id'] == 'SocialCategory') {
                $pattern = '#(<option value="' . $nodeId . '"[^>]*)disabled="disabled"([^>]*>[^<]*</option>)#';
                $replacement = '${1}${2}';
                $this->_patternReplace($pattern, $replacement);
            }
        }
    }

    protected function _threadFieldEdit()
    {
        $this->_threadPrefixEdit();
    }

    protected function _postFieldEdit()
    {
        $this->_threadPrefixEdit();
    }

    protected function _pageContainer()
    {
        if (isset($GLOBALS['forum_view'])) {
            $viewParams = $this->_fetchViewParams();
            /* @var $socialForum Waindigo_SocialGroups_SocialForum */
            $socialForum = Waindigo_SocialGroups_SocialForum::getInstance();
            if (isset($socialForum['social_forum_id'])) {
                $viewParams['socialForum'] = $socialForum;
                $visitor = XenForo_Visitor::getInstance();
                if (isset($visitor['user_id'])) {
                    $member = $socialForum->getMember();
                    if (!array_key_exists('social_forum_member_id', $member) &&
                         $this->_getSocialForumModel()->canJoinSocialForum($socialForum)) {
                        $this->_appendTemplateAfterTopCtrl('waindigo_join_social_forum_topctrl_socialgroups',
                            $viewParams);
                    } else
                        if ($member['is_invited']) {
                            $this->_appendTemplateAfterTopCtrl('waindigo_accept_invite_topctrl_socialgroups',
                                $viewParams);
                        }
                }
            }
        }
    }

    protected function _toolsRebuild()
    {
        $this->_appendTemplate('waindigo_tools_rebuild_socialgroups');
    }

    protected function _resourceCategoryEdit()
    {
        $viewParams = $this->_fetchViewParams();
        $nodes = $viewParams['nodes'];
        foreach ($nodes as $node) {
            if ($node['node_type_id'] == 'SocialCategory') {
                $pattern = '#(<select name="thread_node_id" class="textCtrl" id="ctrl_node_id">.*<option value="' . $node['node_id'] .'".*)disabled="disabled"(.*</select>)#Us';
                $replacement = '${1}${2}';
                $this->_patternReplace($pattern, $replacement);
            }
        }
    }

    protected function _waindigoSocialForumContainerSocialGroups()
    {
        $viewParams = $this->_fetchViewParams();
        $resource = Waindigo_SocialGroups_SocialForum::getInstance()->getResource();
        if ($resource) {
            $this->_prependTemplate('resource_view_header', $viewParams + array(
                'resource' => $resource,
                'titleHtml' => (isset($containerData['h1']) ? $this->_containerData['h1'] : false),
            ));
            $this->_containerData['h1'] = '';
        }
    }

    /**
     *
     * @return Waindigo_SocialGroups_Model_SocialForum
     */
    protected function _getSocialForumModel()
    {
        return Waindigo_SocialGroups_SocialForum::getSocialForumModel();
    }
}