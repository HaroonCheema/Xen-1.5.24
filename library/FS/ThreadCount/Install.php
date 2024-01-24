<?php

class FS_ThreadCount_Install {

    private static $_instance;
 

    public static final function getInstance() {
        if (!self::$_instance) {
            $self = __CLASS__;
            self::$_instance = new $self();
        }
        return self::$_instance;
    }

    public static function install() {
        
        $db = XenForo_Application::getDb();
        $db->query("ALTER TABLE `xf_user` ADD `thread_count` int(10)  NOT NULL DEFAULT 0");
        
        self::UpdateThreadCount();
        
    }

    public static function uninstallCode() {


        $db = XenForo_Application::get('db');

        $db->query("ALTER TABLE xf_user DROP thread_count");
    }
    
    protected static function UpdateThreadCount(){
        
             $db = XenForo_Application::get('db');
        $ThreadCounts = $db->query("SELECT COUNT(thread_id) as thread_count,user_id FROM `xf_thread` GROUP BY `user_id`")->fetchAll();

        
        if(count($ThreadCounts)){
            
            
            foreach($ThreadCounts as $count){
                

                $db->query('
			UPDATE xf_user
			SET thread_count = ?
			WHERE user_id = ?
		', [$count['thread_count'], $count['user_id']]);
                
            }
        }
    }

}