<?php

class ForumCube_PopularThreads_Installer_Installer
{

     public static $createQueries = array(
                                
         
         'fc_thread_view'  => 'CREATE TABLE IF NOT EXISTS `fc_thread_view` (
                                            `id` INT(10) NOT NULL AUTO_INCREMENT,
                                            `date` INT(11) ,
                                            `thread_id` INT(10),
                                            `view_count` INT(11) NULL DEFAULT NULL,
                                            PRIMARY KEY (`id`)
                                    )'
                                   
                                ); 
     
    public static $deleteQueries   = array(        
         
                                    'fc_thread_view' => 'DROP TABLE IF EXISTS `fc_thread_view`',

                                    
    );
    
    public static function install()
    {
        $db = XenForo_Application::get('db');
        
        foreach(self::$createQueries as $query){
            
                $db->query($query);
        }

    }

    public static function unInstall()
    {
        $db = XenForo_Application::get('db');
       
        foreach(self::$deleteQueries as $query){
            
           $db->query($query);
        }
    }

}

