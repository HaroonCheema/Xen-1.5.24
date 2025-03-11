<?php

class FS_CustomFields_ControllerAdmin_UserField extends XFCP_FS_CustomFields_ControllerAdmin_UserField {

    public function actionSave() {
        $parent = parent::actionSave();
        if ($parent instanceof XenForo_ControllerResponse_Redirect) {
           $fieldId = $this->_input->filterSingle('field_id', XenForo_Input::STRING);
          
            
            $dw = XenForo_DataWriter::create('XenForo_DataWriter_UserField');
            if ($fieldId) {
                $dw->setExistingData($fieldId);

                $input = $this->_input->filter(array(
                    'show_address' => XenForo_Input::UINT,
                ));
                $dw->bulkSet($input);
                $dw->save();
            }
        }
        return $parent;
    }

}
