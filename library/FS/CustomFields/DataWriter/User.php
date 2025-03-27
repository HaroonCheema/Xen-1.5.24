<?php

class FS_CustomFields_DataWriter_User extends XFCP_FS_CustomFields_DataWriter_User
{

    protected function _getFields()
    {
        $fields = parent::_getFields();

        $fields['xf_user']['fs_save_address'] = array('type' => self::TYPE_STRING, 'default' => '', 'maxLength' => 10);

        $fields['xf_user']['upgrade_after_addon'] = array('type' => self::TYPE_UINT, 'default' => 0);
        $fields['xf_user']['amplify_order_submitted'] = array('type' => self::TYPE_UINT, 'default' => 0);

        $fields['xf_user']['amplifier_order_id'] = array('type' => self::TYPE_STRING, 'default' => '', 'maxLength' => 150);

        return $fields;
    }
}
