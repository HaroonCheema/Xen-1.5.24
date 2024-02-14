<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class ForumCube_PopularThreads_XenForo_ControllerPublic_Post extends XFCP_ForumCube_PopularThreads_XenForo_ControllerPublic_Post

{
public function actionLike()
{
    
   $postId = $this->_input->filterSingle('post_id', XenForo_Input::UINT);

   $popularLikeModel = $this->_getPopularLikeModel();
   
   $likeModel = $this->_getLikeModel();
   $existingLike = $likeModel->getContentLikeByLikeUser('post', $postId, XenForo_Visitor::getUserId());
   
$dw = XenForo_DataWriter::create('ForumCube_PopularThreads_DataWriter_PopularLikes');
 $fields=array(
     'date'=> time(),
     'post_id' => $postId,
     'like_count'=>'1',
 );



    $id=$popularLikeModel->getIdForPost($postId);
    


   if($existingLike && $id)
   {
     $dw->deleteLikes($id['id']);
   }
    else if(!$existingLike)
    {
       
        $dw->bulkSet($fields);
        $dw->save();
    }
   return parent::actionlike();
    
    
    
}

protected function _getPopularLikeModel()
{
    return $this->getModelFromCache('ForumCube_PopularThreads_Model_PopularLikes');
}

}