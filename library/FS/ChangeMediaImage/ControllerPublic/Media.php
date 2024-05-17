<?php

class FS_ChangeMediaImage_ControllerPublic_Media extends XFCP_FS_ChangeMediaImage_ControllerPublic_Media
{

    // public function actionView()
    // {
    //     $parent = parent::actionView();


    //     $this->_request->setParam('canChangeImage', $mediaModel->canChangeImage($media));

    //     return $parent;
    // }

    public function actionChangeImage()
    {
        $visitor = XenForo_Visitor::getInstance();

        $mediaId = $this->_input->filterSingle('media_id', XenForo_Input::UINT);
        if (!$mediaId) {
            return $this->responseRedirect(
                XenForo_ControllerResponse_Redirect::RESOURCE_CANONICAL_PERMANENT,
                XenForo_Link::buildPublicLink('xengallery')
            );
        }

        $mediaHelper = $this->_getMediaHelper();

        $mediaModel = $this->_getMediaModel();

        $media = $mediaHelper->assertMediaValidAndViewable($mediaId);
        $media = $mediaModel->prepareMedia($media);

        if (!(($visitor['user_id'] == $media['user_id'] && $visitor->hasPermission('fsChangeMediaImagePer', 'fs_change_own_image')) || $visitor->hasPermission('fsChangeMediaImagePer', 'fs_change_other_image'))) {
            throw $this->getNoPermissionResponseException();
        }

        $mediaHelper->assertCanChangeMediaThumbnail($media);

        if ($this->isConfirmedPost()) {
            $changedImage = XenForo_Upload::getUploadedFile('changedImage');

            if ($changedImage) {
                $mediaModel->uploadMediaImage($changedImage, $media);

                // if (XenForo_Visitor::getUserId() != $media['user_id']) {
                // 	XenForo_Model_Log::logModeratorAction('xengallery_media', $media, 'thumbnail_add');
                // }
            }

            return $this->responseRedirect(
                XenForo_ControllerResponse_Redirect::SUCCESS,
                XenForo_Link::buildPublicLink('xengallery', $media)
            );
        } else {
            $viewParams = array(
                'media' => $media,
                'categoryBreadcrumbs' => $this->_getCategoryModel()->getCategoryBreadcrumb($media),
            );

            return $this->responseView('XenGallery_ViewPublic_Media_ThumbnailUpload', 'fs_change_media_image', $viewParams);
        }
    }
}
