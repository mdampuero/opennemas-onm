<?php
/*
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
/**
 * Library for handling unusual string operations.
 *
 * @package    Onm
 * @subpackage Utils
 * @author     Fran Dieguez <fran@openhost.es>
 **/
class String_Utils
{

    var $stringTest = NULL;

    /**
      * Constructor for String_Utils class
      *
      * @access public
      * @param string $string
     **/
    public function __construct($string = null)
    {
        //echo $stringTest." si<br>";
        if(!is_null($string)) {
            $this->stringTest = $string;
        } else {
            $this->stringTest = "";
        }
    }

    /**
     * Delete disallowed chars from a sentence and transform it to a url friendly name
     *
     * @access static
     * @param string $name, the string to clen
     * @return string, the string cleaned
     **/
    static public function normalize_name($name)
    {
        $name = mb_strtolower($name);
        $trade = array( 'á'=>'a', 'à'=>'a', 'ã'=>'a', 'ä'=>'a', 'â'=>'a', 'Á'=>'A', 'À'=>'A', 'Ã'=>'A',
                        'Ä'=>'A', 'Â'=>'A', 'é'=>'e', 'è'=>'e', 'ë'=>'e', 'ê'=>'e', 'É'=>'E', 'È'=>'E',
                        'Ë'=>'E', 'Ê'=>'E', 'í'=>'i', 'ì'=>'i', 'ï'=>'i', 'î'=>'i', 'Í'=>'I', 'Ì'=>'I',
                        'Ï'=>'I', 'Î'=>'I', 'ó'=>'o', 'ò'=>'o', 'õ'=>'o', 'ö'=>'o', 'ô'=>'o', 'Ó'=>'O',
                        'Ò'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ô'=>'O', 'ú'=>'u', 'ù'=>'u', 'ü'=>'u', 'û'=>'u',
                        'Ú'=>'U', 'Ù'=>'U', 'Ü'=>'U', 'Û'=>'U', '$'=>'', '@'=>'', '!'=>'', '#'=>'_',
                        '%'=>'', '^'=>'', '&'=>'', '*'=>'', '('=>'-', ')'=>'-', '-'=>'-', '+'=>'',
                        '='=>'', '\\'=>'-', '|'=>'-','`'=>'', '~'=>'', '/'=>'-', '\"'=>'-','\''=>'',
                        '<'=>'', '>'=>'', '?'=>'-', ','=>'-', 'ç'=>'c', 'Ç'=>'C', '·'=>'',
                        '.'=>'', ';'=>'-', '['=>'-', ']'=>'-','ñ'=>'n','Ñ'=>'n');
        $name = strtr($name, $trade);
        $name = rtrim($name);
        $name = preg_replace('/[\- ]+/', '-', $name);
        return $name;
    }

    /**
     * Sets the variable string
     *
     * @access public
     * @param string $name
     **/
    public function setString($string)
    {
        $this->stringTest=$string;
    }

    /**
     * Gets the variable string
     *
     * @access public
     * @param string $name
     **/
    public function getString()
    {
        return $this->stringTest;
    }

    /**
     * Clean the special chars into a given string
     *
     * Performs a html_entity_encode, mb_strtolower and mb_ereg_replace
     * disallowed chars
     *
     * @access static
     * @param string $str, the string to clen
     * @return string, the string cleaned
     **/
    static public function clearSpecialChars($str)
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
     * @param string $name, the string to clen
     * @return string, the string cleaned
     **/
    static public function setSeparator($str, $separator='-')
    {
        $str = trim($str);
        $str = preg_replace('/[ ]+/', $separator, $str);

        return $str;
    }

    /**
     * Generates a valid permalink
     *
     * @param string $title
     * @param boolean $useStopList
     * @return string
     **/
    static public function get_title($title, $useStopList=true)
    {
        $title = String_Utils::clearSpecialChars($title);
        $title = String_Utils::normalize_name($title);
        $title = mb_ereg_replace('[^a-z0-9\- ]', '', $title);

        if($useStopList) {
            // Remove stop list
            $titule = String_Utils::remove_shorts($title);
        }

        if(empty($titule) || $titule ==" "){ //Si se queda vacio, no quitar shorts.
            $titule=$title;
         }

        $titule = String_Utils::setSeparator($titule, '-');
        $titule = preg_replace('/[\-]+/', '-', $titule);

        return $titule;
    }

    /**
     * Prevent duplicate metadata
     *
     * @access static
     * @param string $metadata
     * @param string $separator By default ','
     * @return string
     **/
    static public function normalize_metadata($metadata, $separator=',')
    {
        $items = explode(',', $metadata);

        foreach($items as $k => $item) {
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
     * @param string $title
     * @return string
     **/
    static public function get_tags($title)
    {
        $tags = String_Utils::clearSpecialChars($title);

        // Remove stop list
        $tags = String_Utils::remove_shorts($tags);
        $tags = String_Utils::setSeparator($tags, ',');
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
    static public function remove_shorts($string)
    {
        $shorts = file( dirname(__FILE__).'/string_utils_stoplist.txt');

        $size = count($shorts);

        for($i=0; $i<$size; $i++) {
            $short  = preg_replace('/\n/', '', $shorts[$i]);
            $string = preg_replace('/^'.$short.'[\.\, ]/', ' ', $string);
            $string = preg_replace('/[\.\, ]'.$short.'[\.\, ]/', ' ', $string);
            $string = preg_replace("/[\.\, ]$short$/", ' ', $string);
        }

        return $string;
    }

    static public function str_stop($string, $max_length=30, $suffix='...')
    {
        if (strlen($string) > $max_length) {
            $string = substr($string, 0, $max_length);
            $pos = strrpos($string, " ");
            if ($pos === false) {
                return substr($string, 0, $max_length).$suffix;
            }
            return substr($string, 0, $pos).$suffix;
        } else {
            return $string;
        }
    }

    static public function unhtmlentities($string)
    {
        // replace numeric entities
        $string = preg_replace('~&#x([0-9a-f]+);~ei', 'chr(hexdec("\\1"))', $string);
        $string = preg_replace('~&#([0-9]+);~e', 'chr("\\1")', $string);
        // replace literal entities
        $trans_tbl = get_html_translation_table(HTML_ENTITIES);
        $trans_tbl = array_flip($trans_tbl);
        return utf8_encode(strtr($string, $trans_tbl));
    }

    /**
     * Disable magic quotes if it is enabled
     *
     * @param array $data
     **/
    static public function disabled_magic_quotes( &$data=NULL )
    {
        if( get_magic_quotes_gpc() ) {
            function stripslashes_deep($value) {
                $value = is_array($value) ?
                            array_map('stripslashes_deep', $value) :
                            stripslashes($value);
                return $value;
            }

            if( !is_null($data) ) {
                $data = array_map('stripslashes_deep', $data);
            } else {
                $_POST = array_map('stripslashes_deep', $_POST);
                $_GET = array_map('stripslashes_deep', $_GET);
                $_COOKIE = array_map('stripslashes_deep', $_COOKIE);
                $_REQUEST = array_map('stripslashes_deep', $_REQUEST);
            }
        }
    }


    static public function clearBadChars($string)
    {
        $string = preg_replace('/'.chr(226).chr(128).chr(169).'/', '', $string);
        return $string;
    }

    /**
     * Gets "n" first words from a given text
     *
     * @access static
     * @param string $text
     * @param integer $num_words
     * @return string
     * @example String_Utils::get_num_words('hello world', 1)
     **/
    static public function get_num_words($text,$num_words)
    {
        $no_html = strip_tags($text ); //Quita etiquetas html.
        $description = explode(" ",$no_html,$num_words);
        $sobra = array_pop($description);
        $words = implode(" ",$description).'...';

    	return $words;
    }

    static public function loadBadWords()
    {
        $entries = file(dirname(__FILE__).'/string_utils_badwords.txt');
        $words = array();
        foreach($entries as $entry) {
            if(preg_match('/^(\d+)\,(.*?)$/', $entry, $matches)) {

                $words[] = array('weight' => $matches[1],
                                 'text'   => trim($matches[2])
                                );
            }
        }

        return $words;
    }

    /**
     * filterBadWords
     **/
    static public function filterBadWords($text, $weight=0, $replaceStr=' ')
    {
        $words = String_Utils::loadBadWords();
        $text = ' ' . $text . ' ';

        foreach($words as $word) {
            if($word['weight'] > $weight) {
                $text = preg_replace('/\W' . $word['text'] . '\W/si', $replaceStr, $text);
            }
        }

        $text = trim($text);

        return $text;
    }

    /**
     * getWeightBadWords
     **/
    static public function getWeightBadWords($text)
    {
        $words = String_Utils::loadBadWords();
        $text = ' ' . $text . ' ';

        $weight = 0;

        foreach($words as $word) {
            if(preg_match_all('/' . $word['text'] . '/si', $text, $matches)) {
                $weight += ($word['weight'] * count($matches[0]));
            }
        }

        return $weight;
    }

    /*
     * implodes a two dimension array to a http params string
     * @param $array
     **/
    static public function toHttpParams(Array $httpParams)
    {

        // The final result
        $result = array();
        if(is_array($httpParams)) {

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

        } else {
            throw new ArgumentError();
        }
    }

    static public function ext_str_ireplace($findme, $replacewith, $subject)
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
    
    static public function pass_gen($length = 8) {
        $chars = "234567890abcdefghijkmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $i = 0;
        $password = "";
        
        while ($i <= $length) {
            $password .= $chars{mt_rand(0,strlen($chars))};
            $i++;
        }
        
        return $password;
    }

}
