<?php

class ForumCube_PopularThreads_Model_Popular extends XenForo_Model
{
   
    public function getViewsById($id,$fetchOptions =array())
    {
        
        $joinOptions = $this->prepareViewsFetchOptions($fetchOptions);
        
        $qry = '    SELECT fc_thread_view.*
                            ' . $joinOptions['selectFields'] . '
                    FROM fc_thread_view'
                    . $joinOptions['joinTables'] .
                    ' WHERE id = '.$id.
                     $joinOptions['groupBy'];

        return $this->_getDb()->fetchRow($qry);        
    }
    
    public function getPopularViews($date,$exclude,$limit)
    {
        if($exclude == "")
            {
                $exclude=0;
            }
        
           $qry = 'SELECT xf_thread.*,xf_post.*,A.* FROM xf_thread 
                   INNER JOIN xf_post
                   ON  (xf_thread.first_post_id=xf_post.post_id) 

                    INNER JOIN ( SELECT thread_id,COUNT(*) as count FROM fc_thread_view WHERE date>'.$date.' and thread_id not In('.$exclude.')  GROUP BY thread_id ORDER BY count DESC limit '.$limit.') AS A 
                   ON (A.thread_id=xf_thread.thread_id and xf_thread.first_post_id=xf_post.post_id) ORDER BY A.count DESC';
           
//           echo '<pre>';
//           var_dump($this->_getDb()->fetchAll($qry));
           return $this->_getDb()->fetchAll($qry); 
    
}
   
   
   
     public function deleteOldData()
   {
       $date=strtotime( '-1 month');
       
       $qry='DELETE FROM fc_thread_view where date<'.$date.'';
      
       return $this->_getDb()->fetchAll($qry); 
   }
   


        public function getAllViews($conditions = array(), $fetchOptions = array())
    {
        $joinOptions = $this->prepareViewsFetchOptions($fetchOptions);
        $whereConditions = $this->prepareViewsConditions($conditions);
        
        $qry = '    SELECT fc_thread_view.*
                            ' . $joinOptions['selectFields'] . '
                    FROM fc_thread_view
                    ' . $joinOptions['joinTables'];
        if($whereConditions)
        {
            $qry .= ' WHERE  '.$whereConditions;
        }
       $qry .= $joinOptions['groupBy'];
   
   
      
        return $this->fetchAllKeyed($qry, 'id');        
    }
    
    public function prepareViewsFetchOptions(array $fetchOptions) {
            $selectFields = '';
            $joinTables = '';
            $orderBy = '';
            $groupBy = '';

            $db = $this->_getDb();
            if(isset($fetchOptions['join']))
            {
            
             }
            
            return array(
                'selectFields' => $selectFields,
                'joinTables' => $joinTables,
                'orderClause'  => ($orderBy ? "ORDER BY $orderBy" : ''),
                'groupBy'    => $groupBy
            );
    }
    protected function prepareViewsConditions($conditions)
    {
        $sqlConditions = array();
        $db = $this->_getDb();
        
    
        
        return $this->getConditionsForClause($sqlConditions);
    }
    
    

    
    

    
}
