<?php
class SliderCaptcha_Listener
{
    public static function TemplateCreate($templateName, array &$params, XenForo_Template_Abstract $template){

		switch($templateName){
			
			case 'captcha_slidercaptcha':
			    $params += array(
			        'scValidateTime' => time(),
			    );
			    break;
		}
	}
}