<?php

/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Api\Service\V1;

class KeywordService extends OrmService
{
    /**
     * Replaces the appearances of all the keywords by their replacements
     *
     * @param string $text the text to change
     * @param array $terms the list of terms to replace
     *
     * @return string the changed text
     */
    public function replaceTerms($text, $keywords)
    {
        $text = mb_detect_encoding($text) !== "UTF-8" ?
            utf8_decode(' ' . $text . ' ') :
            ' ' . $text . ' ';

        $types = [
            'url'       => '',
            'email'     => 'mailto:',
            'intsearch' => '/tag/'
        ];

        // Sorts the array of keywords by the number of words in the keyword
        $keywordsSorted = $this->sortKeywordsByWordCount($keywords);

        // Replace keyboards for placeholders
        list($textWithPlaceholders, $placeholderMap) = $this->replaceWithPlaceholders($text, $keywordsSorted, $types);

        // Replace placeholders for links
        $text = $this->replacePlaceholdersWithLinks($textWithPlaceholders, $placeholderMap);

        return trim($text);
    }

    /**
     * Sorts the array of keywords by the number of words in the keyword.
     * Keywords with more words come first, followed by keywords with fewer words,
     * and finally simple (single-word) keywords at the end.
     *
     * @param array $keywords the list of keywords to sort
     *
     * @return array the sorted list of keywords
     */
    private function sortKeywordsByWordCount($keywords)
    {
        usort($keywords, function ($a, $b) {
            $wordCountA = str_word_count($a->keyword);
            $wordCountB = str_word_count($b->keyword);
            return $wordCountB - $wordCountA;
        });
        return $keywords;
    }

    /**
     * Replaces each keyword with a unique string identifier and stores the actual replacement
     * in an associative array for later processing.
     *
     * @param string $text The text to search and replace.
     * @param array $keywordsSorted The sorted list of keywords.
     * @param array $types The types of replacements (e.g., 'url', 'email', etc.).
     *
     * @return array [string, array] The modified text with placeholders and the associative array of replacements.
     */
    private function replaceWithPlaceholders($text, $keywordsSorted, $types)
    {
        $placeholderMap = [];

        foreach ($keywordsSorted as $keyword) {
            if (array_key_exists($keyword->type, $types)) {
                // Escape the keyword to avoid issues in the regular expression
                $keywordPattern = preg_quote($keyword->keyword, '@');

                // Generate a unique placeholder
                $placeholder = "##KEYWORD_ONM_PLACEHOLDER_" . $keyword->id . "##";

                // Create the replacement <a> tag
                $replacement = sprintf(
                    '<a href="%s" target="_blank">%s</a>',
                    $types[$keyword->type] . $keyword->value,
                    $keyword->keyword
                );

                // Store the placeholder and its corresponding <a> tag in the associative array
                $placeholderMap[$placeholder] = $replacement;

                // Replace the keyword in the text with the unique placeholder
                $text = preg_replace_callback(
                    '@<a\b[^>]*>.*?<\/a>|(\b' . $keywordPattern . '\b)@',
                    function ($matches) use ($placeholder) {
                        // If the first group is empty, it means the keyword was found outside of <a>
                        if (!empty($matches[1])) {
                            return $placeholder;
                        }
                        return $matches[0];
                    },
                    $text
                );
            }
        }

        return [$text, $placeholderMap];
    }

    /**
     * Replaces the placeholders with the actual <a> tags from the associative array.
     *
     * @param string $text The text with placeholders.
     * @param array $placeholderMap The associative array of placeholders and their replacements.
     *
     * @return string The text with the final <a> tags.
     */
    private function replacePlaceholdersWithLinks($text, $placeholderMap)
    {
        foreach ($placeholderMap as $placeholder => $replacement) {
            $text = str_replace($placeholder, $replacement, $text);
        }
        return $text;
    }
}
