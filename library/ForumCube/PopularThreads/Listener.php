<?php
class ForumCube_PopularThreads_Listener
{

    public static function navTabs(array &$extraTabs, $selectedTabId)
    {
      
       //This functionality is removed to hide the tab 
       //   
       $link= XenForo_Link::buildPublicLink('find-popular');
             $extraTabs['popular'] = array(
            'title' => new XenForo_Phrase('fc_popular_threads_tab'),
            'href'  => $link,
            'linksTemplate' => '',     
            'position'  =>  'end',
           'selected'=> ($selectedTabId == "popular"),
        );
       
        
    }
                                                            
    public static function loadClass($class, array &$extend)
    {
        if($class == "XenForo_Model_Thread")
        {

            $extend[] =  "ForumCube_PopularThreads_XenForo_Model_Thread";
        }
        
          if($class == "XenForo_Model_Like")
        {

            $extend[] =  "ForumCube_PopularThreads_XenForo_Model_Like";
        }

    }
    


        
}
