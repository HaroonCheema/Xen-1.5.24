<?php

class FS_DeleteEmail_Install
{
    private static $_instance;


    public static final function getInstance()
    {
        if (!self::$_instance) {
            $self = __CLASS__;
            self::$_instance = new $self();
        }
        return self::$_instance;
    }

    public static function install() {
        $db = XenForo_Application::getDb();
        $db->query("ALTER TABLE `xf_user` ADD `deleted_by` int(10)  NOT NULL DEFAULT 0");
    }

    public static function uninstallCode() {
        $db = XenForo_Application::get('db');

        $db->query("ALTER TABLE xf_user DROP deleted_by");
    }
}
