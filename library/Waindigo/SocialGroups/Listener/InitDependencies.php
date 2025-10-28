<?php

class Waindigo_SocialGroups_Listener_InitDependencies extends Waindigo_Listener_InitDependencies
{

    public function run()
    {
        XenForo_Model_Import::$extraImporters[] = "Waindigo_SocialGroups_Importer_XfAddOns_Groups";

        XenForo_CacheRebuilder_Abstract::$builders['SocialGroups'] = 'Waindigo_SocialGroups_CacheRebuilder_SocialForum';
        
        parent::run();
    }

    public static function initDependencies(XenForo_Dependencies_Abstract $dependencies, array $data)
    {
        $initDependencies = new Waindigo_SocialGroups_Listener_InitDependencies($dependencies, $data);
        $initDependencies->run();
    }
}