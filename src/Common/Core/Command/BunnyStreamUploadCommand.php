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

use Common\Core\Service\Bunny\BunnyStreamService;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class BunnyStreamUploadCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('bunny:stream:upload')
            ->setDescription('Uploads a local video file to Bunny Stream and prints the playback URL.')
            ->setHelp('The command takes a path to a local video, uploads it using the Bunny 
            Stream API and returns the embed URL.')
            ->addArgument('path', InputArgument::REQUIRED, 'Absolute or relative path to the video file to upload.')
            ->addOption('title', 't', InputOption::VALUE_REQUIRED, 'Optional video title, defaults to the 
            filename without extension.');

        $this->steps[] = 4;
    }

    /**
     * Executes the command logic.
     */
    protected function do()
    {
        $path  = $this->input->getArgument('path');
        $title = $this->input->getOption('title') ?: pathinfo($path, PATHINFO_FILENAME);

        $this->writeStep('Validating file path');
        $realPath = realpath($path) ?: $path;

        if (!is_file($realPath)) {
            throw new \InvalidArgumentException(sprintf('The file "%s" does not exist.', $path));
        }

        if (!is_readable($realPath)) {
            throw new \RuntimeException(sprintf('The file "%s" is not readable.', $realPath));
        }

        $this->writeStatus('success', 'DONE');
        $this->writeStatus('info', sprintf(' (%s)', $realPath), true);

        $this->writeStep('Loading Bunny Stream service');

        /** @var BunnyStreamService $bunnyService */
        $bunnyService = $this->getContainer()->get('common.core.bunny_stream.service');
        $bunnyService->assertConfigured();

        $this->writeStatus('success', 'DONE', true);

        $this->writeStep('Creating remote video placeholder');

        $createPayload = $bunnyService->createVideo($title ?: basename($realPath));
        $videoGuid     = $createPayload['guid'] ?? $createPayload['videoGuid'] ?? $createPayload['id'] ?? null;

        if (empty($videoGuid)) {
            throw new \RuntimeException('The Bunny Stream API response did not provide a video identifier.');
        }

        $this->writeStatus('success', 'DONE');
        $this->writeStatus('info', sprintf(' (GUID: %s)', $videoGuid), true);

        $this->writeStep('Uploading video file');
        $mimeType = function_exists('mime_content_type') ? mime_content_type($realPath) : null;
        $bunnyService->uploadVideoFromFile($videoGuid, $realPath, $mimeType);

        $this->writeStatus('success', 'DONE', true);

        $this->writeStep('Fetching video details');
        $details      = $bunnyService->fetchVideo($videoGuid);
        $playbackGuid = $details['guid'] ?? $videoGuid;

        $this->writeStatus('success', 'DONE', true);

        $this->writeStep('Playback URL');
        $embedUrl = $bunnyService->getEmbedUrl($playbackGuid);
        $this->writeStatus('success', $embedUrl, true);
    }
}
