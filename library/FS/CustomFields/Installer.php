<?php

class FS_CustomFields_Installer {

    protected static $_db;
    private static $_init;

    private static function init() {

        self::$_init = ["addField" => "ALTER TABLE `xf_user_field` ADD `show_address`  INT  DEFAULT 0",
            'dropField' => "ALTER TABLE `xf_user_field` DROP `show_address`;",
        ];
    }

    public static function install() {
        self::init();
        $db = XenForo_Application::get('db');
        $db->query(self::$_init['addField']);
    }

    public static function uninstall() {

        $db = XenForo_Application::get('db');

        self::init();
        $db->query(self::$_init['dropField']);
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
