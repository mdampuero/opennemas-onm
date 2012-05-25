<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
// TODO: Move all the class functions to plain functions into functions.php
/**
 * Library for handling unusual string operations.
 *
 * @package    Onm
 * @subpackage Utils
 **/
class StringUtils
{
    /**
     * Delete disallowed chars from a sentence and transform it to a url friendly name
     *
     * @param  string  $name the string to clen
     * @return string the string cleaned
     **/
    public static function normalize_name($name)
    {
        return \Onm\StringUtils::normalize_name($name);
    }

    /**
     * Clean the special chars into a given string
     *
     * Performs a html_entity_encode, mb_strtolower and mb_ereg_replace
     * disallowed chars
     *
     * @param  string  $str the string to clen
     * @return string the string cleaned
     **/
    public static function clearSpecialChars($str)
    {

        return \Onm\StringUtils::clearSpecialChars($str);
    }

    /**
     * Deletes disallowed chars from a sentence and transform it to a url friendly name
     *
     * @param  string  $name the string to clen
     * @return string the string cleaned
     **/
    public static function setSeparator($str, $separator='-')
    {

        return \Onm\StringUtils::setSeparator($str, $separator);
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

        return \Onm\StringUtils::get_title($title, $useStopList);
    }

    /**
     * Prevent duplicate metadata
     *
     * @param  string $metadata
     * @param  string $separator By default ','
     * @return string
     **/
    public static function normalize_metadata($metadata, $separator=',')
    {

        return \Onm\StringUtils::normalize_metadata($metadata, $separator);
    }


    /**
     * Generate a string of key words separated by semicolon
     *
     * @param  string $title
     * @return string
     **/
    public static function get_tags($title)
    {

        return \Onm\StringUtils::get_tags($title);
    }

    /**
     * Modified from Meneame:
     * @link http://svn.meneame.net/index.cgi/branches/version3/www/libs/uri.php
     **/
    public static function remove_shorts($string)
    {

        return \Onm\StringUtils::remove_shorts($string);
    }

    public static function str_stop($string, $maxLength=30, $suffix='...')
    {

        return \Onm\StringUtils::str_stop($string, $maxLength, $suffix);
    }

    public static function unhtmlentities($string)
    {

        return \Onm\StringUtils::unhtmlentities($string);
    }

    /**
     * Disable magic quotes if it is enabled
     *
     * @param array $data
     **/
    public static function disabled_magic_quotes(&$data = null)
    {

        return \Onm\StringUtils::disabled_magic_quotes($data);
    }


    public static function clearBadChars($string)
    {

        return \Onm\StringUtils::filterBadWordsclearBadChars($string);
    }

    /**
     * Gets "n" first words from a given text
     *
     * @param  string  $text
     * @param  integer $num_words
     * @return string
     * @example StringUtils::get_num_words('hello world', 1)
     **/
    public static function get_num_words($text,$num_words)
    {

        return \Onm\StringUtils::get_num_words($text, $num_words);
    }

    public static function loadBadWords()
    {
        $entries = file(dirname(__FILE__).'/string_utils_badwords.txt');
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
    public static function filterBadWords($text, $weight = 0, $replaceStr = ' ')
    {

        return \Onm\StringUtils::filterBadWords($text, $weight, $replaceStr);
    }

    /**
     * getWeightBadWords
     **/
    public static function getWeightBadWords($text)
    {

        return \Onm\StringUtils::getWeightBadWords($text);
    }

    /*
     * implodes a two dimension array to a http params string
     * @param $array
     **/
    public static function toHttpParams(Array $httpParams)
    {

        return \Onm\StringUtils::toHttpParams($httpParams);
    }

    public static function ext_str_ireplace($findme, $replacewith, $subject)
    {

        return \Onm\StringUtils::ext_str_ireplace($findme,
            $replacewith, $subject);
    }

    public static function generatePassword($length = 8)
    {

        return \Onm\StringUtils::generatePassword($length);
    }
}
