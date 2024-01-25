<?php

class FS_DeleteEmail_Listener
{

    public static function listenController($class, array &$extend)
    {

        if ($class == 'XenForo_ControllerPublic_Account') {
            $extend[] = 'FS_DeleteEmail_ControllerPublic_Account';
        }

    }

    public static function extendModelUser($class, array &$extend)
    {
        if ($class == 'XenForo_Model_User') {
            $extend[] = 'FS_DeleteEmail_Listener';
        }
    }
}
