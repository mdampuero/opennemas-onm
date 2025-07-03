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
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Aws\S3\MultipartUploader;
use Aws\Exception\MultipartUploadException;

class StorageCommand extends ContainerAwareCommand
{
    private $output;
    private $item;

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
            ->addOption(
                'instance',
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
        $itemPK      = $input->getOption('item');
        $instanceId  = $input->getOption('instance');

        if (!$operation) {
            $output->writeln('<fg=red;options=bold>FAIL - </> The --operation parameter is required');
            return 1;
        }

        $this->logCommandStart();

        $config  = $this->getStorageConfig();
        $factory = $this->getContainer()->get('core.helper.storage_factory');
        $storage = $factory->create($config);

        switch ($operation) {
            case 'upload':
                return $this->handleUpload($file, $destination, $storage, $output);

            case 'read':
                return $this->handleRead($path, $storage, $output);

            case 'delete':
                return $this->handleDelete($path, $storage, $output);

            case 'uploadByItem':
                return $this->handleUploadByItem($itemPK, $instanceId, $factory, $config, $output);

            default:
                $output->writeln('<fg=red;options=bold>FAIL - Unknown operation: ' . $operation . '</>');
                return 1;
        }

        $this->logCommandFinish();

        return 0;
    }

    /**
     * Logs the start of the command execution with a timestamp.
     */
    private function logCommandStart()
    {
        $this->output->writeln(sprintf(
            str_pad('<options=bold>Starting command', 128, '.')
                . '<fg=green;options=bold>DONE</>'
                . ' <fg=blue;options=bold>(%s)</></>',
            date('Y-m-d H:i:s')
        ));
    }

    /**
     * Logs the completion of the command execution with a timestamp.
     */
    private function logCommandFinish()
    {
        $this->output->writeln(sprintf(
            str_pad('<options=bold>Finish command', 128, '.')
                . '<fg=green;options=bold>DONE</>'
                . ' <fg=blue;options=bold>(%s)</></>',
            date('Y-m-d H:i:s')
        ));
    }

    /**
     * Returns the configuration array for connecting to the storage service.
     *
     * @return array Storage configuration parameters
     */
    private function getStorageConfig(): array
    {
        return [
            'key'        => 'DO801FCQYMJ92HJ3YXEV',
            'secret'     => '16u0BZ/XOIw+6yfH5BoCBnWqlluIL6v7f0IwAXemiBo',
            'region'     => 'ams3',
            'bucket'     => 'onm-test',
            'endpoint'   => 'https://ams3.digitaloceanspaces.com',
            'path_style' => true
        ];
    }

    /**
     * Handles the file upload operation.
     * Validates the input file and destination, uploads the file, and logs the result.
     *
     * @param string $file        Path to the local file to upload
     * @param string $destination Destination path in storage (optional)
     * @param object $storage     Storage client object
     * @param OutputInterface $output Console output interface for messages
     *
     * @return int Exit code (0 on success, 1 on failure)
     */
    private function handleUpload($file, $destination, $storage, OutputInterface $output)
    {
        if (!$file) {
            $output->writeln('<fg=red;options=bold>FAIL - </> The --file parameter is required');
            return 1;
        }

        if (!file_exists($file)) {
            $output->writeln("<fg=red;options=bold>FAIL - </> File does not exist: $file");
            return 1;
        }

        $path   = $destination ?: basename($file);
        $result = $storage->upload($path, file_get_contents($file), ['visibility' => 'public']);
        $this->logResult('Upload ', $result);

        $this->logCommandFinish();
        return 0;
    }

    /**
     * Handles the file read operation from storage.
     * Validates the input path, reads the file content, and logs the result.
     *
     * @param string $path        Path of the file in storage
     * @param object $storage     Storage client object
     * @param OutputInterface $output Console output interface for messages
     *
     * @return int Exit code (0 on success, 1 on failure)
     */
    private function handleRead($path, $storage, OutputInterface $output)
    {
        if (!$path) {
            $output->writeln('<fg=red;options=bold>FAIL - </> The --path parameter is required');
            return 1;
        }

        $result = $storage->read($path);
        $this->logResult('Read', $result !== null, $result);

        $this->logCommandFinish();
        return 0;
    }

    /**
     * Handles the file delete operation in storage.
     * Validates the input path, deletes the file, and logs the result.
     *
     * @param string $path        Path of the file in storage
     * @param object $storage     Storage client object
     * @param OutputInterface $output Console output interface for messages
     *
     * @return int Exit code (0 on success, 1 on failure)
     */
    private function handleDelete($path, $storage, OutputInterface $output)
    {
        if (!$path) {
            $output->writeln('<fg=red;options=bold>FAIL - </> The --path parameter is required');
            return 1;
        }

        $result = $storage->delete($path);
        $this->logResult('Delete', $result);

        $this->logCommandFinish();
        return 0;
    }

    /**
     * Handles the upload operation by item.
     * Validates required parameters, retrieves the item, uploads its associated file
     * using multipart upload with progress reporting, and logs the result.
     *
     * @param mixed $itemPK       Primary key of the item
     * @param mixed $instanceId   Instance identifier
     * @param object $factory     Factory object to get storage client
     * @param array $config       Storage configuration
     * @param OutputInterface $output Console output interface for messages
     *
     * @return int Exit code (0 on success, 1 on failure)
     */
    private function handleUploadByItem($itemPK, $instanceId, $factory, $config, OutputInterface $output)
    {
        if (!$itemPK) {
            $output->writeln('<fg=red;options=bold>FAIL - </> The --item parameter is required');
            return 1;
        }

        if (!$instanceId) {
            $output->writeln('<fg=red;options=bold>FAIL - </> The --instance parameter is required');
            return 1;
        }

        $this->setInstance($instanceId);
        $this->setItem($itemPK);

        $item = $this->getItem();
        if (!$item) {
            $output->writeln('<fg=red;options=bold>FAIL - </> The item does not exist');
            return 1;
        }

        $localFile = $item->information['finalPath'] ?? null;
        $path      = $item->information['relativePath'] ?? null;

        if (!$localFile || !file_exists($localFile)) {
            $output->writeln('<fg=red;options=bold>FAIL - </> Local file does not exist');
            return 1;
        }

        if (!$path) {
            $output->writeln('<fg=red;options=bold>FAIL - </> Relative path is not set in item');
            return 1;
        }

        $s3Client    = $factory->getS3Client();
        $lastPercent = 0;
        $filesize    = filesize($localFile);

        $uploader = new MultipartUploader($s3Client, $localFile, [
            'bucket' => $config['bucket'],
            'key'    => substr($path, 1),
            'ACL'    => 'public-read',
            'before_upload' => function ($params) use (&$lastPercent, $output, $filesize) {
                static $uploaded = 0;
                $uploaded += isset($params['Body']) ? strlen((string) $params['Body']) : 0;
                if ($filesize > 0) {
                    $percent = (int) (($uploaded / $filesize) * 100);
                    if ($percent - $lastPercent >= 5) {
                        $lastPercent = $percent;
                        $this->updateItem($percent);
                        $output->writeln("Upload progress: $percent%");
                    }
                }
            },
        ]);

        try {
            $uploader->upload();
            $this->setNextStep($config, $path);
            $this->logResult('Upload ', true);
        } catch (MultipartUploadException $e) {
            $output->writeln('<fg=red;options=bold>Upload failed: </>' . $e->getMessage());
            return 1;
        }

        $this->logCommandFinish();
        return 0;
    }

    /**
     * Finalizes the current step by setting status to 'done', updating the item's path,
     * and removing the local file if it exists.
     *
     * @param array  $config        Configuration array containing 'endpoint' and 'bucket'.
     * @param string $relativePath  The relative path of the file in the remote storage.
     */
    private function setNextStep($config, $relativePath)
    {
        $item                              = $this->getItem();
        $information                       = $item->information;
        $path                              = $config['endpoint'] . '/' . $config['bucket'] . $relativePath;
        $information['step']['progress']   = '';
        $information['step']['label']      = 'Completed';
        $information['step']['styleClass'] = 'success';
        $information['status']             = 'done';
        $information['path']               = $path;
        $information['source']             = [pathinfo($information['finalPath'], PATHINFO_EXTENSION) => $path];

        // Remove local file if it exists
        unlink($item->information['finalPath'] ?? '');

        $this->getContentService()->updateItem($item->pk_content, [
            'information' => $information,
            'path'        => $path
        ]);
    }

    /**
     * Updates the progress of the current item.
     *
     * @param int|string $progress Progress value (without % sign).
     */
    private function updateItem($progress)
    {
        $item                            = $this->getItem();
        $information                     = $item->information;
        $information['step']['progress'] = $progress . '%';
        $this->getContentService()->updateItem($item->pk_content, [
            'information' => $information
        ]);
    }

    /**
     * Logs the result of an operation to the console output.
     *
     * @param string $operation Name or description of the operation.
     * @param bool   $success   Whether the operation was successful.
     * @param string $extra     Optional extra info to log below the result.
     */
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

    /**
     * Get the value of item
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * Returns the content service instance.
     *
     * @return object The content service.
     */
    private function getContentService()
    {
        return $this->getContainer()->get('api.service.content');
    }

    /**
     * Set the value of item
     *
     * @return  self
     */
    public function setItem($item)
    {
        $this->item = $this->getContentService()->getItem($item);
        return $this;
    }

    /**
     * Set the value of instance
     *
     * @return  self
     */
    public function setInstance($instance)
    {
        $instance = $this->getContainer()->get('orm.manager')
            ->getRepository('Instance')->find($instance);
        $this->getContainer()->get('core.loader')->configureInstance($instance);
        $this->getContainer()->get('core.security')->setInstance($instance);

        return $this;
    }
}
