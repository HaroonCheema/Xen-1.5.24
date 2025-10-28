<?php
class SliderCaptcha_Captcha_Render
{
    public static function captcha_render(array &$extraChoices, XenForo_View $view, array $preparedOption)    
    {
        $extraChoices['SliderCaptcha'] = new XenForo_Phrase('slidercaptcha_selected');
    }
}