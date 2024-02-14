<?php

class ForumCube_PopularThreads_Route_Prefix_Popular implements XenForo_Route_Interface
{
    public function match($routePath, Zend_Controller_Request_Http $request, XenForo_Router $router)
    {
            $action = $router->resolveActionWithStringParam($routePath, $request, 'action_id');
            return $router->getRouteMatch('ForumCube_PopularThreads_ControllerPublic_Popular', $action,'popular');
    }

//    public function buildLink($originalPrefix, $outputPrefix, $action, $extension, $data, array &$extraParams)
//    {
//            return XenForo_Link::buildBasicLinkWithIntegerParam($outputPrefix, $action, $extension, $data, 'quiz_id');
//    }
}