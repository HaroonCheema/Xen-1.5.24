<?php

class ForumCube_PopularThreads_ControllerPublic_Popular Extends XenForo_ControllerPublic_Abstract
{
    
    public function actionIndex() {
        
      $loggedIn=  XenForo_Visitor::getUserId();
      
       
      if($loggedIn)
        {
            return $this->actionPopular('threads',FALSE,7);
        }
        else
        {
          return  $this->actionPopular('threads',FALSE,30);
        }

        
    }
    public function actionThreads()
    {
        $limit = $this->_input->filterSingle('limit', XenForo_Input::UINT);
        if(!$limit)
        {
            $limit=7;
        }
        return $this->actionPopular($threads=TRUE,$posts=FALSE,$limit);
    }
    
    public function actionPosts()
    {
          $limit = $this->_input->filterSingle('limit', XenForo_Input::UINT);
      if(!$limit)
        {
            $limit=7;
        }
       
      
        return $this->actionPopular(FALSE,TRUE,$limit);
    }
    
    //<a href="{xen:link forums, $forum, 'prefix_id={$thread.prefix_id}'}"
   
    protected function actionPopular($threads=FALSE,$posts=FALSE,$days)
    {
      
      
        $option = XenForo_Application::get('options');
        $exclude=$option->fc_popular_threads_exclude;
        $limit=$option->fc_popular_limit;
        
        if($threads)
        {
            if($days=='7')
            {
               $date=  strtotime("-1 week");
            }
             if($days=='30')
            {
               $date=strtotime( '-1 month');
            }
            
           
            $threadModel=$this->_getPopularThreadModel();
            $data=$threadModel->getPopularViews($date,$exclude,$limit);
         
            $num=(count($data));
            
              $page = max(1, $this->_input->filterSingle('page', XenForo_Input::UINT));
	      $perPage = XenForo_Application::get('options')->fc_popular_perpage;
              
                 $start= (($page*$perPage)-$perPage);
                 $offset= $page*$perPage;
                 $data= array_slice($data,$start,$perPage);
            
                $resultStartOffset = $start;
		$resultEndOffset = count($data) + $start;
            
             $viewParams=array(
                  'comingFrom' =>'threads',
                  'selectedFirst'  => 'threads',
                  'selectedSecond'  => $days,
                  'data'=>$data,
                 'threadStartOffset' => $resultStartOffset,
		  'threadEndOffset' => $resultEndOffset,
                   'page' => $page,
		   'perPage' => $perPage,
                 'link' => 'find-popular/threads',
                 'total' =>  $num
              );
             
        }   

          if( $posts)
        { 
                if($days=='7')
            {
               
              $date=  strtotime("-1 week");
            }
             if($days=='30')
            {
               $date=strtotime( '-1 month');
            }
            
               $postModel=$this->_getPopularPostModel();
               $data= $postModel->getPopularPosts($date,$exclude,$limit);
               
               $num=(count($data));
            
              $page = max(1, $this->_input->filterSingle('page', XenForo_Input::UINT));
	      $perPage = XenForo_Application::get('options')->fc_popular_perpage;
              
                 $start= (($page*$perPage)-$perPage);
                 $offset= $page*$perPage;
                 
                 $data= array_slice($data,$start,$perPage);
            
                $resultStartOffset = $start;
		$resultEndOffset = count($data) + $start;
              
                
              $viewParams=array(
                  'comingFrom' =>'posts',
                  'selectedFirst'  => 'posts',
                  'selectedSecond'  => $days,
                  'data'  => $data,
                  'threadStartOffset' => $resultStartOffset,
		  'threadEndOffset' => $resultEndOffset,
                   'page' => $page,
		   'perPage' => $perPage,
                   'link' => 'find-popular/posts',
                   'total' =>  $num,
              );
              
        } 
       
        return $this->responseView('ForumCube_PopularThreds_ViewPublic_Popular', 'fc_popular_threads', $viewParams);
      
    }
    

    
    Private function _getPopularThreadModel()
    {
        return $this->getModelFromCache('ForumCube_PopularThreads_Model_Popular');
    }
    
    Private function _getPopularPostModel()
    {
        return $this->getModelFromCache('XenForo_Model_Like');
    }
    
    
    
}



//SELECT xf_thread.*,xf_post.*,A.* FROM xf_post INNER JOIN xf_thread ON (xf_post.thread_id=xf_thread.thread_id) INNER JOIN ( SELECT content_id,COUNT(*) as count FROM xf_liked_content_ WHERE date>'.$date.' GROUP BY content_id ORDER BY count DESC limit 10) AS A ON (A.content_id=xf_post.post_id and xf_thread.thread_id=xf_post.thread_id and xf_post.thread_id NOT IN('.$exclude.'))ORDER BY A.count DESC


//{xen:string wordTrim, $myString, 4} 

