<?php

class FS_ChangeMediaImage_ControllerPublic_Media extends XFCP_FS_ChangeMediaImage_ControllerPublic_Media
{

    public function actionView()
    {
        $parent = parent::actionView();

        if ($parent instanceof XenForo_ControllerResponse_View) {

            $media = $parent->params['media'];

            $mediaModel = $this->_getMediaModel();

            $parent->params['canChangeImage'] = $mediaModel->canChangeImage($media);
        }

        return $parent;
    }

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
            $file = XenForo_Upload::getUploadedFile('changedImage');

            if ($file) {
                // $mediaModel->uploadMediaImage($file, $media);




                $exif = array();
                // if ($input['upload_type'] == 'image_upload')
                // {
                if (function_exists('exif_read_data')) {
                    $filePath = $file->getTempFile();
                    $fileType = @getimagesize($filePath);

                    if (isset($fileType[2]) && $fileType[2] == IMAGETYPE_JPEG) {
                        @ini_set('exif.encode_unicode', 'UTF-8');
                        $exif = @exif_read_data($filePath, null, true);
                        if (isset($exif['FILE'])) {
                            $exif['FILE']['FileName'] = $file->getFileName();
                        }
                    }
                }
                // }

                // $file->setConstraints($attachmentConstraints);
                if (!$file->isValid()) {
                    return $this->responseError($file->getErrors());
                }


                $fileModel = $this->_getFileModel();

                $fileModel->insertMediaUploadedAttachmentData($file, XenForo_Visitor::getUserId(), $media, $exif);


                $mediaDw = XenForo_DataWriter::create('XenGallery_DataWriter_Media');
                $mediaDw->setExistingData($media);

                $time = XenForo_Application::$time;

                $mediaDw->bulkSet(array(
                    'last_edit_date' => $time,
                    'thumbnail_date' => $time
                ));

                $mediaDw->save();

                // $attachmentId = $attachmentModel->insertTemporaryAttachment($dataId, $input['hash']);

                // $message = new XenForo_Phrase('upload_completed_successfully');


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
