<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\NewsAgency\Command;

use Common\Core\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\SplFileInfo;

/**
 * The SynchronizeCommand class defines a command to synchronize news agency
 * resources for all instances, a specific instance and/or a specific server.
 */
class SynchronizeCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('news-agency:synchronize')
            ->setDescription('Syncronizes news agency resources')
            ->addOption(
                'instances',
                'i',
                InputOption::VALUE_REQUIRED,
                'The list of instances to synchronize (e.g. norf, quux)'
            )->addOption(
                'servers',
                's',
                InputOption::VALUE_REQUIRED,
                'The list of news-agency servers to synchronize (e.g. 1, 2, 3)'
            );
    }

    /**
     * Returns the list of instances to synchronize.
     *
     * @param array $names The list of instance names
     *
     * @return array The list of instances.
     */
    protected function getInstances(?array $names = []) : array
    {
        $oql = sprintf(
            'activated = 1 and activated_modules ~ "%s"',
            'NEWS_AGENCY_IMPORTER'
        );

        if (!empty($names)) {
            $oql .= sprintf(
                ' and  internal_name in ["%s"]',
                implode('","', $names)
            );
        }

        return $this->getContainer()->get('orm.manager')
            ->getRepository('Instance')->findBy($oql);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->getContainer()->get('core.security')->setCliUser();

        $this->start();
        $output->writeln(sprintf(
            str_pad('(1/3) Starting command', 50, '.')
                . '<fg=green;options=bold>DONE</>'
                . ' <fg=blue;options=bold>(%s)</>',
            date('Y-m-d H:i:s', $this->started)
        ));

        list($servers, $instances) = $this->getParameters($input);

        $output->writeln(sprintf(
            str_pad('(2/3) Processing instances', 43, '.')
                . '<fg=yellow;options=bold>IN PROGRESS</> '
                . '<fg=blue;options=bold>(%s instances)</>',
            count($instances)
        ));

        $i = 1;
        foreach ($instances as $instance) {
            $output->write(sprintf(
                '==> (%s/%s) Processing instance %s ',
                $i,
                count($instances),
                $instance->internal_name
            ));

            $this->getContainer()->get('core.loader')->load($instance->internal_name);
            $this->getContainer()->get('core.security')->setInstance($instance);

            $servers = $this->getServers($servers);

            $output->writeln(sprintf(
                '<fg=blue;options=bold>(%s servers)</> ',
                count($servers)
            ));

            $j = 1;

            foreach ($servers as $server) {
                $output->writeln(str_pad(sprintf(
                    '====> (%s/%s) Synchronizing %s',
                    $j,
                    count($servers),
                    $server['name']
                ), 50, '.'));

                $output->write(str_pad('- Downloading resources', 50, '.'));

                $stats = $this->getContainer()
                    ->get('news_agency.service.synchronizer')
                    ->resetStats()
                    ->synchronize($server)
                    ->getStats();

                $output->writeln(sprintf(
                    '<fg=green;options=bold>DONE</> <fg=blue;options=bold>'
                        . '(% 3d downloaded, % 3d deleted, % 3d parsed, % 3d valid, % 3d invalid)</>',
                    $stats['downloaded'],
                    $stats['deleted'],
                    $stats['parsed'],
                    $stats['valid'],
                    $stats['invalid']
                ));

                if (!array_key_exists('auto_import', $server)
                    || empty($server['auto_import'])
                ) {
                    $j++;
                    continue;
                }

                $output->write(str_pad('- Importing resources', 50, '.'));

                $stats = $this->getContainer()->get('news_agency.service.importer')
                    ->configure($server)
                    ->autoImport();

                $output->writeln(sprintf(
                    '<fg=green;options=bold>DONE</> <fg=blue;options=bold>'
                        . '(% 3d imported, % 3d ignored, % 3d invalid)</>',
                    $stats['imported'],
                    $stats['ignored'],
                    $stats['invalid']
                ));
            }
        }

        $this->end();
        $output->writeln(sprintf(
            str_pad('(3/3) Ending command', 50, '.')
                . '<fg=green;options=bold>DONE</>'
                . ' <fg=blue;options=bold>(%s)</>'
                . ' <fg=yellow;options=bold>(%s)</>',
            date('Y-m-d H:i:s', $this->ended),
            $this->getDuration()
        ));
    }

    /**
     * @codeCoverageIgnore
     *
     * Returns a list of files to process.
     *
     * @param string $path The path to a file or a directory.
     *
     * @return array The list of files.
     */
    protected function getFiles($path)
    {
        $finder = new Finder();

        if (empty($path) || !file_exists($path)) {
            throw new \InvalidArgumentException(
                'No such file or directory: ' . $path
            );
        }

        if (is_file($path)) {
            return [ new SplFileInfo($path, '', basename($path)) ];
        }

        return $finder->in($path)->name('*.xml')->files();
    }

    /**
     * Returns the list of parameters for the command based on the input.
     *
     * @param InputInterface $input Description
     *
     * @return array The list of parameters.
     */
    protected function getParameters(InputInterface $input) : array
    {
        $servers   = $input->getOption('servers');
        $instances = $input->getOption('instances');

        if (!empty($instances)) {
            $instances = preg_split('/\s*,\s*/', $instances);
        }

        if (!empty($servers)) {
            $servers = preg_split('/\s*,\s*/', $servers);
        }

        $instances = $this->getInstances($instances);

        return [ $servers, $instances ];
    }

    /**
     * Returns the list of enabled servers to synchronize.
     *
     * @param array $servers The list of server ids.
     *
     * @return array The list of servers to synchronize.
     */
    protected function getServers(?array $servers) : array
    {
        $service = $this->getContainer()->get('api.service.news_agency.server');
        $servers = empty($servers)
            ? $service->getList()['items']
            : $service->getListByIds($servers)['items'];

        return array_filter($servers, function ($a) {
            return !empty($a['activated']);
        });
    }
}
