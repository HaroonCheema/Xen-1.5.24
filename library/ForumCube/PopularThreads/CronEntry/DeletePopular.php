<?php

class ForumCube_PopularThreads_CronEntry_DeletePopular
{
    
    public static function delete()
    {
       
        
        XenForo_Model::create('ForumCube_PopularThreads_Model_Popular')->deleteOldData();
       
        
    }
}