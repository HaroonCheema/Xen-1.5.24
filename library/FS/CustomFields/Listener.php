<?php

class FS_CustomFields_Listener
{
    public static function loadClass($class, &$extend)
    {

        if ($class == "XenForo_ControllerAdmin_UserField") {
            $extend[] = "FS_CustomFields_ControllerAdmin_UserField";
        }

        if ($class == "XenForo_Model_UserField") {
            $extend[] = "FS_CustomFields_Model_UserField";
        }


        if ($class == "XenForo_DataWriter_UserField") {
            $extend[] = "FS_CustomFields_DataWriter_UserField";
        }

        if ($class == "XenForo_DataWriter_User") {
            $extend[] = "FS_CustomFields_DataWriter_User";
        }
    }

    public static function controllerPreDispatch(XenForo_Controller $controller, $action)
    {

        $visitor = XenForo_Visitor::getInstance();

        if ($controller instanceof XenForo_ControllerPublic_Abstract && $visitor['user_id']) {

            if ($visitor['fs_save_address']) {
                return;
            }

            $userId = $visitor['user_id'];

            $options = XenForo_Application::getOptions();

            $userUpgradeIds = $options->fs_address_upgrade_ids;

            if (empty($userUpgradeIds)) {
                return;
            }

            $db = XenForo_Application::getDb();

            $upgradeIds = array_filter(array_map('trim', explode(',', $userUpgradeIds)));

            $upgradeIdsList = implode(',', array_map('intval', $upgradeIds)); // Convert array to a safe string

            $active = $db->fetchRow("
    SELECT user_upgrade_active.*,
           user.*
    FROM xf_user_upgrade_active AS user_upgrade_active
    INNER JOIN xf_user AS user ON
        (user.user_id = user_upgrade_active.user_id)
    WHERE user_upgrade_active.user_id = ?
        AND user_upgrade_active.user_upgrade_id IN ($upgradeIdsList)
", $userId);

            if ($active) {
                return $controller->canonicalizeRequestUrl(
                    XenForo_Link::buildPublicLink('address')
                );
            }
        }

        return;
    }
}
