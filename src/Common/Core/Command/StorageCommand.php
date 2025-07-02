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
use Aws\S3\MultipartUploader;
use Aws\Exception\MultipartUploadException;

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
            ->addOption(
                'destination',
                null,
                InputOption::VALUE_REQUIRED,
                ''
            )
            ->addOption(
                'item',
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

        $operation   = $input->getOption('operation');
        $file        = $input->getOption('file');
        $path        = $input->getOption('path');
        $destination = $input->getOption('destination', '');
        $item        = $input->getOption('item');

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

                $path   = ($destination) ? $destination : basename($localFile);
                $result = $storage->upload($path, file_get_contents($localFile), ['visibility' => 'public']);

                $this->logResult('Upload ', $result);

                if ($item) {
                    $this->updateItemInformationSource($item, [
                        'mp4' => $config['endpoint'] . '/' . $config['bucket'] . $path
                    ]);
                }
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

            case 'uploadByItem':
                if (!$item) {
                    $output->writeln('<fg=red;options=bold>FAIL - </> The --item parameter is required');
                    return 1;
                }

                $itemObject = $this->getItem($item);
                if (!$itemObject) {
                    $output->writeln('<fg=red;options=bold>FAIL - </> The item does not exist');
                    return 1;
                }

                $localFile = $itemObject->information['finalPath'] ?? null;
                $path      = $itemObject->information['relativePath'] ?? null;
                $path = $itemObject->information['relativePath'] ?? null;

                if (!$localFile || !file_exists($localFile)) {
                    $output->writeln('<fg=red;options=bold>FAIL - </> Local file does not exist');
                    return 1;
                }

                if (!$path) {
                    $output->writeln('<fg=red;options=bold>FAIL - </> Relative path is not set in item');
                    return 1;
                }

                // Obtener el cliente S3 desde $storage, asumiendo que tienes este método
                $s3Client = $factory->getS3Client();  // Adaptar si es otro método o forma

                $lastPercent = 0;
                $filesize = filesize($localFile);

                $uploader = new MultipartUploader($s3Client, $localFile, [
                    'bucket' => $config['bucket'],
                    'key'    => substr($path, 1),
                    'ACL'    => 'public-read',
                    'before_upload' => function ($params) use (&$lastPercent, $item, $output, $filesize, $itemObject) {
                        static $uploaded = 0;
                        $uploaded += isset($params['Body']) ? strlen((string) $params['Body']) : 0;
                        if ($filesize > 0) {
                            $percent = (int) (($uploaded / $filesize) * 100);
                            //if ($percent - $lastPercent >= 5) {
                                $lastPercent = $percent;
                                // If updateUploadProgress is needed, uncomment the next line:
                                $this->updateItem($itemObject, $percent);
                                $output->writeln("Upload progress: $percent%");
                            //}
                        }
                    },
                ]);

                try {
                    $result = $uploader->upload();
                    $this->setNextStep($itemObject, $config, $path);
                    $this->logResult('Upload ', true);
                } catch (MultipartUploadException $e) {
                    $output->writeln('<fg=red;options=bold>Upload failed: </>' . $e->getMessage());
                    return 1;
                }

                break;

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

    private function setNextStep($item, $config, $relativePath)
    {
        $instance = $this->getContainer()->get('orm.manager')
            ->getRepository('Instance')->find(1);
        $this->getContainer()->get('core.loader')->configureInstance($instance);
        $this->getContainer()->get('core.security')->setInstance($instance);
        $sc                                = $this->getContainer()->get('api.service.content');
        $information                       = $item->information;
        $path = $config['endpoint'] . '/' . $config['bucket'] . $relativePath;
        $information['step']['progress']   = '';
        $information['step']['label']      = 'Completed';
        $information['step']['styleClass'] = 'success';

        $information['path']   = $path;
        $information['source'] = ['mp4' => $path];

        //unlink($item->information['finalPath'] ?? '');

        $sc->updateItem($item->pk_content, [
            'information' => $information,
            'path'        => $path
        ]);
    }

    private function updateItemInformationSource(string $itemPK, array $source): void
    {
        $instance = $this->getContainer()->get('orm.manager')
            ->getRepository('Instance')->find(1);
        $this->getContainer()->get('core.loader')->configureInstance($instance);
        $this->getContainer()->get('core.security')->setInstance($instance);
        $sc   = $this->getContainer()->get('api.service.content');
        $item = $sc->getItem($itemPK);
        if ($item) {
            $sc->updateItem($itemPK, [
                'information' => [
                    'source' => $source
                ]
            ]);
            $this->logResult('Update InformationSource Item# ' . $itemPK, true);
        }
    }

    private function updateItem($item, $progress)
    {
        $instance = $this->getContainer()->get('orm.manager')
            ->getRepository('Instance')->find(1);
        $this->getContainer()->get('core.loader')->configureInstance($instance);
        $this->getContainer()->get('core.security')->setInstance($instance);
        $sc          = $this->getContainer()->get('api.service.content');
        $information = $item->information;
        $information['step']['progress'] = $progress . '%';
        $sc->updateItem($item->pk_content, [
            'information' => $information
        ]);
    }

    private function getItem(string $itemPK)
    {
        $instance = $this->getContainer()->get('orm.manager')
            ->getRepository('Instance')->find(1);
        $this->getContainer()->get('core.loader')->configureInstance($instance);
        $this->getContainer()->get('core.security')->setInstance($instance);
        $sc   = $this->getContainer()->get('api.service.content');
        $item = $sc->getItem($itemPK);
        if ($item) {
            return $item;
        }
        return null;
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
