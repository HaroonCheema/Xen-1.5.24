<?php

class XenGallery_AlertHandler_Media extends XenForo_AlertHandler_Abstract
{
	protected $_mediaModel;
	protected $_attachmentModel;

	/**
	 * Fetches the content required by alerts.
	 *
	 * @param array $contentIds
	 * @param XenForo_Model_Alert $model Alert model invoking this
	 * @param integer $userId User ID the alerts are for
	 * @param array $viewingUser Information about the viewing user (keys: user_id, permission_combination_id, permissions)
	 *
	 * @return array
	 */
	public function getContentByIds(array $contentIds, $model, $userId, array $viewingUser)
	{
		$mediaModel = $this->_getMediaModel();

		$media = $mediaModel->getMediaByIds($contentIds, array(
			'join' => XenGallery_Model_Media::FETCH_ATTACHMENT
				 | XenGallery_Model_Media::FETCH_CATEGORY
				 | XenGallery_Model_Media::FETCH_USER
				 | XenGallery_Model_Media::FETCH_ALBUM
		));

		return $mediaModel->prepareMediaItems($media);
	}

	/**
	* Determines if the media is viewable.
	* @see XenForo_AlertHandler_Abstract::canViewAlert()
	*/
	public function canViewAlert(array $alert, $content, array $viewingUser)
	{
		$mediaModel = $this->_getMediaModel();

		if (!$mediaModel->canViewMediaItem($content, $null, $viewingUser))
		{
			return false;
		}

		if ($content['album_id'] > 0)
		{
			/** @var XenGallery_Model_Album $albumModel */
			$albumModel = XenForo_Model::create('XenGallery_Model_Album');

			$content = $albumModel->prepareAlbum($content);
			$content = $albumModel->prepareAlbumWithPermissions($content);

			if (!$albumModel->canViewAlbum($content, $null, $viewingUser))
			{
				return false;
			}
		}
		else if ($content['category_id'] > 0)
		{
			/** @var XenGallery_Model_Category $categoryModel */
			$categoryModel = XenForo_Model::create('XenGallery_Model_Category');

			if (!$categoryModel->canViewCategory($content, $null, $viewingUser))
			{
				return false;
			}			
		}

		return true;
	}

	/**
	 * @return XenGallery_Model_Media
	 */
	protected function _getMediaModel()
	{
		if (!$this->_mediaModel)
		{
			$this->_mediaModel = XenForo_Model::create('XenGallery_Model_Media');
		}

		return $this->_mediaModel;
	}

	/**
	 * @return XenForo_Model_Attachment
	 */
	protected function _getAttachmentModel()
	{
		if (!$this->_attachmentModel)
		{
			$this->_attachmentModel = XenForo_Model::create('XenForo_Model_Attachment');
		}
	
		return $this->_attachmentModel;
	}
}
