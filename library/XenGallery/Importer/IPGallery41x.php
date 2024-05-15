<?php

class XenGallery_Importer_IPGallery41x extends XenGallery_Importer_IPGallery40x
{
	public static function getName()
	{
		return ' XFMG: Import From IP.Gallery (IP.Board 4.1/4.2)';
	}

	public function stepRatings($start, array $options)
	{
		$options = array_merge(array(
			'limit' => 100,
			'max' => false
		), $options);

		$sDb = $this->_sourceDb;
		$prefix = $this->_prefix;

		if ($options['max'] === false)
		{
			$options['max'] = $sDb->fetchOne('
				SELECT MAX(review_id)
				FROM ' . $prefix . 'gallery_reviews
			');
		}

		$ratings = $this->_getRatings($start, $options);

		if (!$ratings)
		{
			return true;
		}

		$next = 0;
		$total = 0;

		$this->_userIdMap = $this->_importModel->getUserIdsMapFromArray($ratings, 'review_author');
		foreach ($ratings AS $rating)
		{
			$next = $rating['review_id'];
			if (!isset($this->_userIdMap[$rating['review_author']]))
			{
				continue;
			}

			$success = $this->_importRating($rating, $options);
			if ($success)
			{
				$total++;
			}
		}

		$this->_session->incrementStepImportTotal($total);

		return array($next, $options, $this->_getProgressOutput($next, $options['max']));
	}

	protected function _getRatings($start, array $options)
	{
		$sDb = $this->_sourceDb;
		$prefix = $this->_prefix;

		return $sDb->fetchAll($sDb->limit(
			'
				SELECT *
				FROM ' . $prefix . 'gallery_reviews
				WHERE review_id > ?
				ORDER BY review_id ASC
			', $options['limit']
		), $start);
	}

	protected function _importRating(array $rating, array $options)
	{
		$model = $this->_getMediaGalleryImportersModel();

		$contentId = $model->mapMediaId($rating['review_image_id']);
		$contentType = 'media';

		if (!$contentId)
		{
			return false;
		}

		$userId = $this->_userIdMap[$rating['review_author']];

		$xengalleryRating = array(
			'content_id' => $contentId,
			'content_type' => $contentType,
			'user_id' => $userId,
			'username' => $rating['review_author_name'],
			'rating_date' => $rating['review_date'],
			'rating' => intval($rating['review_rating'])
		);

		$xfDb = XenForo_Application::getDb();

		$existingRating = $xfDb->fetchRow('
			SELECT *
			FROM xengallery_rating
			WHERE content_type = ?
				AND content_id = ?
				AND user_id = ?
		', array($contentType, $contentId, $userId));

		if ($existingRating)
		{
			return $xfDb->update('xengallery_rating', $xengalleryRating, 'rating_id = ' . $xfDb->quote($existingRating['rating_id']));
		}

		$importedRatingId = $model->importRating($rating['review_id'], $xengalleryRating);

		if ($rating['review_content'])
		{
			$message = trim($this->_parseIPBoardBbCode($rating['review_content']));
			if ($message)
			{
				$xengalleryComment = array(
					'content_id' => $contentId,
					'content_type' => 'media',
					'message' => $message,
					'user_id' => $userId,
					'username' => $xengalleryRating['username'],
					'ip_id' => $model->getLatestIpIdFromUserId($userId),
					'comment_date' => $xengalleryRating['rating_date'],
					'comment_state' => 'visible',
					'likes' => 0,
					'like_users' => array(),
					'rating_id' => $importedRatingId
				);
				$model->importComment(0, $xengalleryComment);
			}
		}

		return $importedRatingId;
	}
}