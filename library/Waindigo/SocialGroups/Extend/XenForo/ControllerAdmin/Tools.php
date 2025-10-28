<?php

/**
 * 
 * @see XenForo_ControllerAdmin_Tools
 */
class Waindigo_SocialGroups_Extend_XenForo_ControllerAdmin_Tools extends XFCP_Waindigo_SocialGroups_Extend_XenForo_ControllerAdmin_Tools
{
    
    /**
     * 
     * @see XenForo_ControllerAdmin_Tools::actionCacheRebuild()
     */
    public function actionCacheRebuild()
    {
        $GLOBALS['XenForo_ControllerAdmin_Tools'] = $this;
        
        return parent::actionCacheRebuild();
    }
    
    /**
     *
     * @see XenForo_ControllerAdmin_Tools::actionTriggerDeferred()
     */
    public function actionTriggerDeferred()
    {
        $GLOBALS['XenForo_ControllerAdmin_Tools'] = $this;
    
        return parent::actionTriggerDeferred();
    }
}