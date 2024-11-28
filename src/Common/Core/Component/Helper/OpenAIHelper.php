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
        'temperature'       => 0.5,
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

    protected $instructions = [];

    /**
     * The service url base.
     *
     * @var String
     */
    protected $openaiEndpointBase = 'https://api.openai.com';

    /**
     * The service url chat.
     *
     * @var String
     */
    protected $endpointChat = '/v1/chat/completions';

    /**
     * The service url models.
     *
     * @var String
     */
    protected $endpointModels = '/v1/models';

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

    public function getInstructions()
    {
        $this->instructions = $this->container->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('openai_instructions', []);
        return $this->instructions;
    }

    public function addInstruction($instruction)
    {
        $instructions = $this->getInstructions();
        array_push($instructions, $instruction);
        $this->setInstructions($instructions);
    }

    public function setInstructions($instructions)
    {
        $this->instructions = $instructions;
        return $this;
    }

    public function getInstructionsByFilter(array $filter = []): array
    {
        $instructions = $this->getInstructions();

        return $filter
            ? array_filter($instructions, function ($i) use ($filter) {
                return in_array($i['type'], $filter);
            })
            : $instructions;
    }

    protected function insertInstructions($prompt = '', $instructions = [])
    {
        if (count($instructions)) {
            return $prompt . sprintf(' (ten en cuenta estas instrucciones: %s)', implode(', ', array_map(
                function ($item) {
                    return $item['value'];
                },
                $instructions
            )));
        }
        return $prompt;
    }

    public function generatePrompt($messages)
    {
        $prompt = $this->insertInstructions($messages['promptInput'], $this->getInstructionsByFilter([
            'Both',
            $messages['promptSelected']['mode']
        ]));
        if ($messages['promptSelected']['mode'] == 'New') {
            $prompt .= ($messages["input"] ?? false) ? sprintf(', Tema: "%s"', $messages["input"]) : '';
        } elseif ($messages['promptSelected']['mode'] == 'Edit') {
            $prompt .= ($messages["input"] ?? false) ? sprintf(', Este es el texto: "%s"', $messages["input"]) : '';
        }
        return $prompt;
    }

    public function sendMessage($messages, $params = [])
    {
        $data = array_merge($this->getConfig(), $params);

        if (empty($this->openaiApiKey)) {
            return [
                'error' => 'API key is missing'
            ];
        }

        if ($messages["toneSelected"]["name"] ?? false) {
            $this->addInstruction([
                "type" => "Both",
                "value" => sprintf('Utiliza un tono: "%s"', $messages["toneSelected"]["name"])
            ]);
        }

        $data['messages'] = [];

        if ($messages["roleSelected"]["prompt"] ?? false) {
            $data['messages'][] = [
                'role' => 'system',
                'content' => $messages["roleSelected"]["prompt"]
            ];
        }

        if ($messages["input"] ?? false) {
            $data['messages'][] = [
                'role' => 'user',
                'content' => $this->generatePrompt($messages)
            ];
        }

        $responseData = [];

        try {
            // return array(
            //     "request" => $messages,
            //     "data" => $data,
            //     "message" => "La Segunda Guerra Mundial: La Madre de Todas las Batallas (y de los Rankings)",
            //     "tokens" => array(
            //         "prompt_tokens" => 14,
            //         "completion_tokens" => 50,
            //         "total_tokens" => 64,
            //         "prompt_tokens_details" => array(
            //             "cached_tokens" => 0
            //         ),
            //         "completion_tokens_details" => array(
            //             "reasoning_tokens" => 0
            //         )
            //     )
            // );
            $response = $this->client->request('POST', $this->openaiEndpointBase . $this->endpointChat, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $this->openaiApiKey
                ],
                'json' => $data
            ]);
            $response = json_decode($response->getBody(), true);

            $responseData['message'] = isset($response['choices'][0]['message']['content'])
                ? $this->removeQuotesAndPeriod($response['choices'][0]['message']['content'])
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

    public function checkApiKey($apiKey)
    {
        $this->client->request('GET', $this->openaiEndpointBase . $this->endpointModels, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $apiKey
            ]
        ]);
        return true;
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
            'tokens'   => $tokens,
            'params'   => $params,
            'date'     => $date->format('Y-m-d H: i: s')
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

        $this->setInstructions($this->container->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('openai_instructions', []));

        if ($provider === 'custom') {
            $this->openaiApiKey = $credentials['apikey'];
        } else {
            // $this->openaiApiKey = $this->container->getParameter('opennemas.openai.key');
            $this->openaiApiKey = '';
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

    public function removeQuotesAndPeriod($string)
    {
        $string = trim($string, '"');

        if (substr($string, -1) === '.') {
            $string = rtrim($string, '.');
        }

        return $string;
    }

    public function getTones()
    {
        $tones = $this->container->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('openai_tones', []);

        return $tones;
    }

    public function setTones($tones = [])
    {
        $this->container->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->set('openai_tones', $tones);
        return $this;
    }

    public function getInputTypes()
    {
        $inputTypes = $this->container->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('openai_input_types', []);

        return $inputTypes;
    }

    public function setInputTypes($inputTypes = [])
    {
        $this->container->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->set('openai_input_types', $inputTypes);
        return $this;
    }

    public function getRoles()
    {
        $roles = $this->container->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('openai_roles', []);

        return $roles;
    }

    public function setRoles($roles = [])
    {
        $this->container->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->set('openai_roles', $roles);
        return $this;
    }

    public function getModes()
    {
        $modes = $this->container->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('openai_modes', []);

        return $modes;
    }

    public function getInstructionTypes()
    {
        $instructionTypes = $this->container->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('openai_instruction_types', []);

        return $instructionTypes;
    }

    public function setInstructionTypes($instructionTypes)
    {
        $this->container->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->set('openai_instruction_types', $instructionTypes);
        return $this;
    }
}
