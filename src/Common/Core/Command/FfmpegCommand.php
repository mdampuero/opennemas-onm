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
use Symfony\Component\Process\Exception\ProcessFailedException;

class FfmpegCommand extends ContainerAwareCommand
{
    private $output;

    /**
     * Configures the command.
     */
    protected function configure()
    {
        $this
            ->setName('app:core:ffmpeg')
            ->setDescription('Transcode video using ffmpeg and show progress.')
            ->addOption(
                'input',
                null,
                InputOption::VALUE_REQUIRED,
                'Input video file path'
            )
            ->addOption(
                'output',
                null,
                InputOption::VALUE_REQUIRED,
                'Output video file path'
            )
            ->addOption(
                'crf',
                null,
                InputOption::VALUE_OPTIONAL,
                'CRF quality factor (default: 28)',
                28
            )
            ->addOption(
                'scale',
                null,
                InputOption::VALUE_OPTIONAL,
                'Scale (default: 1024:768)',
                '1024:768'
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
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;

        $crf    = $input->getOption('crf');
        $scale  = $input->getOption('scale');
        $itemPK = $input->getOption('item');

        if (!$itemPK) {
            $output->writeln('<fg=red;options=bold>FAIL - </> The --item parameters are required');
            return 1;
        }

        $item      = $this->getItem($itemPK);
        $inputFile = $item->information['finalPath'] ?? null;

        if ($inputFile) {
            $pathInfo = pathinfo($inputFile);
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
            str_pad('<options=bold>Starting FFMPEG command', 128, '.')
                . '<fg=green;options=bold>DONE</>'
                . ' <fg=blue;options=bold>(%s)</></>',
            date('Y-m-d H:i:s')
        ));

        // Get video duration in seconds
        $durationCmd = sprintf(
            '/usr/bin/ffprobe -v error -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 %s',
            escapeshellarg($inputFile)
        );
        $durationSeconds = (float) trim(shell_exec($durationCmd));
        $durationMs      = $durationSeconds * 1000 * 1000;

        $output->writeln(sprintf('<info>Video duration: %.2f seconds</info>', $durationSeconds));

        $this->getContainer()->get('application.log')->info("Input: $inputFile, Output: $outputFile");

        $ffmpegCommand = sprintf(
            '/usr/bin/ffmpeg -i %s -vf %s -c:v libx265 -crf %d -progress pipe:1 -nostats %s',
            escapeshellarg($inputFile),
            escapeshellarg('scale=' . $scale),
            $crf,
            escapeshellarg($outputFile)
        );

        $process = new Process($ffmpegCommand);
        $process->setTimeout(null);

        $lastPrintedPct = -5;

        $process->run(function ($type, $buffer) use ($durationMs, $item, &$lastPrintedPct, $output) {
            foreach (explode("\n", $buffer) as $line) {
                $line = trim($line);

                if (strpos($line, 'out_time_ms=') === 0) {
                    $out_time_ms = (int) substr($line, strlen('out_time_ms='));
                    $progressPct = ($durationMs > 0)
                        ? min(100, (int) round(($out_time_ms / $durationMs) * 100))
                        : 0;

                    if ($progressPct - $lastPrintedPct >= 5) {
                        $lastPrintedPct = $progressPct;
                        $this->updateItem($item, $progressPct);
                        $output->writeln(sprintf(
                            str_pad('<options=bold>Progress', 128, '.')
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
            str_pad('<options=bold>Finish FFMPEG command', 128, '.')
                . '<fg=green;options=bold>DONE</>'
                . ' <fg=blue;options=bold>(%s)</></>',
            date('Y-m-d H:i:s')
        ));

        // Eliminar el archivo original
        // if (!unlink($inputFile)) {
        //     $output->writeln('<fg=red>WARNING - Could not delete original file: ' . $inputFile . '</>');
        //     $this->getContainer()->get('application.log')->warning("Could not delete original file: $inputFile");
        // } else {
        //     $output->writeln('<fg=yellow>Original file deleted: ' . $inputFile . '</>');
        // }

        // // Renombrar el archivo comprimido con el nombre del original
        // if (!rename($outputFile, $inputFile)) {
        //     $output->writeln('<fg=red>WARNING - Could not rename output file to original name</>');
        //     $this->getContainer()->get('application.log')->warning("Could not rename $outputFile to $inputFile");
        // } else {
        //     $output->writeln('<fg=yellow>Output file renamed to original name: ' . $inputFile . '</>');

        //     // Actualizar tamaño del archivo en bytes y MB

        //
        // }
         $this->setNextStep($item, $inputFile);
        return 0;
    }


    private function getItem(string $itemPK)
    {
        $instance = $this->getContainer()->get('orm.manager')
            ->getRepository('Instance')->find(1);
        $this->getContainer()->get('core.loader')->configureInstance($instance);
        $this->getContainer()->get('core.security')->setInstance($instance);
        $sc   = $this->getContainer()->get('api.service.content');
        $item = $sc->getItem($itemPK);
        return $item;
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

    private function setNextStep($item, $inputFile = null)
    {
        $instance = $this->getContainer()->get('orm.manager')
            ->getRepository('Instance')->find(1);
        $this->getContainer()->get('core.loader')->configureInstance($instance);
        $this->getContainer()->get('core.security')->setInstance($instance);
        $sc                                = $this->getContainer()->get('api.service.content');
        $information                       = $item->information;
        $newSizeBytes                      = filesize($inputFile);
        $newSizeMB                         = number_format($newSizeBytes / (1024 * 1024), 2);
        $information['fileSize']           = (string) $newSizeBytes;
        $information['fileSizeMB']         = (string) $newSizeMB;
        $information['step']['progress']   = '0%';
        $information['step']['label']      = 'Uploading to S3';
        $information['step']['styleClass'] = 'primary';
        $sc->updateItem($item->pk_content, [
            'information' => $information
        ]);
    }
}
