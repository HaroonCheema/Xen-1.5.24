<?php

class FS_CustomFields_Listener {

    public static function loadClass($class, &$extend) {


        if ($class == "XenForo_ControllerAdmin_UserField") {
            $extend[] = "FS_CustomFields_ControllerAdmin_UserField";
        }
        
            if ($class == "XenForo_Model_UserField") {
            $extend[] = "FS_CustomFields_Model_UserField";
        }
        

        if ($class == "XenForo_DataWriter_UserField") {
            $extend[] = "FS_CustomFields_DataWriter_UserField";
        }
    }

}
