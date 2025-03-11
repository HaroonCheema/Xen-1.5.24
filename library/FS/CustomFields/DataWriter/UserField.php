<?php

class FS_CustomFields_DataWriter_UserField extends XFCP_FS_CustomFields_DataWriter_UserField {

    protected function _getFields() {
        $fields = parent::_getFields();
      
        $fields['xf_user_field']['show_address'] = array('type' => self::TYPE_UINT, 'default' => 0);
        return $fields;
    }


}
