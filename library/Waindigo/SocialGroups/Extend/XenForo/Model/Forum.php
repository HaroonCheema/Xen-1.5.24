<?php

/**
 *
 * @see XenForo_Model_Forum
 */
class Waindigo_SocialGroups_Extend_XenForo_Model_Forum extends XFCP_Waindigo_SocialGroups_Extend_XenForo_Model_Forum
{

    /**
     *
     * @see XenForo_Model_Forum::canUploadAndManageAttachment()
     */
    public function canUploadAndManageAttachment(array $forum, &$errorPhraseKey = '', array $nodePermissions = null,
        array $viewingUser = null)
    {
        $socialForum = Waindigo_SocialGroups_SocialForum::getInstance();
        if ($forum['node_type_id'] == 'SocialCategory' && !isset($socialForum['social_forum_id'])) {
            $socialForumMember = $this->_getSocialForumMemberModel()->getMaximumMembershipForUserId(
                XenForo_Visitor::getUserId());
            // TODO: Need to check whether a user can upload an attachment for a specific group
            if ($socialForumMember['level']) {
                $nodePermissions = XenForo_Application::get('options')->waindigo_socialGroups_permissions[$socialForumMember['level']];
            }
        }

        return parent::canUploadAndManageAttachment($forum, $errorPhraseKey, $nodePermissions, $viewingUser);
    }

    /**
     *
     * @return Waindigo_SocialGroups_Model_SocialForumMember
     */
    protected function _getSocialForumMemberModel()
    {
        return $this->getModelFromCache('Waindigo_SocialGroups_Model_SocialForumMember');
    }
}