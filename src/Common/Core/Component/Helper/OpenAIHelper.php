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

    public $conversion = 1000000;

    public $relationTokenWord = 1.5;

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
        $this->client    = new Client([
            'timeout' => 120,
        ]);
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
            $payload = $data;
            unset($payload['meta']);
            $response = $this->client->request('POST', $this->openaiEndpointBase . $this->endpointChat, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $this->getApiKey()
                ],
                'json' => $payload
            ]);
            $response = json_decode($response->getBody(), true);

            $response['meta'] = $data['meta'];

            $responseData["request"] = $data;

            $responseData['message'] = $response['choices'][0]['message']['content'] ?? '';

            $responseData['tokens'] = $response['usage'] ?? [];

            $responseData['tokens']['words'] =
                ($response['usage']['total_tokens'] ?? false) ?
                $this->calcWords($response['usage']['total_tokens'])
                : 0;

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
        return getService('orm.manager')->getDataSet('Settings', 'manager')->get('openai_models') ?? [];
    }

    public function getModelsFromApi()
    {
        try {
            $response = $this->client->request('GET', $this->openaiEndpointBase . $this->endpointModels, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $this->container->getParameter('opennemas.openai.key')
                ]
            ]);

            $body   = $response->getBody()->getContents();
            $models = json_decode($body, true);

            /** Filter only GPT */
            $textModels = array_filter($models["data"], function ($model) {
                return preg_match('/^(gpt-)/', $model['id']);
            });

            /** Order */
            usort($textModels, function ($a, $b) {
                return $b['created'] <=> $a['created'];
            });

            /** Fromat date */
            $textModels = array_map(function ($model) {
                $model['formatted_date']     = date('Y-m-d', $model['created']);
                $model['active']             = false;
                $model['default']            = false;
                $model['cost_input_tokens']  = 0;
                $model['sale_input_tokens']  = 0;
                $model['cost_output_tokens'] = 0;
                $model['sale_output_tokens'] = 0;
                return $model;
            }, $textModels);

            return $textModels;
        } catch (Exception $e) {
            return [];
        }
    }

    protected function saveAction($params, $response)
    {
        $messages = $params['messages'] ?? [];
        $date     = new DateTime('now');

        unset($params['messages']);

        $data = [
            'messages' => $messages,
            'response' => $response['choices'][0]['message']['content'] ?? '',
            'tokens'   => $response['usage'] ?? [],
            'params'   => $params,
            'date'     => $date->format('Y-m-d H:i:s'),
            'service'  => $this->getService()
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

    protected function getApiKey()
    {
        $credentials = $this->getCredentials();
        $provider    = $this->getService();

        if ($provider === 'custom') {
            $this->openaiApiKey = $credentials['apikey'];
        } else {
            $this->openaiApiKey = $this->container->getParameter('opennemas.openai.key');
        }
        return $this->openaiApiKey;
    }

    public function getConfig()
    {
        $settings = $this->getOpenaiConfig();

        $this->setInstructions($this->container->get('orm.manager')
            ->getDataSet('Settings', 'manager')
            ->get('openai_instructions', []));

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

    public function getUsageMonthly($service = 'opennemas')
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
            "date >= '%s' and date < '%s' and service = '%s'",
            $startDate->format('Y-m-d H:i:s'),
            $endDate->format('Y-m-d H:i:s'),
            $service
        );

        return $this->container->get('api.service.ai')->getList($oql);
    }

    public function getTokensByMonth($month, $year)
    {

        $dates = $this->getDates($month, $year);

        $oql = sprintf(
            "date >= '%s' and date < '%s' and service = '%s'",
            $dates['start_date']->format('Y-m-d H:i:s'),
            $dates['end_date']->format('Y-m-d H:i:s'),
            $this->getService()
        );

        $result = $this->container->get('api.service.ai')->getList($oql);

        return $result;
    }

    public function generateDateRangeArray($month, $year)
    {
        $dates       = $this->getDates($month, $year);
        $dateArray   = [];
        $currentDate = $dates['start_date'];
        $endDate     = $dates['end_date'];

        while ($currentDate <= $endDate) {
            $formattedIndex = $currentDate->format('d');
            $dateArray[$formattedIndex] = [
                'words' => [
                    'total' => 0,
                    'input' => 0,
                    'output' => 0,
                    'items' => []
                ],
                'price' => [
                    'total' => 0,
                    'input' => 0,
                    'output' => 0,
                    'items' => []
                ],
                'usage' => [
                    'total' => 0,
                    'items' => []
                ]
            ];
            $currentDate->modify('+1 day');
        }

        return $dateArray;
    }

    public function generateMonths($startDate)
    {
        $currentDate = new DateTime();
        $startDate->modify('first day of this month');
        $months = [];

        while ($startDate < $currentDate) {
            $months[] = [
                'label' => _($startDate->format('F')),
                'year' => (int) $startDate->format('Y'),
                'month' => (int) $startDate->format('m')
            ];
            $startDate->modify('+1 month');
        }

        return array_reverse($months);
    }

    public function getFirstAction()
    {
        $sql  = 'SELECT * FROM ai_actions'
            . ' WHERE date IS NOT NULL'
            . ' ORDER BY date ASC LIMIT 1';
        $list = $this->container->get('api.service.ai')->getListBySql($sql);

        return $list['items'][0] ?? null;
    }

    public function getStats($month, $year)
    {
        $first    = $this->getFirstAction();
        $months   = ($first) ? $this->generateMonths($first->date) : $this->generateMonths(new DateTime());
        $response = [
            'labels'  => [],
            'words'   => ['total' => 0, 'input' => 0, 'output' => 0],
            'price'   => ['total' => 0, 'input' => 0, 'output' => 0],
            'usage'   => ['total' => 0],
            'filters' => $months,
            'service' => $this->getService()
        ];

        if ($first) {
            $tokens = $this->getTokensByMonth($month, $year);
            $days   = $this->generateDateRangeArray($month, $year);

            $this->groupByDays($tokens, $days);

            $response = $this->generateResponseStats($response, $days);
        }

        return $response;
    }

    public function generateResponseStats($response, $days)
    {
        foreach ($days as $key => $day) {
            $response['labels'][]         = $key;
            $response['words']['total']  += $day['words']['total'];
            $response['words']['input']  += $day['words']['input'];
            $response['words']['output'] += $day['words']['output'];
            $response['price']['total']  += $day['price']['total'];
            $response['price']['input']  += $day['price']['input'];
            $response['price']['output'] += $day['price']['output'];
            $response['usage']['total']  += $day['usage']['total'];
            $response['words']['items'][] = $day['words']['total'];
            $response['price']['items'][] = round($day['price']['total'], 4);
            $response['usage']['items'][] = $day['usage']['total'];
        }

        return $response;
    }

    public function groupByDays($tokens, &$days)
    {
        foreach ($tokens['items'] as $item) {
            $price = $this->getPrices($item);

            $day               = $item->getData()['date']->format('d');
            $totalTokens       = $item->getData()['tokens']['total_tokens'];
            $totalTokensInput  = $item->getData()['tokens']['prompt_tokens'];
            $totalTokensOutput = $item->getData()['tokens']['completion_tokens'];
            $totalInputPrice   = ($item->getData()['tokens']['prompt_tokens'] / $this->conversion * $price['i']);
            $totalOutputPrice  = ($item->getData()['tokens']['completion_tokens'] / $this->conversion * $price['o']);
            $totalPrice        = $totalInputPrice + $totalOutputPrice;

            $days[$day]['words']['input']  += $this->calcWords($totalTokensInput);
            $days[$day]['words']['output'] += $this->calcWords($totalTokensOutput);
            $days[$day]['words']['total']  += $this->calcWords($totalTokens);
            $days[$day]['price']['total']  += $totalPrice;
            $days[$day]['price']['input']  += $totalInputPrice;
            $days[$day]['price']['output'] += $totalOutputPrice;
            $days[$day]['usage']['total']++;
        }
    }

    public function getPrices($item)
    {
        $priceInput  = 0;
        $priceOutput = 0;

        if ($item->getData()['params']['meta'] ?? false) {
            $meta = $item->getData()['params']['meta'];
            if ($item->service != 'custom') {
                $priceInput  = $meta['sale_input_tokens'] / $this->relationTokenWord;
                $priceOutput = $meta['sale_output_tokens'] / $this->relationTokenWord;
            } else {
                $priceInput  = $meta['cost_input_tokens'] / $this->relationTokenWord;
                $priceOutput = $meta['cost_output_tokens'] / $this->relationTokenWord;
            }
        }

        return [
            'i' => $priceInput,
            'o' => $priceOutput,
        ];
    }

    public function calcWords($tokens)
    {
        return (int) ($tokens / $this->relationTokenWord);
    }

    public function getDates($month, $year)
    {
        $startDate = DateTime::createFromFormat('Y-m-d', "$year-$month-01");

        $endDate = clone $startDate;
        $endDate->modify('last day of this month');
        return [
            'start_date' => $startDate,
            'end_date' => $endDate,
        ];
    }

    public function getSpentMoney()
    {
        $total   = 0;
        $results = $this->getUsageMonthly();

        foreach ($results['items'] as $item) {
            $price   = $this->getPrices($item);
            $tokensI = ($item->tokens['prompt_tokens'] ?? 0) / $this->conversion * $price['i'];
            $tokensO = ($item->tokens['completion_tokens'] ?? 0) / $this->conversion * $price['o'];
            $total  += $tokensI + $tokensO;
        }

        return round($total, 15);
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

    public function getDefaultfModel($models = [])
    {
        $result = array_filter($models, function ($item) {
            return isset($item['default']) && $item['default'] == "true";
        });
        return reset($result);
    }
    /* public function getCurrentModel(){

    }*/
    /**
     * Get the value of openaiConfig
     */
    public function getOpenaiConfig()
    {
        $settingsInstance = getService('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('openai_config', []);
        $settingsManager  = getService('orm.manager')
            ->getDataSet('Settings', 'manager')
            ->get('openai_settings');

        $models = $this->getModels();
        if (!empty($settingsInstance['model'])) {
            $filter = array_filter($models, function ($item) {
                return $item['id'] === 'gpt-';
            });
            $model  = reset($filter);
        } else {
            $model = $this->getDefaultfModel($models);
        }

        $config = [
            'temperature' => !empty($settingsInstance['temperature'])
                ? $settingsInstance['temperature']
                : $settingsManager['temperature'],
            'max_tokens' => !empty($settingsInstance['max_tokens'])
                ? $settingsInstance['max_tokens']
                : $settingsManager['max_tokens'],
            'frequency_penalty' => !empty($settingsInstance['frequency_penalty'])
                ? $settingsInstance['frequency_penalty']
                : $settingsManager['frequency_penalty'],
            'presence_penalty' => !empty($settingsInstance['presence_penalty'])
                ? $settingsInstance['presence_penalty']
                : $settingsManager['presence_penalty'],
        ];

        $config['meta']  = $model;
        $config['model'] = $config['meta']['id'];
        return $config;
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
