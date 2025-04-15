<?php

namespace Common\Core\Component\Helper;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ClientException;

class OpenAIHelper
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

    // Base URL for the OpenAI API
    protected $baseEndpoint = 'https://api.openai.com';

    // Specific endpoint for chat completions
    protected $endpointChat = '/v1/chat/completions';

    // Specific endpoint for list models
    protected $endpointModels = '/v1/models';

    // Array for models
    protected $suggestedModels = [];

    /**
     * Initializes the OpenAI service.
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
     * Sends a message to the OpenAI API.
     *
     * @param array $data Data required to send to the API, such as the message and API key.
     * @param array $struct Response structure to store the result.
     * @return array Structure with the result or error.
     */
    public function sendMessage($data, $struct)
    {
        try {
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

                    // Make the POST request to the OpenAI API
                    $response = $this->client->request('POST', $this->baseEndpoint . $this->endpointChat, [
                        'headers' => [
                            'Content-Type' => 'application/json',
                            'Authorization' => 'Bearer ' . $data['apiKey']
                        ],
                        'json' => $payload
                    ]);
                    $response = json_decode($response->getBody(), true);

                    // Simulated response for testing purposes (optional)
                    //$response = $this->simulResponse();

                    return $this->normalizeResponse($response, $struct);
                } catch (ClientException $e) {
                    if ($i === $this->getMaxRetries() - 1) {
                        throw $e;
                    }
                    $this->errorLog->error('ONMAI - ClientException - Retry ' . ($i + 1) . ': ' . $e->getMessage());

                    // Wait between retries
                    sleep($this->retryDelay);
                } catch (RequestException $e) {
                    if ($i === $this->getMaxRetries() - 1) {
                        throw $e;
                    }
                    $this->errorLog->error('ONMAI - RequestException - Retry ' . ($i + 1) . ': ' . $e->getMessage());

                    // Wait between retries
                    sleep($this->retryDelay);
                } catch (\Exception $e) {
                    if ($i === $this->getMaxRetries() - 1) {
                        throw $e;
                    }
                    $this->errorLog->error('ONMAI - Exception - Retry ' . ($i + 1) . ': ' . $e->getMessage());

                    // Wait between retries
                    sleep($this->retryDelay);
                }
            }
        } catch (Exception $e) {
            // Handle errors
            $struct['error'] = $e->getMessage();
            $this->errorLog->error('ONMAI - Exception - Final: ' . $e->getMessage());
            return $struct;
        }
    }

    /**
     * Normalizes the response from the API to fit the expected structure.
     *
     * @param array $originalResponse The original response from the OpenAI API.
     * @param array $struct The structure that will store the results.
     * @return array Normalized structure with the result and token usage.
     */
    public function normalizeResponse($originalResponse, $struct)
    {
        // Extract the message content from the OpenAI response
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
     * Gets the base endpoint for the OpenAI API.
     *
     * @return string Base URL for the API.
     */
    public function getBaseEndpoint()
    {
        return $this->baseEndpoint;
    }

    /**
     * Gets the chat endpoint for the OpenAI API.
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
     * Simulates a response from the OpenAI API for testing purposes.
     *
     * @return array Simulated response.
     */
    public function simulResponse()
    {
        return [
            'id' => 'chatcmpl-AtDf5dAEXRCkkKo2Z5pQttSf3vWVd',
            'object' => 'chat.completion',
            'created' => 1737723919,
            'model' => 'gpt-4-turbo-2024-04-09',
            'choices' => [
                [
                    'index' => 0,
                    'message' => [
                        'role' => 'assistant',
                        'content' => '<p>Explore the <strong>latest advancements</strong> in technology </p>',
                        'refusal' => null
                    ],
                    'logprobs' => null,
                    'finish_reason' => 'stop'
                ]
            ],
            'usage' => [
                'prompt_tokens' => 475,
                'completion_tokens' => 45,
                'total_tokens' => 520
            ]
        ];
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
}
