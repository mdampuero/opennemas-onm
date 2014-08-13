<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Onm\Varnish;

/**
 * Class that allows to store Varnish ban/purge commands
 */
class MessageExchanger
{
    public $messages = array();

    /**
     * Adds a new BAN message to the queue.
     *
     * @param string $ban The message to add.
     *
     * @return MessageExchanger The object instance.
     */
    public function addBanMessage($ban)
    {
        $this->messages []= $ban;

        return $this;
    }

    /**
     * Returns the Varnish BAN messages.
     *
     * @return array the list of BAN messages.
     */
    public function getMessages()
    {
        return $this->messages;
    }
}
