<?php

/**
 *
 * @see XenForo_Model_InlineMod_Post
 */
class Waindigo_SocialGroups_Extend_XenForo_Model_InlineMod_Post extends XFCP_Waindigo_SocialGroups_Extend_XenForo_Model_InlineMod_Post
{

    protected $_socialForumMembers = null;

    /**
     *
     * @see XenForo_Model_InlineMod_Post::_getThreadAndForumFromPost()
     */
    protected function _getThreadAndForumFromPost(array $post, array $threads, array $forums)
    {
        list ($thread, $forum) = parent::_getThreadAndForumFromPost($post, $threads, $forums);

        if ($thread['social_forum_id']) {
            $socialForumMember = $this->_getSocialForumMember($thread['social_forum_id']);
            $socialForumModel = $this->_getSocialForumModel();
            $forum['nodePermissions'] = $socialForumModel->getNodePermissions($thread,
                array(
                    $socialForumMember
                ));
        }

        return array(
            $thread,
            $forum
        );
    }

    /**
     *
     * @param int $socialForumId
     * @return array $socialForumMember
     */
    protected function _getSocialForumMember($socialForumId)
    {
        if (!$this->_socialForumMembers) {
            $visitor = XenForo_Visitor::getInstance();
            $this->_socialForumMembers = $this->_getSocialForumMemberModel()->getSocialForumMembers(
                array(
                    'user_id' => $visitor['user_id']
                ));
        }
        foreach ($this->_socialForumMembers as $socialForumMember) {
            if ($socialForumMember['social_forum_id'] == $socialForumId) {
                return $socialForumMember;
            }
        }
        return array();
    }

    /**
     *
     * @return Waindigo_SocialGroups_Model_SocialForum
     */
    protected function _getSocialForumModel()
    {
        return Waindigo_SocialGroups_SocialForum::getSocialForumModel();
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