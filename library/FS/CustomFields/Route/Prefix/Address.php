<?php

class FS_CustomFields_Route_Prefix_Address implements XenForo_Route_Interface
{
	public function match($routePath, Zend_Controller_Request_Http $request, XenForo_Router $router)
	{
		return $router->getRouteMatch('FS_CustomFields_ControllerPublic_Address', $routePath);
	}
}