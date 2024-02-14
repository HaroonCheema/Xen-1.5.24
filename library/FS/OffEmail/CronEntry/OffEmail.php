<?php

class FS_OffEmail_CronEntry_OffEmail
{
    public static function offEmailOnConversation()
    {
        $db = XenForo_Application::getDb();

        $sql = 'UPDATE xf_user_option SET email_on_conversation = ? WHERE email_on_conversation = ?';
        $db->query($sql, [0, 1]);
    }
}
