<?php

/**
 *
 * @see EWRporta_Model_Blocks
 */
class Waindigo_SocialGroups_Extend_EWRporta_Model_Blocks extends XFCP_Waindigo_SocialGroups_Extend_EWRporta_Model_Blocks
{

    /**
     *
     * @see EWRporta_Model_Blocks::getBlockParams
     */
    public function getBlockParams($block, $page = 1, $params = array())
    {
        switch ($block['block_id']) {
            case 'Waindigo_NewSocialForums':
                $model = new Waindigo_SocialGroups_Blocks_NewSocialForums();
                break;
        }

        if (isset($model)) {
            $params[$block['block_id']] = $model->getModule($block['options'], $page);
        }

        return parent::getBlockParams($block, $page, $params);
    }
}