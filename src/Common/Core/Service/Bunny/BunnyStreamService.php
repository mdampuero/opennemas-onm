<?php

/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Common\Core\Service\Bunny;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

/**
 * Service helper for interacting with the Bunny Stream API.
 *
 * Fill in the LIBRARY_ID and API_KEY constants with your Bunny Stream credentials
 * before using the service in production environments.
 */
class BunnyStreamService
{
    /**
     * Bunny Stream API base URL.
     */
    public const API_BASE_URL = 'https://video.bunnycdn.com';

    /**
     * Bunny Stream embed base URL.
     */
    public const EMBED_BASE_URL = 'https://iframe.mediadelivery.net/embed';

    /**
     * Bunny Stream library identifier.
     */
    public const LIBRARY_ID = '513420';

    /**
     * Bunny Stream API key.
     */
    public const API_KEY = '38fab01c-6ec1-4a68-b9935990ab12-aa91-4ac8';

    private $client;
    private $apiBaseUrl;
    private $embedBaseUrl;
    private $libraryId;
    private $apiKey;

    public function __construct()
    {
        $this->applyConfiguration([
            'api_base_url'   => self::API_BASE_URL,
            'embed_base_url' => self::EMBED_BASE_URL,
            'library_id'     => self::LIBRARY_ID,
            'api_key'        => self::API_KEY,
        ]);
    }

    /**
     * Ensures the mandatory configuration values are filled in.
     */
    public function assertConfigured(): void
    {
        $this->getLibraryId();
        $this->getApiKey();
    }

    public function configure(array $config): void
    {
        $this->applyConfiguration([
            'api_base_url'   => $config['api_base_url'] ?? $this->apiBaseUrl,
            'embed_base_url' => $config['embed_base_url'] ?? $this->embedBaseUrl,
            'library_id'     => $config['library_id'] ?? $this->libraryId,
            'api_key'        => $config['api_key'] ?? $this->apiKey,
        ]);
    }

    /**
     * Creates a video placeholder on Bunny Stream.
     *
     * @throws \RuntimeException When the API call fails.
     */
    public function createVideo(string $title): array
    {
        try {
            $response = $this->client->post(
                sprintf('/library/%s/videos', $this->getLibraryId()),
                [
                    'headers' => $this->getJsonHeaders(),
                    'json'    => ['title' => $title],
                ]
            );
        } catch (GuzzleException $exception) {
            throw new \RuntimeException(
                sprintf('Unable to create the Bunny Stream video: %s', $exception->getMessage()),
                0,
                $exception
            );
        }

        return json_decode((string) $response->getBody(), true) ?: [];
    }

    /**
     * Uploads the binary contents of a video file to Bunny Stream.
     *
     * @throws \RuntimeException When the API call fails or the file cannot be read.
     */
    public function uploadVideoFromFile(string $videoGuid, string $filePath, ?string $mimeType = null): void
    {
        $handle = fopen($filePath, 'rb');

        if (false === $handle) {
            throw new \RuntimeException(sprintf('Unable to open "%s" for reading.', $filePath));
        }

        try {
            $this->client->put(
                sprintf('/library/%s/videos/%s', $this->getLibraryId(), $videoGuid),
                [
                    'headers' => [
                        'AccessKey'    => $this->getApiKey(),
                        'Content-Type' => $mimeType ?: 'application/octet-stream',
                    ],
                    'body'    => $handle,
                ]
            );
        } catch (GuzzleException $exception) {
            throw new \RuntimeException(
                sprintf('Unable to upload the video file: %s', $exception->getMessage()),
                0,
                $exception
            );
        } finally {
            fclose($handle);
        }
    }

    /**
     * Fetches the details of the uploaded video.
     *
     * @throws \RuntimeException When the API call fails.
     */
    public function fetchVideo(string $videoGuid): array
    {
        try {
            $response = $this->client->get(
                sprintf('/library/%s/videos/%s', $this->getLibraryId(), $videoGuid),
                ['headers' => $this->getJsonHeaders()]
            );
        } catch (GuzzleException $exception) {
            throw new \RuntimeException(
                sprintf('Unable to fetch the video details: %s', $exception->getMessage()),
                0,
                $exception
            );
        }

        return json_decode((string) $response->getBody(), true) ?: [];
    }

    /**
     * Builds the playback URL for a given Bunny Stream video GUID.
     */
    public function getEmbedUrl(string $playbackGuid): string
    {
        return sprintf('%s/%s/%s', rtrim($this->embedBaseUrl, '/'), $this->getLibraryId(), $playbackGuid);
    }

    public function deleteVideo(string $videoGuid): void
    {
        try {
            $this->client->delete(
                sprintf('/library/%s/videos/%s', $this->getLibraryId(), $videoGuid),
                ['headers' => ['AccessKey' => $this->getApiKey()]]
            );
        } catch (GuzzleException $exception) {
            throw new \RuntimeException(
                sprintf('Unable to delete the Bunny Stream video: %s', $exception->getMessage()),
                0,
                $exception
            );
        }
    }

    private function getLibraryId(): string
    {
        $libraryId = trim($this->libraryId);

        if ($libraryId === '') {
            throw new \RuntimeException('Bunny Stream library ID is not configured. Set it in the storage settings.');
        }

        return $libraryId;
    }

    private function getApiKey(): string
    {
        $apiKey = trim($this->apiKey);

        if ($apiKey === '') {
            throw new \RuntimeException('Bunny Stream API key is not configured. Set it in the storage settings.');
        }

        return $apiKey;
    }

    private function getJsonHeaders(): array
    {
        return [
            'AccessKey'    => $this->getApiKey(),
            'Accept'       => 'application/json',
            'Content-Type' => 'application/json',
        ];
    }

    private function applyConfiguration(array $config): void
    {
        $this->apiBaseUrl   = rtrim($config['api_base_url'] ?? self::API_BASE_URL, '/');
        $this->embedBaseUrl = rtrim($config['embed_base_url'] ?? self::EMBED_BASE_URL, '/');
        $this->libraryId    = (string) ($config['library_id'] ?? '');
        $this->apiKey       = (string) ($config['api_key'] ?? '');

        $this->client = new Client([
            'base_uri' => $this->apiBaseUrl,
            'timeout'  => 600000,
        ]);
    }
}
