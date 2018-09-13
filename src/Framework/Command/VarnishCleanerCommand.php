<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class VarnishCleanerCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('varnish:clean')
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

Examples:

Clean all from one instance:
app/console clean:varnish obj.http.x-instance == "internal_name"

Clean element with tag "sometag" from one instance:
app/console clean:varnish obj.http.x-instance == "internal_name" && obj.http.x-tags == "sometag"
EOF
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(" - Cleaning Varnish cache");

        $request = $input->getArgument('request');

        $cleaner = $this->getContainer()->get('varnish_cleaner');

        $response = $cleaner->ban($request);

        foreach ($response as $line) {
            $output->writeln($line);
        }

        return false;
    }
}
