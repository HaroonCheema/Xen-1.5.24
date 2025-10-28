<?php

/**
 *
 * @see XenResource_ControllerPublic_Resource
 */
class Waindigo_SocialGroups_Extend_XenResource_ControllerPublic_Resource extends XFCP_Waindigo_SocialGroups_Extend_XenResource_ControllerPublic_Resource
{

    public function actionSave()
    {
        $GLOBALS['XenResource_ControllerPublic_Resource'] = $this;

        return parent::actionSave();
    }
}