<?php

class FS_ChangeMediaImage_Model_Media extends XFCP_FS_ChangeMediaImage_Model_Media
{
        public function canChangeImage(array $media, &$errorPhraseKey = '', array $viewingUser = null)
        {
                $this->standardizeViewingUserReference($viewingUser);

                if ($media['user_id'] == $viewingUser['user_id'] && XenForo_Permission::hasPermission($viewingUser['permissions'], 'fsChangeMediaImagePer', 'fs_change_own_image')) {
                        return true;
                }

                if (XenForo_Permission::hasPermission($viewingUser['permissions'], 'fsChangeMediaImagePer', 'fs_change_other_image')) {
                        return true;
                }

                $errorPhraseKey = 'xengallery_no_change_image_permission';
                return false;
        }

        public function uploadMediaImage(XenForo_Upload $upload, array $media)
        {

                if (!$media) {
                        throw new XenForo_Exception('Missing media record.');
                }

                if (!$upload->isValid()) {
                        throw new XenForo_Exception($upload->getErrors(), true);
                }

                if (!$upload->isImage()) {
                        throw new XenForo_Exception(new XenForo_Phrase('uploaded_file_is_not_valid_image'), true);
                };

                $baseTempFile = $upload->getTempFile();

                $imageType = $upload->getImageInfoField('type');
                $width = $upload->getImageInfoField('width');
                $height = $upload->getImageInfoField('height');

                return $this->processMediaImage($media, $baseTempFile, $imageType, $width, $height);
        }

        public function processMediaImage(array $media, $fileName, $imageType = false, $width = false, $height = false)
        {
                if (!$imageType || !$width || !$height) {
                        $imageInfo = getimagesize($fileName);
                        if (!$imageInfo) {
                                throw new XenForo_Exception('Non-image passed in to mediaImage');
                        }
                        $imageType = $imageInfo[2];
                }

                if (!in_array($imageType, array(IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG))) {
                        throw new XenForo_Exception(new XenForo_Phrase('uploaded_file_is_not_valid_image'), true);
                }

                if (!XenForo_Image_Abstract::canResize($width, $height)) {
                        throw new XenForo_Exception(new XenForo_Phrase('uploaded_image_is_too_big'), true);
                }

                if ($media['media_type'] == 'video_embed') {
                        $changedImageFile = $this->getOriginalDataFilePath($media['media_tag']);
                        $changedImageFile = $changedImageFile[0];
                } else {
                        $changedImageFile = $this->getOriginalDataFilePath($media);
                }

                XenForo_Helper_File::createDirectory(dirname($changedImageFile));

                $changeImage = new XenGallery_Helper_Image($fileName);
                if ($changeImage) {

                        $changedImage = $changeImage->saveToPath($changedImageFile);

                        if ($changedImage) {
                                @unlink($fileName);
                                $writeSuccess = true;
                        } else {
                                $writeSuccess = false;
                        }

                        if ($writeSuccess) {
                                $dw = XenForo_DataWriter::create('XenForo_DataWriter_AttachmentData');
                                $dw->setExistingData($media['data_id']);
                                $dw->bulkSet(array(
                                        'width' => $width,
                                        'height' => $height,
                                        'file_size' => strlen($changedImageFile)
                                ));
                                $dw->setExtraData(XenForo_DataWriter_AttachmentData::DATA_TEMP_FILE, $changedImageFile);
                                $dw->save();

                                $mediaDw = XenForo_DataWriter::create('XenGallery_DataWriter_Media');
                                $mediaDw->setExistingData($media);

                                $time = XenForo_Application::$time;

                                $mediaDw->bulkSet(array(
                                        'last_edit_date' => $time,
                                        'thumbnail_date' => $time
                                ));

                                $mediaDw->save();
                        }

                        return $writeSuccess;
                }

                return false;
        }
}
