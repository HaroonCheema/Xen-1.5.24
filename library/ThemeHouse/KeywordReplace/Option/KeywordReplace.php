<?php

/**
 * Helper for keyword replace option.
 */
abstract class ThemeHouse_KeywordReplace_Option_KeywordReplace
{

    /**
     * Renders the keyword replace option row.
     *
     * @param XenForo_View $view View object
     * @param string $fieldPrefix Prefix for the HTML form field name
     * @param array $preparedOption Prepared option info
     * @param boolean $canEdit True if an "edit" link should appear
     *
     * @return XenForo_Template_Abstract Template object
     */
    public static function renderOption(XenForo_View $view, $fieldPrefix, array $preparedOption, $canEdit)
    {
        $value = $preparedOption['option_value'];

        $choices = array();
        foreach ($value as $word => $wordOptions) {
            $choices[] = array(
                'word' => $word,
                'live' => isset($wordOptions['live']) ? $wordOptions['live'] : 0,
                'replace' => isset($wordOptions['replace']) ? $wordOptions['replace'] : '',
                'limit' => isset($wordOptions['limit']) ? $wordOptions['limit'] : '',
                'replace_type' => isset($wordOptions['replace_type']) ? $wordOptions['replace_type'] : ''
            );
        }

        $editLink = $view->createTemplateObject('option_list_option_editlink',
            array(
                'preparedOption' => $preparedOption,
                'canEditOptionDefinition' => $canEdit
            ));

        return $view->createTemplateObject('th_option_template_keyword_replace',
            array(
                'fieldPrefix' => $fieldPrefix,
                'listedFieldName' => $fieldPrefix . '_listed[]',
                'preparedOption' => $preparedOption,
                'formatParams' => $preparedOption['formatParams'],
                'editLink' => $editLink,

                'choices' => $choices,
                'nextCounter' => count($choices)
            ));
    }

    /**
     * Verifies and prepares the keyword replace option to the correct format.
     *
     * @param array $words List of words to censor (from input). Keys: word,
     * exact, replace
     * @param XenForo_DataWriter $dw Calling DW
     * @param string $fieldName Name of field/option
     *
     * @return true
     */
    public static function verifyOption(array &$words, XenForo_DataWriter $dw, $fieldName)
    {
        $output = array();

        foreach ($words as $word) {
            if (!isset($word['word']) || strval($word['word']) === '') {
                continue;
            }

            $output[strval(strtolower($word['word']))] = $word;
        }

        $words = $output;

        return true;
    }
}