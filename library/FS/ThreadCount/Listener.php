<?php

class FS_ThreadCount_Listener {

    public static function listenController($class, array &$extend) {

        if ($class == 'XenForo_ControllerPublic_Forum') {
            $extend[] = 'FS_ThreadCount_ControllerPublic_Forum';
        }

        if ($class == 'XenForo_ControllerPublic_Post') {
            $extend[] = 'FS_ThreadCount_ControllerPublic_Post';
        }

        if ($class == 'XenForo_ControllerPublic_InlineMod_Thread') {
            $extend[] = 'FS_ThreadCount_ControllerPublic_InlineMod_Thread';
        }
        
        if ($class == 'XenForo_ControllerAdmin_User') {
            $extend[] = 'FS_ThreadCount_ControllerAdmin_User';
        }
        
        if ($class == 'XenForo_ControllerPublic_Member') {
            $extend[] = 'FS_ThreadCount_ControllerPublic_Member';
        }
        

    }
    
    public static function extendModelUser($class, array &$extend)
    {        
        if ($class == 'XenForo_Model_User') { 
           $extend[] = 'FS_ThreadCount_Model_User';
        }
    }   

}
