<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Onm;
/**
 * Class for handling Messages for showing to the user.
 *
 * @package    Onm
 */
class Message
{

    /**
     * Priority level for errors
     */
    const ERROR   = 'error';
    /**
     * Priority level for notice
     */
    const NOTICE  = 'notice';
    /**
     * Priority level for success
    */
    const SUCCESS = 'success';

    /*
     * Initializes the message save handler
     *
     */
    public static function initMessageHandler()
    {
        if (!isset($_SESSION)) {
            session_start();
        }
        if (!array_key_exists('messages', $_SESSION)) {
                $_SESSION['messages'] = array(
                self::NOTICE => array(),
                self::ERROR => array(),
                self::SUCCESS => array(),
            );
        }
    }

    /**
     * Adds a message to the message handler
     *
     * Example:
     *   m::add(array('one error','another error'), m::ERROR);
     *     => Adds two messages to the error message handler
     *
     *   m::add('one succedded message', m::SUCCESS);
     *     => Adds one messages to the success message handler
     *
     *   m::add('one notice error'));
     *     => If you omit the priority it will be added to the notice message handler
     *
     *  For render all the messages in the template you must include
     *  {render_messages} smarty function.
     */
    public static function add($message = null, $priority = self::NOTICE)
    {
        self::initMessageHandler();
        if (is_array($message)) {
            foreach ($message as $msg) {
                array_push($_SESSION['messages'][$priority], $msg);
            }
        } else {
            array_push($_SESSION['messages'][$priority], $message);
        }

    }

    /*
     * Gets all the messages form the message handler
     *
     * @param $priority the priority for gettings the settings
     */
    public static function getAll($priority = null)
    {

        self::initMessageHandler();

        if (!isset($_SESSION)
            || !is_array($_SESSION)
            || !array_key_exists('messages', $_SESSION)
        ) {

        }
        if (is_null($priority)) {
            $output =  $_SESSION['messages'];
        } else {
                $output =  $_SESSION['messages'][$priority];
        }

        return $output;
    }

    /*
     * Returns html for all the messages from the message handler
     *
     * @param $priority
     */
    public static function getHTMLforAll($priority = null)
    {
        $notices = self::getAll(self::NOTICE);
        $errors  = self::getAll(self::ERROR);
        $sucess  = self::getAll(self::SUCCESS);

        $noticeHTML = '';

        if (count($notices) > 0) {
            $messages = '<ul>';
            foreach ($notices as $msg) {
                $messages .= "<li>{$msg}</li>";
            }
            $messages .= '</ul>';
            $noticeHTML = sprintf("<div class=\"notice\">%s</div>", $messages);
        }

        if (count($errors) > 0) {
            $messages = '<ul>';
            foreach ($errors as $msg) {
                $messages .= "<li>{$msg}</li>";
            }
            $messages .= '</ul>';
            $noticeHTML .= sprintf("<div class=\"error\">%s</div>", $messages);
        }

        if (count($sucess) > 0) {
            $messages = '<ul>';
            foreach ($sucess as $msg) {
                $messages .= "<li>{$msg}</li>";
            }
            $messages .= '</ul>';
            $noticeHTML .= sprintf("<div class=\"success\">%s</div>", $messages);
        }

        self::clean();

        return $noticeHTML;
    }

    /**
     * Cleans messages save handler
     */
    public static function clean()
    {
        if (isset($_SESSION)) {
            unset($_SESSION['messages']);
        }
    }
}
