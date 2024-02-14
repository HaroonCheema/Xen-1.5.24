<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
 class ForumCube_PopularThreads_XenForo_Model_Thread extends XFCP_ForumCube_PopularThreads_XenForo_Model_Thread
 {

        
        
        public function updateThreadViews()
        {
            
          $db = $this->_getDb();
         
          
          $count=$db->fetchAll('SELECT thread_id, COUNT(*) AS total
				FROM xf_thread_view
				GROUP BY thread_id');
      
         foreach($count as $views)
             {
             for($i=0;$i<$views['total'];$i++)
             {
             $threadId=$views['thread_id'];
             $insert=1;
             		$db->query('
			Insert into fc_thread_view (thread_id,view_count,date) values  ('.$threadId.','.$insert.','.time().')');
                   
             }
             
        }
    
             return parent::updateThreadViews();
         }
         
                

        
 }
