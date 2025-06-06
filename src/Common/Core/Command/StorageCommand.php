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

use Common\Core\Component\Helper\StorageHelperFactory;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Psr\Log\LoggerInterface;

class StorageCommand extends ContainerAwareCommand
{
    private $output;

    /**
     * Configures the command.
     */
    protected function configure()
    {
        $this
            ->setName('app:core:storage')
            ->setDescription('Perform storage operations like upload, read, delete, etc.')
            ->addOption(
                'operation',
                null,
                InputOption::VALUE_REQUIRED,
                ''
            )
            ->addOption(
                'file',
                null,
                InputOption::VALUE_REQUIRED,
                ''
            )
            ->addOption(
                'path',
                null,
                InputOption::VALUE_REQUIRED,
                ''
            )
            ->setHelp('');
    }

    /**
     * Executes the command.
     *
     * @param InputInterface  $input  The input object.
     * @param OutputInterface $output The output object.
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;

        $operation = $input->getOption('operation');
        $file      = $input->getOption('file');
        $path      = $input->getOption('path');

        if (!$operation) {
            $output->writeln('<fg=red;options=bold>FAIL - </> The --operation parameter is required');
            return 1;
        }

        $output->writeln(sprintf(
            str_pad('<options=bold>Starting command', 128, '.')
                . '<fg=green;options=bold>DONE</>'
                . ' <fg=blue;options=bold>(%s)</></>',
            date('Y-m-d H:i:s', time())
        ));

        $config = [
            'key'        => 'DO801FCQYMJ92HJ3YXEV',
            'secret'     => '16u0BZ/XOIw+6yfH5BoCBnWqlluIL6v7f0IwAXemiBo',
            'region'     => 'ams3',
            'bucket'     => 'onm-test',
            'endpoint'   => 'https://ams3.digitaloceanspaces.com',
            'path_style' => true
        ];

        $factory = $this->getContainer()->get('core.helper.storage_factory');
        $storage = $factory->create($config);

        switch ($operation) {
            case 'upload':
                if (!$file) {
                    $output->writeln('<fg=red;options=bold>FAIL - </> The --file parameter is required');
                    return 1;
                }

                $localFile = $file;

                if (!file_exists($localFile)) {
                    $output->writeln("<fg=red;options=bold>FAIL - </> File does not exist: $localFile");
                    return 1;
                }

                $path   = basename($localFile);
                $result = $storage->upload($path, file_get_contents($localFile), ['visibility' => 'public']);
                $url    = rtrim($config['endpoint'], '/') . '/' . $config['bucket'] . '/' . ltrim($path, '/');
                $this->logResult('Upload - ' . $url . ' ', $result);
                break;

            case 'read':
                if (!$path) {
                    $output->writeln('<fg=red;options=bold>FAIL - </> The --path parameter is required');
                    return 1;
                }

                $result = $storage->read($path);
                $this->logResult('Read', $result !== null, $result);
                break;


            case 'delete':
                if (!$path) {
                    $output->writeln('<fg=red;options=bold>FAIL - </> The --path parameter is required');
                    return 1;
                }
                $result = $storage->delete($path);
                $this->logResult('Delete', $result);
                break;

            // case 'replace':
            // $result = $storage->replace($path, $newContent);
            //$this->logResult('Replace', $result);
            // break;

            // case 'list':
            // $result = $storage->listContents('test', true);
            //$this->logResult('List', is_array($result), print_r($result, true));
            // break;

            default:
                $output->writeln('<fg=red;options=bold>FAIL - Unknown operation: ' . $operation . '</>');
                return 1;
        }

        $output->writeln(sprintf(
            str_pad('<options=bold>Finish command', 128, '.')
                . '<fg=green;options=bold>DONE</>'
                . ' <fg=blue;options=bold>(%s)</></>',
            date('Y-m-d H:i:s', time())
        ));

        return 0;
    }

    private function logResult(string $operation, bool $success, string $extra = ''): void
    {
        $this->output->writeln(sprintf(
            str_pad("<options=bold>$operation", 128, '.')
                . ($success
                    ? '<fg=green;options=bold>DONE</>'
                    : '<fg=red;options=bold>FAIL</>')
                . ' <fg=blue;options=bold>(%s)</></>',
            date('Y-m-d H:i:s')
        ));

        if ($extra) {
            $this->output->writeln("<comment>$extra</comment>");
        }
    }
}
