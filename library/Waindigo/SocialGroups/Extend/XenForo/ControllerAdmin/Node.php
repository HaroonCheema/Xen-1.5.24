<?php

/**
 *
 * @see XenForo_ControllerAdmin_Node
 */
class Waindigo_SocialGroups_Extend_XenForo_ControllerAdmin_Node extends XFCP_Waindigo_SocialGroups_Extend_XenForo_ControllerAdmin_Node
{

    /**
     *
     * @see XenForo_ControllerAdmin_Node::_postDispatch()
     */
    protected function _postDispatch($controllerResponse, $controllerName, $action)
    {
        /* @var $controllerResponse XenForo_ControllerResponse_Reroute */
        if ($controllerResponse instanceof XenForo_ControllerResponse_Reroute) {
            if ($controllerResponse->controllerName == 'Waindigo_SocialGroups_ControllerAdmin_SocialCategory') {
                if (!class_exists('XFCP_Waindigo_SocialGroups_ControllerAdmin_SocialCategory', false)) {
                    $createClass = XenForo_Application::resolveDynamicClass('XenForo_ControllerAdmin_Forum',
                        'controller');
                    eval(
                        'class XFCP_Waindigo_SocialGroups_ControllerAdmin_SocialCategory extends ' . $createClass . ' {}');
                }
            }
        }
    }
}