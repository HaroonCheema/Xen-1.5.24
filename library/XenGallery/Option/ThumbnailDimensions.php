<?php

class XenGallery_Option_ThumbnailDimensions
{
	public static function verifyOption(array &$values, XenForo_DataWriter $dw, $fieldName)
	{
		$width = intval($values['width']);
		$height = intval($values['height']);

		if ($width < 1 || $height < 1)
		{
			$dw->error(new XenForo_Phrase('xengallery_you_must_enter_thumbnail_width_height_greater_than_0'));
			return false;
		}

		return true;
	}
}