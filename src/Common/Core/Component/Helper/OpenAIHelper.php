<?php

namespace Common\Core\Component\Helper;

use DateTime;
use GuzzleHttp\Client;
use SebastianBergmann\Environment\Console;

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
    protected $openaiApiKey;

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
        /*return array(
            "message" => "La Segunda Guerra Mundial fue un conflicto que tuvo lugar entre 1939 y 1945, en el que se
             enfrentaron dos bloques de países: las potencias del Eje, lideradas por Alemania, Italia",
            "tokens" => array(
                "prompt_tokens" => 14,
                "completion_tokens" => 50,
                "total_tokens" => 64,
                "prompt_tokens_details" => array(
                    "cached_tokens" => 0
                ),
                "completion_tokens_details" => array(
                    "reasoning_tokens" => 0
                )
            )
        );*/

        $data = array_merge($this->getConfig(), $params);

        if (empty($this->openaiApiKey)) {
            return [
                'error' => 'API key is missing'
            ];
        }

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

        $responseData = [];

        try {
            $response = $this->client->request('POST', $this->openaiEndpoint, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $this->openaiApiKey
                ],
                'json' => $data
            ]);

            $response = json_decode($response->getBody(), true);

            $responseData['message'] = isset($response['choices'][0]['message']['content'])
                ? $response['choices'][0]['message']['content']
                : '';

            $responseData['tokens'] = array_key_exists('usage', $response) && !empty($response['usage'])
                ? $response['usage']
                : [];

            $this->saveAction($data, $response);
        } catch (\Exception $e) {
            $responseData['error'] = $e->getMessage();
        }

        return $responseData;
    }


    protected function saveAction($params, $response)
    {
        $messages     = $params['messages'] ?? [];
        $responseData = isset($response['choices'][0]['message']['content'])
            ? $response['choices'][0]['message']['content']
            : '';

        $tokens = array_key_exists('usage', $response) && !empty($response['usage'])
            ? $response['usage']
            : [];

        $date = new DateTime('now');

        unset($params['messages']);

        $data = [
            'messages' => $messages,
            'response' => $responseData,
            'tokens' => $tokens,
            'params' => $params,
            'date' => $date->format('Y-m-d H:i:s')
        ];

        $this->container->get('api.service.ai')->createItem($data);
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
            if (!is_numeric($tokenValue)) {
                continue;
            }

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

        $provider = $this->container->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('openai_service');

        $credentials = $this->container->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('openai_credentials', []);

        if ($provider === 'custom') {
            $this->openaiApiKey = $credentials['apikey'];
        } else {
            $this->openaiApiKey = $this->container->getParameter('opennemas.openai.key');
        }

        if (empty($settings)) {
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

    public function getTokensMonthly()
    {
        $date       = new DateTime();
        $currentDay = (int) $date->format('d');

        $startDate = new DateTime();
        $endDate   = new DateTime();

        if ($currentDay < 27) {
            $startDate->modify('first day of last month')
                ->setDate($date->format('Y'), $date->format('m') - 1, 27)
                ->setTime(0, 0, 0);
            $endDate->setDate($date->format('Y'), $date->format('m'), 27)
                ->setTime(0, 0, 0);
        } else {
            $startDate->setDate($date->format('Y'), $date->format('m'), 27)
                ->setTime(0, 0, 0);
            $endDate->modify('first day of next month')
                ->setDate($date->format('Y'), $date->format('m') + 1, 27)
                ->setTime(0, 0, 0);
        }

        $oql = sprintf(
            "date >= '%s' and date < '%s'",
            $startDate->format('Y-m-d H:i:s'),
            $endDate->format('Y-m-d H:i:s')
        );

        $result = $this->container->get('api.service.ai')->getList($oql);

        return $result;
    }


    public function getPricing()
    {
        return $this->pricing;
    }

    public function getSpentMoney()
    {
        $conversion = 1000000;

        $tokens  = $this->getTokensMonthly();
        $pricing = $this->getPricing();

        $total = 0;

        foreach ($tokens['items'] as $tokenInfo) {
            $model            = $tokenInfo->getData()['params']['model'];
            $promptTokens     = $tokenInfo->getData()['tokens']['prompt_tokens'];
            $completionTokens = $tokenInfo->getData()['tokens']['completion_tokens'];

            if (isset($pricing[$model])) {
                $inputPrice  = isset($pricing[$model]['input']) ? $pricing[$model]['input'] : 0;
                $outputPrice = isset($pricing[$model]['output']) ? $pricing[$model]['output'] : 0;

                $total += ($promptTokens * $inputPrice + $completionTokens * $outputPrice) / $conversion;
            }
        }

        $total = round($total, 15);

        return $total;
    }
}
