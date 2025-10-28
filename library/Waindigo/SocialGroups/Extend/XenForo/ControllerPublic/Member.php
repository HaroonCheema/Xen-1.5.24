<?php

/**
 *
 * @see XenForo_ControllerPublic_Member
 */
class Waindigo_SocialGroups_Extend_XenForo_ControllerPublic_Member extends XFCP_Waindigo_SocialGroups_Extend_XenForo_ControllerPublic_Member
{

    /**
     * Gets social forums for the specified member
     *
     * @return XenForo_ControllerResponse_Abstract
     */
    public function actionSocialForums()
    {
        $userId = $this->_input->filterSingle('user_id', XenForo_Input::UINT);
        $user = $this->getHelper('UserProfile')->assertUserProfileValidAndViewable($userId);

        $socialForumModel = $this->_getSocialForumModel();

        $socialForums = $socialForumModel->getSocialForums(array(
            'user_id' => $userId
        ));

        foreach ($socialForums as &$socialForum) {
            $socialForum = $socialForumModel->prepareSocialForum($socialForum);
        }

        $viewParams = array(
            'socialForums' => $socialForums,
            'user' => $user
        );

        return $this->responseView('Waindigo_SocialGroups_ViewPublic_Member_SocialForums',
            'waindigo_member_social_forums_socialgroups', $viewParams);
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