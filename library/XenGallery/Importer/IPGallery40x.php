<?php

class XenGallery_Importer_IPGallery40x extends XenGallery_Importer_IPGallery34x
{
	/**
	 * Name of meta area key for content tags import
	 *
	 * @var string
	 */
	protected $_metaArea = 'gallery';

	public static function getName()
	{
		return ' XFMG: Import From IP.Gallery (IP.Board 4.0)';
	}

	public function validateConfiguration(array &$config)
	{
		$errors = array();

		$config['db']['prefix'] = preg_replace('/[^a-z0-9_]/i', '', $config['db']['prefix']);

		if (empty($config['importLog']))
		{
			$errors[] = new XenForo_Phrase('xengallery_no_import_log_table_specified');
		}

		try
		{
			$db = Zend_Db::factory('mysqli',
				array(
					'host' => $config['db']['host'],
					'port' => $config['db']['port'],
					'username' => $config['db']['username'],
					'password' => $config['db']['password'],
					'dbname' => $config['db']['dbname'],
					'charset' => $config['db']['charset']
				)
			);
			$db->getConnection();
		}
		catch (Zend_Db_Exception $e)
		{
			$errors[] = new XenForo_Phrase('source_database_connection_details_not_correct_x', array('error' => $e->getMessage()));
		}

		if ($errors)
		{
			return $errors;
		}

		try
		{
			$db->query('
				SELECT member_id
				FROM ' . $config['db']['prefix'] . 'core_members
				LIMIT 1
			');
		}
		catch (Zend_Db_Exception $e)
		{
			if ($config['db']['dbname'] === '')
			{
				$errors[] = new XenForo_Phrase('please_enter_database_name');
			}
			else
			{
				$errors[] = new XenForo_Phrase('table_prefix_or_database_name_is_not_correct');
			}
		}

		if (!empty($config['ipboard_path']))
		{
			if (!file_exists($config['ipboard_path']) || !is_dir($config['ipboard_path'] . '/uploads'))
			{
				$errors[] = new XenForo_Phrase('error_could_not_find_uploads_directory_at_specified_path');
			}
		}

		if (!$errors)
		{
			$config['charset'] = 'utf8';
		}

		return $errors;
	}

	protected function _getAlbums($start, array $options)
	{
		$sDb = $this->_sourceDb;
		$prefix = $this->_prefix;

		return $sDb->fetchAll(
			$sDb->limit('
				SELECT album.*, member.name
				FROM ' . $prefix . 'gallery_albums AS album
				INNER JOIN ' . $prefix . 'core_members AS member ON
					(album.album_owner_id = member.member_id)
				WHERE album.album_id > ?
				ORDER BY album.album_id ASC
			', $options['limit'])
		, $start);
	}

	protected function _getCategories($start, array $options)
	{
		$sDb = $this->_sourceDb;
		$prefix = $this->_prefix;

		return $sDb->fetchAll(
			$sDb->limit('
				SELECT category.*, 2 AS category_type, lang_title.word_custom AS category_name, lang_desc.word_custom AS category_description
				FROM ' . $prefix . 'gallery_categories AS category
				LEFT JOIN ' . $prefix . 'core_sys_lang_words AS lang_title ON
					(lang_title.lang_id = 1 AND lang_title.word_key = CONCAT(\'gallery_category_\', category.category_id))
				LEFT JOIN ' . $prefix . 'core_sys_lang_words AS lang_desc ON
					(lang_desc.lang_id = 1 AND lang_desc.word_key = CONCAT(\'gallery_category_\', category.category_id, \'_desc\'))
				WHERE category.category_id > ?
				ORDER BY category.category_id ASC
			', $options['limit'])
		, $start);
	}

	protected function _getMedia($start, array $options)
	{
		$sDb = $this->_sourceDb;
		$prefix = $this->_prefix;

		return $sDb->fetchAll(
			$sDb->limit('
				SELECT image.*, member.name
				FROM ' . $prefix . 'gallery_images AS image
				INNER JOIN ' . $prefix . 'core_members AS member ON
					(image.image_member_id = member.member_id)
				WHERE image.image_id > ?
					AND image.image_approved = 1
				ORDER BY image.image_id ASC
			', $options['limit'])
		, $start);
	}

	protected function _getImageFilePath(array $item)
	{
		return $this->_config['ipboard_path'] . '/uploads/' . $item['image_masked_file_name'];
	}

	protected function _getComments($start, array $options)
	{
		$sDb = $this->_sourceDb;
		$prefix = $this->_prefix;

		return $sDb->fetchAll($sDb->limit(
			'
				SELECT comment.*, member.name
				FROM ' . $prefix . 'gallery_comments AS comment
				INNER JOIN ' . $prefix . 'core_members AS member ON
					(comment.comment_author_id = member.member_id)
				WHERE comment.comment_id > ?
					AND comment.comment_approved = 1
				ORDER BY comment.comment_id ASC
			', $options['limit']
		), $start);
	}

	public function stepRatings($start, array $options)
	{
		$options = array_merge(array(
			'max' => false
		), $options);

		$sDb = $this->_sourceDb;
		$prefix = $this->_prefix;

		if ($options['max'] === false)
		{
			$options['max'] = $sDb->fetchOne('
				SELECT MAX(id)
				FROM ' . $prefix . 'core_ratings
				WHERE class IN(\'IPSgalleryImage\', \'IPS\gallery\Image\')
			');
		}

		return parent::stepRatings($start, $options);
	}

	protected function _getRatings($start, array $options)
	{
		$sDb = $this->_sourceDb;
		$prefix = $this->_prefix;

		return $sDb->fetchAll($sDb->limit(
			'
				SELECT rating.*, member.name,
					rating.id AS rate_id,
					rating.member AS rate_member_id,
					rating.item_id AS rate_type_id,
					rating.rating AS rate_rate,
					UNIX_TIMESTAMP() AS rate_date,
					\'image\' AS rate_type
				FROM ' . $prefix . 'core_ratings AS rating
				INNER JOIN ' . $prefix . 'core_members AS member ON
					(rating.member = member.member_id)
				WHERE rating.id > ?
					AND rating.class IN(\'IPSgalleryImage\', \'IPS\gallery\Image\')
				ORDER BY rating.id ASC
			', $options['limit']
		), $start);
	}

	protected function _parseIPBoardBbCode($message, $autoLink = true)
	{
		$message = preg_replace('/<br( \/)?>(\r?\n)?/si', "\n", $message);
		$message = str_replace('&nbsp;' , ' ', $message);

		// handle the IPB media format
		if (stripos($message, 'ipsEmbeddedVideo') !== false)
		{
			$message = $this->_parseIPBoardMediaCode($message);
		}

		if (stripos($message, 'ipsQuote') !== false)
		{
			$message = $this->_parseIPBoardQuoteCode($message);
		}

		$search = $this->_getIPBoardBBCodeReplacements();

		$message = preg_replace(array_keys($search), $search, $message);
		$message = strip_tags($message);

		return $this->_convertToUtf8($message, true);
	}

	protected function _getIPBoardBBCodeReplacements()
	{
		return array(

			// this is likely the closest to correct this can be - in IPB this is replaced with the base_url as stored in settings
			// but this can be blank, so it would still leave IMG and URLs with relative URLs which will not work in XF.
			'#<___base_url___>#siU' => XenForo_Application::getOptions()->boardUrl,

			// common attachment links - attachment links containing thumbnailed images
			'#<a [^>]*href=(\'|")([^"\']+)\\1[^>]*class="ipsAttachLink\s*ipsAttachLink_image".*data-fileid="(\d+)".*</a>#siU' => '[ATTACH]\\3.IPB[/ATTACH]',
			'#<a [^>]*class="ipsAttachLink\s*ipsAttachLink_image"[^>]*href=(\'|")([^"\']+)\\1.*data-fileid="(\d+)".*</a>#siU' => '[ATTACH]\\3.IPB[/ATTACH]',

			// common attachment links - attachment links pointing to attached files
			'#<a [^>]*href=".*attachment\.php\?id=(\d+)"[^>]*class="ipsAttachLink"[^>]*>.*</a>#siU' => '[ATTACH]\\1.IPB[/ATTACH]',
			'#<a [^>]*class="ipsAttachLink"[^>]*href=".*attachment\.php\?id=(\d+)"[^>]*>.*</a>#siU' => '[ATTACH]\\1.IPB[/ATTACH]',

			// less common attachment links - attached image no link
			'#<img [^>]*class="ipsImage\s*ipsImage_thumbnailed"[^>]*data-fileid="(\d+)"[^>]*src="[^"]*"[^>]*>#siU' => '[ATTACH]\\1.IPB[/ATTACH]',

			// code block - handle it specifically
			'#<pre [^>]*class="ipsCode"[^>]*>(.*)</pre>(\r?\n)??#siU' => '[CODE]\\1[/CODE]',

			// emoticons
			'#<img [^>]*src="<fileStore\.core_Emoticons>[^>]*"[^>]*alt="([^"]+)" srcset=".*"[^>]*>#siU' => ' \\1 ',
			'#<img [^>]*alt="([^"]+)"[^>]*src="<fileStore\.core_Emoticons>[^>]*" srcset=".*"[^>]*>#siU' => ' \\1 ',
			'#<img [^>]*src="<fileStore\.core_Emoticons>[^>]*"[^>]*alt="([^"]+)"[^>]*>#siU' => ' \\1 ',
			'#<img [^>]*alt="([^"]+)"[^>]*src="<fileStore\.core_Emoticons>[^>]*"[^>]*>#siU' => ' \\1 ',

			// IPB 4.0 spoiler
			'#<blockquote [^>]*class="ipsStyle_spoiler"[^>]*>(.*)</blockquote>(\r?\n)??#siU' => '[SPOILER]\\1[/SPOILER]',

			// IPB 4.1 spoiler
			'#<div [^>]*class="ipsSpoiler"[^>]*>.*<div [^>]*class="ipsSpoiler_contents"[^>]*>(.*)</div>\s*</div>(\r?\n)??#siU' => '[SPOILER]\\1[/SPOILER]',

			'#<span [^>]*style="color:\s*([^";\\]]+?)[^"]*"[^>]*>(.*)</span>#siU' => '[COLOR=\\1]\\2[/COLOR]',
			'#<span [^>]*style="font-family:\s*([^";\\],]+?)[^"]*"[^>]*>(.*)</span>#siU' => '[FONT=\\1]\\2[/FONT]',
			'#<span [^>]*style="font-size:\s*([^";\\]]+?)[^"]*"[^>]*>(.*)</span>#siU' => '[SIZE=\\1]\\2[/SIZE]',
			'#<span[^>]*>(.*)</span>#siU' => '\\1',
			'#<(strong|b)>(.*)</\\1>#siU' => '[B]\\2[/B]',
			'#<(em|i)>(.*)</\\1>#siU' => '[I]\\2[/I]',
			'#<(u)>(.*)</\\1>#siU' => '[U]\\2[/U]',
			'#<(strike|s)>(.*)</\\1>#siU' => '[S]\\2[/S]',
			'#<a [^>]*href=(\'|")([^"\']+)\\1[^>]*>(.*)</a>#siU' => '[URL="\\2"]\\3[/URL]',
			'#<img [^>]*src="([^"]+)"[^>]*>#' => '[IMG]\\1[/IMG]',
			'#<img [^>]*src=\'([^\']+)\'[^>]*>#' => '[IMG]\\1[/IMG]',

			'#<(p|div) [^>]*style="text-align:\s*left;?">(.*)</\\1>(\r?\n)??#siU' => "[LEFT]\\2[/LEFT]\n",
			'#<(p|div) [^>]*style="text-align:\s*center;?">(.*)</\\1>(\r?\n)??#siU' => "[CENTER]\\2[/CENTER]\n",
			'#<(p|div) [^>]*style="text-align:\s*right;?">(.*)</\\1>(\r?\n)??#siU' => "[RIGHT]\\2[/RIGHT]\n",
			'#<(p|div) [^>]*class="bbc_left"[^>]*>(.*)</\\1>(\r?\n)??#siU' => "[LEFT]\\2[/LEFT]\n",
			'#<(p|div) [^>]*class="bbc_center"[^>]*>(.*)</\\1>(\r?\n)??#siU' => "[CENTER]\\2[/CENTER]\n",
			'#<(p|div) [^>]*class="bbc_right"[^>]*>(.*)</\\1>(\r?\n)??#siU' => "[RIGHT]\\2[/RIGHT]\n",

			// lists
			'#<ul[^>]*>(.*)</ul>(\r?\n)??#siU' => "[LIST]\\1[/LIST]\n",
			'#<ol[^>]*>(.*)</ol>(\r?\n)??#siU' => "[LIST=1]\\1[/LIST]\n",
			'#<li[^>]*>(.*)</li>(\r?\n)??#siU' => "[*]\\1\n",


			// strip the unnecessary whitespace between start of bullet point and text
			'#(\[\*\])\s*?#siU' => '\\1',

			'#<(p|pre)[^>]*>(&nbsp;|' . chr(0xC2) . chr(0xA0) .'|\s)*</\\1>(\r?\n)??#siU' => "\n",
			'#<p[^>]*>\s*(.*)\s*</p>\s*?#siU' => "\\1\n\n",
			'#<div[^>]*>\s*(.*)\s*</div>\s*?#siU' => "\\1\n\n",

			'#<pre[^>]*>(.*)</pre>(\r?\n)??#siU' => "[CODE]\\1[/CODE]\n",

			'#<!--.*-->#siU' => ''
		);
	}

	protected function _parseIPBoardMediaCode($message)
	{
		return preg_replace_callback(
			'#<div [^>]*class="ipsEmbeddedVideo\s?"[^>]*>.*?<div>.*?<iframe [^>]*src="(.*)"[^>]*></iframe>.*?</div>.*?</div>#siU',
			array($this, '_convertIPBoardMediaTag'),
			$message
		);
	}

	protected function _getIPBoardQuoteReplacements()
	{
		return array(
			// IPB 4.1 quotes
			'#<blockquote [^>]*class="ipsQuote"[^>]*data-ipsquote-username="([^"]+)"[^>]*data-ipsquote-contentcommentid="(\d+)"[^>]*>.*<div [^>]*class="ipsQuote_contents[^"]*"[^>]*>(.*)</div>\s*</blockquote>(\r?\n)??#siU' => '[QUOTE="\\1, post: \\2"]\\3[/QUOTE]',
			'#<blockquote [^>]*class="ipsQuote"[^>]*data-ipsquote-contentcommentid="(\d+)"[^>]*data-ipsquote-username="([^"]+)"[^>]*>.*<div [^>]*class="ipsQuote_contents[^"]*"[^>]*>(.*)</div>\s*</blockquote>(\r?\n)??#siU' => '[QUOTE="\\2, post: \\1"]\\3[/QUOTE]',

			'#<blockquote [^>]*class="ipsQuote"[^>]*data-ipsquote-username="([^"]+)"[^>]*>.*<div [^>]*class="ipsQuote_contents[^"]*"[^>]*>(.*)</div>\s*</blockquote>(\r?\n)??#siU' => '[QUOTE=\\1]\\2[/QUOTE]',

			'#<blockquote [^>]*class="ipsQuote"[^>]*>.*<div [^>]*class="ipsQuote_contents[^"]*"[^>]*>(.*)</div>\s*</blockquote>(\r?\n)??#siU' => '[QUOTE]\\1[/QUOTE]',

			// IPB 4.0 quotes
			'#<blockquote [^>]*class="ipsQuote"[^>]*data-cite="([^"]+)"[^>]*data-ipsquote-contentcommentid="(\d+)"[^>]*>(.*)</blockquote>(\r?\n)??#siU' => '[QUOTE="\\1, post: \\2"]\\3[/QUOTE]',
			'#<blockquote [^>]*class="ipsQuote"[^>]*data-ipsquote-contentcommentid="(\d+)"[^>]*data-cite="([^"]+)"[^>]*>(.*)</blockquote>(\r?\n)??#siU' => '[QUOTE="\\2, post: \\1"]\\3[/QUOTE]',

			'#<blockquote [^>]*class="ipsQuote"[^>]*data-cite="([^"]+)"[^>]*>(.*)</blockquote>(\r?\n)??#siU' => '[QUOTE=\\1]\\2[/QUOTE]',

			'#<blockquote [^>]*class="ipsQuote"[^>]*>(.*)</blockquote>(\r?\n)??#siU' => '[QUOTE]\\1[/QUOTE]'
		);
	}

	protected function _parseIPBoardQuoteCode($message)
	{
		foreach ($this->_getIPBoardQuoteReplacements() AS $pattern => $replacement)
		{
			do
			{
				$newMessage = preg_replace($pattern, $replacement, $message);
				if ($newMessage === $message)
				{
					break;
				}

				$message = $newMessage;
			}
			while (true);
		}

		return $message;
	}
}