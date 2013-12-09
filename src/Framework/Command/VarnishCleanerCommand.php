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

class VarnishCleanerCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('clean:varnish')
            ->setDefinition(
                array(
                    new InputArgument('request', InputArgument::REQUIRED, ''),
                )
            )
            ->setDescription('Sends prune commands to Varnish servers')
            ->setHelp(
                <<<EOF
The <info>clean:varnish</info> sends BAN commands to varnish in
order to purge cache elements that matches a given criteria.

<info>php app/console clean:varnish</info>

EOF
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(" - Cleaning Varnish cache");

        global $sc;

        $request = $input->getArgument('request');

        $cleaner = $sc->get('varnish_cleaner');

        $response = $cleaner->ban($request);

        foreach ($response as $line) {
            $output->writeln($line);
        }

        return false;
    }
}
