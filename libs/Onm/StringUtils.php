<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Onm;

use Common\Model\Entity\Content;
use Opennemas\Data\Type\Str;

/**
 * The StringUtils class provides methods to transform strings.
 */
class StringUtils
{
    /**
     * List of chars and replacements.
     *
     * @var array
     */
    protected static $trade = [
        "'"  => ' ',
        "\n" => ' ',
        "\r" => ' ',
        ' '  => ' ',
        '!'  => ' ',
        '"'  => ' ',
        '#'  => ' ',
        '$'  => ' ',
        '%'  => ' ',
        '&'  => ' ',
        '('  => ' ',
        ')'  => ' ',
        '*'  => ' ',
        '+'  => ' ',
        ','  => ' ',
        '.'  => ' ',
        ':'  => ' ',
        ';'  => ' ',
        '?'  => ' ',
        '¡'  => ' ',
        '¿'  => ' ',
        '“'  => ' ',
        '”'  => ' ',
        '…'  => ' ',
        '-'  => ' ',
        '/'  => ' ',
        '<'  => ' ',
        '='  => ' ',
        '>'  => ' ',
        '@'  => ' ',
        '['  => ' ',
        '\"' => ' ',
        '\'' => ' ',
        '\\' => ' ',
        ']'  => ' ',
        '^'  => ' ',
        '`'  => ' ',
        '|'  => ' ',
        '~'  => ' ',
        '£'  => ' ',
        'ª'  => 'a',
        '´'  => ' ',
        '·'  => ' ',
        'º'  => 'o',
        'À'  => 'A',
        'Á'  => 'A',
        'Á'  => 'A',
        'Â'  => 'A',
        'Ã'  => 'A',
        'Ä'  => 'A',
        'Å'  => 'A',
        'Æ'  => 'AE',
        'Ç'  => 'C',
        'È'  => 'E',
        'È'  => 'E',
        'É'  => 'E',
        'Ê'  => 'E',
        'Ë'  => 'E',
        'Ì'  => 'I',
        'Í'  => 'I',
        'Î'  => 'I',
        'Ï'  => 'I',
        'Ð'  => 'D',
        'Ñ'  => 'N',
        'Ò'  => 'O',
        'Ò'  => 'O',
        'Ó'  => 'O',
        'Ô'  => 'O',
        'Õ'  => 'O',
        'Ö'  => 'O',
        'Ø'  => 'O',
        'Ù'  => 'U',
        'Ú'  => 'U',
        'Û'  => 'U',
        'Ü'  => 'U',
        'Ý'  => 'Y',
        'Þ'  => 'TH',
        'ß'  => 's',
        'à'  => 'a',
        'á'  => 'a',
        'â'  => 'a',
        'ã'  => 'a',
        'ä'  => 'a',
        'å'  => 'a',
        'æ'  => 'ae',
        'ç'  => 'c',
        'è'  => 'e',
        'é'  => 'e',
        'ê'  => 'e',
        'ë'  => 'e',
        'ì'  => 'i',
        'í'  => 'i',
        'î'  => 'i',
        'ï'  => 'i',
        'ð'  => 'd',
        'ñ'  => 'n',
        'ò'  => 'o',
        'ó'  => 'o',
        'ô'  => 'o',
        'õ'  => 'o',
        'ö'  => 'o',
        'ø'  => 'o',
        'ù'  => 'u',
        'ú'  => 'u',
        'û'  => 'u',
        'ü'  => 'u',
        'ý'  => 'y',
        'þ'  => 'th',
        'ÿ'  => 'y',
        'Ā'  => 'A',
        'ā'  => 'a',
        'Ă'  => 'A',
        'ă'  => 'a',
        'Ą'  => 'A',
        'ą'  => 'a',
        'Ć'  => 'C',
        'ć'  => 'c',
        'Ĉ'  => 'C',
        'ĉ'  => 'c',
        'Ċ'  => 'C',
        'ċ'  => 'c',
        'Č'  => 'C',
        'č'  => 'c',
        'Ď'  => 'D',
        'ď'  => 'd',
        'Đ'  => 'D',
        'đ'  => 'd',
        'Ē'  => 'E',
        'ē'  => 'e',
        'Ĕ'  => 'E',
        'ĕ'  => 'e',
        'Ė'  => 'E',
        'ė'  => 'e',
        'Ę'  => 'E',
        'ę'  => 'e',
        'Ě'  => 'E',
        'ě'  => 'e',
        'Ĝ'  => 'G',
        'ĝ'  => 'g',
        'Ğ'  => 'G',
        'ğ'  => 'g',
        'Ġ'  => 'G',
        'ġ'  => 'g',
        'Ģ'  => 'G',
        'ģ'  => 'g',
        'Ĥ'  => 'H',
        'ĥ'  => 'h',
        'Ħ'  => 'H',
        'ħ'  => 'h',
        'Ĩ'  => 'I',
        'ĩ'  => 'i',
        'Ī'  => 'I',
        'ī'  => 'i',
        'Ĭ'  => 'I',
        'ĭ'  => 'i',
        'Į'  => 'I',
        'į'  => 'i',
        'İ'  => 'I',
        'ı'  => 'i',
        'Ĳ'  => 'IJ',
        'ĳ'  => 'ij',
        'Ĵ'  => 'J',
        'ĵ'  => 'j',
        'Ķ'  => 'K',
        'ķ'  => 'k',
        'ĸ'  => 'k',
        'Ĺ'  => 'L',
        'ĺ'  => 'l',
        'Ļ'  => 'L',
        'ļ'  => 'l',
        'Ľ'  => 'L',
        'ľ'  => 'l',
        'Ŀ'  => 'L',
        'ŀ'  => 'l',
        'Ł'  => 'L',
        'ł'  => 'l',
        'Ń'  => 'N',
        'ń'  => 'n',
        'Ņ'  => 'N',
        'ņ'  => 'n',
        'Ň'  => 'N',
        'ň'  => 'n',
        'ŉ'  => 'n',
        'Ŋ'  => 'N',
        'ŋ'  => 'n',
        'Ō'  => 'O',
        'ō'  => 'o',
        'Ŏ'  => 'O',
        'ŏ'  => 'o',
        'Ő'  => 'O',
        'ő'  => 'o',
        'Œ'  => 'OE',
        'œ'  => 'oe',
        'Ŕ'  => 'R',
        'ŕ'  => 'r',
        'Ŗ'  => 'R',
        'ŗ'  => 'r',
        'Ř'  => 'R',
        'ř'  => 'r',
        'Ś'  => 'S',
        'ś'  => 's',
        'Ŝ'  => 'S',
        'ŝ'  => 's',
        'Ş'  => 'S',
        'ş'  => 's',
        'Š'  => 'S',
        'š'  => 's',
        'Ţ'  => 'T',
        'ţ'  => 't',
        'Ť'  => 'T',
        'ť'  => 't',
        'Ŧ'  => 'T',
        'ŧ'  => 't',
        'Ũ'  => 'U',
        'ũ'  => 'u',
        'Ū'  => 'U',
        'ū'  => 'u',
        'Ŭ'  => 'U',
        'ŭ'  => 'u',
        'Ů'  => 'U',
        'ů'  => 'u',
        'Ű'  => 'U',
        'ű'  => 'u',
        'Ų'  => 'U',
        'ų'  => 'u',
        'Ŵ'  => 'W',
        'ŵ'  => 'w',
        'Ŷ'  => 'Y',
        'ŷ'  => 'y',
        'Ÿ'  => 'Y',
        'Ź'  => 'Z',
        'ź'  => 'z',
        'Ż'  => 'Z',
        'ż'  => 'z',
        'Ž'  => 'Z',
        'ž'  => 'z',
        'ſ'  => 's',
        'Ơ'  => 'O',
        'ơ'  => 'o',
        'Ư'  => 'U',
        'ư'  => 'u',
        'Ǎ'  => 'A',
        'ǎ'  => 'a',
        'Ǐ'  => 'I',
        'ǐ'  => 'i',
        'Ǒ'  => 'O',
        'ǒ'  => 'o',
        'Ǔ'  => 'U',
        'ǔ'  => 'u',
        'Ǖ'  => 'U',
        'ǖ'  => 'u',
        'Ǘ'  => 'U',
        'ǘ'  => 'u',
        'Ǚ'  => 'U',
        'ǚ'  => 'u',
        'Ǜ'  => 'U',
        'ǜ'  => 'u',
        'Ș'  => 'S',
        'ș'  => 's',
        'Ț'  => 'T',
        'ț'  => 't',
        'ɑ'  => 'a',
        'Ạ'  => 'A',
        'ạ'  => 'a',
        'Ả'  => 'A',
        'ả'  => 'a',
        'Ấ'  => 'A',
        'ấ'  => 'a',
        'Ầ'  => 'A',
        'ầ'  => 'a',
        'Ẩ'  => 'A',
        'ẩ'  => 'a',
        'Ẫ'  => 'A',
        'ẫ'  => 'a',
        'Ậ'  => 'A',
        'ậ'  => 'a',
        'Ắ'  => 'A',
        'ắ'  => 'a',
        'Ằ'  => 'A',
        'ằ'  => 'a',
        'Ẳ'  => 'A',
        'ẳ'  => 'a',
        'Ẵ'  => 'A',
        'ẵ'  => 'a',
        'Ặ'  => 'A',
        'ặ'  => 'a',
        'Ẹ'  => 'E',
        'ẹ'  => 'e',
        'Ẻ'  => 'E',
        'ẻ'  => 'e',
        'Ẽ'  => 'E',
        'ẽ'  => 'e',
        'Ế'  => 'E',
        'ế'  => 'e',
        'Ề'  => 'E',
        'ề'  => 'e',
        'Ể'  => 'E',
        'ể'  => 'e',
        'Ễ'  => 'E',
        'ễ'  => 'e',
        'Ệ'  => 'E',
        'ệ'  => 'e',
        'Ỉ'  => 'I',
        'ỉ'  => 'i',
        'Ị'  => 'I',
        'ị'  => 'i',
        'Ọ'  => 'O',
        'ọ'  => 'o',
        'Ỏ'  => 'O',
        'ỏ'  => 'o',
        'Ố'  => 'O',
        'ố'  => 'o',
        'Ồ'  => 'O',
        'ồ'  => 'o',
        'Ổ'  => 'O',
        'ổ'  => 'o',
        'Ỗ'  => 'O',
        'ỗ'  => 'o',
        'Ộ'  => 'O',
        'ộ'  => 'o',
        'Ớ'  => 'O',
        'ớ'  => 'o',
        'Ờ'  => 'O',
        'ờ'  => 'o',
        'Ở'  => 'O',
        'ở'  => 'o',
        'Ỡ'  => 'O',
        'ỡ'  => 'o',
        'Ợ'  => 'O',
        'ợ'  => 'o',
        'Ụ'  => 'U',
        'ụ'  => 'u',
        'Ủ'  => 'U',
        'ủ'  => 'u',
        'Ứ'  => 'U',
        'ứ'  => 'u',
        'Ừ'  => 'U',
        'ừ'  => 'u',
        'Ử'  => 'U',
        'ử'  => 'u',
        'Ữ'  => 'U',
        'ữ'  => 'u',
        'Ự'  => 'U',
        'ự'  => 'u',
        'Ỳ'  => 'Y',
        'ỳ'  => 'y',
        'Ỵ'  => 'Y',
        'ỵ'  => 'y',
        'Ỷ'  => 'Y',
        'ỷ'  => 'y',
        'Ỹ'  => 'Y',
        'ỹ'  => 'y',
        '‐'  => ' ',
        '‒'  => ' ',
        '–'  => ' ',
        '—'  => ' ',
        '―'  => ' ',
        '‘'  => ' ',
        '’'  => ' ',
        '“'  => ' ',
        '”'  => ' ',
        '«'  => ' ',
        '»'  => ' ',
        '⁃'  => ' ',
        '€'  => 'E',
        '−'  => ' ',
    ];

    /**
     * Clean the special chars and add - for separate words
     *
     * @param  mixed  $string the string to transform
     *
     * @return mixed the string cleaned
     */
    public static function generateSlug($string, $useStopList = true, $delimiter = '-')
    {
        if (is_array($string)) {
            return array_map(function ($a) use ($useStopList, $delimiter) {
                return self::generateSlug($a, $useStopList, $delimiter);
            }, $string);
        }

        $string = strip_tags($string);

        // Remove numeric separators from numbers
        $string = preg_replace('/([0-9]+)[.,]([0-9]+)/', '$1$2', $string);

        // Remove UTF-8 C0 controls chars encoded in HTML entities
        // http://www.w3schools.com/charsets/ref_utf_basic_latin.asp
        $string = preg_replace('/&#[0-9]+;/', '', $string);

        // Use intl extension to clean
        $string = transliterator_transliterate(
            "NFD; [:Nonspacing Mark:] Remove; NFC; Lower();",
            $string
        );

        $string = trim(strtr($string, self::$trade));

        // Remove stop list
        if ($useStopList) {
            $stringLower = mb_strtolower($string);
            $newString   = Str::removeShortWords($stringLower);

            if (mb_strlen($newString) > 20) {
                $string = $newString;
            }
        }

        // Remove some characters
        $string = str_replace([
            "%c2%a0", "%c2%a1", "%c2%a9", "%c2%ab", "%c2%ae", "%c2%b0",
            "%c2%b4", "%c2%bb", "%c2%bf", "%c3%97", "%cb%8a", "%cc%80",
            "%cc%81", "%cc%84", "%cc%8c", "%cd%81", "%e2%80%93", "%e2%80%94",
            "%e2%80%98", "%e2%80%99", "%e2%80%9a", "%e2%80%9b", "%e2%80%9c",
            "%e2%80%9d", "%e2%80%9e", "%e2%80%9f", "%e2%80%a6", "%e2%80%b9",
            "%e2%80%ba", "%e2%84%a2", "&#160;", "&mdash;", "&nbsp;", "&ndash;",
            "\xc2\xa0",
        ], ' ', $string);

        // Clean trailing and duplicated spaces
        $string = preg_replace(['/\s+/', '/[\t\n]/'], ' ', $string);
        $string = trim($string);

        $string = str_replace('.', '-', $string);
        $string = preg_replace("@[\-]{2,}@", $delimiter, $string);
        $string = preg_replace('/&.+?;/', '', $string); // kill entities

        return trim(preg_replace('/\s+/', '-', trim($string)));
    }

    /**
     * Prepares HTML code to use it as html entity attribute
     *
     * @param string $string the string to clean
     *
     * @return string $string the cleaned string
     */
    public static function htmlAttribute($string)
    {
        $string = html_entity_decode($string, ENT_QUOTES, 'UTF-8');

        return htmlspecialchars(strip_tags(stripslashes($string)), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    /**
     * Delete disallowed chars from a sentence and transform it to a url friendly name
     *
     * @param  string  $name the string to clean
     *
     * @return string the string cleaned
     */
    public static function normalize($name)
    {
        $newname = mb_strtolower($name, 'UTF-8');
        $newname = strtr($newname, self::$trade);
        $newname = rtrim($newname);

        return $newname;
    }

    /**
     * Cleans double slashes and trailing slash from an string url
     *
     * @param string $url the url to normalize
     * @return string the normalized url
     */
    public static function normalizeUrl($url)
    {
        $urlParts = explode('?', $url);
        $url      = $urlParts[0];

        $urlParams = '';
        if (array_key_exists('1', $urlParts)) {
            $urlParams = '?' . $urlParts[1];
        }

        $url = rtrim($url, '/');

        if ($urlParams !== '' && $url !== '/') {
            while (strpos($url, '//') != false) {
                $url = str_replace('//', '/', $url);
            }
        }

        if (empty($url)) {
            $url = '/';
        }

        return $url . $urlParams;
    }

    /**
     * Converts all object properties to utf-8.
     *
     * @param mixed $input Object or list of input to convert.
     *
     * @return mixed Object or list of input with properties in utf-8.
     */
    public static function convertToUtf8($input)
    {
        if (is_object($input)) {
            $keys = array_keys(get_object_vars($input));

            if ($input instanceof \Content || $input instanceof Content) {
                $keys = array_merge($keys, $input->getL10nKeys());
            }

            foreach ($keys as $key) {
                if (($input instanceof \Content
                    || $input instanceof Content)
                    && is_array($input->{$key})
                    && in_array($key, $input->getL10nKeys())
                ) {
                    $input->{$key} = array_map(function ($a) {
                        return self::convertToUtf8($a);
                    }, $input->{$key});

                    continue;
                }

                $input->{$key} = self::convertToUtf8($input->{$key});
            }

            return $input;
        }

        if (is_array($input)) {
            foreach ($input as &$value) {
                $value = self::convertToUtf8($value);
            }

            return $input;
        }

        if (is_string($input)) {
            $input = !empty(mb_detect_encoding($input)) ?
                mb_convert_encoding($input, 'UTF-8', mb_detect_encoding($input)) :
                '';
        }

        return $input;
    }

    /**
     * Converts all object properties to utf-8.
     *
     * @param Object $objects Objects to convert.
     */
    protected static function convertObjectToUtf8(&$object)
    {
        $keys = array_keys(get_object_vars($object));

        if ($object instanceof \Content) {
            $keys = array_merge($keys, $object->getL10nKeys());
        }

        foreach ($keys as $key) {
            $value = $object->{$key};

            if (is_string($value)) {
                $object->{$key} = mb_convert_encoding(
                    $value,
                    'UTF-8',
                    mb_detect_encoding($value)
                );
            }

            if ($object instanceof \Content
                && is_array($value)
                && in_array($key, $object->getL10nKeys())
            ) {
                $object->{$key} = array_map(function ($a) {
                    return mb_convert_encoding($a, 'UTF-8', mb_detect_encoding($a));
                }, $value);
            }
        }
    }
}
