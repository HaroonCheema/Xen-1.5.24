<?php

class FS_CustomFields_ControllerAdmin_UserUpgrade extends XFCP_FS_CustomFields_ControllerAdmin_UserUpgrade
{

    public function actionExport()
    {

        $options = XenForo_Application::getOptions();

        $userUpgradeIds = $options->fs_address_upgrade_ids;

        $csvFileName = $options->fs_address_csv_file_name . ".csv";

        if (empty($options->fs_address_csv_file_name)) {
            $csvFileName = "active_user_upgrade.csv";
        }

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $csvFileName . '"');

        $output = fopen('php://output', 'w');

        if (empty($userUpgradeIds)) {

            fclose($output);

            exit;
        }

        $activeUpgradeUsers = $this->_getUpgradeRecordsListParamsExport(true);

        $dataColumn = ["username", "email", "title", "start_date", "end_date"];

        $isFirst = true;

        if (count($activeUpgradeUsers['upgradeRecords'])) {
            $activeUpgrades = $activeUpgradeUsers['upgradeRecords'];

            foreach ($activeUpgrades as $key => $value) {

                if ($isFirst) {

                    $customFields = $this->getModelFromCache('XenForo_Model_UserField')->getUserFields(
                        array('showaddress' => true),
                        array('valueUserId' => $value['user_id'])
                    );

                    $activeCustomFields = $this->getModelFromCache('XenForo_Model_UserField')->prepareUserFields($customFields, true);

                    foreach (array_keys($activeCustomFields) as $customKey) {
                        $dataColumn[] = $customKey;
                    }

                    fputcsv($output, $dataColumn);
                    $isFirst = false;
                }

                $row = [
                    isset($value['username']) ? $value['username'] : "",
                    isset($value['email']) ? $value['email'] : "",
                    isset($value['title']) ? $value['title'] : "",
                    isset($value['start_date']) ? $value['start_date'] : "",
                    isset($value['end_date']) ? $value['end_date'] : "",

                    isset($value['receive_swag']) ? $value['receive_swag'] : "",
                    isset($value['shipping_name']) ? $value['shipping_name'] : "",
                    isset($value['shipping_street_address']) ? $value['shipping_street_address'] : "",
                    isset($value['shipping_street_address_2']) ? $value['shipping_street_address_2'] : "",
                    isset($value['shipping_city']) ? $value['shipping_city'] : "",
                    isset($value['shipping_postal']) ? $value['shipping_postal'] : "",
                    isset($value['shipping_country']) ? $value['shipping_country'] : "",
                    isset($value['shipping_state']) ? $value['shipping_state'] : "",
                    isset($value['shipping_country']) ? $value['shipping_country'] : "",
                ];

                fputcsv($output, $row);
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

        $options = XenForo_Application::getOptions();

        $userUpgradeIds = $options->fs_address_upgrade_ids;

        $upgradeIds = array_filter(array_map('trim', explode(',', $userUpgradeIds)));

        // $upgradeIdsList = implode(',', array_map('intval', $upgradeIds)); // Convert array to a safe string

        $conditions = array(
            'active' => $active,
            'user_upgrade_id' => $upgradeIds
        );

        return array(
            'upgradeRecords' => $userUpgradeModel->getUserUpgradeRecordsExport($conditions, $fetchOptions),
        );
    }
}
