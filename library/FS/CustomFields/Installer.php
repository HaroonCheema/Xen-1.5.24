<?php

class FS_CustomFields_Installer {

    protected static $_db;
    private static $_init;

    private static function init() {

        self::$_init = [
            "addField" => "ALTER TABLE `xf_user_field` ADD `show_address`  INT  DEFAULT 0",
            'saveAddressUser' => "ALTER TABLE `xf_user` ADD `fs_save_address` VARCHAR(10) NOT NULL DEFAULT ''",
            "upgradeAfterAddon" => "ALTER TABLE `xf_user` ADD `upgrade_after_addon`  INT  DEFAULT 0",
            "amplifyOrderSubmitted" => "ALTER TABLE `xf_user` ADD `amplify_order_submitted`  INT  DEFAULT 0",
            'amplifierOrderId' => "ALTER TABLE `xf_user` ADD `amplifier_order_id` VARCHAR(150) NOT NULL DEFAULT ''",
            'dropField' => "ALTER TABLE `xf_user_field` DROP `show_address`;",
            'dropFieldUser' => "ALTER TABLE `xf_user` DROP `fs_save_address`;",
            'dropUpgradeAfterAddon' => "ALTER TABLE `xf_user` DROP `upgrade_after_addon`;",
            'dropAmplifyOrderSubmitted' => "ALTER TABLE `xf_user` DROP `amplify_order_submitted`;",
            'dropAmplifierOrderId' => "ALTER TABLE `xf_user` DROP `amplifier_order_id`;",
        ];
    }

    public static function install($installedAddon) {
        $installedVersion = is_array($installedAddon) ? $installedAddon['version_id'] : 0;
        if ($installedVersion <= 1) { //New Install
            self::init();
            $db = XenForo_Application::get('db');
            $db->query(self::$_init['addField']);
            $db->query(self::$_init['saveAddressUser']);
            $db->query(self::$_init['upgradeAfterAddon']);
            $db->query(self::$_init['amplifyOrderSubmitted']);
            $db->query(self::$_init['amplifierOrderId']);
        }
    }

    public static function uninstall() {

        $db = XenForo_Application::get('db');

        self::init();
        $db->query(self::$_init['dropField']);
        $db->query(self::$_init['dropFieldUser']);
        $db->query(self::$_init['dropUpgradeAfterAddon']);
        $db->query(self::$_init['dropAmplifyOrderSubmitted']);
        $db->query(self::$_init['dropAmplifierOrderId']);
    }

    protected static function _executeQuery($sql, array $bind = array()) {
        try {
            return self::_getDb()->query($sql, $bind);
        } catch (Zend_Db_Exception $e) {
            return false;
        }
    }

    /**
     * @return Zend_Db_Adapter_Abstract
     */
    protected static function _getDb() {
        if (!self::$_db) {
            self::$_db = XenForo_Application::getDb();
        }

        return self::$_db;
    }
}
