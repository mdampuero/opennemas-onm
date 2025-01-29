<?php

namespace Common\Core\Component\Helper;

use Exception;
use GuzzleHttp\Client;

/**
 * Helper class to retrieve AI data.
 */
class OpenAIHelper
{
    /**
     * The HTTP client.
     *
     * @var Client
     */
    protected $client;

    /**
     * The service container.
     *
     * @var ServiceContainer
     */
    protected $container;

    /**
     * Maximum number of retries for requests.
     *
     * @var int
     */
    protected $maxRetries = 3;

    /**
     * Delay (in seconds) between retries.
     *
     * @var int
     */
    protected $retryDelay = 2;

    /**
     * Timeout (in seconds) for requests.
     *
     * @var int
     */
    protected $timeout = 120;

    /**
     * The base URL for the API.
     *
     * @var string
     */
    protected $baseEndpoint = 'https://api.openai.com';

    /**
     * The endpoint for chat completion requests.
     *
     * @var string
     */
    protected $endpointChat = '/v1/chat/completions';

    /**
     * Default settings for the API requests.
     *
     * @var array
     */
    protected $defaultSettings = [
        'temperature'       => 1,
        'max_tokens'        => 1000,
        'frequency_penalty' => 0.9,
        'presence_penalty'  => 0.9
    ];

    /**
     * Initializes the OpenAIHelper service.
     *
     * @param Container $container The service container.
     */
    public function __construct($container)
    {
        $this->container = $container;
        $this->client    = new Client([
            'timeout' => $this->getTimeout(),
        ]);
    }

    /**
     * Sends a message to the OpenAI API.
     *
     * @param array $data The payload data.
     * @param array $struct The structure to normalize the response into.
     * @return array The normalized response structure.
     */
    public function sendMessage($data, $struct)
    {
        try {
            for ($i = 0; $i < $this->getMaxRetries(); $i++) {
                try {
                    $payload = [
                        'messages'          => $data['messages'],
                        'model'             => $data['model'],
                        'temperature'       => (float) $data['settings']['temperature'],
                        'max_tokens'        => (int) $data['settings']['max_tokens'],
                        'frequency_penalty' => (float) $data['settings']['frequency_penalty'],
                        'presence_penalty'  => (float) $data['settings']['presence_penalty'],
                    ];

                    $response = $this->client->request('POST', $this->baseEndpoint . $this->endpointChat, [
                        'headers' => [
                            'Content-Type' => 'application/json',
                            'Authorization' => 'Bearer ' . $data['apiKey']
                        ],
                        'json' => $payload
                    ]);
                    $response = json_decode($response->getBody(), true);

                    // Simulated response for testing purposes
                    //$response = $this->simulResponse();

                    return $this->normalizeResponse($response, $struct);
                } catch (\Exception $e) {
                    if ($i === $this->getMaxRetries() - 1) {
                        throw $e;
                    }

                    sleep($this->retryDelay);
                }
            }
        } catch (\Exception $e) {
            $struct['error'] = $e->getMessage();
            return $struct;
        }
    }

    /**
     * Normalizes the API response into a predefined structure.
     *
     * @param array $originalResponse The original API response.
     * @param array $struct The structure to normalize the response into.
     * @return array The normalized response structure.
     */
    public function normalizeResponse($originalResponse, $struct)
    {
        if (isset($originalResponse['choices'][0]['message']['content'])) {
            $struct['result'] = $originalResponse['choices'][0]['message']['content'] ?? '';
            unset($originalResponse['choices'][0]['message']['content']);
        }

        if (isset($originalResponse['usage'])) {
            $struct['tokens']['input']  = $originalResponse['usage']['prompt_tokens'] ?? 0;
            $struct['tokens']['output'] = $originalResponse['usage']['completion_tokens'] ?? 0;
            $struct['tokens']['total']  = $originalResponse['usage']['total_tokens'] ?? 0;
        }

        $struct['error']    = null;
        $struct['original'] = $originalResponse;

        return $struct;
    }

    /**
     * Get the value of maxRetries.
     *
     * @return int
     */
    public function getMaxRetries()
    {
        return $this->maxRetries;
    }

    /**
     * Get the value of retryDelay.
     *
     * @return int
     */
    public function getRetryDelay()
    {
        return $this->retryDelay;
    }

    /**
     * Get the base URL for the API.
     *
     * @return string
     */
    public function getBaseEndpoint()
    {
        return $this->baseEndpoint;
    }

    /**
     * Get the chat endpoint URL.
     *
     * @return string
     */
    public function getEndpointChat()
    {
        return $this->endpointChat;
    }

    /**
     * Get the timeout for requests.
     *
     * @return int
     */
    public function getTimeout()
    {
        return $this->timeout;
    }

    /**
     * Get the default settings for API requests.
     *
     * @return array
     */
    public function getDefaultSettings()
    {
        return $this->defaultSettings;
    }

    /**
     * Set the default settings for API requests.
     *
     * @param array $defaultSettings The new settings.
     * @return self
     */
    public function setDefaultSettings($defaultSettings)
    {
        $this->defaultSettings = $defaultSettings;

        return $this;
    }

    /**
     * Simulates a response from the OpenAI API.
     *
     * @return array Simulated response data.
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
}
