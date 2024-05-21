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

        public function deletePreviousImages($media)
        {
                $originalThumbFile = $this->getMediaThumbnailFilePath($media);

                @unlink($originalThumbFile);

                $changedImageFile = $this->getOriginalDataFilePath($media);

                @unlink($changedImageFile);
        }
}
