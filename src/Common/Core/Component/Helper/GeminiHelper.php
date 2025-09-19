<?php

namespace Common\Core\Component\Helper;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ClientException;

class GeminiHelper
{
    /**
     * The application log service.
     *
     * @var Monolog
     */
    protected $appLog;

    /**
     * The error log service.
     *
     * @var Monolog
     */
    protected $errorLog;

    /**
     * HTTP client instance for making API requests.
     *
     * @var Client
     */
    protected $client;

    /**
     * Dependency injection container.
     *
     * @var mixed
     */
    protected $container;

    /**
     * Maximum number of retry attempts in case of failure.
     *
     * @var int
     */
    protected $maxRetries = 3;

    /**
     * Delay (in seconds) between retry attempts.
     *
     * @var int
     */
    protected $retryDelay = 2;

    /**
     * Request timeout (in seconds).
     *
     * @var int
     */
    protected $timeout = 120;

    /**
     * Base endpoint for the API.
     *
     * @var string
     */
    protected $baseEndpoint = 'https://generativelanguage.googleapis.com/';

    /**
     * Chat model endpoint for API requests.
     *
     * @var string
     */
    protected $endpointChat = 'v1beta/models/';

    // Specific endpoint for list models
    protected $endpointModels = '/v1/models';

    // Array for models
    protected $suggestedModels = [];

    // Array for data log
    protected $dataLog = [];

    /**
     * Safety settings configuration for content moderation.
     *
     * @var array
     */
    protected $safetySetting = [
        [
            'category' => "HARM_CATEGORY_HATE_SPEECH",
            'threshold' => "BLOCK_NONE"
        ],
        [
            'category' => "HARM_CATEGORY_SEXUALLY_EXPLICIT",
            'threshold' => "BLOCK_NONE"
        ],
        [
            'category' => "HARM_CATEGORY_HARASSMENT",
            'threshold' => "BLOCK_NONE"
        ],
        [
            'category' => "HARM_CATEGORY_DANGEROUS_CONTENT",
            'threshold' => "BLOCK_NONE"
        ]
    ];

    /**
     * Initializes the Gemini service.
     *
     * @param ContainerInterface $container The service container.
     * @param LoggerInterface    $appLog    Logger for application-level logs.
     * @param LoggerInterface    $errorLog  Logger for error logs.
     */
    public function __construct($container, $appLog, $errorLog)
    {
        $this->container = $container;
        $this->appLog    = $appLog;
        $this->errorLog  = $errorLog;
        $this->client    = new Client([
            'timeout' => $this->getTimeout(),
        ]);
    }

    /**
     * Retrieves the safety settings for content moderation.
     *
     * @return array Safety settings.
     */
    public function getSafetySetting()
    {
        return $this->safetySetting;
    }

    /**
     * Sends a message request to the API and handles retries in case of failures.
     *
     * @param array $data Request data including model and API key.
     * @param array $struct Structure to store the response.
     * @return array Response structure including results or error details.
     */
    public function sendMessage($data, $struct)
    {
        try {
            $request = $this->getDataLog();
            for ($i = 0; $i < $this->getMaxRetries(); $i++) {
                try {
                    $request = $this->client->request(
                        'POST',
                        $this->baseEndpoint . $this->endpointChat .
                            $data['model'] . ':generateContent?key=' . $data['apiKey'],
                        [
                            'json' => $this->generatePayload($data)
                        ]
                    );

                    $response = json_decode($request->getBody(), true);
                    $normalized = $this->normalizeResponse($response, $struct);
                    if (trim($normalized['result']) === 'ERROR') {
                        if ($i === $this->getMaxRetries() - 1) {
                            $normalized['error'] = 'ERROR';
                            $normalized['result'] = '';
                            return $normalized;
                        }
                        $this->errorLog->error(
                            'ONMAI - ERROR result - Retry ' . ($i + 1),
                            $request
                        );
                        sleep($this->retryDelay);
                        continue;
                    }

                    return $normalized;
                } catch (ClientException $e) {
                    if ($i === $this->getMaxRetries() - 1) {
                        throw $e;
                    }
                    $this->errorLog->error(
                        'ONMAI - ClientException - Retry ' . ($i + 1) . ': ' . $e->getMessage(),
                        $request
                    );

                    // Wait between retries
                    sleep($this->retryDelay);
                } catch (RequestException $e) {
                    if ($i === $this->getMaxRetries() - 1) {
                        throw $e;
                    }
                    $this->errorLog->error(
                        'ONMAI - RequestException - Retry ' . ($i + 1) . ': ' . $e->getMessage(),
                        $request
                    );

                    // Wait between retries
                    sleep($this->retryDelay);
                } catch (\Exception $e) {
                    if ($i === $this->getMaxRetries() - 1) {
                        throw $e;
                    }
                    $this->errorLog->error(
                        'ONMAI - Exception - Retry ' . ($i + 1) . ': ' . $e->getMessage(),
                        $request
                    );

                    // Wait between retries
                    sleep($this->retryDelay);
                }
            }
        } catch (Exception $e) {
            // Handle errors
            $struct['error'] = $e->getMessage();
            $this->errorLog->error('ONMAI - Exception - Final: ' . $e->getMessage(), $request);
            return $struct;
        }
    }

    /**
     * Generates the payload structure for the API request.
     *
     * @param array $data Request parameters including messages and settings.
     * @return array Structured payload for API request.
     */
    public function generatePayload($data = [])
    {
        $payload = [
            'contents' => [
                [
                    'parts' => [
                        [
                            'text' => $data['messages'][0]['content']
                        ]
                    ]
                ]
            ],
            'generationConfig' => [],
            'safetySettings' => $this->getSafetySetting()
        ];

        if ($data['meta']['params'] ?? false) {
            foreach ($data['meta']['params'] as $param) {
                $payload['generationConfig'][$param['key']] = $param['value'];
            }
        }

        return $payload;
    }

    /**
     * Normalizes the API response to extract relevant information.
     *
     * @param array $originalResponse Raw API response.
     * @param array $struct Structure to store the normalized response.
     * @return array Normalized response structure.
     */
    public function normalizeResponse($originalResponse, $struct)
    {
        if (isset($originalResponse['candidates'][0]['content']['parts'][0]['text'])) {
            $struct['result'] = $originalResponse['candidates'][0]['content']['parts'][0]['text'] ?? '';
            unset($originalResponse['candidates'][0]['content']['parts'][0]['text']);
        }

        if (isset($originalResponse['usageMetadata'])) {
            $struct['tokens']['input']  = $originalResponse['usageMetadata']['promptTokenCount'] ?? 0;
            $struct['tokens']['output'] = $originalResponse['usageMetadata']['candidatesTokenCount'] ?? 0;
            $struct['tokens']['total']  = $originalResponse['usageMetadata']['totalTokenCount'] ?? 0;
        }

        $struct['error']    = null;
        $struct['original'] = $originalResponse;

        return $struct;
    }

    /**
     * Retrieves the maximum number of retry attempts.
     *
     * @return int Maximum retry count.
     */
    public function getMaxRetries()
    {
        return $this->maxRetries;
    }

    /**
     * Retrieves the request timeout duration.
     *
     * @return int Timeout in seconds.
     */
    public function getTimeout()
    {
        return $this->timeout;
    }

    /**
     * Get the value of suggestedModels
     */
    public function getSuggestedModels($data)
    {
        return [];
    }

    /**
     * Get the value of dataLog
     */
    public function getDataLog()
    {
        return [
            'request' => $this->dataLog
        ];
    }

    /**
     * Set the value of dataLog
     */
    public function setDataLog($dataLog)
    {
        $this->dataLog = $dataLog;
    }
}
