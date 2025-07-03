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

class FfmpegCommand extends ContainerAwareCommand
{
    private $item;
    private const FFMPEG_PATH    = '/usr/bin/ffmpeg';
    private const FFPROBE_PATH   = '/usr/bin/ffprobe';
    private const DEFAULT_PARAMS = '-c:v libx264 -crf 24 -preset slow -vf "scale=1280:-2" -c:a aac -b:a 128k';

    /**
     * Configures the command.
     */
    protected function configure()
    {
        $this
            ->setName('app:core:ffmpeg')
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
        $itemPK = $input->getOption('item');
        $params = self::DEFAULT_PARAMS;

        if (!$itemPK) {
            $output->writeln('<fg=red;options=bold>FAIL - </> The --item parameters are required');
            return 1;
        }

        $instanceId = $input->getOption('instance');

        if (!$instanceId) {
            $output->writeln('<fg=red;options=bold>FAIL - </> The --instance parameters are required');
            return 1;
        }

        $this->setInstance($instanceId);
        $this->setItem($itemPK);

        $item = $this->getItem();
        if (!$item) {
            $output->writeln('<fg=red;options=bold>FAIL - </> The item does not exist');
            return 1;
        }

        $inputFile = $item->information['finalPath'] ?? null;

        if ($inputFile) {
            $pathInfo   = pathinfo($inputFile);
            $outputFile = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '_output.' . $pathInfo['extension'];
        } else {
            $output->writeln('<fg=red;options=bold>FAIL - </> No input file found for the item.');
            return 1;
        }

        if (!file_exists($inputFile)) {
            $output->writeln("<fg=red;options=bold>FAIL - </> Input file does not exist: $inputFile");
            return 1;
        }

        $output->writeln(sprintf(
            str_pad('<options=bold>Starting FFMPEG command', 64, '.')
                . '<fg=green;options=bold>DONE</>'
                . ' <fg=blue;options=bold>(%s)</></>',
            date('Y-m-d H:i:s')
        ));

        // Get video duration in seconds
        $durationCmd = sprintf(
            '%s -v error -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 %s',
            self::FFPROBE_PATH,
            escapeshellarg($inputFile)
        );

        $durationSeconds = (float) trim(shell_exec($durationCmd));
        $durationMs      = $durationSeconds * 1000 * 1000;

        $output->writeln(sprintf('<info>Video duration: %.2f seconds</info>', $durationSeconds));

        $this->getContainer()->get('application.log')->info("Input: $inputFile, Output: $outputFile");

        $ffmpegCommand = sprintf(
            '%s -i %s %s -progress pipe:1 -nostats %s',
            self::FFMPEG_PATH,
            escapeshellarg($inputFile),
            $params,
            escapeshellarg($outputFile)
        );

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
                    $output->writeln('<fg=green;options=bold>Transcoding completed!</>');
                }
            }
        });

        if (!$process->isSuccessful()) {
            $this->getContainer()->get('application.log')->error("FFmpeg process failed: " .
                $process->getErrorOutput());
            $output->writeln('<fg=red;options=bold>FAIL - FFmpeg process failed</>');
            return 1;
        }

        $output->writeln(sprintf(
            str_pad('<options=bold>Finish FFMPEG command', 64, '.')
                . '<fg=green;options=bold>DONE</>'
                . ' <fg=blue;options=bold>(%s)</></>',
            date('Y-m-d H:i:s')
        ));

        //Delete original file
        if (!unlink($inputFile)) {
            $output->writeln('<fg=red>WARNING - Could not delete original file: ' . $inputFile . '</>');
            $this->getContainer()->get('application.log')->warning("Could not delete original file: $inputFile");
        } else {
            $output->writeln('<fg=yellow>Original file deleted: ' . $inputFile . '</>');
        }

        // Rename output file to original name
        if (!rename($outputFile, $inputFile)) {
            $output->writeln('<fg=red>WARNING - Could not rename output file to original name</>');
            $this->getContainer()->get('application.log')->warning("Could not rename $outputFile to $inputFile");
        } else {
            $output->writeln('<fg=yellow>Output file renamed to original name: ' . $inputFile . '</>');

            // Update item information
            $this->setNextStep($inputFile);
        }
        return 0;
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
    private function setNextStep($inputFile = null)
    {
        $item                              = $this->getItem();
        $information                       = $item->information;
        $newSizeBytes                      = filesize($inputFile);
        $newSizeMB                         = number_format($newSizeBytes / (1024 * 1024), 2);
        $information['fileSize']           = (string) $newSizeBytes;
        $information['fileSizeMB']         = (string) $newSizeMB;
        $information['step']['progress']   = '0%';
        $information['status']             = 'done';
        $information['step']['label']      = 'Uploading to S3';
        $information['step']['styleClass'] = 'primary';
        $this->updateInformation([
            'information' => $information
        ]);
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
