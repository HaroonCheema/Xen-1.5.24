<?php

class Waindigo_SocialGroups_Blocks_NewSocialForums extends XenForo_Model
{

    /**
     * @param array $options
     * @param int $page
     * @return array
     */
    public function getModule($options, $page)
    {
        $socialForumModel = Waindigo_SocialGroups_SocialForum::getSocialForumModel();

        $params['socialForums'] = $socialForumModel->getSocialForums(array(),
            array(
                'limit' => (isset($options['limit']) ? $options['limit'] : 5),
                'order' => 'created_date'
            ));

        foreach ($params['socialForums'] as &$forum) {
            $forum['urls'] = Waindigo_SocialGroups_Template_Helper_SocialForum::getAvatarUrls($forum);
        }

        return $params;
    }
}