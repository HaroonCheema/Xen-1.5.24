<?php

class FS_GuestAvatarHide_Listener
{
    public static function init(XenForo_Dependencies_Abstract $dependencies, array $data)
    {
        // Override XF1's avatar URL resolver that helperAvatarHtml() calls under the hood
        XenForo_Template_Helper_Core::$helperCallbacks['avatar'] = array(__CLASS__, 'helperAvatarUrl');
    }

    // Signature mirrors how XF1 Core calls the URL helper from helperAvatarHtml()
    public static function helperAvatarUrl($user, $size, $forceType = null, $canonical = false)
    {
        if (!is_array($user)) {
            $user = array();
        }

        // sanitize $forceType to known values or null
        if ($forceType) {
            switch ($forceType) {
                case 'default':
                case 'custom':
                    break;
                default:
                    $forceType = null;
            }
        }

        $url = self::getAvatarUrl($user, $size, $forceType);

        // if guest, always return your generic avatar
        if (!XenForo_Visitor::getUserId()) {
            $baseUrl = XenForo_Application::get('options')->boardUrl;
            // if canonical was requested, make sure we return an absolute URI

            if (strpos($url, 'avatars/avatar_l') !== false || strpos($url, 'avatars/l/') !== false) {
                $newUrl = rtrim($baseUrl, '/') . '/data/guest_avatar_l.jpg';
            } elseif (strpos($url, 'avatars/avatar_m') !== false || strpos($url, 'avatars/m/') !== false) {
                $newUrl = rtrim($baseUrl, '/') . '/data/guest_avatar_m.jpg';
            } elseif (strpos($url, 'avatars/avatar_s') !== false || strpos($url, 'avatars/s/') !== false) {
                $newUrl = rtrim($baseUrl, '/') . '/data/guest_avatar_s.jpg';
            }
            // $newUrl = rtrim($baseUrl, '/') . '/data/guest_avatar_m.jpg';
            return htmlspecialchars($canonical ? XenForo_Link::convertUriToAbsoluteUri($newUrl, true) : $newUrl);
        }

        // logged in, resolve via our logic
        if ($canonical) {
            $url = XenForo_Link::convertUriToAbsoluteUri($url, true);
        }

        return htmlspecialchars($url);
    }

    public static function getAvatarUrl(array $user, $size, $forceType = '')
    {
        // if user has an id and we are not forcing default, try gravatar or custom
        if (!empty($user['user_id']) && $forceType != 'default') {
            if (!empty($user['gravatar']) && $forceType != 'custom') {
                return self::_getGravatarUrl($user, $size);
            } else if (!empty($user['avatar_date'])) {
                // custom avatar uploaded
                return self::_getCustomAvatarUrl($user, $size);
            }
        }

        // otherwise, default avatar
        return self::_getDefaultAvatarUrl($size);
    }

    // build URL to a user's custom uploaded avatar, mirrors XF1 storage layout
    protected static function _getCustomAvatarUrl(array $user, $size)
    {
        // optional banned avatar handling kept from your original code
        if (!empty($user['is_banned'])) {
            if (!empty($user['avatar_crop_x']) || !empty($user['avatar_crop_y']) || ($user['avatar_height'] != 192 || $user['avatar_width'] != 192)) {
                // leave the DB updates commented, they were commented in your original
                // $db = XenForo_Application::getDb();
                // $db->update('xf_user_profile', array('avatar_crop_x' => 0, 'avatar_crop_y' => 0), 'user_id = ' . $user['user_id']);
                // $db->update('xf_user', array('avatar_date' => XenForo_Application::$time, 'avatar_height' => 192, 'avatar_width' => 192), 'user_id = ' . $user['user_id']);
            }

            if (!$imagePath = XenForo_Template_Helper_Core::styleProperty('imagePath')) {
                $imagePath = 'styles/default';
            }

            return "{$imagePath}/bannedavatar/avatar_banned_{$size}.png";
        }

        // standard XF1 avatar path
        $group = floor($user['user_id'] / 1000);
        return XenForo_Application::$externalDataUrl . "/avatars/{$size}/{$group}/{$user['user_id']}.jpg?{$user['avatar_date']}";
    }

    // delegate gravatar building to XF1 core
    protected static function _getGravatarUrl(array $user, $size)
    {
        // Core will detect $user['gravatar'] and return the correct URL
        return XenForo_Template_Helper_Core::getAvatarUrl($user, $size);
    }

    // get the XF1 default letter avatar URL
    protected static function _getDefaultAvatarUrl($size)
    {
        // empty array = core returns the default avatar image for the requested size
        return XenForo_Template_Helper_Core::getAvatarUrl(array(), $size);
    }
}
