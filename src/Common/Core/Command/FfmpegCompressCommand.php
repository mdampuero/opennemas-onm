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
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class FfmpegCompressCommand extends ContainerAwareCommand
{
    private $item;
    private const FFMPEG_PATH        = '/usr/bin/ffmpeg';
    private const FFPROBE_PATH       = '/usr/bin/ffprobe';
    private const DEFAULT_PARAMS     = '-c:v libx264 -crf 24 -preset slow -vf "scale = 1280:-2" -c:a aac -b:a 128k';
    private const OUTPUT_FILE_FORMAT = 'mp4';
    private $taskPK                  = null;
    private $instance                = null;
    private $em;
    private $loggerApp;
    private $loggerErr;

    /**
     * Configures the command.
     */
    protected function configure()
    {
        $this
            ->setName('app:ffmpeg:compress')
            ->setDescription('Transcode video using ffmpeg and show progress.')
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
            ->addOption(
                'task',
                null,
                InputOption::VALUE_REQUIRED,
                ''
            )
            ->setHelp('');
    }

    /**
     * Executes the command.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->em        = $this->getContainer()->get('orm.manager');
        $this->loggerApp = $this->getContainer()->get('application.log');
        $this->loggerErr = $this->getContainer()->get('error.log');
        $itemPK          = $input->getOption('item');
        $this->taskPK    = $input->getOption('task');
        $instanceId      = $input->getOption('instance');

        if (!$itemPK) {
            $this->loggerErr->error('FFMPEG - The --item parameters are required');
            return 1;
        }

        if (!$instanceId) {
            $this->loggerErr->error('FFMPEG - The --instance parameters are required');
            return 1;
        }

        $this->setInstance($instanceId);

        $config = $this->getConfig();
        $params = $config['compress']['command'] ?? self::DEFAULT_PARAMS;

        $this->loggerApp->info('FFMPEG - compress command started', [
            'itemPK' => $itemPK,
            'params' => $params,
            'taskPK' => $this->taskPK
        ]);
        $this->setItem($itemPK);

        $item = $this->getItem();

        if (!$item) {
            $this->loggerErr->error('FFMPEG - The item does not exist');
            return 1;
        }

        $this->loggerApp->info('FFMPEG - Assembling chunks', [
            'folderTmp' => $item->information['folderTmp'],
            'fileName'  => $item->information['fileName'],
            'totalChunks' => $item->information['totalChunks']
        ]);

        $resultAssembling = $this->assemblingChunks(
            $item->information['folderTmp'],
            $item->information['fileName'],
            $item->information['totalChunks']
        );

        $this->loggerApp->info('FFMPEG - Assembled file', [
            'path' => $resultAssembling['path'],
            'tmp'  => $resultAssembling['tmp']
        ]);

        $inputFile = $resultAssembling['tmp'] ?? null;

        if ($inputFile) {
            $pathInfo   = pathinfo($inputFile);
            $outputFile = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '_output.' . self::OUTPUT_FILE_FORMAT;
        } else {
            $this->loggerErr->error('FFMPEG - No input file found for the item.');
            return 1;
        }

        if (!file_exists($inputFile)) {
            $this->loggerErr->error("FFMPEG - Input file does not exist: $inputFile");
            return 1;
        }

        // Get video duration in seconds
        $durationCmd = sprintf(
            '%s -v error -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 %s',
            self::FFPROBE_PATH,
            escapeshellarg($inputFile)
        );

        $durationSeconds = (float) trim(shell_exec($durationCmd));
        $durationMs      = $durationSeconds * 1000 * 1000;

        $output->writeln(sprintf('<info>Video duration: %.2f seconds</info>', $durationSeconds));

        $ffmpegCommand = str_replace(": ", ":", sprintf(
            '%s -i %s %s -progress pipe:1 -nostats %s',
            self::FFMPEG_PATH,
            escapeshellarg($inputFile),
            $params,
            escapeshellarg($outputFile)
        ));

        $this->loggerApp->info('FFMPEG - Starting FFMPEG command', [
            'command' => $ffmpegCommand
        ]);

        $process = new Process($ffmpegCommand);
        $process->setTimeout(null);

        $lastPrintedPct = -5;

        $process->run(function ($type, $buffer) use ($durationMs, &$lastPrintedPct, $output) {
            unset($type);
            foreach (explode("\n", $buffer) as $line) {
                $line = trim($line);

                if (strpos($line, 'out_time_ms=') === 0) {
                    $out_time_ms = (int) substr($line, strlen('out_time_ms='));
                    $progressPct = ($durationMs > 0)
                        ? min(100, (int) round(($out_time_ms / $durationMs) * 100))
                        : 0;

                    if ($progressPct - $lastPrintedPct >= 5) {
                        $lastPrintedPct = $progressPct;
                        $this->updateProgress($progressPct);
                        $output->writeln(sprintf(
                            str_pad('<options=bold>Progress', 64, '.')
                                . '<fg=cyan;options=bold>%d%%</>'
                                . ' <fg=blue;options=bold>(%s)</></>',
                            $progressPct,
                            date('Y-m-d H:i:s')
                        ));
                    }
                }

                if ($line === 'progress=end') {
                    $this->loggerApp->info('FFMPEG - Transcoding completed successfully');
                }
            }
        });

        if (!$process->isSuccessful()) {
            $this->loggerErr->error("FFMPEG - FFmpeg process failed: " .
                $process->getErrorOutput());
            $output->writeln('<fg=red;options=bold>FAIL - ' . $process->getErrorOutput() . '</>');
            if ($this->taskPK) {
                $conn = $this->getContainer()->get('orm.manager')->getConnection('manager');
                $conn->update('tasks', [
                    'status' => 'error',
                    'output' => $process->getErrorOutput()
                ], ['id' => $this->taskPK]);
            }
            return 1;
        }

        if ($this->taskPK) {
            $this->loggerApp->info('FFMPEG - Remove task');
            $this->em->remove($this->em->getRepository('Task')->find($this->taskPK));
        }

        //Delete original file
        $this->loggerApp->info("FFMPEG - Delete original file " . $inputFile);
        if (!unlink($inputFile)) {
            $this->loggerErr->warning("FFMPEG - Could not delete original file: $inputFile");
        }

        // Rename output file to original name
        $localPath = str_replace(
            '_output.' . self::OUTPUT_FILE_FORMAT,
            '.' . self::OUTPUT_FILE_FORMAT,
            $outputFile
        );
        $remotePath = $resultAssembling['path'];

        if (!rename($outputFile, $localPath)) {
            $this->loggerErr->warning("FFMPEG - Could not rename $outputFile to $inputFile");
        } else {
            // Update item information
            $this->setNextStep($localPath, $remotePath);
        }
        $this->loggerApp->info('FFMPEG - Finished compressing video', [
            'localPath'  => $localPath,
            'remotePath' => $remotePath
        ]);
        return 0;
    }

    private function assemblingChunks($uploadDir, $fileName, $totalChunks)
    {
        $output = fopen($uploadDir . '/' . $fileName, 'wb');
        if (!$output) {
            throw new \RuntimeException('Could not open output file for writing: ' . $fileName);
        }

        for ($i = 0; $i < $totalChunks; $i++) {
            $chunkPath = $uploadDir . '/' . $i;
            if (!file_exists($chunkPath)) {
                throw new \RuntimeException('Chunk file does not exist: ' . $chunkPath);
            }
            $input = fopen($chunkPath, 'rb');
            stream_copy_to_stream($input, $output);
            fclose($input);
            unlink($chunkPath);
        }
        fclose($output);

        return [
            'path' => $this->generatePath(new \DateTime()),
            'tmp'  => $uploadDir . '/' . $fileName
        ];
    }

    private function generatePath(\DateTime $date): string
    {
        $now         = microtime(true);
        $datetime    = \DateTime::createFromFormat('U.u', sprintf('%.6f', $now));
        $newFileName = $datetime->format('YmdHisv') . '.' . self::OUTPUT_FILE_FORMAT;
        return preg_replace('/\/+/', '/', sprintf(
            '/%s/%s/%s/%s',
            'media',
            $this->instance->internal_name . '/videos',
            $date->format('Y/m/d'),
            $newFileName
        ));
    }

    /**
     * Updates the current item's information using the content service.
     *
     * @param array $information The information to update for the item.
     */
    private function updateInformation($information)
    {
        $item = $this->getItem();
        $this->getContentService()->updateItem($item->pk_content, $information);
    }

    /**
     * Updates the progress value of the current step in the item's information.
     *
     * @param int|string $progress The progress value (without the % symbol).
     */
    private function updateProgress($progress)
    {
        $item                            = $this->getItem();
        $information                     = $item->information;
        $information['step']['progress'] = $progress . '%';
        $this->updateInformation([
            'information' => $information
        ]);
    }

    /**
     * Sets the next step in the item's process, calculating file size and updating status.
     *
     * @param string|null $inputFile The path to the input file to calculate its size.
     */
    private function setNextStep($localPath, $remotePath)
    {
        $item                              = $this->getItem();
        $information                       = $item->information;
        $newSizeBytes                      = filesize($localPath);
        $newSizeMB                         = number_format($newSizeBytes / (1024 * 1024), 2);
        $information['fileSize']           = (string) $newSizeBytes;
        $information['fileSizeMB']         = (string) $newSizeMB;
        $information['localPath']          = $localPath;
        $information['remotePath']         = $remotePath;
        $information['step']['progress']   = '0%';
        $information['status']             = 'done';
        $information['step']['label']      = 'Uploading to S3';
        $information['step']['styleClass'] = 'primary';

        $information['thumbnails'] = $this->createThumbnail($information);

        $this->loggerApp->info('FFMPEG - Updating item information', [
            'information' => $information
        ]);
        $this->updateInformation([
            'information' => $information
        ]);
    }

    private function createThumbnail($information)
    {
        $thumbnailPath = str_replace(
            '.' . self::OUTPUT_FILE_FORMAT,
            '.jpg',
            $information['localPath']
        );

        $thumbnailPath = str_replace(
            '/videos/' . self::OUTPUT_FILE_FORMAT,
            '/images/',
            $thumbnailPath
        );

        $config  = $this->getConfig();
        $second  = $config['thumbnail']['seconds'] ?? 5;
        $command = sprintf(
            '%s -ss %d -i %s -vframes 1 -q:v 2 -y %s',
            self::FFMPEG_PATH,
            $second,
            escapeshellarg($information['localPath']),
            escapeshellarg($thumbnailPath)
        );
        $this->loggerApp->info('FFMPEG - Create thumbnail command', [
            'command' => $command
        ]);
        $process = new Process($command);
        $process->run();
        if (!$process->isSuccessful()) {
            $this->getContainer()->get('error.log')->error("FFmpeg process failed: " .
                $process->getErrorOutput());
            $conn = $this->em->getConnection('manager');
            $conn->update('tasks', [
                'status' => 'error',
                'output' => $process->getErrorOutput()
            ], ['id' => $this->taskPK]);
        }
        return $thumbnailPath;
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
        $this->instance = $this->getContainer()->get('orm.manager')
            ->getRepository('Instance')->find($instance);
        $this->getContainer()->get('core.loader')->configureInstance($this->instance);
        $this->getContainer()->get('core.security')->setInstance($this->instance);

        return $this;
    }

    /**
     * Set the value of instance
     *
     * @return  self
     */
    public function getInstance()
    {
        return $this->instance;
    }

    /**
     * Get storage config for the current instance.
     * Falls back to manager settings if instance config is empty.
     */
    public function getConfig()
    {
        $manager = $this->getContainer()->get('orm.manager');

        $config = $manager
            ->getDataSet('Settings', 'instance')
            ->get('storage_settings', []);

        if (empty($config)) {
            $config = $manager
                ->getDataSet('Settings', 'manager')
                ->get('storage_settings', []);
        }

        return $config;
    }
}
