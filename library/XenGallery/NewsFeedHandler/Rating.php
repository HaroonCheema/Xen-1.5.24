<?php

/**
 * News feed handler for media rating actions
 *
 */
class XenGallery_NewsFeedHandler_Rating extends XenForo_NewsFeedHandler_Abstract
{
	protected $_ratingModel;

	protected $_mediaModel;

	protected $_albumModel;

	protected $_categoryModel;

	/**
	 * Just returns a value for each requested ID
	 * but does no actual DB work
	 *
	 * @param array $contentIds
	 * @param XenForo_Model_NewsFeed $model
	 * @param array $viewingUser Information about the viewing user (keys: user_id, permission_combination_id, permissions)
	 *
	 * @return array
	 */
	public function getContentByIds(array $contentIds, $model, array $viewingUser)
	{
		$rating = $this->_getRatingModel()->getRatingsByIds($contentIds, array(
			'join' => XenGallery_Model_Rating::FETCH_USER | XenGallery_Model_Rating::FETCH_CONTENT
		));
		$rating = $this->_getMediaModel()->prepareMediaItems($rating);

		return $rating;
	}

	/**
	 * Determines if the given news feed item is viewable.
	 *
	 * @param array $item
	 * @param mixed $content
	 * @param array $viewingUser
	 *
	 * @return boolean
	 */
	public function canViewNewsFeedItem(array $item, $content, array $viewingUser)
	{
		if ($content['content_type'] == 'album')
		{
			$albumModel = $this->_getAlbumModel();
			$content = $albumModel->prepareAlbum($content);
			$content['albumPermissions']['view'] = array(
				'permission' => 'view',
				'access_type' => $content['access_type'],
				'share_users' => $content['share_users']
			);

			return $albumModel->canViewAlbum($content, $null, $viewingUser);
		}
		else if ($content['content_type'] == 'media')
		{
			if ($content['album_id'] > 0)
			{
				$albumModel = $this->_getAlbumModel();
				$content = $albumModel->prepareAlbum($content);
				$content['albumPermissions']['view'] = array(
					'permission' => 'view',
					'access_type' => $content['access_type'],
					'share_users' => $content['share_users']
				);

				if (!$albumModel->canViewAlbum($content, $null, $viewingUser))
				{
					return false;
				}
			}
			else if ($content['category_id'] > 0)
			{
				if (!$this->_getCategoryModel()->canViewCategory($content, $null, $viewingUser))
				{
					return false;
				}
			}

			return $this->_getMediaModel()->canViewMediaItem($content, $null, $viewingUser);
		}
	}

	/**
	 * @return XenGallery_Model_Rating
	 */
	protected function _getRatingModel()
	{
		if (!$this->_ratingModel)
		{
			$this->_ratingModel = XenForo_Model::create('XenGallery_Model_Rating');
		}

		return $this->_ratingModel;
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
	 * @return XenGallery_Model_Album
	 */
	protected function _getAlbumModel()
	{
		if (!$this->_albumModel)
		{
			$this->_albumModel = XenForo_Model::create('XenGallery_Model_Album');
		}

		return $this->_albumModel;
	}

	/**
	 * @return XenGallery_Model_Category
	 */
	protected function _getCategoryModel()
	{
		if (!$this->_categoryModel)
		{
			$this->_categoryModel = XenForo_Model::create('XenGallery_Model_Category');
		}

		return $this->_categoryModel;
	}
}