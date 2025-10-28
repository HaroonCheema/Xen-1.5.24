<?php

/**
 * Helper to do common string operations, such as keyword replacing.
 */
class ThemeHouse_KeywordReplace_Helper_String
{

    /**
     * Keyword replace cache for default keyword replace string option.
     *
     * @var array null when not set up
     */
    protected static $_keywordReplaceCache = null;

    /**
     * Keyword limit cache for default keyword limit option.
     *
     * @var array null when not set up
     */
    protected static $_keywordLimitCache = null;

    /**
     * Keyword count cache.
     *
     * @var array null when not set up
     */
    protected static $_countCache = null;

    /**
     * Private constructor.
     * Use statically.
     */
    private function __construct()
    {
    }

    /**
     * Replaces keywords in the given string.
     *
     * @param string $string
     * @param array|null $words Words to replace. Null to use option value.
     *
     * @return string
     */
    public static function keywordReplaceString($string, array $words = null)
    {
        $allowCache = ($words === null); // ok to use cache for default
        $keywordReplaceCache = ($allowCache ? self::$_keywordReplaceCache : null);
        $keywordLimitCache = ($allowCache ? self::$_keywordLimitCache : null);
        $countCache = ($allowCache ? self::$_countCache : null);

        if ($keywordReplaceCache === null) {
            if ($words === null) {
                $words = XenForo_Application::get('options')->th_keywordReplace;
            }

            if (!$words) {
                if ($allowCache) {
                    self::$_keywordReplaceCache = array();
                    self::$_keywordLimitCache = array();
                }

                return $string;
            }

            $keywordReplaceArrays = self::buildKeywordReplaceArrays($words);
            $keywordReplaceCache = $keywordReplaceArrays['replaceCache'];
            $keywordLimitCache = $keywordReplaceArrays['limitCache'];
            $countCache = XenForo_Application::get('options')->th_keywordReplace_limitPerPage;

            if ($allowCache) {
                self::$_keywordReplaceCache = $keywordReplaceCache;
                self::$_keywordLimitCache = $keywordLimitCache;
                self::$_countCache = $countCache;
            }
        }

        foreach ($keywordReplaceCache as $keyword => $replacement) {
            if (XenForo_Application::get('options')->th_keywordReplace_limitPerPage) {
                if ($keywordLimitCache[$keyword] != -1) {
                    $limit = min(
                        array(
                            $keywordLimitCache[$keyword],
                            $countCache
                        ));
                } else {
                    $limit = $countCache;
                }
            } else {
                $limit = $keywordLimitCache[$keyword];
            }

            $string = preg_replace($keyword, $replacement, $string, $limit, $count);
            if ($count && $limit != -1) {
                $countCache = $countCache - $count;
                if ($allowCache) {
                    self::$_countCache = $countCache;
                    self::$_keywordLimitCache[$keyword] = $keywordLimitCache[$keyword] - $count;
                }
            }
        }

        return $string;
    }

    /**
     * Builds the keyword replace array.
     *
     * @param array $words List of words (from option format)
     *
     * @return array Possible keys: key-value search/replace pairs
     */
    public static function buildKeywordReplaceArrays(array $words)
    {
        $keywordReplaceCache = array();
        $keywordLimitCache = array();

        foreach ($words as $word => $replace) {
            if (isset($replace['live']) and $replace['live'] == true) {
                $word = htmlentities($word, ENT_QUOTES);
                $search = '#(?<=\W|^)' . preg_quote($word, '#') . '(?=\W|$)#iu';
                if (!empty($replace['replace_type'])) {
                    if ($replace['replace_type'] == 'url') {
                        $keywordReplaceCache[$search] = '<a href="' . $replace['replace'] . '" class="keywordReplace">$1</a>';
                    } elseif ($replace['replace_type'] == 'overlay') {
                        $keywordReplaceCache[$search] = '<a href="' . $replace['replace'] .
                             '" class="OverlayTrigger keywordReplace">$1</a>';
                    }
                } else {
                    $keywordReplaceCache[$search] = $replace['replace'];
                }
                if (!empty($replace['limit'])) {
                    $keywordLimitCache[$search] = $replace['limit'];
                } elseif (XenForo_Application::get('options')->th_keywordReplace_limitPerWord) {
                    $keywordLimitCache[$search] = XenForo_Application::get('options')->th_keywordReplace_limitPerWord;
                } else {
                    $keywordLimitCache[$search] = -1;
                }
            }
        }
        $keywordReplaceArrays = array(
            'replaceCache' => $keywordReplaceCache,
            'limitCache' => $keywordLimitCache
        );
        return $keywordReplaceArrays;
    }
}