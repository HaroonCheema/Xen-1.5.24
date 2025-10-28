<?php

class ThemeHouse_KeywordReplace_Listener_FileHealthCheck
{

    public static function fileHealthCheck(XenForo_ControllerAdmin_Abstract $controller, array &$hashes)
    {
        $hashes = array_merge($hashes,
            array(
                'library/ThemeHouse/KeywordReplace/Extend/XenForo/BbCode/Formatter/Base.php' => 'ac818bfee4a82e80cf108dd9a0bfd786',
                'library/ThemeHouse/KeywordReplace/Helper/String.php' => '248fde4d22c852bd8946f10557c544af',
                'library/ThemeHouse/KeywordReplace/Install/Controller.php' => 'a777fded605cd2b7b651536b0b0c66ac',
                'library/ThemeHouse/KeywordReplace/Listener/LoadClass.php' => '2fbc712d871c2121aa551cb296e71511',
                'library/ThemeHouse/KeywordReplace/Option/KeywordReplace.php' => 'c572da39d38b577fc50c625233b9ec39',
                'library/ThemeHouse/KeywordReplace/Option/UserGroupChooser.php' => '5c4a78b98eefd398891143ca552dab0d',
                'library/ThemeHouse/Install.php' => '18f1441e00e3742460174ab197bec0b7',
                'library/ThemeHouse/Install/20151109.php' => '2e3f16d685652ea2fa82ba11b69204f4',
                'library/ThemeHouse/Deferred.php' => 'ebab3e432fe2f42520de0e36f7f45d88',
                'library/ThemeHouse/Deferred/20150106.php' => 'a311d9aa6f9a0412eeba878417ba7ede',
                'library/ThemeHouse/Listener/ControllerPreDispatch.php' => 'fdebb2d5347398d3974a6f27eb11a3cd',
                'library/ThemeHouse/Listener/ControllerPreDispatch/20150911.php' => 'f2aadc0bd188ad127e363f417b4d23a9',
                'library/ThemeHouse/Listener/InitDependencies.php' => '8f59aaa8ffe56231c4aa47cf2c65f2b0',
                'library/ThemeHouse/Listener/InitDependencies/20150212.php' => 'f04c9dc8fa289895c06c1bcba5d27293',
                'library/ThemeHouse/Listener/LoadClass.php' => '5cad77e1862641ddc2dd693b1aa68a50',
                'library/ThemeHouse/Listener/LoadClass/20150518.php' => 'f4d0d30ba5e5dc51cda07141c39939e3',
            ));
    }
}