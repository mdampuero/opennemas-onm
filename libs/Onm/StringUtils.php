<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 * @package    Onm_Utils
 */
namespace Onm;

/**
 * Library for handling unusual string operations.
 *
 * @package    Onm
 */
class StringUtils
{
    /**
     * @var array $trade List of chars and its replacements.
     *                    Used in generateSlug()
     */
    static protected $trade = [
        'á' => 'a', 'à' => 'a', 'ã' => 'a', 'ä' => 'a', 'â' => 'a', 'Á' => 'A',
        'À' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Â' => 'A', 'é' => 'e', 'è' => 'e',
        'ë' => 'e', 'ê' => 'e', 'É' => 'E', 'È' => 'E', 'Ë' => 'E', 'Ê' => 'E',
        'í' => 'i', 'ì' => 'i', 'ï' => 'i', 'î' => 'i', 'Í' => 'I', 'Ì' => 'I',
        'Ï' => 'I', 'Î' => 'I', 'ó' => 'o', 'ò' => 'o', 'õ' => 'o', 'ö' => 'o',
        'ô' => 'o', 'Ó' => 'O', 'Ò' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ô' => 'O',
        'ú' => 'u', 'ù' => 'u', 'ü' => 'u', 'û' => 'u', 'Ú' => 'U', 'Ù' => 'U',
        'Ü' => 'U', 'Û' => 'U', '$' => '',  '@' => '',  '!' => '',  '#' => '',
        '%' => '',  '^' => '',  '&' => '',  '*' => '',  '(' => '',  ')' => '',
        '-' => '-', '+' => '',  '=' => '',  '\\' => '-', '|' => '-', '`' => '',
        '~' => '',  '/' => '-', '\"' => '', '\'' => '', '<' => '',  '>' => '',
        '?' => '-', ',' => '-', 'ç' => 'c', 'Ç' => 'C',  '·' => '', 'ª' => '',
        'º' => '', ';' => '-', '[' => '-', ']' => '-', 'ñ' => 'n', 'Ñ' => 'N',
        'Š' => 'S', 'š' => 's', 'Đ' => 'Dj', 'đ' => 'dj', 'Ž' => 'Z', 'ž' => 'z',
        'Č' => 'C', 'č' => 'c', 'Ć' => 'C', 'ć' => 'c', 'À' => 'A', 'Á' => 'A',
        'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Æ' => 'A', 'Ç' => 'C',
        'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I',
        'Î' => 'I', 'Ï' => 'I', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O',
        'Õ' => 'O', 'Ö' => 'O', 'Ø' => 'O', 'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U',
        'Ü' => 'U', 'Ý' => 'Y', 'Þ' => 'B', 'ß' => 'Ss', 'à' => 'a', 'á' => 'a',
        'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'a', 'ç' => 'c',
        'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i',
        'î' => 'i', 'ï' => 'i', 'ð' => 'o', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o',
        'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ø' => 'o', 'ù' => 'u', 'ú' => 'u',
        'û' => 'u', 'ý' => 'y', 'ý' => 'y', 'þ' => 'b', 'ÿ' => 'y', 'Ŕ' => 'R',
        'ŕ' => 'r', '/' => '-', ' ' => '-', '"' => '',  '!' => '',  '¡' => '',
        '‐' => '', '‒' => '', '–' => '', '—' => '',
        '―' => '', '⁃' => '', '−' => '', "\r" => ' ', "\n" => '',
    ];

    /**
     * @var array $accentsReplacMap List of chars and its replacements.
     *                               Used in removeAccents()
     */
    static protected $accentsReplacMap = [
        // Decompositions for Latin-1 Supplement
        'ª' => 'a', 'º' => 'o',
        'À' => 'A', 'Á' => 'A',
        'Â' => 'A', 'Ã' => 'A',
        'Ä' => 'A', 'Å' => 'A',
        'Æ' => 'AE','Ç' => 'C',
        'È' => 'E', 'É' => 'E',
        'Ê' => 'E', 'Ë' => 'E',
        'Ì' => 'I', 'Í' => 'I',
        'Î' => 'I', 'Ï' => 'I',
        'Ð' => 'D', 'Ñ' => 'N',
        'Ò' => 'O', 'Ó' => 'O',
        'Ô' => 'O', 'Õ' => 'O',
        'Ö' => 'O', 'Ù' => 'U',
        'Ú' => 'U', 'Û' => 'U',
        'Ü' => 'U', 'Ý' => 'Y',
        'Þ' => 'TH','ß' => 's',
        'à' => 'a', 'á' => 'a',
        'â' => 'a', 'ã' => 'a',
        'ä' => 'a', 'å' => 'a',
        'æ' => 'ae','ç' => 'c',
        'è' => 'e', 'é' => 'e',
        'ê' => 'e', 'ë' => 'e',
        'ì' => 'i', 'í' => 'i',
        'î' => 'i', 'ï' => 'i',
        'ð' => 'd', 'ñ' => 'n',
        'ò' => 'o', 'ó' => 'o',
        'ô' => 'o', 'õ' => 'o',
        'ö' => 'o', 'ø' => 'o',
        'ù' => 'u', 'ú' => 'u',
        'û' => 'u', 'ü' => 'u',
        'ý' => 'y', 'þ' => 'th',
        'ÿ' => 'y', 'Ø' => 'O',
        // Decompositions for Latin Extended-A
        'Ā' => 'A', 'ā' => 'a',
        'Ă' => 'A', 'ă' => 'a',
        'Ą' => 'A', 'ą' => 'a',
        'Ć' => 'C', 'ć' => 'c',
        'Ĉ' => 'C', 'ĉ' => 'c',
        'Ċ' => 'C', 'ċ' => 'c',
        'Č' => 'C', 'č' => 'c',
        'Ď' => 'D', 'ď' => 'd',
        'Đ' => 'D', 'đ' => 'd',
        'Ē' => 'E', 'ē' => 'e',
        'Ĕ' => 'E', 'ĕ' => 'e',
        'Ė' => 'E', 'ė' => 'e',
        'Ę' => 'E', 'ę' => 'e',
        'Ě' => 'E', 'ě' => 'e',
        'Ĝ' => 'G', 'ĝ' => 'g',
        'Ğ' => 'G', 'ğ' => 'g',
        'Ġ' => 'G', 'ġ' => 'g',
        'Ģ' => 'G', 'ģ' => 'g',
        'Ĥ' => 'H', 'ĥ' => 'h',
        'Ħ' => 'H', 'ħ' => 'h',
        'Ĩ' => 'I', 'ĩ' => 'i',
        'Ī' => 'I', 'ī' => 'i',
        'Ĭ' => 'I', 'ĭ' => 'i',
        'Į' => 'I', 'į' => 'i',
        'İ' => 'I', 'ı' => 'i',
        'Ĳ' => 'IJ','ĳ' => 'ij',
        'Ĵ' => 'J', 'ĵ' => 'j',
        'Ķ' => 'K', 'ķ' => 'k',
        'ĸ' => 'k', 'Ĺ' => 'L',
        'ĺ' => 'l', 'Ļ' => 'L',
        'ļ' => 'l', 'Ľ' => 'L',
        'ľ' => 'l', 'Ŀ' => 'L',
        'ŀ' => 'l', 'Ł' => 'L',
        'ł' => 'l', 'Ń' => 'N',
        'ń' => 'n', 'Ņ' => 'N',
        'ņ' => 'n', 'Ň' => 'N',
        'ň' => 'n', 'ŉ' => 'n',
        'Ŋ' => 'N', 'ŋ' => 'n',
        'Ō' => 'O', 'ō' => 'o',
        'Ŏ' => 'O', 'ŏ' => 'o',
        'Ő' => 'O', 'ő' => 'o',
        'Œ' => 'OE','œ' => 'oe',
        'Ŕ' => 'R','ŕ' => 'r',
        'Ŗ' => 'R','ŗ' => 'r',
        'Ř' => 'R','ř' => 'r',
        'Ś' => 'S','ś' => 's',
        'Ŝ' => 'S','ŝ' => 's',
        'Ş' => 'S','ş' => 's',
        'Š' => 'S', 'š' => 's',
        'Ţ' => 'T', 'ţ' => 't',
        'Ť' => 'T', 'ť' => 't',
        'Ŧ' => 'T', 'ŧ' => 't',
        'Ũ' => 'U', 'ũ' => 'u',
        'Ū' => 'U', 'ū' => 'u',
        'Ŭ' => 'U', 'ŭ' => 'u',
        'Ů' => 'U', 'ů' => 'u',
        'Ű' => 'U', 'ű' => 'u',
        'Ų' => 'U', 'ų' => 'u',
        'Ŵ' => 'W', 'ŵ' => 'w',
        'Ŷ' => 'Y', 'ŷ' => 'y',
        'Ÿ' => 'Y', 'Ź' => 'Z',
        'ź' => 'z', 'Ż' => 'Z',
        'ż' => 'z', 'Ž' => 'Z',
        'ž' => 'z', 'ſ' => 's',
        // Decompositions for Latin Extended-B
        'Ș' => 'S', 'ș' => 's',
        'Ț' => 'T', 'ț' => 't',
        // Euro Sign
        '€' => 'E',
        // GBP (Pound) Sign
        '£' => '',
        // Vowels with diacritic (Vietnamese)
        // unmarked
        'Ơ' => 'O', 'ơ' => 'o',
        'Ư' => 'U', 'ư' => 'u',
        // grave accent
        'Ầ' => 'A', 'ầ' => 'a',
        'Ằ' => 'A', 'ằ' => 'a',
        'Ề' => 'E', 'ề' => 'e',
        'Ồ' => 'O', 'ồ' => 'o',
        'Ờ' => 'O', 'ờ' => 'o',
        'Ừ' => 'U', 'ừ' => 'u',
        'Ỳ' => 'Y', 'ỳ' => 'y',
        // hook
        'Ả' => 'A', 'ả' => 'a',
        'Ẩ' => 'A', 'ẩ' => 'a',
        'Ẳ' => 'A', 'ẳ' => 'a',
        'Ẻ' => 'E', 'ẻ' => 'e',
        'Ể' => 'E', 'ể' => 'e',
        'Ỉ' => 'I', 'ỉ' => 'i',
        'Ỏ' => 'O', 'ỏ' => 'o',
        'Ổ' => 'O', 'ổ' => 'o',
        'Ở' => 'O', 'ở' => 'o',
        'Ủ' => 'U', 'ủ' => 'u',
        'Ử' => 'U', 'ử' => 'u',
        'Ỷ' => 'Y', 'ỷ' => 'y',
        // tilde
        'Ẫ' => 'A', 'ẫ' => 'a',
        'Ẵ' => 'A', 'ẵ' => 'a',
        'Ẽ' => 'E', 'ẽ' => 'e',
        'Ễ' => 'E', 'ễ' => 'e',
        'Ỗ' => 'O', 'ỗ' => 'o',
        'Ỡ' => 'O', 'ỡ' => 'o',
        'Ữ' => 'U', 'ữ' => 'u',
        'Ỹ' => 'Y', 'ỹ' => 'y',
        // acute accent
        'Ấ' => 'A', 'ấ' => 'a',
        'Ắ' => 'A', 'ắ' => 'a',
        'Ế' => 'E', 'ế' => 'e',
        'Ố' => 'O', 'ố' => 'o',
        'Ớ' => 'O', 'ớ' => 'o',
        'Ứ' => 'U', 'ứ' => 'u',
        // dot below
        'Ạ' => 'A', 'ạ' => 'a',
        'Ậ' => 'A', 'ậ' => 'a',
        'Ặ' => 'A', 'ặ' => 'a',
        'Ẹ' => 'E', 'ẹ' => 'e',
        'Ệ' => 'E', 'ệ' => 'e',
        'Ị' => 'I', 'ị' => 'i',
        'Ọ' => 'O', 'ọ' => 'o',
        'Ộ' => 'O', 'ộ' => 'o',
        'Ợ' => 'O', 'ợ' => 'o',
        'Ụ' => 'U', 'ụ' => 'u',
        'Ự' => 'U', 'ự' => 'u',
        'Ỵ' => 'Y', 'ỵ' => 'y',
        // Vowels with diacritic (Chinese, Hanyu Pinyin)
        'ɑ' => 'a',
        // macron
        'Ǖ' => 'U', 'ǖ' => 'u',
        // acute accent
        'Ǘ' => 'U', 'ǘ' => 'u',
        // caron
        'Ǎ' => 'A', 'ǎ' => 'a',
        'Ǐ' => 'I', 'ǐ' => 'i',
        'Ǒ' => 'O', 'ǒ' => 'o',
        'Ǔ' => 'U', 'ǔ' => 'u',
        'Ǚ' => 'U', 'ǚ' => 'u',
        // grave accent
        'Ǜ' => 'U', 'ǜ' => 'u',
    ];

    /**
     * @var array $spanishWords List of spanish words that could be removed
     *                           from a phrase while generating slugs
     */
    static protected $spanishWords = [
        'a',
        'as',
        'al',
        'ante',
        'ante',
        'aquel',
        'aquelo',
        'aquela',
        'aquello',
        'aquella',
        'aquellas',
        'aquellos',
        'aunque',
        'bajo',
        'bien',
        'cabe',
        'cinco',
        'como',
        'con',
        'conmigo',
        'contra',
        'cuatro',
        'de',
        'del',
        'desde',
        'dos',
        'durante',
        'e',
        'el',
        'eles',
        'elas',
        'en',
        'entre',
        'es',
        'esa',
        'esas',
        'ese',
        'eso',
        'esos',
        'esta',
        'estas',
        'este',
        'esto',
        'estos',
        'excepto',
        'hacia',
        'hasta',
        'hay',
        'la',
        'las',
        'le',
        'les',
        'lo',
        'los',
        'me',
        'mediante',
        'mi',
        'nosotras',
        'nosotros',
        'nove',
        'nueve',
        'o',
        'os',
        'ocho',
        'oito',
        'otro',
        'outro',
        'ou',
        'para',
        'pero',
        'por',
        'que',
        'salvo',
        'se',
        'segun',
        'seis',
        'sete',
        'si',
        'siete',
        'sin',
        'sen',
        'sino',
        'sobre',
        'su',
        'sus',
        'te',
        'tras',
        'tres',
        'tu',
        'un',
        'una',
        'unha',
        'unhas',
        'unas',
        'uno',
        'unos',
        'y',
        'ya',
        'yo',
        'si',
    ];

    /**
     * Clean the special chars into a file name
     *
     * @param  string  $name the string to clean
     *
     * @return string the string cleaned
     */
    public static function cleanFileName($name)
    {
        $name = trim($name);
        $name = html_entity_decode($name, ENT_COMPAT, 'UTF-8');
        $name = mb_strtolower($name, 'UTF-8');
        $name = preg_replace('/\s/', '-', $name);
        $name = StringUtils::normalize($name);

        return $name;
    }

    /**
     * Removes some spanish bad words and swears from the given string
     *
     * @param string $string the string to clean
     *
     * @return string the cleaned string
     */
    public static function clearBadChars($string)
    {
        $string = preg_replace('/' . chr(226) . chr(128) . chr(169) . '/', '', $string);

        return $string;
    }

    /**
     * Clear the special quotes
     *
     * @param  string  $text the string to transform
     *
     * @return string the string cleaned
     */
    public static function clearQuotes($text)
    {
        $text = preg_replace('/(>[^<"]*)["]+([^<"]*<)/', "$1&#34;$2", $text);
        $text = preg_replace("/(>[^<']*)[']+([^<']*<)/", "$1&#39;$2", $text);
        $text = str_replace('“', '&#8220;', $text);
        $text = str_replace('”', '&#8221;', $text);
        $text = str_replace('‘', '&#8216;', $text);
        $text = str_replace('’', '&#8217;', $text);

        return $text;
    }

    /**
     * Clean the special chars into a given string
     *
     * Performs a html_entity_encode, mb_strtolower and mb_ereg_replace
     * disallowed chars
     *
     * @param  string  $str the string to clen
     *
     * @return string the string cleaned
     */
    public static function clearSpecialChars($str)
    {
        $str = html_entity_decode($str, ENT_COMPAT, 'UTF-8');
        $str = mb_strtolower($str, 'UTF-8');
        $str = mb_ereg_replace('[^a-z0-9áéíóúñüç_\,\- ]', ' ', $str);

        return $str;
    }

    /**
     * Converts to UTF-8 an string
     *
     * @param string $str the string to convert
     *
     * @return string the UTF-8 converted string
     */
    public static function convertToUTF8AndStrToLower($str)
    {
        // $str = mb_convert_encoding($str, 'UTF-8', mb_detect_encoding($str));
        $str = mb_convert_encoding(
            $str,
            "UTF-8",
            "CP1252,CP1251,ISO-8859-1,UTF-8,ISO-8859-15"
        );

        return mb_strtolower($str, 'UTF-8');
    }

    /**
     * Replaces matches of a text with another text
     *
     * Ignores the case and do keep the original capitalization by using $1 in $replacewith
     *
     * @param string $findme      the text to replace
     * @param string $replacewith the replacement text
     * @param string $subject     the text to change
     *
     * @return string the new string
     */
    public static function extStrIreplace($findme, $replacewith, $subject)
    {
        $rest   = $subject;
        $result = '';

        while (stripos($rest, $findme) !== false) {
             $pos = stripos($rest, $findme);

             // Remove the wanted string from $rest and append it to $result
             $result .= substr($rest, 0, $pos);
             $rest    = substr($rest, $pos, strlen($rest) - $pos);

             // Remove the wanted string from $rest and place it correctly into $result
             $result .= str_replace('$1', substr($rest, 0, strlen($findme)), $replacewith);
             $rest    = substr($rest, strlen($findme), strlen($rest) - strlen($findme));
        }

        // After the last match, append the rest
        $result .= $rest;

        return $result;
    }

    /**
     * Removes bad words from a text
     *
     * @param string $text       the text to clean
     * @param int    $weight     the minimum bad word weight to clean
     * @param string $replaceStr the replacement string for the bad words
     *
     * @return string the cleaned string
     */
    public static function filterBadWords($text, $weight = 0, $replaceStr = ' ')
    {
        $words = self::loadBadWords();
        $text  = ' ' . $text . ' ';
        if ($replaceStr != ' ') {
            $replaceStr = ' ' . $replaceStr . ' ';
        }

        foreach ($words as $word) {
            if ($word['weight'] > $weight) {
                $text = preg_replace(
                    '/\W' . $word['text'] . '\W/si',
                    $replaceStr,
                    $text
                );
            }
        }

        $text = trim($text);

        return $text;
    }

    /**
     * Returns a random password with an specific length
     *
     * @param int $length the length of the password
     *
     * @return string the password string
     */
    public static function generatePassword($length = 8)
    {
        $chars    = "234567890abcdefghijkmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $i        = 0;
        $password = "";

        while ($i <= $length - 1) {
            $password .= $chars{mt_rand(0, strlen($chars) - 1)};
            $i++;
        }

        return $password;
    }

    /**
     * Returns a randomly generated string from a given length
     *
     * @param int $length the length in chars of the generated string
     *
     * @return string the random string
     */
    public static function generateRandomString($length = 10)
    {
        $validCharacters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $validCharacters[rand(0, strlen($validCharacters) - 1)];
        }

        return $randomString;
    }

    /**
     * Removes all the accents from a string
     *
     * @return void
     * @author
     */
    public static function removeAccents($string)
    {
        $string = strtr($string, self::$accentsReplacMap);

        return $string;
    }

     /**
     * Clean the special chars and add - for separate words
     *
     * @param  string  $string the string to transform
     *
     * @return string the string cleaned
     */
    public static function generateSlug($string, $useStopList = true, $delimiter = '-')
    {
        $string = strip_tags($string);
        // Remove UTF-8 C0 controls chars encoded in HTML entities
        // http://www.w3schools.com/charsets/ref_utf_basic_latin.asp
        $string = preg_replace('/&#(0?[0-9]|1[0-9]|2[0-9]|3[0-1]);/', '', $string);

        // Use intl extension to clean
        $string = transliterator_transliterate(
            "NFD; [:Nonspacing Mark:] Remove; NFC; Lower();",
            $string
        );

        // Remove stop list
        if ($useStopList) {
            $newString = self::removeShorts($string);
            if (mb_strlen($newString) > 20) {
                $string = $newString;
            }
        }

        $string = trim(strtr($string, self::$trade));

        // Convert nbsp, ndash and mdash to hyphens
        $string = str_replace(['%c2%a0', '%e2%80%93', '%e2%80%94'], '-', $string);
        // Convert nbsp, ndash and mdash HTML entities to hyphens
        $string = str_replace(
            ['&nbsp;', '&#160;', '&ndash;', '&#8211;', '&mdash;', '&#8212;'],
            '-',
            $string
        );

        $charEntities = [
            // iexcl and iquest
            '%c2%a1', '%c2%bf',
            // angle quotes
            '%c2%ab', '%c2%bb', '%e2%80%b9', '%e2%80%ba',
            // curly quotes
            '%e2%80%98', '%e2%80%99', '%e2%80%9c', '%e2%80%9d',
            '%e2%80%9a', '%e2%80%9b', '%e2%80%9e', '%e2%80%9f',
            // copy, reg, deg, hellip and trade
            '%c2%a9', '%c2%ae', '%c2%b0', '%e2%80%a6', '%e2%84%a2',
            // acute accents
            '%c2%b4', '%cb%8a', '%cc%81', '%cd%81',
            // grave accent, macron, caron
            '%cc%80', '%cc%84', '%cc%8c',
        ];

        // Strip these characters entirely
        $string = str_replace($charEntities, '', $string);

        // Convert times to x
        $string = str_replace('%c3%97', 'x', $string);
        // Remove punctuation marks
        $string = str_replace(
            [
                '"', "'", "…", ".", ",", "“", "”", ",", ".",
                ":", ";", "?", "¿", "!", "¡", "'", ")", ")"
            ],
            '',
            $string
        );

        // Clean trailing and duplicated spaces
        $string = preg_replace(['/\s+/', '/[\t\n]/'], ' ', $string);
        $string = trim($string);

        $string = str_replace('.', '-', $string);
        $string = preg_replace("@[\-]{2,}@", $delimiter, $string);
        $string = preg_replace('/&.+?;/', '', $string); // kill entities

        return trim($string, '-');
    }

    /**
     * Generates a valid slug, this function is a "copy" of self::generateSlug,
     * keep it here to avoid problems for unchanged uses.
     *
     * @param  string  $title
     * @param  boolean $useStopList whether use the stopList array
     * @param  string  $delimiter
     *
     * @return string
     */
    public static function getTitle($origString, $useStopList = true, $delimiter = '-')
    {
        $useStopList = true;

        return self::generateSlug($origString, $useStopList, $delimiter);
    }

    /**
     * Gets "n" first words from a given text
     *
     * @example self::getNumWords('hello world', 1)
     *
     * @param  string  $text the text to operate with
     * @param  integer $numWords
     *
     * @return string
     */
    public static function getNumWords($text, $numWords)
    {
        $noHtml      = strip_tags($text);
        $description = explode(" ", $noHtml, $numWords + 1);
        array_pop($description);

        $words = implode(" ", $description) . '...';

        return $words;
    }

    /**
     * Returns a string with keywords from a given text
     *
     * @param  string $text the text where extract keywords from
     *
     * @return string the string of keywords separated by commas
     */
    public static function getTags($text)
    {
        // $text = self::clearSpecialChars($text);
        $text = preg_replace('/[\.]+/', '', $text);

        // Remove stop list
        $text = self::removeShorts($text);
        $text = self::setSeparator($text, ',');
        $text = preg_replace('/[\,]+/', ',', $text);

        // Remove duplicates
        $tags = array_unique(explode(',', $text));
        $tags = implode(', ', $tags);

        return $tags;
    }

    /**
     * Returns the weight of a text from its bad words
     *
     * @param string $text the text to work with
     *
     * @return int the weight of the text
     */
    public static function getWeightBadWords($text)
    {
        $words = self::loadBadWords();
        $text  = ' ' . $text . ' ';

        $weight = 0;

        foreach ($words as $word) {
            if (preg_match_all('/' . $word['text'] . '/si', $text, $matches)) {
                $weight += ($word['weight'] * count($matches[0]));
            }
        }

        return $weight;
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
     * Retuns a list of bad words with their weigth
     *
     * @return array the list of words
     */
    public static function loadBadWords()
    {
        $badWords = array(
            array(
                'weight' => 5,
                'text'   => 'm[i]?erda',
            ),
            array(
                'weight' => 5,
                'text'   => 'marica',
            ),
            array(
                'weight' => 5,
                'text'   => 'carallo',
            ),
            array(
                'weight' => 10,
                'text'   => '[h]?ostia',
            ),
            array(
                'weight' => 20,
                'text'   => 'puta[s]?',
            ),
            array(
                'weight' => 30,
                'text'   => 'cabr[oó]n[a]?',
            ),
            array(
                'weight' => 50,
                'text'   => 'fill[ao] d[ae] puta',
            ),
            array(
                'weight' => 50,
                'text'   => 'hij[ao] de puta',
            ),
        );

        return $badWords;
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
     * Delete disallowed chars from a sentence and transform it to a url friendly name
     *
     * @param  string  $name the string to clean
     *
     * @return string the string cleaned
     */
    public static function normalizeTag($name)
    {
        $newname = mb_strtolower($name, 'UTF-8');

        // Keep # for estrelladigital and renderTags = hashtag
        $trade = self::$trade;
        unset($trade['#']);

        $newname = strtr($newname, $trade);
        $newname = rtrim($newname);

        return $newname;
    }

    /**
     * Prevent duplicate metadata
     *
     * @param string $metadata  the metadata to clean
     * @param string $separator Separator character to use for splitting the words.
     *                          By default ','
     *
     * @return string the cleaned metadata string
     */
    public static function normalizeMetadata($metadata)
    {
        $items = explode(',', $metadata);

        foreach ($items as $k => $item) {
            $items[$k] = self::normalizeTag(trim($item));
        }

        $items = array_flip($items);
        $items = array_keys($items);

        $metadata = implode(',', $items);

        return $metadata;
    }

    /**
     * Delete disallowed chars from a sentence and transform it to a url friendly name
     *
     * @param  string  $name the string to clean
     *
     * @return string the string cleaned
     */
    public static function normalizeName($name)
    {
        $name = self::normalize($name);
        $name = preg_replace('/[\- ]+/', '-', $name);

        return $name;
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
     * Removes some short spanish words, preps, conjunctions...
     *
     * Modified from Meneame:
     * @link http://svn.meneame.net/index.cgi/branches/version3/www/libs/uri.php
     *
     * @param string $string the string to clean
     *
     * @return string the cleaned string
     */
    public static function removeShorts($string)
    {
        $shorts = self::$spanishWords;

        $newstring = $string;
        foreach ($shorts as $word) {
            $newstring = preg_replace('/\b' . $word . '[\.\,\s]/', '', $newstring);
            $newstring = preg_replace('/[\.\,\s]' . $word . '[\.\,\s]/', '', $newstring);
            $newstring = preg_replace('/[\.\,\s]' . $word . '\b/', '', $newstring);
        }

        if (!empty(trim($newstring))) {
            $string = $newstring;
        }

        return $string;
    }

    /**
     * Deletes disallowed chars from a sentence and transform it to a url friendly name
     *
     * @param string $str       the string to clean
     * @param string $separator the separator chapter to use
     *
     * @return string the string cleaned
     */
    public static function setSeparator($str, $separator = '-')
    {
        $str = trim($str);
        $str = preg_replace('/[ ]+/', $separator, $str);

        return $str;
    }

    /**
     * Returns a trimmed string with a max number of elements
     * and appends an elipsis at the end
     *
     * @param string $string the string to trim
     * @param int $maxLength the max length of the final string
     * @param string $suffix the text to append at the end of the trimmed string
     *
     * @return string the trimmed string
     */
    public static function strStop($string, $maxLength = 30, $suffix = '...')
    {
        if (strlen($string) > $maxLength) {
            $string = substr($string, 0, $maxLength);
            $pos    = strrpos($string, " ");
            if ($pos === false) {
                return substr($string, 0, $maxLength) . $suffix;
            }

            return substr($string, 0, $pos) . $suffix;
        } else {
            return $string;
        }
    }

    /**
     * Converts an string to ascii code
     *
     * @return string the ascii encoded string
     */
    public static function toAscii($string)
    {
        $string = iconv('UTF-8', 'ASCII//TRANSLIT', $string);

        return $string;
    }

    /**
     * Implodes a two dimension array to a http params strins
     *
     * @param Array $httpParams an array of http parameters
     *
     * @return string the url compatible list of params
     */
    public static function toHttpParams(array $httpParams)
    {
        $result = array();

        // Implode each key => value parameter into key-value
        foreach ($httpParams as $param) {
            foreach ($param as $key => $value) {
                $result[] = $key . '=' . $value;
            }
        }

        // And implode all key=value parameters with &
        $result = implode('&', $result);

        return $result;
    }

    /**
     * Reverse operation for htmlentities
     *
     * @param string $string the string to operate with
     *
     * @return string the string without HTML entities
     */
    public static function unhtmlentities($string)
    {
        // replace numeric entities
        $string = preg_replace('~&#x([0-9a-f]+);~ei', 'chr(hexdec("\\1"))', $string);
        $string = preg_replace('~&#([0-9]+);~e', 'chr("\\1")', $string);
        // replace literal entities
        $transTbl = get_html_translation_table(HTML_ENTITIES);
        $transTbl = array_flip($transTbl);

        return utf8_encode(strtr($string, $transTbl));
    }

    /**
     * Converts all object properties to utf-8.
     *
     * @param  mixed $objects Object or list of objects to convert.
     * @return mixed          Object or list of objects with properties in utf-8.
     */
    public static function convertToUtf8($objects)
    {
        if (!is_array($objects)) {
            self::convertObjectToUtf8($objects);
            return $objects;
        }

        foreach ($objects as &$object) {
            self::convertObjectToUtf8($object);
        }

        return $objects;
    }

    /**
     * Converts all object properties to utf-8.
     *
     * @param Object $objects Objects to convert
     */
    public static function convertObjectToUtf8(&$object)
    {
        foreach (get_object_vars($object) as $key => $value) {
            if (is_string($value)) {
                $object->{$key} = mb_convert_encoding(
                    $value,
                    'UTF-8',
                    mb_detect_encoding($value)
                );
            }
        }
    }
}
