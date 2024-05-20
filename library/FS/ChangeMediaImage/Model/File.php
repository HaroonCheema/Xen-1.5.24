<?php

class FS_ChangeMediaImage_Model_File extends XFCP_FS_ChangeMediaImage_Model_File
{
        public function insertMediaUploadedAttachmentData(XenForo_Upload $file, $userId, $media, array $exif = array())
        {
                $dimensions = array();
                $fileIsVideo = false;
                $tempThumbFile = false;

                $options = XenForo_Application::getOptions();

                $extension = XenForo_Helper_File::getFileExtension($file->getFileName());
                $allowedImageExtensions = preg_split('/\s+/', trim($options->xengalleryImageExtensions));
                $allowedVideoExtensions = preg_split('/\s+/', trim($options->xengalleryVideoExtensions));

                if (in_array($extension, $allowedImageExtensions)) {
                        $dimensions = array(
                                'width' => $file->getImageInfoField('width'),
                                'height' => $file->getImageInfoField('height'),
                        );

                        if (XenForo_Image_Abstract::canResize($dimensions['width'], $dimensions['height'])) {
                                $imageFile = $file->getTempFile();
                        } else {
                                $imageFile = $options->xengalleryDefaultNoThumb;
                        }

                        $tempThumbFile = tempnam(XenForo_Helper_File::getTempDir(), 'xfmg');
                        if ($tempThumbFile) {
                                @copy($imageFile, $tempThumbFile);
                        }
                } else if (in_array($extension, $allowedVideoExtensions)) {
                        $fileIsVideo = true;

                        if ($options->get('xengalleryVideoTranscoding', 'thumbnail')) {
                                try {
                                        $video = new XenGallery_Helper_Video($file->getTempFile());
                                        $tempThumbFile = $video->getKeyFrame();

                                        list($width, $height) = $video->getVideoDimensions();

                                        $dimensions['width'] = $width;
                                        $dimensions['height'] = $height;
                                } catch (XenForo_Exception $e) {
                                }
                        }

                        if (!$tempThumbFile) {
                                $tempThumbFile = tempnam(XenForo_Helper_File::getTempDir(), 'xfmg');
                                if ($tempThumbFile) {
                                        @copy($options->xengalleryDefaultNoThumb, $tempThumbFile);
                                }
                        }
                } else {
                        // not a supported image, not a supported video, bail out.
                        return false;
                }

                if ($tempThumbFile) {
                        $image = new XenGallery_Helper_Image($tempThumbFile);
                        if ($image) {
                                $image->resize(
                                        $dimensions['thumbnail_width'] = $options->xengalleryThumbnailDimension['width'],
                                        $dimensions['thumbnail_height'] = $options->xengalleryThumbnailDimension['height'],
                                        'crop'
                                );

                                $image->saveToPath($tempThumbFile);

                                unset($image);
                        }
                }

                $mediaModel = $this->_getMediaModel();

                try {
                        $dataDw = XenForo_DataWriter::create('XenForo_DataWriter_AttachmentData');

                        $filename = $file->getFileName();

                        $dataDw->set('user_id', $userId);
                        $dataDw->setExistingData($media['data_id']);  //  added this eline

                        if ($fileIsVideo) {
                                $filename = preg_replace('/\\.[^.\\s]{3,4}$/', '.mp4', $filename);
                                $dataDw->set('file_path', $mediaModel->getVideoFilePath());
                        }
                        $dataDw->set('filename', $filename);
                        $dataDw->bulkSet($dimensions);

                        $dataDw->setExtraData(XenForo_DataWriter_AttachmentData::DATA_TEMP_FILE, $file->getTempFile());
                        if ($tempThumbFile) {
                                $dataDw->setExtraData(XenForo_DataWriter_AttachmentData::DATA_TEMP_THUMB_FILE, $tempThumbFile);
                        }

                        $dataDw->setExtraData(XenGallery_DataWriter_AttachmentData::DATA_XMG_FILE_IS_VIDEO, $fileIsVideo);
                        $dataDw->setExtraData(XenGallery_DataWriter_AttachmentData::DATA_XMG_DATA, true);

                        $dataDw->save();
                } catch (Exception $e) {
                        if ($tempThumbFile) {
                                @unlink($tempThumbFile);
                        }

                        throw $e;
                }

                if ($tempThumbFile) {
                        @unlink($tempThumbFile);
                }

                $exif = $this->_getMediaModel()->sanitizeExifData($exif);

                $db = $this->_getDb();
                $db->query('
			INSERT IGNORE INTO xengallery_exif_cache
				(data_id, media_exif_data_cache_full, cache_date)
			VALUES
				(?, ?, ?)
		', array($dataDw->get('data_id'), @json_encode($exif), XenForo_Application::$time));

                return $dataDw->get('data_id');
        }
}
