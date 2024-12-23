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
        $placeholderMap    = [];
        $replacedKeywords  = [];
        $processedKeywords = [];

        foreach ($keywordsSorted as $keyword) {
            if (array_key_exists($keyword->type, $types)) {
                if (in_array($keyword->keyword, $replacedKeywords)) {
                    continue;
                }

                if (in_array($keyword->keyword, $processedKeywords)) {
                    continue;
                }

                $placeholder = "##KEYWORD_ONM_PLACEHOLDER_" . $keyword->id . "##";
                $replacement = $this->createReplacementLink($keyword, $types[$keyword->type]);

                $placeholderMap[$placeholder] = $replacement;

                $text = $this->replaceKeywordInText(
                    $text,
                    preg_quote($keyword->keyword, '@'),
                    $placeholder,
                    $processedKeywords
                );

                $replacedKeywords[]  = $keyword->keyword;
                $processedKeywords[] = $keyword->keyword;
            }
        }

        return [$text, $placeholderMap];
    }

    /**
     * Creates an HTML <a> tag as a replacement for the keyword, using its URL and display text.
     *
     * @param object $keyword The keyword object containing details like the value and display text.
     * @param string $typeUrl The base URL associated with the keyword type.
     * @return string The formatted <a> tag containing the keyword's URL and display text.
     */
    private function createReplacementLink($keyword, $typeUrl)
    {
        return sprintf(
            '<a href="%s" target="_blank">%s</a>',
            $typeUrl . $keyword->value,
            $keyword->keyword
        );
    }

    /**
     * Replaces occurrences of the keyword in the text with a unique placeholder,
     * excluding instances inside <a> tags and considering processed compound keywords.
     *
     * @param string $text The text where the keyword needs to be replaced.
     * @param string $keywordPattern The regular expression pattern of the keyword.
     * @param string $placeholder The unique placeholder string that will replace the keyword.
     * @param array &$processedKeywords An array that tracks processed keywords to skip.
     * @return string The modified text with the keyword replaced by the placeholder.
     */
    private function replaceKeywordInText($text, $keywordPattern, $placeholder, &$processedKeywords = [])
    {
        $processedParts = [];
        $remainingText  = $text;

        foreach ($processedKeywords as $processedKeyword) {
            if (strpos($remainingText, $processedKeyword) !== false) {
                $remainingText    = str_replace(
                    $processedKeyword,
                    "##PROCESSED_" . md5($processedKeyword) . "##",
                    $remainingText
                );
                $processedParts[] = $processedKeyword;
            }
        }

        $replacementCount = 0;
        $updatedText      = preg_replace_callback(
            '@\b' . preg_quote($keywordPattern, '@') . '\b@',
            function ($matches) use ($placeholder, &$replacementCount) {
                if ($replacementCount === 0) {
                    $replacementCount++;
                    return $placeholder;
                }
                return $matches[0];
            },
            $remainingText
        );

        $finalText = $updatedText;
        foreach ($processedParts as $processedPart) {
            $finalText = str_replace("##PROCESSED_" . md5($processedPart) . "##", $processedPart, $finalText);
        }

        if ($replacementCount > 0) {
            $processedKeywords[] = $keywordPattern;
        }

        return $finalText;
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
