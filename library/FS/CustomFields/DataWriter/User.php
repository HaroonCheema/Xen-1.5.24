<?php

class FS_CustomFields_DataWriter_User extends XFCP_FS_CustomFields_DataWriter_User
{

    protected function _getFields()
    {
        $fields = parent::_getFields();

        $fields['xf_user']['fs_save_address'] = array('type' => self::TYPE_STRING, 'default' => '', 'maxLength' => 10);
        return $fields;
    }
}
