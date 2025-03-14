<?php

class FS_CustomFields_ControllerAdmin_UserUpgrade extends XFCP_FS_CustomFields_ControllerAdmin_UserUpgrade
{

    public function actionExport()
    {

        $options = XenForo_Application::getOptions();

        $activeUpgradeUsers = $this->_getUpgradeRecordsListParams(true);
        $expiredUpgradeUsers = $this->_getUpgradeRecordsListParams(false);

        $dataColumn = ["username", "email", "title", "start_date", "end_date", "shipping_name", "shipping_street_address", "shipping_street_address_2", "shipping_city", "shipping_state", "shipping_postal", "shipping_country", "receive_swag", "t_shirt_size", "share_address"];

        // if (count($activeUpgradeUsers['upgradeRecords']) || count($expiredUpgradeUsers['upgradeRecords'])) {

        $csvFileName = $options->fs_address_csv_file_name . ".csv";

        if (empty($options->fs_address_csv_file_name)) {
            $csvFileName = "active_user_upgrade.csv";
        }

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $csvFileName . '"');

        $output = fopen('php://output', 'w');

        fputcsv($output, $dataColumn);
        // }

        if (count($activeUpgradeUsers['upgradeRecords'])) {
            $activeUpgrades = $activeUpgradeUsers['upgradeRecords'];

            foreach ($activeUpgrades as $key => $value) {

                $customFields = $this->getModelFromCache('XenForo_Model_UserField')->getUserFields(
                    array(),
                    array('valueUserId' => $value['user_id'])
                );

                $activeCustomFields = $this->getModelFromCache('XenForo_Model_UserField')->prepareUserFields($customFields, true);

                if (isset($activeCustomFields['receive_swag']['field_value']) && strtolower($activeCustomFields['receive_swag']['field_value']) == "yes") {

                    $row = [
                        isset($value['username']) ? $value['username'] : "",
                        isset($value['email']) ? $value['email'] : "",
                        isset($value['title']) ? $value['title'] : "",
                        isset($value['start_date']) ? $value['start_date'] : "",
                        isset($value['end_date']) ? $value['end_date'] : "",
                        isset($activeCustomFields['shipping_name']) ? $activeCustomFields['shipping_name']['field_value'] : "",
                        isset($activeCustomFields['shipping_street_address']) ? $activeCustomFields['shipping_street_address']['field_value'] : "",
                        isset($activeCustomFields['shipping_street_address_2']) ? $activeCustomFields['shipping_street_address_2']['field_value'] : "",
                        isset($activeCustomFields['shipping_city']) ? $activeCustomFields['shipping_city']['field_value'] : "",
                        isset($activeCustomFields['shipping_state']) ? $activeCustomFields['shipping_state']['field_value'] : "",
                        isset($activeCustomFields['shipping_postal']) ? $activeCustomFields['shipping_postal']['field_value'] : "",
                        isset($activeCustomFields['shipping_country']) ? $activeCustomFields['shipping_country']['field_value'] : "",
                        isset($activeCustomFields['receive_swag']) ? $activeCustomFields['receive_swag']['field_value'] : "",
                        isset($activeCustomFields['t_shirt_size']) ? $activeCustomFields['t_shirt_size']['field_value'] : "",
                        isset($activeCustomFields['share_address']) ? $activeCustomFields['share_address']['field_value'] : ""
                    ];

                    fputcsv($output, $row);
                }
            }
        }

        if ($expiredUpgradeUsers['upgradeRecords']) {
            $expiredUpgrades = $expiredUpgradeUsers['upgradeRecords'];

            foreach ($expiredUpgrades as $key => $value) {

                $customFields = $this->getModelFromCache('XenForo_Model_UserField')->getUserFields(
                    array(),
                    array('valueUserId' => $value['user_id'])
                );

                $expiredCustomFields = $this->getModelFromCache('XenForo_Model_UserField')->prepareUserFields($customFields, true);

                if (isset($expiredCustomFields['receive_swag']['field_value']) && strtolower($expiredCustomFields['receive_swag']['field_value']) == "yes") {

                    $row = [
                        isset($value['username']) ? $value['username'] : "",
                        isset($value['email']) ? $value['email'] : "",
                        isset($value['title']) ? $value['title'] : "",
                        isset($value['start_date']) ? $value['start_date'] : "",
                        isset($value['end_date']) ? $value['end_date'] : "",
                        isset($expiredCustomFields['shipping_name']) ? $expiredCustomFields['shipping_name']['field_value'] : "",
                        isset($expiredCustomFields['shipping_street_address']) ? $expiredCustomFields['shipping_street_address']['field_value'] : "",
                        isset($expiredCustomFields['shipping_street_address_2']) ? $expiredCustomFields['shipping_street_address_2']['field_value'] : "",
                        isset($expiredCustomFields['shipping_city']) ? $expiredCustomFields['shipping_city']['field_value'] : "",
                        isset($expiredCustomFields['shipping_state']) ? $expiredCustomFields['shipping_state']['field_value'] : "",
                        isset($expiredCustomFields['shipping_postal']) ? $expiredCustomFields['shipping_postal']['field_value'] : "",
                        isset($expiredCustomFields['shipping_country']) ? $expiredCustomFields['shipping_country']['field_value'] : "",
                        isset($expiredCustomFields['receive_swag']) ? $expiredCustomFields['receive_swag']['field_value'] : "",
                        isset($expiredCustomFields['t_shirt_size']) ? $expiredCustomFields['t_shirt_size']['field_value'] : "",
                        isset($expiredCustomFields['share_address']) ? $expiredCustomFields['share_address']['field_value'] : ""
                    ];

                    fputcsv($output, $row);
                }
            }
        }

        fclose($output);

        exit;
    }
}
