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
        // Spaces necessary to evaluate first and last pattern matching
        $text = mb_detect_encoding($text) !== "UTF-8" ?
            utf8_decode(' ' . $text . ' ') :
             ' ' . $text . ' ';

        foreach ($keywords as $keyword) {
            // Select keyword type
            $replacement = '<a href="%s" target="_blank">%s</a>';
            switch ($keyword->type) {
                case 'url':
                    $replacement = sprintf($replacement, $keyword->value, $keyword->keyword);
                    break;
                case 'email':
                    $replacement = sprintf($replacement, 'mailto:' . $keyword->value, $keyword->keyword);
                    break;
                case 'intsearch':
                    $replacement = sprintf($replacement, "/tag/" . $keyword->value, $keyword->keyword);
                    break;
                default:
                    continue 2;
            }

            // The \b matches a word boundary
            $text = preg_replace(
                '@\b' . $keyword->keyword . '\b@',
                '\1' . $replacement . '\4',
                $text
            );
        }

        return trim($text);
    }
}
