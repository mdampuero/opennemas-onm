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

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class FrameworkCheckCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('framework:check')
            ->setDescription('Checks the application confirmation')
            ->setHelp(
                <<<EOF
The <info>framework:check</info> checks if the application can be executed with current configuration values.

<info>php app/console framework:check</info>

EOF
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("Framework status\n================\n");

        $frameworkStatus = $this->getContainer()->get('onm.framework_status');

        // Get services configurations
        $dbConfig           = $this->getContainer()->getParameter('database');
        $cacheHandler       = $this->getContainer()->getParameter('cache_handler');
        $cacheHandlerConfig = $this->getContainer()->getParameter('cache_handler_params');

        // Get services status
        $nfsCheck   = $frameworkStatus->checkNfs();
        $dbCheck    = $frameworkStatus->checkDatabaseConnection();
        $cacheCheck = $frameworkStatus->checkCacheConnection();

        $output->writeln("NFS   status: ". ($nfsCheck ? "OK": "FAILED"));
        $output->writeln("DB    status: ". ($dbCheck ? "OK": "FAILED"));
        $output->writeln("Cache status: ". ($cacheCheck ? "OK": "FAILED"));

        $output->writeln("\nCurrent configuration\n================\n");
        $output->writeln("Database:");
        $output->writeln(var_export($dbConfig['dbal'], true));

        $output->writeln("Cache: ".$cacheHandler);
        $output->writeln(var_export($cacheHandlerConfig, true));

        return true;
    }
}
