<?php

class ThemeHouse_KeywordReplace_Listener_LoadClass extends ThemeHouse_Listener_LoadClass
{

    protected function _getExtendedClasses()
    {
        return array(
            'ThemeHouse_KeywordReplace' => array(
                'bb_code' => array(
                    'XenForo_BbCode_Formatter_Base'
                ), 
            ), 
        );
    }

    public static function loadClassBbCode($class, array &$extend)
    {
        $loadClassBbCode = new ThemeHouse_KeywordReplace_Listener_LoadClass($class, $extend, 'bb_code');
        $extend = $loadClassBbCode->run();
    }
}