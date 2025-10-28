<?php
  
class SliderCaptcha_ControllerPublic_Index extends XenForo_ControllerPublic_Abstract
{
	public function actionToken()
	{
		$this->_assertPostOnly();
		$hash = '';

		if( isset($_POST['action']) )
		{
			if( htmlentities($_POST['action'], ENT_QUOTES, 'UTF-8') == 'sctoken' )
			{
				if( isset($_POST['time']) )
				{
				    $key = XenForo_Application::getOptions()->sliderCaptchaSerectKey;
				    $time = $_POST['time'];
				    $secret = sha1('xf_slidercaptcha_secretkey'.$key.$time);
                    $ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
                    $hash = sha1('xf_slidercaptcha'.$ip.$time.$secret);
				} else {
				    $hash = '';
				}
			}
			else
			{
				$hash = '';
			}
		}
		else
		{
			$hash = '';
		}
		$this->_routeMatch->setResponseType('json');
		return $this->responseMessage($hash);
	}
}