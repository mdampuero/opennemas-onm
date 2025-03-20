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

    /**
     * Conversion factor used in calculations.
     */
    public $conversion = 1000000;

    /**
     * Relation between tokens and words for estimation.
     */
    public $relationTokenWord = 1.5;

    /**
     * Maximum number of retry attempts for a failed request.
     */
    public $maxRetries = 3;

    /**
     * Delay in seconds before retrying a failed request.
     */
    public $retryDelay = 2;

    /**
     * Mapping of configuration keys to their expected data types.
     */
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

    /**
     * List of supported languages with their codes and display names.
     */
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

    /**
     * The service instance used for processing.
     */
    protected $service;

    /**
     * Array storing predefined instructions.
     */
    protected $instructions = [];

    /**
     * The user-generated prompt for requests.
     */
    protected $userPrompt = '';

    /**
     * Default structure for API response handling.
     */
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

    /**
     * Supported AI engines with their display names.
     */
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

    /**
     * Retrieves the Onmai settings for the manager.
     *
     * @return array The manager's Onmai settings with default engines if missing.
     */
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

    /**
     * Retrieves the Onmai settings for the current instance, ensuring default values if missing.
     *
     * @return array The instance's Onmai settings with predefined defaults.
     */
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

    /**
     * Updates the Onmai settings for the current instance.
     *
     * @param array $settings The settings to be saved.
     * @return mixed The result of the settings update operation.
     */
    public function setInstanceSettings($settings)
    {
        return $this->container->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->set('onmai_settings', $settings);
    }

    /**
     * Retrieves the current Onmai settings by merging instance and manager settings.
     *
     * @return array The current settings including engine, model, and associated parameters.
     */
    public function getCurrentSettings($model = null)
    {
        $managerSettings  = $this->getManagerSettings();
        $instanceSettings = $this->getInstanceSettings();

        if ($model) {
            $engine = $model;
        } else {
            $engine = !empty($instanceSettings['model'])
                ? $instanceSettings['model']
                : $managerSettings['model'];
        }

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

    /**
     * Casts a value to its appropriate type (int, float, bool, or string).
     *
     * @param mixed $value The value to be cast.
     * @return mixed The casted value, type can be int, float, bool, or string.
     */
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

    /**
     * Masks an API key by showing only the first 3 and last 4 characters, hiding the middle.
     *
     * @param string $apiKey The API key to be masked.
     * @return string The masked API key.
     */
    protected function maskApiKey($apiKey)
    {
        if (strlen($apiKey) > 7) {
            return substr($apiKey, 0, 3) . str_repeat('.', strlen($apiKey) - 7) . substr($apiKey, -4);
        }

        return $apiKey;
    }

    /**
     * Retrieves the structure response.
     *
     * @return array The structure response array.
     */
    public function getStructureResponse()
    {
        return $this->structureResponse;
    }

    /**
     * Retrieves and filters instructions from the manager settings, excluding disabled ones.
     *
     * @return array The filtered list of instructions.
     */
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

    /**
     * Adds a new instruction to the existing list of instructions.
     *
     * @param array $instruction The instruction to be added.
     */
    public function addInstruction($instruction)
    {
        $instructions = $this->getInstructions();
        array_push($instructions, $instruction);
        $this->setInstructions($instructions);
    }

    /**
     * Sets the list of instructions.
     *
     * @param array $instructions The instructions to be set.
     * @return $this The current instance for method chaining.
     */
    public function setInstructions($instructions)
    {
        $this->instructions = $instructions;
        return $this;
    }

    /**
     * Retrieves instructions filtered by the given criteria.
     *
     * @param array $filter An associative array containing 'type' and 'field' filters.
     * @return array The filtered list of instructions.
     */
    public function getInstructionsByFilter(array $filter = []): array
    {
        return $filter
            ? array_filter($this->instructions, function ($i) use ($filter) {
                return in_array($i['type'], $filter['type']) && in_array($i['field'], $filter['field']);
            })
            : $this->instructions;
    }

    /**
     * Inserts the given instructions into the user prompt with an optional role description.
     *
     * @param array $instructions The list of instructions to insert.
     * @param string $role The role description to be included in the instructions.
     */
    protected function insertInstructions($instructions = [], $role = '')
    {
        $instructionsString = sprintf("### INSTRUCCIONES IMPORTANTES:\n%s", $role);
        if (count($instructions)) {
            $counter = 0;

            $instructionList = implode("\n", array_map(
                function ($item) use (&$counter) {
                    $counter++;
                    return $counter . '. ' . $item['value'];
                },
                $instructions
            ));

            $instructionsString .= sprintf(". Sigue estas reglas estrictamente:\n%s", $instructionList);
        }
        $this->userPrompt .= $instructionsString;
    }

    /**
     * Adds the selected tone to the `userPrompt` if it's present in the messages.
     *
     * @param array $messages The array containing the selected tone information.
     */
    protected function insertTone($messages = [])
    {
        if ($messages["toneSelected"]["name"] ?? false) {
            $this->userPrompt .= sprintf("\n\n### TONO:\n%s", $messages["toneSelected"]["name"]);
        }
    }

    /**
     * Adds the selected language to the `userPrompt` if it's present in the messages.
     *
     * @param array $messages The array containing the selected language information.
     */
    protected function insertLanguage($messages = [])
    {
        if ($messages["toneSelected"]["name"] ?? false) {
            $this->userPrompt .= sprintf("\n\n### IDIOMA DE LA RESPUESTA:\n%s", $messages["toneSelected"]["name"]);
        }
    }

    /**
     * Generates a prompt from messages with instructions, tone, language, and other fields.
     *
     * @param array $messages Contains prompt details such as mode, field, role, locale, and input.
     *
     * @return string The generated prompt.
     */
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

    /**
     * Sends a message by preparing settings, generating a prompt, and calling the appropriate engine.
     *
     * @param array $messages Contains the user's input and other necessary information for the prompt.
     *
     * @return array The response from the engine, including the result or error if any.
     */
    public function sendMessage($messages)
    {
        $this->getInstructions();

        $data = $this->getCurrentSettings($messages['promptSelected']['model'] ?? null);

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

    /**
     * Transforms the given fields of text based on specific instructions and tone.
     *
     * @param array $or The array containing the text fields to be transformed.
     * @param array $fields The specific fields of text to transform (e.g., 'title', 'description').
     * @param array $tone The tone to be used in the transformation.
     *
     * @return array The array with transformed text fields or the original array if no transformation occurs.
     */
    public function translate($text, $lang, $params = [])
    {
        if (empty($text) || empty($lang)) {
            return ['error' => 'Empty text or language', 'result' => ''];
        }

        $this->userPrompt  = "### OBJETIVO:\n" .
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

    /**
     * Transforms the given fields of text based on specific instructions and tone.
     *
     * @param array $or The array containing the text fields to be transformed.
     * @param array $fields The specific fields of text to transform (e.g., 'title', 'description').
     * @param array $tone The tone to be used in the transformation.
     *
     * @return array The array with transformed text fields or the original array if no transformation occurs.
     */
    public function transform($or = [], $fields = ['title', 'title_int', 'description', 'body'])
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

            $this->userPrompt  = sprintf("\n\n### OBJETIVO:\n%s", $or["prompt"]);
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

    /**
     * Removes HTML code blocks and bold markdown formatting from the input string.
     *
     * @param string $input The input string containing HTML code blocks and markdown.
     *
     * @return string The cleaned input with HTML code blocks and bold markdown removed.
     */
    public function removeHtmlCodeBlocks($input)
    {
        $output = preg_replace('/```html\n(.*?)\n```/s', '$1', $input);
        $output = preg_replace('/^\*\*(.*?)\*\*$/s', '$1', $output);

        return $output;
    }

    /**
     * Generates word counts for the input, output, and total tokens in the response.
     *
     * @param array $response The response array containing token counts.
     *
     * @return void The method updates the response array with the word counts for input, output, and total.
     */
    public function generateWords(&$response)
    {
        $response['words']['input']  = $this->calcWords($response['tokens']['input']);
        $response['words']['output'] = $this->calcWords($response['tokens']['output']);
        $response['words']['total']  = $this->calcWords($response['tokens']['total']);
    }

    /**
     * Saves the action data, including request/response messages, token counts, and additional parameters.
     *
     * Prepares the data, formats the required information, and calls an external service to create a new item.
     *
     * @param array $params The parameters containing messages and other relevant data.
     * @param array $response The response data, including the result, token counts, and original response.
     *
     * @return void This method does not return anything. It performs a save operation via an external service.
     */
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

    /**
     * Retrieves the monthly usage data for a given service, starting from the 27th of the previous month
     * to the 27th of the current or next month.
     *
     * This method calculates the start and end dates based on the current date and the given service,
     * then generates an OQL query to fetch the usage data.
     *
     * @param string $service The name of the service for which usage data is retrieved (default is 'onmai').
     *
     * @return array The list of usage data retrieved based on the OQL query.
     */
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

    /**
     * Retrieves tokens data for a specific month and year by generating an OQL query
     * that filters records based on the start and end dates of the provided month and year.
     *
     * The method uses the `getDates` method to calculate the date range, then builds an OQL query
     * to fetch data from the API service.
     *
     * @param int $month The month (1-12) for which the tokens data is retrieved.
     * @param int $year The year for which tokens data is retrieved.
     *
     * @return array The list of tokens data retrieved based on the OQL query.
     */
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

    /**
     * Generates an array representing a date range, where each day in the range is represented
     * as an index, and the values for words, price, and usage are initialized to zero.
     *
     * The method uses the `getDates` method to calculate the start and end dates for the provided
     * month and year. It then iterates through each day in the date range and creates a structure
     * for tracking total, input, and output values for words, price, and usage.
     *
     * @param int $month The month (1-12) for which the date range is generated.
     * @param int $year The year for which the date range is generated.
     *
     * @return array An array with dates as keys (formatted as 'd'), and values containing
     *               initialized data for words, price, and usage.
     */
    public function generateDateRangeArray($month, $year)
    {
        $dates       = $this->getDates($month, $year);
        $dateArray   = [];
        $currentDate = $dates['start_date'];
        $endDate     = $dates['end_date'];

        while ($currentDate <= $endDate) {
            $formattedIndex             = $currentDate->format('d');
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

    /**
     * Generates an array of months from the provided start date up until the current month.
     *
     * The method iterates through each month starting from the provided `startDate` and generates
     * an array containing the month labels, year, and month number, while ensuring the months are
     * in reverse order, with the most recent month appearing first.
     *
     * @param DateTime $startDate The start date from which the months will be generated.
     *
     * @return array An array of months with each month containing a label (month name), year, and month number.
     */
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

    /**
     * Retrieves the first action from the database.
     *
     * This method queries the `ai_actions` table to find the first record where the `date` field
     * is not null, ordering the results by date in ascending order. It returns the first item
     * in the list of results, or `null` if no actions are found.
     *
     * @return array|null The first action in the list, or `null` if no actions are found.
     */
    public function getFirstAction()
    {
        $sql  = 'SELECT * FROM ai_actions'
            . ' WHERE date IS NOT NULL'
            . ' ORDER BY date ASC LIMIT 1';
        $list = $this->container->get('api.service.ai')->getListBySql($sql);

        return $list['items'][0] ?? null;
    }

    /**
     * Retrieves the statistics for a given month and year.
     *
     * This method fetches the first action from the database and generates a list of months
     * starting from that action's date. It then calculates statistics such as word counts,
     * pricing, and usage for the specified month and year, grouping the data by day.
     * The result is returned in a structured response with labels, words, price, and usage statistics.
     *
     * @param int $month The month for which the statistics are to be retrieved.
     * @param int $year The year for which the statistics are to be retrieved.
     *
     * @return array An array containing the statistics for the given month and year.
     */
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

    /**
     * Generates the statistics response based on the daily data.
     *
     * This method iterates through the given days and aggregates the statistics such as total
     * words (input, output), price, and usage for each day. The statistics are accumulated
     * into the provided response structure, which includes labels, total values, and items for
     * each category (words, price, usage).
     *
     * @param array $response The response array to be updated with the calculated statistics.
     * @param array $days An array of daily data containing words, price, and usage statistics.
     *
     * @return array The updated response array with the aggregated statistics.
     */
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

    /**
     * Groups the tokens by day and aggregates statistics such as words, price, and usage.
     *
     * This method processes each token in the given `$tokens` array, extracts relevant data,
     * and groups it by day. The statistics for words (input, output, total), price (input,
     * output, total), and usage are accumulated for each day in the `$days` array. The price
     * is calculated based on the token input/output and a conversion factor.
     *
     * @param array $tokens The array of tokens containing the necessary data (words, price, etc.).
     * @param array &$days The array to store aggregated data by day, updated by reference.
     *
     * @return void
     */
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

    /**
     * Retrieves the price for input and output tokens based on the provided item data.
     *
     * This method calculates the price for input and output tokens by retrieving relevant
     * metadata from the item's parameters. If the item's service is "onmai", it calculates
     * the price using specific parameters (`sale_input_tokens` and `sale_output_tokens`)
     * and a relation factor (`relationTokenWord`).
     *
     * @param object $item The item object containing data such as service and metadata.
     *
     * @return array An associative array containing the prices for input ('i') and output ('o') tokens.
     */
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

    /**
     * Calculates the number of words based on the given tokens.
     *
     * This method converts a number of tokens into words using a predefined relation factor
     * (`relationTokenWord`). The result is cast to an integer to represent the calculated word count.
     *
     * @param int $tokens The number of tokens to convert into words.
     *
     * @return int The calculated number of words based on the relation factor.
     */
    public function calcWords($tokens)
    {
        return (int) ($tokens / $this->relationTokenWord);
    }

    /**
     * Gets the start and end dates for a given month and year.
     *
     * This method creates a start date for the first day of the specified month and year,
     * and calculates the end date as the last day of the same month. The start and end dates
     * are returned as `DateTime` objects.
     *
     * @param int $month The month for which to calculate the date range (1-12).
     * @param int $year The year for which to calculate the date range.
     *
     * @return array An associative array with two keys: 'start_date' and 'end_date',
     *               each containing a `DateTime` object representing the start and end dates, respectively.
     */
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

    /**
     * Calculates the total amount of money spent based on input and output tokens usage.
     *
     * This method retrieves the monthly usage data and calculates the cost based on the
     * input and output tokens for each usage item. The cost is determined by multiplying
     * the tokens by their respective prices, which are fetched using the `getPrices` method.
     * The total cost is then calculated and returned rounded to 15 decimal places.
     *
     * @return float The total amount of money spent, rounded to 15 decimal places.
     */
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

    /**
     * Retrieves and merges tone settings, optionally including manager tones.
     *
     * @param bool $showManager Whether to include manager tones. Default is true.
     * @return array The sorted list of tones.
     */
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

    /**
     * Sets the tones in the instance settings.
     *
     * @param array $tones List of tones to be set.
     * @return $this The current instance for method chaining.
     */
    public function setTones($tones = [])
    {
        $this->container->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->set('onmai_tones', $tones);
        return $this;
    }

    /**
     * Retrieves roles, optionally merging with manager settings.
     *
     * @param bool $showManager Whether to include manager settings in the result. Defaults to true.
     * @param array $filter Optional filter to apply to the roles.
     * @return array Sorted list of roles based on the specified filter.
     */
    public function getRoles($showManager = true, $filter = [])
    {
        $is = $this->getInstanceSettings();
        $ri = $is['roles'] ?? [];
        $rm = [];
        if ($showManager) {
            $sm = $this->container->get('orm.manager')->getDataSet('Settings', 'manager');
            $rm = $this->addFlagReadOnly($sm->get('onmai_roles', []));
        }

        return $this->sortByName(array_merge($rm, $ri), $filter);
    }

    /**
     * Sorts an array of items by their 'name' field, optionally filtering by a specific field value.
     *
     * @param array $array The array of items to be sorted.
     * @param array $filter Optional filter to apply based on a specific field value.
     * @return array Sorted and filtered array.
     */
    protected function sortByName($array, $filter = [])
    {
        $fieldValue = $filter['field'] ?? null;

        if (!empty($fieldValue)) {
            $array = array_filter($array, function ($item) use ($fieldValue) {
                return isset($item['field']) && $item['field'] === $fieldValue;
            });
        }

        usort($array, function ($a, $b) {
            $nameA = trim(strtolower($a['name'] ?? ''));
            $nameB = trim(strtolower($b['name'] ?? ''));
            return strcmp($nameA, $nameB);
        });
        return $array;
    }

    /**
     * Adds a 'readOnly' flag with a value of true to each item in the array.
     *
     * @param array $array The array of items to modify.
     * @return array The modified array with 'readOnly' flag added.
     */
    protected function addFlagReadOnly($array)
    {
        return array_map(function ($item) {
            $item['readOnly'] = true;
            return $item;
        }, $array);
    }

    /**
     * Prepares the items in the array before saving by truncating certain fields
     * and removing read-only items.
     *
     * @param array $array The array of items to process.
     * @return array The modified array with adjustments made.
     */
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

    /**
     * Sets the roles in the instance settings.
     *
     * @param array $roles The roles to set.
     * @return $this The current instance for method chaining.
     */
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

    /**
     * Gets the default model from manager settings.
     *
     * @return string The default model name or an empty string if not set.
     */
    public function getModelDefault()
    {
        $managerSettings = $this->getManagerSettings();
        return $managerSettings['model'] ?? '';
    }

    /**
     * Retrieves a list of models from manager settings, including engine title and model ID.
     *
     * @return array An array of models with 'id' and 'title' fields.
     */
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
