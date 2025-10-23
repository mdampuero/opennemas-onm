<?php

namespace Common\Core\Component\Helper;

use Common\Core\Service\Bunny\BunnyStreamService;
use Psr\Log\LoggerInterface;

class BunnyStorageHelper
{
    private $service;
    private $logApp;
    private $logError;

    public function __construct(BunnyStreamService $service, LoggerInterface $logApp, LoggerInterface $logError)
    {
        $this->service = $service;
        $this->logApp  = $logApp;
        $this->logError = $logError;
    }

    public function upload(string $path, string $contents, array $params = []): bool
    {
        $tmpFile = tempnam(sys_get_temp_dir(), 'bunny_upload_');
        if ($tmpFile === false) {
            $this->logError->error('BUNNY - Unable to create temporary file for upload.', ['path' => $path]);
            return false;
        }

        try {
            file_put_contents($tmpFile, $contents);
        } catch (\Throwable $exception) {
            $this->logError->error('BUNNY - Unable to write temporary upload file.', [
                'path'  => $path,
                'error' => $exception->getMessage(),
            ]);
            @unlink($tmpFile);
            return false;
        }

        $title = $params['title'] ?? pathinfo($path, PATHINFO_FILENAME);

        try {
            $createPayload = $this->service->createVideo($title ?: basename($path));
            $videoGuid     = $createPayload['guid'] ?? $createPayload['videoGuid'] ?? $createPayload['id'] ?? null;

            if (!$videoGuid) {
                throw new \RuntimeException('Missing video identifier from Bunny Stream response.');
            }

            $mimeType = $params['mimeType'] ?? (function_exists('mime_content_type')
                ? mime_content_type($tmpFile)
                : null);
            $this->service->uploadVideoFromFile($videoGuid, $tmpFile, $mimeType);
            $this->logApp->info('BUNNY - Uploaded file to Bunny Stream.', [
                'path'      => $path,
                'videoGuid' => $videoGuid,
            ]);
            return true;
        } catch (\Throwable $exception) {
            $this->logError->error('BUNNY - Upload failed.', [
                'path'  => $path,
                'error' => $exception->getMessage(),
            ]);
            return false;
        } finally {
            @unlink($tmpFile);
        }
    }

    public function delete(string $identifier): bool
    {
        $videoGuid = $this->extractGuid($identifier);
        if (!$videoGuid) {
            return false;
        }

        try {
            $this->service->deleteVideo($videoGuid);
            $this->logApp->info('BUNNY - Deleted video.', ['videoGuid' => $videoGuid]);
            return true;
        } catch (\Throwable $exception) {
            $this->logError->error('BUNNY - Unable to delete video.', [
                'videoGuid' => $videoGuid,
                'error'     => $exception->getMessage(),
            ]);
            return false;
        }
    }

    public function exists(string $identifier): bool
    {
        $videoGuid = $this->extractGuid($identifier);
        if (!$videoGuid) {
            return false;
        }

        try {
            $this->service->fetchVideo($videoGuid);
            return true;
        } catch (\Throwable $exception) {
            return false;
        }
    }

    public function read(string $identifier): ?string
    {
        $videoGuid = $this->extractGuid($identifier);
        if (!$videoGuid) {
            return null;
        }

        try {
            $details = $this->service->fetchVideo($videoGuid);
            return json_encode($details);
        } catch (\Throwable $exception) {
            $this->logError->error('BUNNY - Unable to read video information.', [
                'videoGuid' => $videoGuid,
                'error'     => $exception->getMessage(),
            ]);
            return null;
        }
    }

    public function replace(string $identifier, string $contents): bool
    {
        $videoGuid = $this->extractGuid($identifier);
        if (!$videoGuid) {
            return false;
        }

        $tmpFile = tempnam(sys_get_temp_dir(), 'bunny_replace_');
        if ($tmpFile === false) {
            return false;
        }

        try {
            file_put_contents($tmpFile, $contents);
            $mimeType = function_exists('mime_content_type') ? mime_content_type($tmpFile) : null;
            $this->service->uploadVideoFromFile($videoGuid, $tmpFile, $mimeType);
            $this->logApp->info('BUNNY - Replaced video file.', ['videoGuid' => $videoGuid]);
            return true;
        } catch (\Throwable $exception) {
            $this->logError->error('BUNNY - Unable to replace video.', [
                'videoGuid' => $videoGuid,
                'error'     => $exception->getMessage(),
            ]);
            return false;
        } finally {
            @unlink($tmpFile);
        }
    }

    private function extractGuid(string $identifier): ?string
    {
        $identifier = trim($identifier);
        if ($identifier === '') {
            return null;
        }

        if (strpos($identifier, 'bunny://') === 0) {
            $identifier = substr($identifier, 8);
        }

        return $identifier ?: null;
    }
}
