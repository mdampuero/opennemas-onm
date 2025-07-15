<?php

/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Api\Service\V1;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class VideoService extends ContentService
{
    /**
     * Removes one or multiple items from the storage if they are of type 'upload'.
     *
     * @param mixed $itemPK The primary key, identifier, or an array of identifiers of the items to remove.
     *
     * @return void
     */
    public function removeFromStorage($itemPK)
    {
        $factory = $this->container->get('core.helper.storage_factory');
        $storage = $factory->create();

        $itemPKs = is_array($itemPK) ? $itemPK : [$itemPK];

        foreach ($itemPKs as $pk) {
            $item = $this->getItem($pk);
            if ($item->type === 'upload' && !empty($item->information['relativePath'])) {
                $storage->delete($item->information['relativePath']);
            }
        }
    }

    /**
     * Generates a thumbnail image from a given video file path.
     *
     * @param string $inputPath Full path to the input video file.
     * @return string|null Returns the path to the generated thumbnail, or null on failure.
     * @throws \RuntimeException If ffmpeg fails or the input file doesn't exist.
     */
    public function generateThumbnailFromVideoPath(string $inputPath): ?string
    {
        if (!file_exists($inputPath)) {
            throw new \RuntimeException("File not found: $inputPath");
        }

        $pathInfo      = pathinfo($inputPath);
        $thumbnailPath = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '_thumbnailTmp.jpg';

        $ffmpegPath = '/usr/bin/ffmpeg'; // o configurable por parámetro si lo prefieres

        $command = sprintf(
            '%s -ss 5 -i %s -vframes 1 -q:v 2 -y %s',
            escapeshellcmd($ffmpegPath),
            escapeshellarg($inputPath),
            escapeshellarg($thumbnailPath)
        );

        $process = new Process($command);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return $thumbnailPath;
    }
}
