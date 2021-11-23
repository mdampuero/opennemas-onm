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

        foreach ($keywords as $keyword) {
            if (array_key_exists($keyword->type, $types)) {
                $text = preg_replace(
                    '@\b' . $keyword->keyword . '\b@',
                    sprintf(
                        '\1<a href="%s" target="_blank">%s</a>\4',
                        $types[$keyword->type] . $keyword->value,
                        $keyword->keyword
                    ),
                    $text
                );
            }
        }

        return trim($text);
    }
}
