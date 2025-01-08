<?php

namespace Common\Core\Component\Helper;

use DateTime;
use Exception;
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
        'model'             => 'gpt-4o-mini',
        'max_tokens'        => 50,
        'temperature'       => 0.5,
        'frequency_penalty' => 0.9,
        'presence_penalty'  => 0.9,
    ];

    public $map = [
        "openai_service" => "string",
        "openai_credentials" => [
            "apikey" => "string",
        ],
        "openai_roles" => [
            [
                "name" => "string",
                "prompt" => "string",
            ],
        ],
        "openai_tones" => [
            [
                "name" => "string",
                "description" => "string",
            ],
        ],
        "openai_config" => [
            "model" => "string",
            "max_tokens" => "integer",
            "temperature" => "float",
            "frequency_penalty" => "float",
            "presence_penalty" => "float",
        ],
    ];

    /**
     * The service pricing.
     *
     * @var Array
     */
    protected $pricing = [
        'gpt-4o-mini' => [
            'input'  => 0.15,
            'output' => 0.60
        ],
        'gpt-4-turbo' => [
            'input'  => 10.00,
            'output' => 30.00
        ]
    ];

    protected $service;

    protected $credentials = [];

    protected $instructions = [];

    protected $openaiConfig = [];

    protected $userPrompt = '';

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
            ->getDataSet('Settings', 'manager')
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
        return $filter
            ? array_filter($this->instructions, function ($i) use ($filter) {
                return in_array($i['type'], $filter['type']) && in_array($i['field'], $filter['field']);
            })
            : $this->instructions;
    }

    protected function insertInstructions($instructions = [])
    {
        $instructionsString = '';
        if (count($instructions)) {
            $counter = 0;

            $instructionList = implode("\n", array_map(
                function ($index, $item) use (&$counter) {
                    $counter++;
                    return $counter . '. ' . $item['value'];
                },
                array_keys($instructions),
                $instructions
            ));

            $instructionsString = sprintf("### INSTRUCCIONES:\n%s", $instructionList);
        }
        $this->userPrompt .= $instructionsString;
    }

    protected function insertTone($messages = [])
    {
        if ($messages["toneSelected"]["name"] ?? false) {
            $this->userPrompt .= sprintf("\n\n### TONO:\n%s", $messages["toneSelected"]["name"]);
        }
    }

    public function generatePrompt($messages)
    {
        $this->insertInstructions($this->getInstructionsByFilter(
            [
                'type'  => ['Both', $messages['promptSelected']['mode']],
                'field' => ['all', $messages['promptSelected']['field_or']],
            ]
        ));

        if ($messages['promptSelected']['mode_or'] == 'New') {
            $this->userPrompt .= ($messages["input"] ?? false) ? sprintf("\n\n### TEMA:\n%s", $messages["input"]) : "";
        } elseif ($messages['promptSelected']['mode_or'] == 'Edit') {
            $this->userPrompt .= ($messages["input"] ?? false) ? sprintf("\n\n### TEXTO:\n%s", $messages["input"]) : '';
        }
        $this->insertTone($messages);

        $this->userPrompt .= sprintf("\n\n### CONTENIDO:\n%s", $messages["promptInput"]);

        return $this->userPrompt;
    }

    public function sendMessage($messages, $params = [])
    {
        $data = array_merge($this->getConfig(), $params);

        if ($messages["locale"] ?? false) {
            $this->addInstruction([
                'type' => 'Both',
                'field' => 'all',
                'value' => sprintf(
                    'El idioma configurado es "%s". Responde usando este idioma y las convenciones culturales.',
                    $messages['locale']
                )
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
            $response = $this->client->request('POST', $this->openaiEndpointBase . $this->endpointChat, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $this->getApiKey()
                ],
                'json' => $data
            ]);
            $response = json_decode($response->getBody(), true);

            $responseData["request"] = $data;

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

    public function getModels()
    {
        try {
            $models = $this->pricing;
            $data   = [];
            foreach ($models as $key => $model) {
                $data[] = [
                    'id' => $key
                ];
            }
            return $data;
        } catch (Exception $e) {
            return [['id' => 'gpt-4o-mini']];
        }
    }

    public function getModelsFromApi()
    {
        try {
            $response = $this->client->request('GET', $this->openaiEndpointBase . $this->endpointModels, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $this->getApiKey()
                ]
            ]);

            $body   = $response->getBody()->getContents();
            $models = json_decode($body, true);

            $textModels = array_filter($models["data"], function ($model) {
                return preg_match('/^(gpt-)/', $model['id']);
            });
            return array_values($textModels);
        } catch (Exception $e) {
            return [['id' => 'gpt-4o-mini']];
        }
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
            'date'     => $date->format('Y-m-d H:i:s')
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

    protected function getApiKey()
    {
        $credentials = $this->getCredentials();
        $provider    = $this->getService();

        if ($provider === 'custom') {
            $this->openaiApiKey = $credentials['apikey'];
        } else {
            // $this->openaiApiKey = $this->container->getParameter('opennemas.openai.key');
            $this->openaiApiKey = '';
        }
        return $this->openaiApiKey;
    }

    public function getConfig()
    {
        $settings = $this->getOpenaiConfig();

        $this->setInstructions($this->container->get('orm.manager')
            ->getDataSet('Settings', 'manager')
            ->get('openai_instructions', []));

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

    public function getTones($showManager = true)
    {
        $si = $this->container->get('orm.manager')->getDataSet('Settings', 'instance');
        $tm = [];
        if ($showManager) {
            $sm = $this->container->get('orm.manager')->getDataSet('Settings', 'manager');
            $tm = $this->addFlagReadOnly($sm->get('openai_tones', []));
        }

        return $this->sortByName(array_merge($tm, $si->get('openai_tones', [])));
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

    public function getRoles($showManager = true)
    {
        $si = $this->container->get('orm.manager')->getDataSet('Settings', 'instance');
        $rm = [];
        if ($showManager) {
            $sm = $this->container->get('orm.manager')->getDataSet('Settings', 'manager');
            $rm = $this->addFlagReadOnly($sm->get('openai_roles', []));
        }

        return $this->sortByName(array_merge($rm, $si->get('openai_roles', [])));
    }

    protected function sortByName($array)
    {
        usort($array, function ($a, $b) {
            $nameA = trim(strtolower($a['name'] ?? ''));
            $nameB = trim(strtolower($b['name'] ?? ''));
            return strcmp($nameA, $nameB);
        });
        return $array;
    }

    protected function addFlagReadOnly($array)
    {
        return array_map(function ($item) {
            $item['readOnly'] = true;
            return $item;
        }, $array);
    }

    public function preSave($array)
    {
        foreach ($array as $key => $item) {
            if ($item['readOnly'] ?? false && $item['readOnly'] === true) {
                unset($array[$key]);
                continue;
            }
            if ($item["name"] ?? false) {
                $array[$key]["name"] = substr($item["name"], 0, 64);
            }
            if ($item["prompt"] ?? false) {
                $array[$key]["prompt"] = substr($item["prompt"], 0, 2048);
            }
        }
        return $array;
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

    public function getConfigAll()
    {
        return [
            'openai_service'      => $this->getService(),
            'openai_credentials'  => $this->getCredentials(),
            'openai_roles'        => $this->getRoles(false),
            'openai_tones'        => $this->getTones(false),
            'openai_config'       => $this->getConfig()
        ];
    }


    /**
     * Get the value of service
     */
    public function getService()
    {
        return $this->container->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('openai_service');
    }

    /**
     * Set the value of service
     *
     * @return  self
     */
    public function setService($service)
    {
        $this->service = $service;

        return $this;
    }

    /**
     * Get the value of credentials
     */
    public function getCredentials()
    {
        return $this->container->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('openai_credentials', []);
    }

    /**
     * Set the value of credentials
     *
     * @return  self
     */
    public function setCredentials($credentials)
    {
        $this->credentials = $credentials;

        return $this;
    }

    /**
     * Get the value of openaiConfig
     */
    public function getOpenaiConfig()
    {
        return $this->container->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('openai_config', []);
    }

    /**
     * Set the value of openaiConfig
     *
     * @return  self
     */
    public function setOpenaiConfig($openaiConfig)
    {
        $this->openaiConfig = $openaiConfig;

        return $this;
    }

    /**
     * Validates if a given value matches a specified type.
     *
     * @param mixed $value The value to validate.
     * @param string $type The expected type ("string", "integer", "float", or "boolean").
     * @return bool True if the value matches the type, false otherwise.
     */
    protected function validateType($value, string $type): bool
    {
        switch ($type) {
            case "string":
                return is_string($value);
            case "integer":
                return is_int($value);
            case "float":
                return is_float($value) || is_int($value);
            case "boolean":
                return is_bool($value);
            default:
                return false;
        }
    }

    /**
     * Checks if all elements in an array are arrays.
     *
     * @param array $array The array to check.
     * @return bool True if all elements are arrays, false otherwise.
     */
    protected function allAreArrays(array $array): bool
    {
        foreach ($array as $item) {
            if (!is_array($item)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Validates the structure of a JSON object against a provided map.
     *
     * @param mixed $data The JSON data to validate.
     * @param array $map The map defining the expected structure and types.
     * @return bool True if the structure matches the map, false otherwise.
     */
    public function validateJsonStructure($data, array $map): bool
    {
        if (!$data) {
            return false;
        }

        foreach ($map as $key => $type) {
            if (!array_key_exists($key, $data)) {
                return false;
            }
            if (is_array($type)) {
                if (isset($type[0]) && is_array($type[0])) {
                    if (!is_array($data[$key]) || !$this->allAreArrays($data[$key])) {
                        return false;
                    }
                    foreach ($data[$key] as $item) {
                        if (!$this->validateJsonStructure($item, $type[0])) {
                            return false;
                        }
                    }
                } else {
                    if (!is_array($data[$key]) || !$this->validateJsonStructure($data[$key], $type)) {
                        return false;
                    }
                }
            } else {
                if (!$this->validateType($data[$key], $type)) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Replaces existing configuration values with new ones from a provided array.
     *
     * @param array $newConfig The new configuration values.
     * @return array The updated configuration.
     */
    public function replaceConfig($newConfig)
    {
        $currentConfig = $this->getConfigAll();
        foreach ($newConfig as $key => $item) {
            if (key_exists($key, $currentConfig)) {
                $currentConfig[$key] = $item;
            }
        }
        return $currentConfig;
    }
}
