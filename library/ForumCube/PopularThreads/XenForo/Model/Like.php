<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class ForumCube_PopularThreads_XenForo_Model_Like extends XFCP_ForumCube_PopularThreads_XenForo_Model_Like
 {
    
     public function getPopularPosts($date,$exclude,$limit)
    {
        if($exclude == "")
            {
                $exclude=0;
            }
            
                 $qry = 'SELECT xf_thread.*,xf_post.*,A.* FROM xf_post INNER JOIN xf_thread ON (xf_post.thread_id=xf_thread.thread_id) INNER JOIN ( SELECT content_id,COUNT(*) as count FROM xf_liked_content WHERE like_date>'.$date.' GROUP BY content_id ORDER BY count DESC limit '.$limit.') AS A ON (A.content_id=xf_post.post_id and xf_thread.thread_id=xf_post.thread_id and xf_post.thread_id NOT IN('.$exclude.'))ORDER BY A.count DESC';
       
          
            return $this->_getDb()->fetchAll($qry); 
        
   }
   
    
 
}