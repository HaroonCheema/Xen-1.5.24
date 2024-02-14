<?php

class FS_DeleteEmail_Install
{
    public static function install()
    {
        $db = XenForo_Application::getDb();
        $db->query("ALTER TABLE `xf_user` ADD `deleted_by` int(10)  NOT NULL DEFAULT 0");
    }

    public static function uninstallCode()
    {
        $db = XenForo_Application::get('db');

        $db->query("ALTER TABLE xf_user DROP deleted_by");
    }
}
