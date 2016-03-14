<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <onm-devs@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\Messenger;

/**
 * The Messenger class manages messages and status codes for web service
 * actions.
 */
class Messenger
{
    /**
     * The list of status codes.
     *
     * @var array
     */
    protected $codes = [];

    /**
     * The list of messages
     *
     * @var array
     */
    protected $messages = [];

    /**
     * Adds a message and a status code to the lists.
     *
     * @param type variable Description
     *
     * @return type Description
     */
    public function add($str, $type = 'info', $code = 200)
    {
        $this->codes[]    = $code;
        $this->messages[] = [ 'type' => $type, 'message' => $str ];
    }

    /**
     * Returns the status code to use in the response.
     *
     * @return integer The status code.
     */
    public function getCode()
    {
        $code = $this->codes[0];

        // If different codes return 207 (multi-status)
        foreach ($this->codes as $c) {
            if ($code !== $c) {
                return 207;
            }
        }

        return $code;
    }

    /**
     * Returns the list of messages.
     *
     * @return array The list of messages
     */
    public function getMessages()
    {
        return $this->messages;
    }
}
