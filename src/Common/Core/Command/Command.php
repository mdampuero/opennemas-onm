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
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * The Command class defines a generic actions to make command reporting easy.
 */
abstract class Command extends ContainerAwareCommand
{
    /**
     * The timestamp when the command was ended.
     *
     * @var int
     */
    protected $ended;

    /**
     * The input component.
     *
     * @var InputInterface
     */
    protected $input;

    /**
     * The output component.
     *
     * @var OutputInterface
     */
    protected $output;

    /**
     * The number of minimum characters for a padded message.
     *
     * @var int
     */
    protected $padding = 60;

    /**
     * The timestamp when the command was started.
     *
     * @var int
     */
    protected $started;

    /**
     * The number of the current step for every level.
     *
     * @var array
     */
    protected $step = [ 1, 1, 1 ];

    /**
     * The total number of steps of the command.
     *
     * @var int
     */
    protected $steps;

    /**
     * The list of message types and colors to use in output.
     *
     * @var array
     */
    protected $types = [
        'error'   => 'red',
        'info'    => 'blue',
        'success' => 'green',
        'warning' => 'yellow'
    ];

    /**
     * Updates the ended time with the current timestamp.
     */
    protected function end()
    {
        $this->ended = time();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input  = $input;
        $this->output = $output;

        $this->getContainer()->get('core.security')->setCliUser();

        $this->start();
        $this->writeStep('Starting command');
        $this->writeStatus('success', 'DONE ');
        $this->writeStatus('info', date('(Y-m-d H:i:s)', $this->started), true);

        $this->do();

        $this->end();
        $this->writeStep('Ending command');
        $this->writeStatus('success', 'DONE');
        $this->writeStatus('info', date(' (Y-m-d H:i:s)', $this->ended));
        $this->writeStatus('warning', " ({$this->getDuration()})", true);
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

    /**
     * Writes an custom message with extra charaters until the configured
     * padding is reached.
     *
     * @param string $message   The message to write.
     * @param bool   $lineBreak Whether to write a line break after the message.
     */
    protected function writePad($message, $lineBreak = false)
    {
        $message = str_pad($message, $this->padding, '.');

        $lineBreak
            ? $this->output->writeln($message)
            : $this->output->write($message);
    }

    /**
     * Writes an status message to the current output based on the parameters.
     *
     * @param string $type      The message type.
     * @param string $message   The message to write.
     * @param bool   $lineBreak Whether to write a line break after the message.
     */
    protected function writeStatus($type, $message, $lineBreak = false)
    {
        $message = "<fg={$this->types[$type]};options=bold>$message</>";

        $lineBreak
            ? $this->output->writeln($message)
            : $this->output->write($message);
    }

    /**
     * Writes an step message to the current output based on the parameters.
     *
     * @param string $message   The message to write.
     * @param bool   $lineBreak Whether to write a line break after the message.
     * @param int    $level     The level of the step message.
     */
    protected function writeStep($message, $lineBreak = false, $level = 1)
    {
        $prefix  = str_repeat('==', $level - 1) . ($level > 1 ? '> ' : '');
        $message = '<fg=yellow;options=bold>' . $prefix . '</>'
            . ($level <= 1 ? '<options=bold>' : '')
            . str_pad(sprintf(
                '(%s/%s) %s',
                $this->step[$level - 1]++,
                $this->steps[$level - 1],
                $message
            ), $this->padding - strlen($prefix), '.')
            . ($level <= 1 ? '</>' : '');

        $lineBreak
            ? $this->output->writeln($message)
            : $this->output->write($message);
    }
}
