<?php

class FS_CustomFields_ControllerAdmin_UserUpgrade extends XFCP_FS_CustomFields_ControllerAdmin_UserUpgrade
{

    public function actionExport()
    {

        $options = XenForo_Application::getOptions();

        $activeUpgradeUsers = $this->_getUpgradeRecordsListParams(true);
        $expiredUpgradeUsers = $this->_getUpgradeRecordsListParams(false);

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
}
