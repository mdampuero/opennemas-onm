<?php
/*
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Onm;
/**
 * Library for handling unusual string operations.
 *
 * @package    Onm
 * @subpackage Utils
 * @author     Fran Dieguez <fran@openhost.es>
 **/
class StringUtils
{
    /**
     * Delete disallowed chars from a sentence and transform it to a url friendly name
     *
     * @param  string  $name, the string to clen
     * @return string, the string cleaned
     **/
    public static function normalize_name($name)
    {
        $name = self::normalize($name);
        $name = preg_replace('/[\- ]+/', '-', $name);

        return $name;
    }

    /**
     * Delete disallowed chars from a sentence and transform it to a url friendly name
     *
     * @param  string  $name, the string to clen
     * @return string, the string cleaned
     **/
    public static function normalize($name)
    {
        $name = mb_strtolower($name);
        $trade = array(
            'á'=>'a', 'à'=>'a', 'ã'=>'a', 'ä'=>'a', 'â'=>'a', 'Á'=>'A',
            'À'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Â'=>'A', 'é'=>'e', 'è'=>'e',
            'ë'=>'e', 'ê'=>'e', 'É'=>'E', 'È'=>'E', 'Ë'=>'E', 'Ê'=>'E',
            'í'=>'i', 'ì'=>'i', 'ï'=>'i', 'î'=>'i', 'Í'=>'I', 'Ì'=>'I',
            'Ï'=>'I', 'Î'=>'I', 'ó'=>'o', 'ò'=>'o', 'õ'=>'o', 'ö'=>'o',
            'ô'=>'o', 'Ó'=>'O', 'Ò'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ô'=>'O',
            'ú'=>'u', 'ù'=>'u', 'ü'=>'u', 'û'=>'u', 'Ú'=>'U', 'Ù'=>'U',
            'Ü'=>'U', 'Û'=>'U', '$'=>'', '@'=>'', '!'=>'', '#'=>'_',
            '%'=>'', '^'=>'', '&'=>'', '*'=>'', '('=>'-', ')'=>'-',
            '-'=>'-', '+'=>'', '='=>'', '\\'=>'-', '|'=>'-','`'=>'',
            '~'=>'', '/'=>'-', '\"'=>'-','\''=>'', '<'=>'', '>'=>'',
            '?'=>'-', ','=>'-', 'ç'=>'c', 'Ç'=>'C', '·'=>'', '.'=>'',
            ';'=>'-', '['=>'-', ']'=>'-','ñ'=>'n','Ñ'=>'n'
        );
        $name = strtr($name, $trade);
        $name = rtrim($name);

        return $name;
    }

    /**
     * Clean the special chars into a given string
     *
     * Performs a html_entity_encode, mb_strtolower and mb_ereg_replace
     * disallowed chars
     *
     * @access static
     * @param  string  $str, the string to clen
     * @return string, the string cleaned
     **/
    public static function clearSpecialChars($str)
    {
        $str = html_entity_decode($str, ENT_COMPAT, 'UTF-8');
        $str = mb_strtolower($str, 'UTF-8');
        $str = mb_ereg_replace('[^a-z0-9áéíóúñüç_\,\- ]', ' ', $str);

        return $str;
    }

    /**
     * Deletes disallowed chars from a sentence and transform it to a url friendly name
     *
     * @access static
     * @param  string  $name, the string to clen
     * @return string, the string cleaned
     **/
    public static function setSeparator($str, $separator='-')
    {
        $str = trim($str);
        $str = preg_replace('/[ ]+/', $separator, $str);

        return $str;
    }

    /**
     * Generates a valid permalink
     *
     * @param  string  $title
     * @param  boolean $useStopList
     * @return string
     **/
    public static function get_title($title, $useStopList=true)
    {
        $title = self::clearSpecialChars($title);
        $title = self::normalize_name($title);
        $title = mb_ereg_replace('[^a-z0-9\- ]', '', $title);

        if ($useStopList) {
            // Remove stop list
            $titule = self::remove_shorts($title);
        }

        if (empty($titule) || $titule == " ") {
            $titule=$title;
        }

        $titule = self::setSeparator($titule, '-');
        $titule = preg_replace('/[\-]+/', '-', $titule);

        return $titule;
    }

    /**
     * Prevent duplicate metadata
     *
     * @param string $metadata
     * @param string $separator By default ','
     *
     * @return string
     **/
    public static function normalize_metadata($metadata, $separator=',')
    {
        $items = explode(',', $metadata);

        foreach ($items as $k => $item) {
            $items[$k] = trim($item);
        }

        $items = array_flip($items);
        $items = array_keys($items);

        $metadata = implode(',', $items);

        return $metadata;
    }


    /**
     * Generate a string of key words separated by semicolon
     *
     * @access static
     * @param  string $title
     * @return string
     **/
    public static function get_tags($title)
    {
        $tags = self::clearSpecialChars($title);

        // Remove stop list
        $tags = self::remove_shorts($tags);
        $tags = self::setSeparator($tags, ',');
        $tags = preg_replace('|-|', ',', $tags);
        $tags = preg_replace('/[\,]+/', ',', $tags);

        // Remove duplicates
        $tags = array_unique(explode(',', $tags));
        $tags = implode(', ', $tags);

        return $tags;
    }

    /**
     * Modified from Meneame:
     * @link http://svn.meneame.net/index.cgi/branches/version3/www/libs/uri.php
     **/
    public static function remove_shorts($string)
    {
        $shorts = <<<EOF
[0-9]+
[a-zA-Z]
a
as
ahi
al
ante
ante
aquel
aquelo
aquela
aquello
aquella
aquellas
aquellos
aunque
bajo
bien
cabe
cinco
como
con
conmigo
contra
cuatro
de
del
desde
dos
durante
e
el
eles
elas
en
entre
es
esa
esas
ese
eso
esos
esta
estas
este
esto
estos
excepto
hacia
hasta
hay
la
las
le
les
lo
los
me
mediante
mi
nosotras
nosotros
nove
nueve
o
os
ocho
oito
otro
outro
ou
para
pero
por
que
salvo
se
segun
seis
sete
si
siete
sin
sen
sino
sobre
su
sus
te
tras
tres
tu
un
una
unha
unhas
unas
uno
unos
ya
yo
si
EOF;
        $shorts = preg_split('@\n@', $shorts);

        $size = count($shorts);

        for ($i=0; $i<$size; $i++) {
            $short  = preg_replace('/\n/', '', $shorts[$i]);
            $string = preg_replace('/^'.$short.'[\.\, ]/', ' ', $string);
            $string = preg_replace('/[\.\, ]'.$short.'[\.\, ]/', ' ', $string);
            $string = preg_replace("/[\.\, ]$short$/", ' ', $string);
        }

        return $string;
    }

    public static function str_stop($string, $maxLength=30, $suffix='...')
    {
        if (strlen($string) > $maxLength) {
            $string = substr($string, 0, $maxLength);
            $pos = strrpos($string, " ");
            if ($pos === false) {
                return substr($string, 0, $maxLength).$suffix;
            }

            return substr($string, 0, $pos).$suffix;
        } else {
            return $string;
        }
    }

    public static function unhtmlentities($string)
    {
        // replace numeric entities
        $string   = preg_replace('~&#x([0-9a-f]+);~ei', 'chr(hexdec("\\1"))', $string);
        $string   = preg_replace('~&#([0-9]+);~e', 'chr("\\1")', $string);
        // replace literal entities
        $transTbl = get_html_translation_table(HTML_ENTITIES);
        $transTbl = array_flip($transTbl);

        return utf8_encode(strtr($string, $transTbl));
    }


    /**
     * Disable magic quotes if it is enabled
     *
     * @param array $data
     **/
    public static function disabled_magic_quotes(&$data=NULL )
    {
        // @codeCoverageIgnoreStart
        if (get_magic_quotes_gpc()) {
            function stripslashes_deep($value)
            {
                $value = is_array($value) ?
                            array_map('stripslashes_deep', $value) :
                            stripslashes($value);

                return $value;
            }

            if (!is_null($data)) {
                $data = array_map('stripslashes_deep', $data);
            } else {
                $_POST = array_map('stripslashes_deep', $_POST);
                $_GET = array_map('stripslashes_deep', $_GET);
                $_COOKIE = array_map('stripslashes_deep', $_COOKIE);
                $_REQUEST = array_map('stripslashes_deep', $_REQUEST);
            }
        }
        // @codeCoverageIgnoreEnd
    }

    public static function clearBadChars($string)
    {
        $string = preg_replace('/'.chr(226).chr(128).chr(169).'/', '', $string);

        return $string;
    }

    /**
     * Gets "n" first words from a given text
     *
     * @param  string  $text
     * @param  integer $numWords
     * @return string
     * @example self::get_numWords('hello world', 1)
     **/
    public static function get_num_words($text, $numWords)
    {
        $noHtml      = strip_tags($text);
        $description = explode(" ", $noHtml, $numWords + 1);
        $sobra       = array_pop($description);
        $words       = implode(" ", $description).'...';

        return $words;
    }

    public static function loadBadWords()
    {
        // $entries = file(dirname(__FILE__).'/self_badwords.txt');
        $entries = <<<EOF
5, m[i]?erda
5, marica
5, carallo
10, [h]?ostia
20, puta[s]?
30, cabr[oó]n[a]?
50, fill[ao] d[ae] puta
50, hij[ao] de puta
EOF;

        $entries = preg_split('@\n@', $entries);

        $words = array();
        foreach ($entries as $entry) {
            if (preg_match('/^(\d+)\,(.*?)$/', $entry, $matches)) {
                $words[] = array(
                    'weight' => $matches[1],
                    'text'   => trim($matches[2])
                );
            }
        }

        return $words;
    }

    /**
     * filterBadWords
     **/
    public static function filterBadWords($text, $weight=0, $replaceStr=' ')
    {
        $words = self::loadBadWords();
        $text = ' ' . $text . ' ';
        if ($replaceStr != ' ') {
            $replaceStr = ' '.$replaceStr. ' ';
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
     * getWeightBadWords
     **/
    public static function getWeightBadWords($text)
    {
        $words = self::loadBadWords();
        $text = ' ' . $text . ' ';

        $weight = 0;

        foreach ($words as $word) {
            if (preg_match_all('/' . $word['text'] . '/si', $text, $matches)) {
                $weight += ($word['weight'] * count($matches[0]));
            }
        }

        return $weight;
    }

    /*
     * implodes a two dimension array to a http params string
     * @param $array
     **/
    public static function toHttpParams(Array $httpParams)
    {
        // The final result
        $result = array();

        // Iterate over each key-value parameter
        foreach ($httpParams as $param) {

            // Implode each key => value parameter into key-value
            foreach ($param as $key => $value) {
                $result []= $key.'='.$value;
            }

        }

        // And implode all key=value parameters with &
        $result = implode('&', $result);

        return $result;

    }

    public static function ext_str_ireplace($findme, $replacewith, $subject)
    {
        // Replaces $findme in $subject with $replacewith
        // Ignores the case and do keep the original capitalization by using $1 in $replacewith
        // Required: PHP 5

        $rest = $subject;
        $result = '';

        while (stripos($rest, $findme) !== false) {
             $pos = stripos($rest, $findme);

             // Remove the wanted string from $rest and append it to $result
             $result .= substr($rest, 0, $pos);
             $rest = substr($rest, $pos, strlen($rest)-$pos);

             // Remove the wanted string from $rest and place it correctly into $result
             $result .= str_replace('$1', substr($rest, 0, strlen($findme)), $replacewith);
             $rest = substr($rest, strlen($findme), strlen($rest)-strlen($findme));
        }

        // After the last match, append the rest
        $result .= $rest;

        return $result;
    }

    public static function generatePassword($length = 8)
    {
        $chars = "234567890abcdefghijkmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $i = 0;
        $password = "";

        while ($i <= $length-1) {
            $password .= $chars{mt_rand(0, strlen($chars)-1)};
            $i++;
        }

        return $password;
    }

}
