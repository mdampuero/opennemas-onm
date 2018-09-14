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

class CheckStatusCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('core:status:check')
            ->setDescription('Checks the core application status')
            ->setHelp(
                <<<EOF
The <info>core:status:check</info> checks if the application can be executed with current configuration values.

<info>php app/console core:status:check</info>

EOF
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $checker = $this->getContainer()->get('core.status.checker');

        $output->write('+ Checking NFS status... ');
        $status = $checker->checkNfs() ? '<info>DONE</info>' : '<error>FAIL</error>';
        $output->writeln($status);

        $output->write('+ Checking database connection... ');
        $status = $checker->checkDatabaseConnection() ? '<info>DONE</info>' : '<error>FAIL</error>';
        $output->writeln($status);

        if ($output->isVerbose()) {
            $this->dumpConfiguration($checker->getDatabaseConfiguration(), $output);
        }

        $output->write('+ Checking cache connection... ');
        $status = $checker->checkCacheConnection() ? '<info>DONE</info>' : '<error>FAIL</error>';
        $output->writeln($status);

        if ($output->isVerbose()) {
            $this->dumpConfiguration($checker->getCacheConfiguration(), $output);
        }
    }

    /**
     * Dumps configuration to output.
     *
     * @param array           $config The configuration.
     * @param OutputInterface $output The output interface.
     */
    protected function dumpConfiguration($config, $output)
    {
        $output->writeln("  + Configuration:");

        foreach ($config as $key => $value) {
            $output->writeln('      <fg=magenta>' . str_pad("$key:", 11, ' ') . '</>' . $value);
        }
    }
}
