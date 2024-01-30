<?php

class FS_DeleteEmail_Listener
{
    public static function listenController($class, array &$extend)
    {
        if ($class == 'XenForo_ControllerPublic_Account') {
            $extend[] = 'FS_DeleteEmail_ControllerPublic_Account';
        }

        if ($class == 'XenForo_ControllerAdmin_User') {
            $extend[] = 'FS_DeleteEmail_ControllerAdmin_User';
        }
    }
}
