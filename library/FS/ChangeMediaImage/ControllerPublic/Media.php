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

        if (!$media) {
            throw new XenForo_Exception('Missing media record.');
        }

        if (!(($visitor['user_id'] == $media['user_id'] && $visitor->hasPermission('fsChangeMediaImagePer', 'fs_change_own_image')) || $visitor->hasPermission('fsChangeMediaImagePer', 'fs_change_other_image'))) {
            throw $this->getNoPermissionResponseException();
        }

        if ($this->isConfirmedPost()) {

            $file = XenForo_Upload::getUploadedFile('changedImage');


            if ($file) {

                $attachmentModel = $this->_getAttachmentModel();
                $attachmentHandler = $attachmentModel->getAttachmentHandler('xengallery_media');
                $attachmentConstraints = $attachmentHandler->getUploadConstraints($media['media_type']);


                $exif = array();
                if ($media['media_type'] == 'image_upload') {
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
                }

                $file->setConstraints($attachmentConstraints);

                if (!$file->isImage()) {
                    throw new XenForo_Exception(new XenForo_Phrase('uploaded_file_is_not_valid_image'), true);
                };

                if (!$file->isValid()) {
                    return $this->responseError($file->getErrors());
                }


                if ($attachmentConstraints['storage'] > 0) {
                    $visitor = XenForo_Visitor::getInstance();

                    $newFileSize = filesize($file->getTempFile());

                    if (($visitor['xengallery_media_quota'] + ($newFileSize / 1024)) > ($attachmentConstraints['storage'] / 1024)) {
                        return $this->responseError(new XenForo_Phrase(
                            'xengallery_you_have_exceeded_your_allowed_storage_quota',
                            array(
                                'quota' => XenForo_Locale::numberFormat($attachmentConstraints['storage'], 'size'),
                                'filesize' => XenForo_Locale::numberFormat($newFileSize, 'size'),
                                'storage' => XenForo_Locale::numberFormat($visitor['xengallery_media_quota'] * 1024, 'size')
                            )
                        ));
                    }
                }

                $mediaModel->deletePreviousImages($media);

                $fileModel = $this->_getFileModel();

                $dataId = $fileModel->insertMediaUploadedAttachmentData($file, XenForo_Visitor::getUserId(), $media, $exif);
                if (!$dataId) {
                    return $this->responseError(new XenForo_Phrase('uploaded_file_does_not_have_an_allowed_extension'));
                }


                $mediaDw = XenForo_DataWriter::create('XenGallery_DataWriter_Media');
                $mediaDw->setExistingData($media);

                $time = XenForo_Application::$time;

                $mediaDw->bulkSet(array(
                    'last_edit_date' => $time,
                    'thumbnail_date' => $time
                ));

                $mediaDw->save();

                $message = new XenForo_Phrase('upload_completed_successfully');
            }

            return $this->responseRedirect(
                XenForo_ControllerResponse_Redirect::SUCCESS,
                XenForo_Link::buildPublicLink('xengallery', $media),
                $message
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
