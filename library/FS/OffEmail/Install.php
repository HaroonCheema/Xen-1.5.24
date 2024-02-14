<?php

class FS_OffEmail_Install
{
    public static function install()
    {
        $db = XenForo_Application::getDb();

        $sql = 'UPDATE xf_user_option SET email_on_conversation = ?';
        $db->query($sql, [0]);
    }

    public static function uninstallCode()
    {
        $db = XenForo_Application::getDb();

        $sql = 'UPDATE xf_user_option SET email_on_conversation = ?';
        $db->query($sql, [1]);
    }
}
