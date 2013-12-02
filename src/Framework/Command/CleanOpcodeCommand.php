<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Framework\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CleanOpcodeCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('clean:opcode')
            ->setDescription('Resets the Zend OpCache registers')
            ->setHelp(
                <<<EOF
The <info>clean:opcode</info> cache from PHP VM.

<info>php app/console clean:opcode</info>

EOF
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->write(" - Cleaning Zend OpCode cache ");

        // Only run the opcode cache reset if is supported
        if (extension_loaded('Zend Opcache')) {
            opcache_reset();
            $output->writeln(" [DONE]");

        } else {
            $output->writeln(" [Not supported]");
        }
        return false;
    }
}
