<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Core\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

/**
 * The Command class defines a generic actions to make command reporting easy.
 */
abstract class Command extends ContainerAwareCommand
{
    /**
     * The timestamp when the command was ended.
     *
     * @var integer
     */
    protected $ended;

    /**
     * The timestamp when the command was started.
     *
     * @var integer
     */
    protected $started;

    /**
     * Updates the ended time with the current timestamp.
     */
    protected function end()
    {
        $this->ended = time();
    }

    /**
     * Returns the duration of the current command.
     *
     * @return string The duration of the current command.
     */
    protected function getDuration()
    {
        return date('H:i:s', $this->ended - $this->started);
    }

    /**
     * Updates the started time with the current timestamp.
     */
    protected function start()
    {
        $this->started = time();
    }
}
