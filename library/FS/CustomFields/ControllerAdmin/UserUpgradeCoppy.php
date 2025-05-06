<?php

class FS_CustomFields_ControllerAdmin_UserUpgrade extends XFCP_FS_CustomFields_ControllerAdmin_UserUpgrade
{

    public function actionExport()
    {

        $options = XenForo_Application::getOptions();

        $activeUpgradeUsers = $this->_getUpgradeRecordsListParamsExport(true);
        $expiredUpgradeUsers = $this->_getUpgradeRecordsListParamsExport(false);

        $saveAddressField = $options->fs_save_address_fields;

        $csvFileName = $options->fs_address_csv_file_name . ".csv";

        if (empty($options->fs_address_csv_file_name)) {
            $csvFileName = "active_user_upgrade.csv";
        }

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $csvFileName . '"');

        $output = fopen('php://output', 'w');

        $dataColumn = ["username", "email", "title", "start_date", "end_date"];

        $isFirst = true;

        if (count($activeUpgradeUsers['upgradeRecords'])) {
            $activeUpgrades = $activeUpgradeUsers['upgradeRecords'];

            foreach ($activeUpgrades as $key => $value) {

                $customFields = $this->getModelFromCache('XenForo_Model_UserField')->getUserFields(
                    array('showaddress' => true),
                    array('valueUserId' => $value['user_id'])
                );

                $activeCustomFields = $this->getModelFromCache('XenForo_Model_UserField')->prepareUserFields($customFields, true);

                if ($isFirst) {

                    foreach (array_keys($activeCustomFields) as $customKey) {
                        $dataColumn[] = $customKey;
                    }

                    fputcsv($output, $dataColumn);
                    $isFirst = false;
                }

                if ($saveAddressField && isset($activeCustomFields[$saveAddressField]['field_value'])) {
                    $row = [
                        isset($value['username']) ? $value['username'] : "",
                        isset($value['email']) ? $value['email'] : "",
                        isset($value['title']) ? $value['title'] : "",
                        isset($value['start_date']) ? $value['start_date'] : "",
                        isset($value['end_date']) ? $value['end_date'] : "",
                    ];

                    foreach (array_keys($activeCustomFields) as $customKey) {
                        $row[] = isset($activeCustomFields[$customKey]['field_value']) ? $activeCustomFields[$customKey]['field_value'] : "";
                    }

                    fputcsv($output, $row);
                }
            }
        }

        if ($expiredUpgradeUsers['upgradeRecords']) {
            $expiredUpgrades = $expiredUpgradeUsers['upgradeRecords'];

            foreach ($expiredUpgrades as $key => $value) {

                $customFields = $this->getModelFromCache('XenForo_Model_UserField')->getUserFields(
                    array('showaddress' => true),
                    array('valueUserId' => $value['user_id'])
                );

                $expiredCustomFields = $this->getModelFromCache('XenForo_Model_UserField')->prepareUserFields($customFields, true);

                if ($isFirst) {

                    foreach (array_keys($expiredCustomFields) as $customKey) {
                        $dataColumn[] = $customKey;
                    }

                    fputcsv($output, $dataColumn);
                    $isFirst = false;
                }

                if ($saveAddressField && isset($expiredCustomFields[$saveAddressField]['field_value'])) {

                    $row = [
                        isset($value['username']) ? $value['username'] : "",
                        isset($value['email']) ? $value['email'] : "",
                        isset($value['title']) ? $value['title'] : "",
                        isset($value['start_date']) ? $value['start_date'] : "",
                        isset($value['end_date']) ? $value['end_date'] : "",
                    ];

                    foreach (array_keys($expiredCustomFields) as $customKey) {
                        $row[] = isset($expiredCustomFields[$customKey]['field_value']) ? $expiredCustomFields[$customKey]['field_value'] : "";
                    }

                    fputcsv($output, $row);
                }
            }
        }

        fclose($output);

        exit;
    }

    protected function _getUpgradeRecordsListParamsExport($active)
    {
        $userUpgradeModel = $this->_getUserUpgradeModel();

        $page = $this->_input->filterSingle('page', XenForo_Input::UINT);
        $perPage = 20000;
        $pageNavParams = array();

        $fetchOptions = array(
            'page' => $page,
            'perPage' => $perPage,
            'join' => XenForo_Model_UserUpgrade::JOIN_UPGRADE,
        );

        $orderBy = $this->_input->filterSingle('order', XenForo_Input::STRING);
        $orderDirection = $this->_input->filterSingle('direction', XenForo_Input::STRING);
        if ($orderBy) {
            $fetchOptions['order'] = $orderBy;
            $fetchOptions['direction'] = $orderDirection;
            $pageNavParams['order'] = $orderBy;
            $pageNavParams['direction'] = $orderDirection;
        }

        $conditions = array(
            'active' => $active
        );

        return array(
            'upgradeRecords' => $userUpgradeModel->getUserUpgradeRecordsExport($conditions, $fetchOptions),
        );
    }
}
