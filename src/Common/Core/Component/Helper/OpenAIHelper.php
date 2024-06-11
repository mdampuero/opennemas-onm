<?php

namespace Common\Core\Component\Helper;

use GuzzleHttp\Client;

/**
 * Helper class to retrieve OpenAI data.
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
     * The service Key.
     *
     * @var Array
     */
    protected $defautlParams = [
        'model'             => 'gpt-3.5-turbo',
        'max_tokens'        => 50,
        'temperature'       => 1.1,
        'frequency_penalty' => 0.9,
        'presence_penalty'  => 0.9,
    ];

    /**
     * The service pricing.
     *
     * @var Array
     */
    protected $pricing = [
        'gpt-3.5-turbo' => [
            'input'  => 0.50,
            'output' => 1.50
        ],
        'gpt-4-turbo' => [
            'input'  => 10.00,
            'output' => 30.00
        ],
        'gpt-4o' => [
            'input'  => 5.00,
            'output' => 15.00
        ],
    ];

    /**
     * The service url.
     *
     * @var String
     */
    protected $openaiEndpoint = 'https://api.openai.com/v1/chat/completions';

    /**
     * The service Key.
     *
     * @var String
     */
    protected $openaiApiKey = 'sk-api-user-d3iXVlWZnWnKB9aiTD8OT3BlbkFJH7vBMP5vYhrHArmTLHUk';

     /**
     * Initializes the Menu service.
     *
     * @param Container          $container The service container.
     */
    public function __construct($container)
    {
        $this->container = $container;
        $this->client    = new Client();
    }

    public function sendMessage($messages, $params = [])
    {
        $data = array_merge($this->getConfig(), $params);

        $data['messages'] = [];

        if (array_key_exists('system', $messages) && !empty($messages['system'])) {
            $data['messages'][] = [
                'role' => 'system',
                'content' => $messages['system']
            ];
        }

        if (array_key_exists('user', $messages) && !empty($messages['user'])) {
            $data['messages'][] = [
                'role' => 'user',
                'content' => $messages['user']
            ];
        }

        $response = $this->client->request('POST', $this->openaiEndpoint, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->openaiApiKey
            ],
            'json' => $data
        ]);

        // Decodificar la respuesta JSON
        $response = json_decode($response->getBody(), true);

        $responseData = [];
        // Guardar la respuesta en otro campo de texto
        $responseData['message'] = isset($response['choices'][0]['message']['content'])
            ? $response['choices'][0]['message']['content']
            : '';

        $responseData['tokens'] = array_key_exists('usage', $response) && !empty($response['usage'])
            ? $response['usage']
            : [];

        return $responseData;
    }

    public function saveTokens($tokens)
    {
        $config = $this->getConfig();

        $currenTokens = $this->getTokens();

        $model = $config['model'] ?? 'unset';

        if (!array_key_exists($model, $currenTokens)) {
            $currenTokens[$model] = [];
        }

        foreach ($tokens as $tokenType => $tokenValue) {
            if (!array_key_exists($tokenType, $currenTokens[$model])) {
                $currenTokens[$model][$tokenType] = 0;
            }

            $currenTokens[$model][$tokenType] = is_numeric($currenTokens[$model][$tokenType])
                ? (int) $currenTokens[$model][$tokenType]
                : 0;

            $currenTokens[$model][$tokenType] += $tokenValue;
        }

        $this->container->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->set('openai_tokens', $currenTokens);
    }

    public function setDefaultParams($data)
    {
        foreach ($this->defautlParams as $key => $value) {
            if (array_key_exists($key, $data) && !empty($data[$key])) {
                $this->defautlParams[$key] = $data[$key];
            }
        }

        return $this;
    }

    public function getDafaultParams()
    {
        return $this->defautlParams;
    }

    public function getConfig()
    {
        $settings = $this->container->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('openai_config', []);

        if (empty($settings)) {
            //TODO: Get settings from manager
            $settings = $this->defautlParams;
        }

        foreach ($settings as $key => $value) {
            if (is_numeric($value)) {
                $settings[$key] = (float) $value;
            }
        }

        return $settings;
    }

    public function getTokens()
    {
        $tokens = $this->container->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('openai_tokens', []);

        return $tokens;
    }

    public function getPricing()
    {
        return $this->pricing;
    }

    public function getSpentMoney()
    {
        //Costs are per 1M tokens
        $conversion = 1000000;

        $tokens  = $this->getTokens();
        $pricing = $this->getPricing();

        $total = 0;

        foreach ($tokens as $modelName => $tokenInfo) {
            if (!array_key_exists($modelName, $pricing)) {
                continue;
            }

            $inputTokens = is_numeric($tokenInfo['prompt_tokens'])
                ? (float) $tokenInfo['prompt_tokens']
                : 0;

            $total += ($inputTokens * $pricing[$modelName]['input']) / $conversion;

            $outputTokens = is_numeric($tokenInfo['completion_tokens'])
                ? (float) $tokenInfo['completion_tokens']
                : 0;

            $total += ($outputTokens * $pricing[$modelName]['output']) / $conversion;
        }

        return $total;
    }
}
