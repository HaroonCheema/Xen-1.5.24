<?php

class FS_ChangeMediaImage_Listener
{
    public static function listenController($class, array &$extend)
    {
        if ($class == 'XenGallery_ControllerPublic_Media') {
            $extend[] = 'FS_ChangeMediaImage_ControllerPublic_Media';
        }
    }

    public static function extendModelMedia($class, array &$extend)
    {
        if ($class == 'XenGallery_Model_Media') {
            $extend[] = 'FS_ChangeMediaImage_Model_Media';
        }
    }
}
