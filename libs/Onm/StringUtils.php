<?php
/**
 * Defines the Onm\StringUtils class
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package    Onm_Utils
 */
namespace Onm;

/**
 * Library for handling unusual string operations.
 *
 * @package    Onm
 **/
class StringUtils
{
    /**
     * Delete disallowed chars from a sentence and transform it to a url friendly name
     *
     * @param  string  $name the string to clean
     *
     * @return string the string cleaned
     **/
    public static function normalizeName($name)
    {
        $name = self::normalize($name);
        $name = preg_replace('/[\- ]+/', '-', $name);

        return $name;
    }

    /**
     * Delete disallowed chars from a sentence and transform it to a url friendly name
     *
     * @param  string  $name the string to clean
     *
     * @return string the string cleaned
     **/
    public static function normalize($name)
    {
        $newname = mb_strtolower($name, 'UTF-8');
        $trade = array(
            'á'=>'a', 'à'=>'a', 'ã'=>'a', 'ä'=>'a', 'â'=>'a', 'Á'=>'A',
            'À'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Â'=>'A', 'é'=>'e', 'è'=>'e',
            'ë'=>'e', 'ê'=>'e', 'É'=>'E', 'È'=>'E', 'Ë'=>'E', 'Ê'=>'E',
            'í'=>'i', 'ì'=>'i', 'ï'=>'i', 'î'=>'i', 'Í'=>'I', 'Ì'=>'I',
            'Ï'=>'I', 'Î'=>'I', 'ó'=>'o', 'ò'=>'o', 'õ'=>'o', 'ö'=>'o',
            'ô'=>'o', 'Ó'=>'O', 'Ò'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ô'=>'O',
            'ú'=>'u', 'ù'=>'u', 'ü'=>'u', 'û'=>'u', 'Ú'=>'U', 'Ù'=>'U',
            'Ü'=>'U', 'Û'=>'U', '$'=>'',  '@'=>'',  '!'=>'',  '#'=>'',
            '%'=>'',  '^'=>'',  '&'=>'',  '*'=>'',  '('=>'',  ')'=>'',
            '-'=>'-', '+'=>'',  '='=>'',  '\\'=>'-', '|'=>'-', '`'=>'',
            '~'=>'',  '/'=>'-', '\"'=>'', '\''=>'', '<'=>'',  '>'=>'',
            '?'=>'-', ','=>'-', 'ç'=>'c', 'Ç'=>'C',  '·'=>'', 'ª'=>'',
            'º'=>'', ';'=>'-', '['=>'-', ']'=>'-', 'ñ'=>'nh', 'Ñ'=>'nh'
        );
        $newname = strtr($newname, $trade);
        $newname = rtrim($newname);

        return $newname;
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
     * @param string $str       the string to clean
     * @param string $separator the separator chapter to use
     *
     * @return string the string cleaned
     **/
    public static function setSeparator($str, $separator = '-')
    {
        $str = trim($str);
        $str = preg_replace('/[ ]+/', $separator, $str);

        return $str;
    }

    /**
     * Converts an string to ascii code
     *
     * @return string the ascii encoded string
     **/
    public static function toAscii($string)
    {
        $string = iconv('UTF-8', 'ASCII//TRANSLIT', $string);

        return $string;
    }

    /**
     * Generates a valid permalink
     *
     * @param  string  $title
     * @param  boolean $useStopList whether use the stopList array
     *
     * @return string
     **/
    public static function getTitle($title, $useStopList = true, $delimiter = '-')
    {
        $titule = '';
        $title = self::toAscii($title);

        // $title = self::normalizeName($title);
        $title = self::clearSpecialChars($title);
        $title = mb_ereg_replace('[^a-z0-9\- ]', '', $title);
        $title = trim($title);

        if ($useStopList) {
            // Remove stop list
            $titule = self::removeShorts($title);
        }

        $titule = trim($titule);
        if (empty($titule) || $titule == " ") {
            $titule = $title;
        }

        $titule = self::setSeparator($titule, $delimiter);

        # Drop some hyphen transliterations
        $titule = preg_replace("/[-‐‒–—―⁃−­]/", $delimiter, $titule);

        # convert double dash to single
        $titule = preg_replace('/[\-]+/', $delimiter, $titule);

        #strip off leading/trailing dashes
        $titule = trim($titule, $delimiter);

        return $titule;
    }

    /**
     * Prevent duplicate metadata
     *
     * @param string $metadata  the metadata to clean
     * @param string $separator Separator character to use for splitting the words.
     *                          By default ','
     *
     * @return string the cleaned metadata string
     **/
    public static function normalizeMetadata($metadata, $separator = ',')
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
     * Returns a string with keywords from a given text
     *
     * @param  string $text the text where extract keywords from
     *
     * @return string the string of keywords separated by commas
     **/
    public static function getTags($text)
    {
        $tags = self::clearSpecialChars($text);

        // Remove stop list
        $tags = self::removeShorts($tags);
        $tags = self::setSeparator($tags, ',');
        $tags = preg_replace('|-|', ',', $tags);
        $tags = preg_replace('/[\,]+/', ',', $tags);

        // Remove duplicates
        $tags = array_unique(explode(',', $tags));
        $tags = implode(', ', $tags);

        return $tags;
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
     **/
    public static function removeShorts($string)
    {
        $shorts = <<<EOF
a
as
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

    /**
     * Returns a trimmed string with a max number of elements and appends an elipsis
     * at the end
     *
     * @param string $string the string to trim
     * @param int $maxLength the max length of the final string
     * @param string $suffix the text to append at the end of the trimmed string
     *
     * @return string the trimmed string
     **/
    public static function str_stop($string, $maxLength = 30, $suffix = '...')
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

    /**
     * Reverse operation for htmlentities
     *
     * @param string $string the string to operate with
     *
     * @return string the string without HTML entities
     **/
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
     *
     * @return void
     **/
    public static function disabled_magic_quotes(&$data = null)
    {
        // @codeCoverageIgnoreStart
        if (get_magic_quotes_gpc()) {
            if (!is_null($data)) {
                $data = array_map('stripslashes_deep', $data);
            } else {
                $_POST = array_map('stripslashes_deep', $_POST);
                $_GET = array_map('stripslashes_deep', $_GET);
                $_COOKIE = array_map('stripslashes_deep', $_COOKIE);
                $_REQUEST = array_map('stripslashes_deep', $_REQUEST);
            }
        }
    }

    /**
     * Removes some spanish bad words and swears from the given string
     *
     * @param string $string the string to clean
     *
     * @return string the cleaned string
     **/
    public static function clearBadChars($string)
    {
        $string = preg_replace('/'.chr(226).chr(128).chr(169).'/', '', $string);

        return $string;
    }

    /**
     * Gets "n" first words from a given text
     *
     * @example self::get_numWords('hello world', 1)
     *
     * @param  string  $text the text to operate with
     * @param  integer $numWords
     *
     * @return string
     **/
    public static function get_num_words($text, $numWords)
    {
        $noHtml      = strip_tags($text);
        $description = explode(" ", $noHtml, $numWords + 1);
        array_pop($description);
        $words       = implode(" ", $description).'...';

        return $words;
    }

    /**
     * Retuns a list of bad words with their weigth
     *
     * @return array the list of words
     **/
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
     * Removes bad words from a text
     *
     * @param string $text       the text to clean
     * @param int    $weight     the minimum bad word weight to clean
     * @param string $replaceStr the replacement string for the bad words
     *
     * @return string the cleaned string
     **/
    public static function filterBadWords($text, $weight = 0, $replaceStr = ' ')
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
     * Returns the weight of a text from its bad words
     *
     * @param string $text the text to work with
     *
     * @return int the weight of the text
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

    /**
     * Implodes a two dimension array to a http params strins
     *
     * @param Array $httpParams an array of http parameters
     *
     * @return string the url compatible list of params
     **/
    public static function toHttpParams(Array $httpParams)
    {
        $result = array();

        // Implode each key => value parameter into key-value
        foreach ($httpParams as $param) {
            foreach ($param as $key => $value) {
                $result []= $key.'='.$value;
            }
        }

        // And implode all key=value parameters with &
        $result = implode('&', $result);

        return $result;
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
     **/
    public static function extStrIreplace($findme, $replacewith, $subject)
    {
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

    /**
     * Returns a random password with an specific length
     *
     * @param int $length the length of the password
     *
     * @return string the password string
     **/
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


     /**
     * Clean the special chars into a file name
     *
     * @param  string  $name the string to clean
     *
     * @return string the string cleaned
     **/
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
     * Clean the special chars and add - for separate words
     *
     * @param  string  $text the string to transform
     *
     * @return string the string cleaned
     **/
    public static function generateSlug($text)
    {

        $text = trim($text);

        $text = html_entity_decode($text, ENT_COMPAT, 'UTF-8');

        $text = self::normalize($text);
        $text = self::removeShorts($text);

        $text = preg_replace('/[ ]+/', '-', $text);
        $text = preg_replace('/[\-]+/', '-', $text);
        $text = mb_ereg_replace('[^a-z0-9\-]', '', $text);

        return $text;
    }

    /**
     * Clear the special quotes
     *
     * @param  string  $text the string to transform
     *
     * @return string the string cleaned
     **/
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
}
