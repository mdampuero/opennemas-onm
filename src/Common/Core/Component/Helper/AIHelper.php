<?php

namespace Common\Core\Component\Helper;

use DateTime;
use Exception;
use GuzzleHttp\Client;
use SebastianBergmann\Environment\Console;

/**
 * Helper class to retrieve AI data.
 */
class AIHelper
{

    /**
     * The service container.
     *
     * @var ServiceContainer
     */
    protected $container;

    public $conversion = 1000000;

    public $relationTokenWord = 1.5;

    public $maxRetries = 3;

    public $retryDelay = 2;

    public $map = [
        "onmai_service" => "string",
        "onmai_credentials" => [
            "apikey" => "string",
        ],
        "onmai_roles" => [
            [
                "name" => "string",
                "prompt" => "string",
            ],
        ],
        "onmai_tones" => [
            [
                "name" => "string",
                "description" => "string",
            ],
        ],
        "onmai_config" => [
            "model" => "string",
            "max_tokens" => "integer",
            "temperature" => "float",
            "frequency_penalty" => "float",
            "presence_penalty" => "float",
        ],
    ];

    protected $languages = [
        ['code' => 'Español (España)', 'name' => 'Español (España)'],
        ['code' => 'Gallego (España)', 'name' => 'Gallego (España)'],
        ['code' => 'Catalán (España)', 'name' => 'Catalán'],
        ['code' => 'Inglés (Reino Unido)', 'name' => 'Inglés'],
        ['code' => 'Francés (Guayana Francesa)', 'name' => 'Francés'],
        ['code' => 'Alemán (Alemania) (de_DE)', 'name' => 'Alemán'],
        ['code' => 'Portugués (Portugal)', 'name' => 'Portugués'],
        ['code' => 'Portugués (Brasil)', 'name' => 'Portugués-BR'],
        ['code' => 'Hindi (India)', 'name' => 'Hindi'],
        ['code' => 'Chino Mandarín', 'name' => 'Chino Mandarín']
    ];

    protected $service;

    protected $instructions = [];

    protected $userPrompt = '';

    protected $structureResponse = [
        'error' => null,
        'result' => '',
        'tokens' => [
            'input'  => 0,
            'output' => 0,
            'total'  => 0,
        ],
        'original' => [],
    ];

    protected $engines = [
        'openai'    => 'Open AI',
        'gemini'    => 'Gemini',
        'deepseek'  => 'DeepSeek',
        'mistralai' => 'Mistral AI',
    ];

    /**
     * Initializes the Menu service.
     *
     * @param Container          $container The service container.
     */
    public function __construct($container)
    {
        $this->container = $container;
    }

    public function getManagerSettings()
    {
        $managerOnmaiSettings = $this->container->get('orm.manager')
            ->getDataSet('Settings', 'manager')
            ->get('onmai_settings', []);

        foreach ($this->getEngines() as $key => $engine) {
            if (!isset($managerOnmaiSettings['engines'][$key])) {
                $managerOnmaiSettings['engines'][$key] = [
                    'title'    => $engine,
                    'apiKey'   => '',
                    'models'   => []
                ];
            }
        }

        return $managerOnmaiSettings;
    }

    public function getInstanceSettings()
    {
        $setting = $this->container->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('onmai_settings', []);

        $setting['service'] = ($setting['service'] ?? false) ? $setting['service'] : 'onmai';
        $setting['model']   = ($setting['model'] ?? false) ? $setting['model'] : '';
        $setting['roles']   = ($setting['roles'] ?? false) ? $setting['roles'] : [];
        $setting['tones']   = ($setting['tones'] ?? false) ? $setting['tones'] : [];

        return $setting;
    }

    public function setInstanceSettings($settings)
    {
        return $this->container->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->set('onmai_settings', $settings);
    }

    public function getCurrentSettings()
    {
        $managerSettings  = $this->getManagerSettings();
        $instanceSettings = $this->getInstanceSettings();

        $engine = !empty($instanceSettings['model'])
            ? $instanceSettings['model']
            : $managerSettings['model'];

        $engineAndModel  = $this->splitEngineAndModel($engine);
        $currentSettings = [];

        foreach ($managerSettings['engines'][$engineAndModel['engine']]['models'] as $item) {
            if ($item['id'] === $engineAndModel['model_id']) {
                if ($item['params'] ?? false) {
                    foreach ($item['params'] as $key => $param) {
                        $item['params'][$key]['value'] = $this->castValue($param['value']);
                    }
                }

                $meta = $item;
                break;
            }
        }

        unset($managerSettings['engines'][$engineAndModel['engine']]['models']);

        $currentSettings = $managerSettings['engines'][$engineAndModel['engine']];

        if (!empty($instanceSettings['service']) && $instanceSettings['service'] != 'onmai') {
            $currentSettings['apiKey'] = $instanceSettings[$engineAndModel['engine']]['apiKey'];
        }

        $currentSettings['meta']   = $meta;
        $currentSettings['model']  = $currentSettings['meta']['id'];
        $currentSettings['engine'] = $engineAndModel['engine'];

        return $currentSettings;
    }

    public function castValue($value)
    {
        if ($value === null) {
            return null;
        }

        if (filter_var($value, FILTER_VALIDATE_INT) !== false) {
            return (int) $value;
        }

        if (is_numeric($value) && strpos($value, '.') !== false) {
            return (float) $value;
        }

        if (strtolower($value) === 'true') {
            return true;
        }
        if (strtolower($value) === 'false') {
            return false;
        }

        return $value;
    }

    /**
     * Splits a string into two parts by the last underscore.
     *
     * @param string $input The input string to be split.
     * @return array An associative array with 'engine' and 'model_id'.
     */
    public function splitEngineAndModel($input)
    {
        $parts    = explode('_', $input);
        $engine   = implode('_', array_slice($parts, 0, -1));
        $model_id = end($parts);

        return [
            'engine' => $engine,
            'model_id' => $model_id,
        ];
    }

    protected function maskApiKey($apiKey)
    {
        if (strlen($apiKey) > 7) {
            return substr($apiKey, 0, 3) . str_repeat('.', strlen($apiKey) - 7) . substr($apiKey, -4);
        }

        return $apiKey;
    }

    public function getStructureResponse()
    {
        return $this->structureResponse;
    }

    public function getInstructions()
    {
        $instructions = $this->container->get('orm.manager')
            ->getDataSet('Settings', 'manager')
            ->get('onmai_instructions', []);

        $this->instructions = array_filter($instructions, function ($item) {
            return !isset($item['disabled']) || $item['disabled'] != 1;
        });

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

    protected function insertInstructions($instructions = [], $role = '')
    {
        $instructionsString = sprintf("### INSTRUCCIONES IMPORTANTES:\n%s", $role);
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


            $instructionsString .= sprintf(". Sigue estas reglas estrictamente:\n%s", $instructionList);
        }
        $this->userPrompt .= $instructionsString;
    }

    protected function insertTone($messages = [])
    {
        if ($messages["toneSelected"]["name"] ?? false) {
            $this->userPrompt .= sprintf("\n\n### TONO:\n%s", $messages["toneSelected"]["name"]);
        }
    }

    protected function insertLanguage($messages = [])
    {
        if ($messages["toneSelected"]["name"] ?? false) {
            $this->userPrompt .= sprintf("\n\n### IDIOMA DE LA RESPUESTA:\n%s", $messages["toneSelected"]["name"]);
        }
    }

    public function generatePrompt($messages)
    {
        $this->insertInstructions($this->getInstructionsByFilter(
            [
                'type'  => ['Both', $messages['promptSelected']['mode']],
                'field' => ['all', $messages['promptSelected']['field_or']],
            ]
        ), $messages['roleSelected']['prompt'] ?? '');

        if ($messages["locale"] ?? false) {
            $this->userPrompt .= ($messages["locale"] ?? false) ?
                sprintf("\n\n### IDIOMA DE LA RESPUESTA:\n%s", sprintf(
                    'El idioma configurado es "%s". Responde usando este idioma y las convenciones culturales.',
                    $messages['locale']
                )) : "";
        }

        if ($messages['promptSelected']['mode_or'] == 'New') {
            $this->userPrompt .= ($messages["input"] ?? false) ? sprintf("\n\n### TEMA:\n%s", $messages["input"]) : "";
        } elseif ($messages['promptSelected']['mode_or'] == 'Edit') {
            $this->userPrompt .= ($messages["input"] ?? false) ? sprintf("\n\n### TEXTO:\n%s", $messages["input"]) : '';
        }

        $this->insertTone($messages);

        $this->userPrompt .= sprintf("\n\n### OBJETIVO:\n%s", $messages["promptInput"]);

        return $this->userPrompt;
    }

    public function sendMessage($messages)
    {
        $this->getInstructions();

        $data = $this->getCurrentSettings();

        $data['messages'] = [];

        if ($messages["input"] ?? false) {
            $data['messages'][] = [
                'role' => 'user',
                'content' => $this->generatePrompt($messages)
            ];
        }

        if ($data['engine'] ?? false) {
            $response = $this->container->get('core.helper.' . $data['engine'])->sendMessage(
                $data,
                $this->getStructureResponse()
            );

            $response['result'] = $this->removeHtmlCodeBlocks($response['result']);
        } else {
            $response['error'] = 'Error';
        }

        if (empty($response['error'])) {
            $this->generateWords($response);
            $this->saveAction($data, $response);
        }

        return $response;
    }

    public function translate($text, $lang, $params = [])
    {
        if (empty($text) || empty($lang)) {
            return ['error' => 'Empty text or language', 'result' => ''];
        }

        $this->userPrompt = "### OBJETIVO:\n" .
            "Traduce el siguiente texto al idioma seleccionado siguiendo estrictamente las instrucciones.\n";
        $this->userPrompt .= "### TEXTO:\n{$text}\n";
        $this->userPrompt .= "### IDIOMA SELECCIONADO:\n{$lang}\n";

        $instructions = [
            ['value' => 'Debes mantener el estilo del texto original.'],
            ['value' => 'No debes añadir información adicional ni modificar el significado del texto.'],
            ['value' => 'Debes respetar la estructura y longitud del texto.'],
            ['value' => 'Mantén la terminología, puntuación, gramática y ortografía del original.'],
            ['value' => 'No alteres el formato original del texto.']
        ];

        if (!empty($params['tone']['name'] ?? false)) {
            $instructions[] = ['value' => "Adopta un tono {$params['tone']['name']} en la traducción."];
        } else {
            $instructions[] = ['value' => "Debes mantener el tono del texto original."];
        }

        $this->insertInstructions($instructions);

        $data = $this->getCurrentSettings();
        $data['messages'] = [['role' => 'user', 'content' => $this->userPrompt]];

        if (!empty($data['engine'])) {
            $response = $this->container->get('core.helper.' . $data['engine'])->sendMessage(
                $data,
                $this->getStructureResponse()
            );
            $response['result'] = $this->removeHtmlCodeBlocks($response['result']);
        } else {
            return ['error' => 'Error'];
        }

        if (empty($response['error'])) {
            $this->generateWords($response);
            $this->saveAction($data, $response);
        }

        return $response;
    }

    public function transform($or = [], $fields = ['title', 'title_int', 'description', 'body'], $tone = [])
    {
        if (empty($or) || empty($fields) || empty($or['prompt'])) {
            return $or;
        }

        foreach ($fields as $field) {
            if (!key_exists($field, $or)) {
                continue;
            }

            $cleanContent = trim(strip_tags($or[$field]));

            if ($cleanContent === '') {
                return $or[$field];
            }

            $this->userPrompt = sprintf("\n\n### OBJETIVO:\n%s", $or["prompt"]);
            $this->userPrompt .= "\n### TEXTO ORIGINAL:\n{$or[$field]}\n";

            $instructions = [
                ['value' => 'Utiliza el mismo tema del texto original.'],
                ['value' => 'Intenta conservar la misma cantidad de palabras.'],
                ['value' => 'Responde directamente con el texto transformado.'],
                ['value' => 'Si el texto original tiene formato html, debes mantenerlo.']
            ];

            if (!empty($or['tone'] ?? false)) {
                $instructions[] = ['value' => "Adopta un tono {$or['tone']}
                    en la transformación."];
            } else {
                $instructions[] = ['value' => "Debes mantener el tono del texto original."];
            }

            $this->insertInstructions($instructions);

            $data             = $this->getCurrentSettings();
            $data['messages'] = [['role' => 'user', 'content' => $this->userPrompt]];

            if (!empty($data['engine'])) {
                $response = $this->container->get('core.helper.' . $data['engine'])->sendMessage(
                    $data,
                    $this->getStructureResponse()
                );

                $response['result'] = $this->removeHtmlCodeBlocks($response['result']);
            } else {
                return $or[$field];
            }

            if (empty($response['error']) && !empty($response['result'])) {
                $this->generateWords($response);
                $this->saveAction($data, $response);
                $or[$field] = $response['result'];
            }
        }
        return $or;
    }

    public function removeHtmlCodeBlocks($input)
    {
        $output = preg_replace('/```html\n(.*?)\n```/s', '$1', $input);
        $output = preg_replace('/^\*\*(.*?)\*\*$/s', '$1', $output);

        return $output;
    }

    public function generateWords(&$response)
    {
        $response['words']['input']  = $this->calcWords($response['tokens']['input']);
        $response['words']['output'] = $this->calcWords($response['tokens']['output']);
        $response['words']['total']  = $this->calcWords($response['tokens']['total']);
    }

    protected function saveAction($params, $response)
    {
        $messages = $params['messages'] ?? [];

        $messages = [
            'request' => $params['messages'] ?? '',
            'response' => $response['original'] ?? ''
        ];

        $date = new DateTime('now');

        unset($params['messages']);

        $tokens = [
            'words'  => $response['words'] ?? [],
            'tokens' => $response['tokens'] ?? [],
        ];

        $data = [
            'messages' => $messages,
            'response' => $response['result'] ?? '',
            'tokens'   => $tokens,
            'params'   => $params,
            'date'     => $date->format('Y-m-d H:i:s'),
            'service'  => $this->getService()
        ];

        $this->container->get('api.service.ai')->createItem($data);
    }

    public function getUsageMonthly($service = 'onmai')
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
            "date >= '%s' and date < '%s'",
            $dates['start_date']->format('Y-m-d H:i:s'),
            $dates['end_date']->format('Y-m-d H:i:s')
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
        $currentDate    = new DateTime();
        $cloneStartDate = clone $startDate;

        $cloneStartDate->modify('first day of this month');
        $months = [];

        while ($cloneStartDate < $currentDate) {
            $months[] = [
                'label' => _($cloneStartDate->format('F')),
                'year' => (int) $cloneStartDate->format('Y'),
                'month' => (int) $cloneStartDate->format('m')
            ];
            $cloneStartDate->modify('+1 month');
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
            $price            = $this->getPrices($item);
            $day              = $item->getData()['date']->format('d');
            $totalInputPrice  = ($item->getData()['tokens']['tokens']['input'] / $this->conversion * $price['i']);
            $totalOutputPrice = ($item->getData()['tokens']['tokens']['output'] / $this->conversion * $price['o']);
            $totalPrice       = $totalInputPrice + $totalOutputPrice;

            $days[$day]['words']['input']  += $item->getData()['tokens']['words']['input'];
            $days[$day]['words']['output'] += $item->getData()['tokens']['words']['output'];
            $days[$day]['words']['total']  += $item->getData()['tokens']['words']['total'];
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
            if ($item->service == 'onmai') {
                $priceInput  = $meta['sale_input_tokens'] / $this->relationTokenWord;
                $priceOutput = $meta['sale_output_tokens'] / $this->relationTokenWord;
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
            $tokensI = ($item->tokens['tokens']['input'] ?? 0) / $this->conversion * $price['i'];
            $tokensO = ($item->tokens['tokens']['output'] ?? 0) / $this->conversion * $price['o'];
            $total  += $tokensI + $tokensO;
        }

        return round($total, 15);
    }

    public function getTones($showManager = true)
    {
        $is = $this->getInstanceSettings();
        $ti = $is['tones'] ?? [];
        $tm = [];
        if ($showManager) {
            $sm = $this->container->get('orm.manager')->getDataSet('Settings', 'manager');
            $tm = $this->addFlagReadOnly($sm->get('onmai_tones', []));
        }

        return $this->sortByName(array_merge($tm, $ti));
    }

    public function setTones($tones = [])
    {
        $this->container->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->set('onmai_tones', $tones);
        return $this;
    }

    public function getRoles($showManager = true)
    {
        $is = $this->getInstanceSettings();
        $ri = $is['roles'] ?? [];
        $rm = [];
        if ($showManager) {
            $sm = $this->container->get('orm.manager')->getDataSet('Settings', 'manager');
            $rm = $this->addFlagReadOnly($sm->get('onmai_roles', []));
        }

        return $this->sortByName(array_merge($rm, $ri));
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
            ->set('onmai_roles', $roles);
        return $this;
    }

    /**
     * Get the value of service
     */
    public function getService()
    {
        $instanceSettings = $this->getInstanceSettings();
        return !empty($instanceSettings['service'])
            ? $instanceSettings['service']
            : 'onmai';
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

    public function getModelDefault()
    {
        $managerSettings = $this->getManagerSettings();
        return $managerSettings['model'] ?? '';
    }

    public function getModels()
    {
        $managerSettings = $this->getManagerSettings();
        $models          = [];

        foreach ($managerSettings['engines'] as $key => $engine) {
            $title = $engine["title"];
            foreach ($engine["models"] as $model) {
                $modelId  = $model["id"];
                $models[] = [
                    'id'    => "{$key}_{$modelId}",
                    'title' => "{$title} - {$modelId}"
                ];
            }
        }

        return $models;
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
     * Get the value of engines
     */
    public function getEngines()
    {
        return $this->engines;
    }

    /**
     * Set the value of engines
     *
     * @return  self
     */
    public function setEngines($engines)
    {
        $this->engines = $engines;

        return $this;
    }

    /**
     * Get the value of languages
     */
    public function getLanguages()
    {
        $locale = $this->container->get('core.helper.locale')->getConfiguration();
        if (isset($locale['multilanguage']) && $locale['multilanguage'] == true) {
            $languages = [];
            foreach ($locale['available'] as $value) {
                $languages[] = [
                    'code' => $value,
                    'name' => $value
                ];
            }
        }
        if (!empty($languages)) {
            $this->languages = $languages;
        }

        return $this->languages;
    }
}
