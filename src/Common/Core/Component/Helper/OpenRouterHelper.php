<?php

namespace Common\Core\Component\Helper;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ClientException;

class OpenRouterHelper
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

    // HTTP client instance for making requests
    protected $client;

    // Container to store dependencies
    protected $container;

    // Maximum number of retries in case of failure
    protected $maxRetries = 3;

    // Delay between retries in seconds
    protected $retryDelay = 2;

    // Maximum timeout for a request in seconds
    protected $timeout = 120;

    // Base URL for the OpenRouter API
    protected $baseEndpoint = 'https://openrouter.ai/api';

    // Specific endpoint for chat completions
    protected $endpointChat = '/v1/chat/completions';

    // Specific endpoint for list models
    protected $endpointModels = '/v1/models';

    // Array for models
    protected $suggestedModels = [];

    // Array for data log
    protected $dataLog = [];

    /**
     * Initializes the OpenRouter service.
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
     * Sends a message to the OpenRouter API.
     *
     * @param array $data Data required to send to the API, such as the message and API key.
     * @param array $struct Response structure to store the result.
     * @return array Structure with the result or error.
     */
    public function sendMessage($data, $struct)
    {
        try {
            $request = $this->getDataLog();
            // Try the request up to $maxRetries times
            for ($i = 0; $i < $this->getMaxRetries(); $i++) {
                try {
                    // Build the data to send

                    $payload = [
                        'messages'          => $data['messages'],
                        'model'             => $data['model']
                    ];

                    if ($data['meta']['params'] ?? false) {
                        foreach ($data['meta']['params'] as $param) {
                            $payload[$param['key']] = $param['value'];
                        }
                    }

                    // Make the POST request to the OpenRouter API
                    $response = $this->client->request('POST', $this->baseEndpoint . $this->endpointChat, [
                        'headers' => [
                            'Content-Type' => 'application/json',
                            'Authorization' => 'Bearer ' . $data['apiKey']
                        ],
                        'json' => $payload
                    ]);
                    $response = json_decode($response->getBody(), true);
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
     * Normalizes the response from the API to fit the expected structure.
     *
     * @param array $originalResponse The original response from the OpenRouter API.
     * @param array $struct The structure that will store the results.
     * @return array Normalized structure with the result and token usage.
     */
    public function normalizeResponse($originalResponse, $struct)
    {
        // Extract the message content from the OpenRouter response
        if (isset($originalResponse['choices'][0]['message']['content'])) {
            $struct['result'] = $originalResponse['choices'][0]['message']['content'] ?? '';
            unset($originalResponse['choices'][0]['message']['content']);
        }

        // Extract token usage information
        if (isset($originalResponse['usage'])) {
            $struct['tokens']['input']  = $originalResponse['usage']['prompt_tokens'] ?? 0;
            $struct['tokens']['output'] = $originalResponse['usage']['completion_tokens'] ?? 0;
            $struct['tokens']['total']  = $originalResponse['usage']['total_tokens'] ?? 0;
        }

        // Set error to null and store the original response
        $struct['error']    = null;
        $struct['original'] = $originalResponse;

        return $struct;
    }

    /**
     * Gets the maximum number of retries.
     *
     * @return int Maximum number of retries.
     */
    public function getMaxRetries()
    {
        return $this->maxRetries;
    }

    /**
     * Gets the delay between retries.
     *
     * @return int Delay in seconds.
     */
    public function getRetryDelay()
    {
        return $this->retryDelay;
    }

    /**
     * Gets the base endpoint for the OpenRouter API.
     *
     * @return string Base URL for the API.
     */
    public function getBaseEndpoint()
    {
        return $this->baseEndpoint;
    }

    /**
     * Gets the chat endpoint for the OpenRouter API.
     *
     * @return string Chat completion endpoint.
     */
    public function getEndpointChat()
    {
        return $this->endpointChat;
    }

    /**
     * Gets the maximum timeout for a request.
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
        try {
            $response = $this->client->request('GET', $this->baseEndpoint . $this->endpointModels, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $data['apiKey']
                ]
            ]);

            $models = json_decode($response->getBody(), true);

            $ids = array_column($models['data'] ?? [], 'id');

            sort($ids);

            return $ids;
        } catch (ClientException $e) {
            $this->suggestedModels = [];
        } catch (RequestException $e) {
            $this->suggestedModels = [];
        } catch (\Exception $e) {
            $this->suggestedModels = [];
        }

        return $this->suggestedModels;
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
