<?php

namespace Common\Core\Component\Helper;

use GuzzleHttp\Client;

/**
 * Helper class to retrieve AI data and manage Gemini-related configurations.
 */
class GeminiHelper
{
    /**
     * The HTTP client for making API requests.
     *
     * @var Client
     */
    protected $client;

    /**
     * The service container for dependency injection.
     *
     * @var ServiceContainer
     */
    protected $container;

    /**
     * Maximum number of retries for API requests.
     *
     * @var int
     */
    protected $maxRetries = 3;

    /**
     * Delay in seconds between retries.
     *
     * @var int
     */
    protected $retryDelay = 2;

    /**
     * Request timeout in seconds.
     *
     * @var int
     */
    protected $timeout = 120;

    /**
     * The base endpoint URL for the Gemini API.
     *
     * @var string
     */
    protected $baseEndpoint = 'https://generativelanguage.googleapis.com/';

    /**
     * The chat endpoint path for the Gemini API.
     *
     * @var string
     */
    protected $endpointChat = 'v1beta/models/';

    /**
     * The API key for authentication with the Gemini service.
     *
     * @var string
     */
    protected $apiKey = 'AIzaSyDO2jmiEYuYz69oGMRroaFdzlfPMl2o064';

    /**
     * Safety settings configuration for the API.
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
     * Initializes the GeminiHelper service.
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
     * Get the safety settings configuration.
     *
     * @return array The safety settings.
     */
    public function getSafetySetting()
    {
        return $this->safetySetting;
    }

    /**
     * Sends a message to the Gemini API.
     *
     * @param array $data The data to be sent.
     * @param array $struct The structure to normalize the response.
     * @return array The normalized response.
     */
    public function sendMessage($data, $struct)
    {
        try {
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
     * Generates the payload structure for an API request.
     *
     * This method creates a structured payload array based on the provided data.
     * It includes the contents to send, generation configuration settings, and safety settings.
     *
     * @param array $data The input data containing:
     *  - `messages` (array): An array where the message content is extracted from index 1.
     *  - `settings` (array): Contains configuration options like:
     *      - `temperature` (float): Determines the randomness of the generation.
     *      - `max_tokens` (int): The maximum number of tokens to generate.
     *  - `top_p` (float|null): The nucleus sampling parameter (default: 0.9).
     *  - `top_k` (int|null): The top-k sampling parameter (default: 1).
     *
     * @return array The payload array structured for the API request.
     */
    public function generatePayload($data = [])
    {
        return [
            'contents' => [
                [
                    'parts' => [
                        [
                            'text' => $data['messages'][1]['content']
                        ]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature' => (float) $data['settings']['temperature'],
                'top_p' => $data['top_p'] ?? 0.9,
                'top_k' => $data['top_k'] ?? 1,
                'maxOutputTokens' => (int) $data['settings']['max_tokens'],
            ],
            'safetySettings' => $this->getSafetySetting()
        ];
    }

    /**
     * Normalizes the API response.
     *
     * @param array $originalResponse The original API response.
     * @param array $struct The structure to populate with normalized data.
     * @return array The normalized response.
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
     * Get the default settings for API requests.
     *
     * @return array The default settings.
     */
    public function getDefaultSettings()
    {
        return $this->defaultSettings;
    }

    /**
     * Set the default settings for API requests.
     *
     * @param array $defaultSettings The new default settings.
     * @return self
     */
    public function setDefaultSettings($defaultSettings)
    {
        $this->defaultSettings = $defaultSettings;
        return $this;
    }

    /**
     * Get the maximum number of retries for API requests.
     *
     * @return int The maximum retries.
     */
    public function getMaxRetries()
    {
        return $this->maxRetries;
    }

    /**
     * Get the timeout value for API requests.
     *
     * @return int The timeout in seconds.
     */
    public function getTimeout()
    {
        return $this->timeout;
    }
}
