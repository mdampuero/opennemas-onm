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
     * Handles storage operations for one or multiple items depending on the provided mode.
     *
     * @param mixed  $itemPK   The primary key, identifier, or an array of identifiers of the items to remove.
     * @param mixed  $instance The instance configuration.
     * @param string $mode     Operation mode: 'trash', 'restore' or 'delete'.
     *
     * @return void
     */
    public function removeFromStorage($itemPK, $instance, string $mode = 'delete')
    {
        $factory  = $this->container->get('core.helper.storage_factory');
        $storage  = $factory->create($instance);
        $s3Client = $factory->getS3Client();
        $config   = $factory->getConfig();
        $bucket   = $config['provider']['bucket'];

        $itemPKs = is_array($itemPK) ? $itemPK : [$itemPK];
        foreach ($itemPKs as $pk) {
            $item = $this->getItem($pk);
            if ($item->type === 'upload' && !empty($item->information['remotePath'])) {
                $remotePath = $item->information['remotePath'];

                if ($mode === 'delete') {
                    $storage->delete($remotePath);
                    $size = $item->information['fileSize'];

                    if ($size > 0) {
                        $instance->storage_size = max(0, $instance->storage_size - $size);
                        $this->container->get('orm.manager')->persist($instance);
                    }
                } elseif ($mode === 'trash' || $mode === 'restore') {
                    try {
                        $remotePath = ltrim($remotePath, '/');
                        $acl = $mode === 'restore' ? 'public-read' : 'private';
                        $s3Client->putObjectAcl([
                            'Bucket' => $bucket,
                            'Key'    => $remotePath,
                            'ACL'    => $acl,
                        ]);
                    } catch (\Exception $e) {
                    }
                }
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
