<?php

class SliderCaptcha_Route_Prefix_SliderCaptcha implements XenForo_Route_Interface
{	
	public function match($routePath, Zend_Controller_Request_Http $request, XenForo_Router $router)
	{
		$action = $router->resolveActionWithStringParam($routePath, $request, 'id');
		$routeMatch = $router->getRouteMatch('SliderCaptcha_ControllerPublic_Index', $action, 'scvalidate', $routePath);
		return $routeMatch;
	}

	public function buildLink($originalPrefix, $outputPrefix, $action, $extension, $data, array &$extraParams)
	{
		return XenForo_Link::buildBasicLinkWithStringParam($outputPrefix, $action, $extension, $data, 'id');
	}
}