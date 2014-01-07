<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 * Migrate one Category between two Openemas
 *
 *
 **/
namespace Framework\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\OutputInterface;
use Onm\StringUtils;
use Onm\Settings as s;

class MigrateRegionToOnm extends Command
{
    /**
     * Array of database settings to use in migration process.
     *
     * @var array
     */
    protected $settings = array();


    protected $template = array(
        'settings' => array(),
        'tables' => array(),
        'relations' => array(
            // array(
            //     'table1' => 'Usuarios',
            //     'id1'    => 'idUsuarios',
            //     'table2' => 'Columna',
            //     'id2'    => 'idUsuario'
            // )
        ),
        'fields' => array(
            // 'id' => array(
            //     'table-origin' => 'Usuarios',
            //     'field-origin' => 'idUsuarios',
            //     'table-final'  => 'users',
            //     'type'         => 'raw'
            // )
        )
    );

    protected $usuarios = array(
        'settings' => array(
            'table-origin' => 'Usuarios',
            'id-origin'    => 'idUsuarios',
            'table-final'  => 'users',
            'id-final'     => 'id'
        ),
        'tables' => array(
            'Usuarios',
        ),
        'relations' => array(
            // array(
            //     'table1' => 'Usuarios',
            //     'id1'    => 'idUsuarios',
            //     'table2' => 'Columna',
            //     'id2'    => 'idUsuario'
            // ),
        ),
        'fields' => array(
            'id' => array(
                'table-origin' => 'Usuarios',
                'field-origin' => 'idUsuarios',
                'table-final'  => 'users',
                'type'         => 'raw'
            ),
            'username' => array(
                'table-origin' => 'Usuarios',
                'field-origin' => 'Nombre',
                'table-final'  => 'users',
                'type'         => 'raw'
            ),
            'image_avatar' => array(
                'table-origin' => 'Usuarios',
                'field-origin' => 'Foto',
                'table-final'  => 'images',
                'type'         => 'media'
            ),
        )
    );

    protected $articles = array(
        'settings' => array(
            'table-origin' => 'Noticias',
            'id-origin'    => 'idNoticias',
            'table-final'  => 'articles',
            'id-final'     => 'pk_article'
        ),
        'tables' => array(
            'Noticias',
        ),
        'relations' => array(
            // array(
            //     'table1' => 'Usuarios',
            //     'id1'    => 'idUsuarios',
            //     'table2' => 'Columna',
            //     'id2'    => 'idUsuario'
            // ),
        ),
        'fields' => array(
            'pk_article' => array(
                'table-origin' => 'Noticias',
                'field-origin' => 'idNoticias',
                'table-final'  => 'articles',
                'type'         => 'raw'
            ),
            'title_int' => array(
                'table-origin' => 'Noticias',
                'field-origin' => 'Titulo',
                'table-final'  => 'articles',
                'type'         => 'raw'
            ),
            'title_int' => array(
                'table-origin' => 'Noticias',
                'field-origin' => 'Titulo',
                'table-final'  => 'articles',
                'type'         => 'raw'
            ),
            'subtitle' => array(
                'table-origin' => 'Noticias',
                'field-origin' => 'Subtitulo',
                'table-final'  => 'articles',
                'type'         => 'raw'
            ),
            'summary' => array(
                'table-origin' => 'Noticias',
                'field-origin' => 'Entradilla',
                'table-final'  => 'articles',
                'type'         => 'raw'
            ),
            'body' => array(
                'table-origin' => 'Noticias',
                'field-origin' => 'Contenido',
                'table-final'  => 'contents',
                'type'         => 'raw'
            ),
            'starttime' => array(
                'table-origin' => 'Noticias',
                'field-origin' => 'HoraPublicacion',
                'table-final'  => 'contents',
                'type'         => 'timestamp'
            ),
            'created' => array(
                'table-origin' => 'Noticias',
                'field-origin' => 'HoraAlta',
                'table-final'  => 'contents',
                'type'         => 'timestamp'
            ),
            'available' => array(
                'table-origin' => 'Noticias',
                'field-origin' => 'Publicada',
                'table-final'  => 'contents',
                'type'         => 'boolean'
            ),
            'agency' => array(
                'table-origin' => 'Noticias',
                'field-origin' => 'Fuente',
                'table-final'  => 'articles',
                'type'         => 'raw'
            ),
            'views' => array(
                'table-origin' => 'Noticias',
                'field-origin' => 'Visitas',
                'table-final'  => 'contents',
                'type'         => 'integer'
            ),
        )
    );

    protected $categorias = array(
        'settings' => array(
            'table-origin' => 'Noticias_Categorias',
            'id-origin'    => 'idNoticias_Categorias',
            'table-final'  => 'content_categories',
            'id-final'     => 'pk_content_category'
        ),
        'tables' => array(
            'Noticias_Categorias',
        ),
        'relations' => array(),
        'fields' => array(
            'id' => array(
                'table-origin' => 'Noticias_Categorias',
                'field-origin' => 'idNoticias_Categorias',
                'table-final'  => 'content_categories',
                'type'         => 'raw'
            ),
            'name' => array(
                'table-origin' => 'Noticias_Categorias',
                'field-origin' => 'Nombre',
                'table-final'  => 'content_categories',
                'type'         => 'raw'
            ),
            'parent_id' => array(
                'table-origin' => 'Noticias_Categorias',
                'field-origin' => 'idPadre',
                'table-final'  => 'content_categories',
                'type'         => 'raw'
            ),
        )
    );

    protected $originalCategories = array();

    /**
     * Array of database translations
     *
     * @var array
     */
    protected $translations;

    /**
     * Array to save some results during the migration process.
     *
     * @var array
     */
    protected $stats = array(
        'user'      => array(
            'already_imported' => 0,
            'error'            => 0,
            'imported'         => 0
        ),
        'category' => array(
            'already_imported' => 0,
            'error'            => 0,
            'imported'         => 0
        ),
        'article'   => array(
            'already_imported' => 0,
            'error'            => 0,
            'imported'         => 0
        )
    );


    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this
            ->setDefinition(
                array(
                    new InputArgument(
                        'originDB',
                        InputArgument::REQUIRED,
                        'originDB'
                    ),
                    new InputArgument(
                        'finalDB',
                        InputArgument::REQUIRED,
                        'finalDB'
                    ),
                )
            )
            ->setName('migrate:region')
            ->setDescription('Migrate a region database to Openemas')
            ->setHelp(
                "\nThe <info>migrate:region</info> command migrates one region "
                . "DB to new openenmas database.\n\n<info>php bin/console migra"
                . "te:region originDB finalDB</info>"
            )->addOption(
                'host',
                null,
                InputOption::VALUE_OPTIONAL,
                'Host IP/name (default: localhost)'
            )->addOption(
                'type',
                null,
                InputOption::VALUE_OPTIONAL,
                'Database type'
            )->addOption(
                'user',
                null,
                InputOption::VALUE_OPTIONAL,
                'Database user (default:root)'
            )->addOption(
                'pass',
                null,
                InputOption::VALUE_OPTIONAL,
                'Database password'
            )->addOption(
                'url',
                null,
                InputOption::VALUE_OPTIONAL,
                'Region site url'
            )->addOption(
                'instance',
                null,
                InputOption::VALUE_OPTIONAL,
                'Instance name'
            )->addOption(
                'prefix',
                null,
                InputOption::VALUE_OPTIONAL,
                'Prefix used in data tables'
            )->addOption(
                'media-dir',
                null,
                InputOption::VALUE_OPTIONAL,
                'Instance media directory'
            );
    }

    /**
     * Executes the current command.
     *
     * @param InputInterface  $input  An InputInterface instance
     * @param OutputInterface $output An OutputInterface instance
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $this->output->writeln(
            '<fg=yellow>*** Starting ONM Migrator ***</fg=yellow>'
        );

        chdir(APPLICATION_PATH);

        $phpBinPath = exec('which php');
        $dialog = $this->getHelperSet()->get('dialog');

        // Default database configuration values
        $this->settings['database']['host'] = 'localhost';
        $this->settings['database']['type'] = 'mysqli';
        $this->settings['database']['user'] = 'root';
        $this->settings['database']['pass'] = '';

        // Get arguments (required)
        $this->settings['database']['origin'] = $input->getArgument('originDB');
        $this->settings['database']['final']  = $input->getArgument('finalDB');

        // Get options (optional)
        if ($input->getOption('host')) {
            $this->settings['database']['host'] = $input->getOption('host');
        }
        if ($input->getOption('type')) {
            $this->settings['database']['type'] = $input->getOption('type');
        }
        if ($input->getOption('user')) {
            $this->settings['database']['user'] = $input->getOption('user');
        }

        // Get database password or prompt
        if ($input->getOption('pass')) {
            $this->settings['database']['pass'] = $input->getOption('pass');
        } else {
            $this->settings['database']['pass'] = $dialog->askHiddenResponse(
                $this->output,
                'What is the database user password?',
                false
            );

            if (trim($this->settings['database']['pass']) == '') {
                throw new \Exception('The password can not be empty');
            }
        }

        // Get new instance name or prompt
        if ($input->getOption('instance')) {
            $this->settings['instance'] = $input->getOption('instance');
        } else {
            $this->settings['instance'] = $dialog->ask(
                $this->output,
                'What is the instance name? ',
                false
            );
            $this->output->writeln("-: ".$this->settings['instance']);

            if (trim($this->settings['instance']) == '') {
                throw new \Exception('The instance can not be empty');
            }
        }

        // Get prefix to use in database tables or prompt (it could be empty)
        if ($input->getOption('prefix')) {
            $this->settings['database']['prefix'] = $input->getOption('prefix');
        } else {
            $this->settings['database']['prefix'] = $dialog->ask(
                $this->output,
                'What is the prefix in database tables? (Ex: wp_2_) ',
                false
            );
            $this->output->writeln("-: ".$this->settings['database']['prefix']);
        }

        // Get url or prompt (it could be empty)
        if ($input->getOption('url')) {
            $this->settings['url'] = $input->getOption('url');
        } else {
            $this->settings['url'] = $dialog->ask(
                $this->output,
                'What is the ' . $this->settings['instance'] . ' site URL? ',
                false
            );
            $this->output->writeln("-: ".$this->settings['url']);
        }

        // Get media directory path or prompt (it could be empty)
        if ($input->getOption('media-dir')) {
            $this->settings['media-dir'] = $input->getOption('media-dir');
        } else {
            $this->settings['media-dir'] = $dialog->ask(
                $this->output,
                'Where is the ' . $this->settings['instance']
                . ' media directory? ',
                '/opt/backup_opennemas/mundiario/wp-content/uploads/'
            );
            $this->output->writeln("-: ".$this->settings['media-dir']);

            if (trim($this->settings['media-dir']) == '') {
                throw new \Exception('The directory can not be empty');
            }
        }

        $this->displayMigrateInfo($this->output);

        $this->configureMigrator();

        $this->prepareDatabase();
        $this->import();

        $this->displayResults();
        $this->displayFinalInfo();
    }

    /**
     * Displays a message before starting migration.
     */
    protected function displayMigrateInfo()
    {
        $this->output->writeln(
            "\n<fg=yellow>Migrating from <fg=red>"
            . $this->settings['database']['origin'] . '</fg=red> to <fg=green>'
            . $this->settings['database']['final']
            . '</fg=green>...</fg=yellow>'
        );

        $this->output->writeln(
            '   Instance:        ' . $this->settings['instance'] . "\n" .
            '   Site url:        ' . $this->settings['url'] . "\n" .
            '   Media directory: ' . $this->settings['media-dir'] . "\n"
        );

        $this->output->writeln(
            '   Database:      ' . $this->settings['database']['final'] ."\n" .
            '   Database host: ' . $this->settings['database']['host'] ."\n" .
            '   Database type: ' . $this->settings['database']['type'] ."\n" .
            '   Database user: ' . $this->settings['database']['user'] ."\n" .
            '   Database pass: ' . $this->settings['database']['pass'] ."\n"
        );
    }

    /**
     * Displays a message when ONM Migrator finishes the migration.
     */
    protected function displayFinalInfo()
    {
        $this->output->writeln(
            '<fg=yellow>*** ONM Importer finished ***</fg=yellow>'
        );
    }

    /**
     * Display results after importing.
     */
    public function displayResults()
    {
        var_dump($this->stats);
    }

    /**
     * Configures the current migrator.
     */
    protected function configureMigrator()
    {
        define('ORIGINAL_URL', $this->settings['url']);
        define('ORIGINAL_MEDIA', $this->settings['media-dir']);
        define('ORIGINAL_MEDIA_COMMON', '/opt/backup_opennemas/mundiario/wp-content/uploads/');

        define('CACHE_PREFIX', $this->settings['instance']);

        define('BD_HOST', $this->settings['database']['host']);
        define('BD_USER', $this->settings['database']['user']);
        define('BD_PASS', $this->settings['database']['pass']);
        define('BD_TYPE', $this->settings['database']['type']);
        define('BD_DATABASE', $this->settings['database']['final']);
        define('ORIGIN_BD_DATABASE', $this->settings['database']['origin']);
        define('PREFIX', $this->settings['database']['prefix']);

        // Initialize internal constants for logger
        // Logger
        define('SYS_LOG_PATH', realpath(SITE_PATH.DS.'..'.DS."tmp/logs"));
        define('INSTANCE_UNIQUE_NAME', $this->settings['instance']);

        define('IMG_DIR', "images");
        define('MEDIA_PATH', SITE_PATH."media".DS.INSTANCE_UNIQUE_NAME);

        // Initialize Globals and Database
        $GLOBALS['application'] = new \Application();
        \Application::initDatabase();
        // \Application::initLogger();

        // Create new connection and connect
        $GLOBALS['application']->connOrigin = \ADONewConnection(BD_TYPE);
        $GLOBALS['application']->connOrigin->Connect(
            BD_HOST,
            BD_USER,
            BD_PASS,
            ORIGIN_BD_DATABASE
        );

        $_SESSION['username'] = 'script';
        $_SESSION['userid']   = 11;
    }

    /**
     * Prepare database for importing.
     *
     * @note This method is called after collecting options/arguments and before
     *       importing.
     */
    protected function prepareDatabase()
    {
        // $sql = "ALTER TABLE `translation_ids` ".
        //     "ADD `slug`  VARCHAR( 200 ) NOT NULL DEFAULT  '' ";
        // $rss = $GLOBALS['application']->conn->Execute($sql);

        // $sql = "INSERT INTO user_groups (`pk_user_group`, `name`) VALUES (3, 'autores')";
        // $rss = $GLOBALS['application']->conn->Execute($sql);

        // $sql="DELETE FROM `wp-mundiario`.`wp_users` WHERE `wp_users`.`user_login` = 'macada'";
        // $request = $GLOBALS['application']->connOrigin->Prepare($sql);
    }

    /**
     * Import from origin database to final database
     */
    protected function import()
    {
        $this->loadTranslations();

        // $this->importUsers();
        // $this->importCategories();
        $this->importArticles();

        // $this->importCategories();
        // $this->updateMetadatas();
        // $this->importVideos();
        // $this->importbodyArticles();
        // $this->importOtherImages();


        // if ($this->settings['database']['prefix'] != 'wp_') {
        //     $this->importImages('wp_');
        // }
        // $this->importImages();

        // $this->updateBody();

        // $this->importGalleries();

        // $this->importVideos();
    }

    /**
     * Imports users from origin database.
     */
    protected function importUsers()
    {
        $data = $this->getSource($this->usuarios);
        return $this->saveUsers($this->usuarios, $data);
    }

    /**
     * Imports articles from origin database
     */
    protected function importArticles()
    {
        $data = $this->getSource($this->articles);
        var_dump($data);
        // return $this->saveArticles($this->articles, $data);
    }

    /**
     * Imports categories from origin database.
     */
    protected function importCategories()
    {
        $data = $this->getSource($this->categorias);
        return $this->saveCategories($this->categorias, $data);
    }

    /**
     * Gets all source fields from database for each entity.
     *
     * @param  array $schema Database schema.
     * @return array         Array of fields used to create new entities.
     */
    protected function getSource($schema)
    {
        $data = array();

        // Select all ids
        $sql = 'SELECT ' . $schema['settings']['table-origin'] . '.'
            . $schema['settings']['id-origin']
            . ' FROM ' . $schema['settings']['table-origin'];
            // . ' LIMIT 1,10';

        $request = $GLOBALS['application']->connOrigin->Prepare($sql);
        $rs      = $GLOBALS['application']->connOrigin->Execute($request);

        $ids     = $rs->getArray();

        $total = count($ids);
        foreach ($ids as $id) {
        // for ($x = 0; $x < 2; $x++) {
            // $id = $ids[$x];
            // $this->output->writeln('Processing article '. $x.' of '. $total);

            // Build sql statement 'SELECT' chunk
            $sql = 'SELECT ';
            $i = 0;
            foreach ($schema['fields'] as $key => $field) {
                $sql = $sql . $field['table-origin'] . '.'
                    . $field['field-origin'] . ' AS ' . $key;

                if ($i < count($schema['fields']) - 1) {
                    $sql .= ',';
                }

                $i++;
            }

            // Build sql statement 'FROM' chunk
            $sql .= ' FROM ';
            foreach ($schema['tables'] as $key => $table) {
                $sql .= $table;

                if ($key < count($schema['tables']) - 1) {
                    $sql .= ', ';
                }
            }

            // Build sql statement 'WHERE' chuck
            $sql.= ' WHERE '
                    . $schema['settings']['table-origin'] . '.'
                    . $schema['settings']['id-origin'] . '='
                    . $id[$schema['settings']['id-origin']];

            if (isset($schema['relations'])
                    && count($schema['relations']) > 0) {
                foreach ($schema['relations'] as $key => $relation) {
                    if ($key < count($schema['relations'])) {
                        $sql .= ' AND ';
                    }
                    $sql .=  $relation['table1'] . '.' . $relation['id1'] . '='
                        . $relation['table2'] . '.' . $relation['id2'];
                }

            }

            var_dump($sql);

            // Execute sql and save in array
            $request = $GLOBALS['application']->connOrigin->Prepare($sql);
            $rs      = $GLOBALS['application']->connOrigin->Execute($request);
            $results = $rs->getArray();

            if (count($results) > 0) {
                foreach ($results as $result) {
                    $data[] = $result;
                }
            }
        }

        return $data;
    }

    /**
     * Saves the users and returns the amount of new users.
     *
     * @param  array    $schema Database schema.
     * @param  array    $data   Users to save.
     */
    protected function saveUsers($schema, $data)
    {
        foreach ($data as $item) {
            if (!$this->elementIsImported($item['id'], 'author')) {
                // Default values
                $values = array(
                    'sessionexpire' => '30',
                    'url'           => '',
                    'bio'           => '',
                    'avatar_img_id' => 0,
                    'type'          => 0,
                    'deposit'       => '',
                    'token'         => '',
                    'activated'     => 1,
                    'id_user_group' => array('3'),
                );

                $values['username'] = isset($item['username']) ?
                    $this->parseField(
                        $item['username'],
                        $schema['fields']['username']['type']
                    ) : null;

                $values['password'] = isset($item['password']) ?
                    $this->parseField(
                        $item['password'],
                        $schema['fields']['password']['type']
                    ) : null;

                $values['email'] = isset($item['email']) ?
                    $this->parseField(
                        $item['email'],
                        $schema['fields']['email']['type']
                    ) : null;

                $values['name'] = isset($item['name']) ?
                    $this->parseField(
                        $item['name'],
                        $schema['fields']['name']['type']
                    ) : null;

                $values['avatar_img_id'] = isset($item['avatar_img_id']) ?
                    $this->parseField(
                        $item['avatar_img_id'],
                        $schema['fields']['avatar_img_id']['type']
                    ) : 0;

                $values['bio'] = isset($item['bio']) ?
                    $this->parseField(
                        $item['bio'],
                        $schema['fields']['bio']['type']
                    ) : '';

                $values['url'] = isset($item['url']) ?
                    $this->parseField(
                        $item['url'],
                        $schema['fields']['url']['type']
                    ) : '';


                try {
                    $user   = new \User();
                    $user->create($values);

                    $id = $user->id;
                    $this->createTranslation($item['id'], $id, 'author');

                    $this->stats['user']['imported']++;
                } catch (\Exception $e) {
                    $this->stats['user']['error']++;
                }
            } else {
                $this->stats['user']['already_imported']++;
            }
        }
    }

    /**
     * Saves the categories and returns the amount of new categories.
     *
     * @param  array    $schema Database schema.
     * @param  array    $data   Categories to save.
     */
    protected function saveCategories($schema, $data)
    {
        foreach ($data as $item) {
            if (!$this->elementIsImported($item['id'], 'category')) {
                $values = array(
                    'name'              => '',
                    'title'             => '',
                    'inmenu'            => 0,
                    'posmenu'           => 10,
                    'subcategory'       => 0,
                    'internal_category' => 0,
                    'logo_path'         => null,
                    'color'             => null,
                    'params'            => null
                );

                $values['title'] = isset($item['name']) ?
                    $this->parseField(
                        $item['name'],
                        $schema['fields']['name']['type']
                    ) : '';


                $values['name'] = isset($item['name']) ?
                    $this->parseField(
                        $item['name'],
                        $schema['fields']['name']['type']
                    ) : '';

                $values['subcategory'] = isset($item['parent_id'])
                    && isset($this->translations['category'][$item['parent_id']]) ?
                    $this->translations['category'][$item['parent_id']]
                    : null;

                try {
                    $category = new \ContentCategory();
                    $category->create($values);

                    $id = $category->pk_content_category;
                    $this->createTranslation($item['id'], $id, 'category');

                    $this->stats['category']['already_imported']++;
                } catch (\Exception $e) {
                    $this->stats['category']['error']++;
                }
            } else {
                $this->stats['category']['already_imported']++;
            }
        }
    }

    /**
     * Creates a translation entry.
     *
     * @param integer $old  Old content id used in old database.
     * @param integer $new  New content id.
     * @param string  $type Type of the translation.
     */
    protected function createTranslation($old, $new, $type)
    {
        $sql = 'INSERT INTO translation_ids(`pk_content_old`, `pk_content`,
            `type`) VALUES (?,?,?)';
        $values = array($old, $new, $type);

        $stmt = $GLOBALS['application']->conn->Prepare($sql);
        $rss  = $GLOBALS['application']->conn->Execute($stmt, $values);

        if (!$rss) {
            $this->output->writeln(
                'createTranslation: '
                . $GLOBALS['application']->conn->ErrorMsg()
            );
        }

        $this->translations[$type][$old] = $new;
    }

    /**
     * Parses and returns the field.
     *
     * @param  string $field Field to parse.
     * @param  string $type  Type of the field.
     * @return mixed         String if the field
     */
    protected function parseField($field, $type)
    {
        switch ($type) {
            case 'raw': // Remove spaces at beginning and end
                return trim($field);
                break;
            case 'body': // Replaces the content of the field
                return '<p>'. preg_replace(
                    array(
                        "/([\r\n])+/i",
                        "/([\n]{2,})/i",
                        "/([\n]{2,})/i",
                        "/(\n)/i"
                    ),
                    array('</p><p>', '</p><p>', '<br>', '<br>'),
                    $field
                ) . '</p>';
                break;
            case 'media': // Saves the media
                return 0;
            default:
                return $field;
                break;
        }
    }

    /**
     * Fetches all translations.
     */
    protected function loadTranslations()
    {
        $sql = 'SELECT * FROM translation_ids';
        $this->translations = array(
            'author'   => array(),
            'category' => array(),
            'article'  => array()
        );


        $rs = $GLOBALS['application']->conn->Execute($sql);
        $translations = $rs->GetArray();

        foreach ($translations as $translation) {
            $this->translations[$translation['type']]
                [$translation['pk_content_old']] = $translation['pk_content'];
        }
    }

    /**
     * Returns the new category id.
     *
     * @param int $id Old category id.
     */
    protected function matchCategory($id)
    {
        if (array_key_exists($id, $this->translations['category'])) {
            return $this->translation['categories'][$id];
        } else {
            return 20;
        }
    }

    /**
     * Read the correspondence between identifiers
     *
     * @return mixed The new element id if it's already imported. Otherwise,
     *                return false.
     */
    protected function elementIsImported($oldId, $type)
    {
        if (isset($this->translations[$type][$oldId])) {
            return $this->translations[$type][$oldId];
        }

        return false;
    }

    /**
     * Read articles data and insert in new database
     *
     * @return void
     **/
    // protected function importArticles()
    // {

    //     $where = " `".PREFIX."term_relationships`.`term_taxonomy_id` IN (".implode(', ', array_values($this->originalCategories)).") ";
    //     $limit = " ORDER BY `".PREFIX."term_relationships`.`term_taxonomy_id`";

    //     $sql = "SELECT * FROM `".PREFIX."posts`, `".PREFIX."term_relationships` WHERE ".
    //         "`post_type` = 'post' AND `ID`=`object_id` AND post_status='publish' ".
    //         " AND ".$where." ".$limit;

    //     $request = $GLOBALS['application']->connOrigin->Prepare($sql);
    //     $rs      = $GLOBALS['application']->connOrigin->Execute($request);

    //     if (!$rs) {
    //         $this->output->writeln('DB problem: '. $GLOBALS['application']->connOrigin->ErrorMsg());
    //     } else {
    //         $totalRows = $rs->_numOfRows;
    //         $current   = 1;

    //         while (!$rs->EOF) {
    //             $originalArticleID = $rs->fields['ID'];
    //             if ($this->elementIsImported($originalArticleID, 'article') ) {
    //                  //$this->output->writeln("[{$current}/{$totalRows}] Article with id {$originalArticleID} already imported\n");
    //             } else {
    //                // $this->output->writeln("[{$current}/{$totalRows}] Importing article with id {$originalArticleID} - ");

    //                 $data = $this->clearLabelsInBodyArticle($rs->fields['post_content']);
    //                 if (!empty($rs->fields['post_excerpt'])) {
    //                     $summary = $this->convertoUTF8($rs->fields['post_excerpt']);
    //                 } else {
    //                     $summary = $this->convertoUTF8(strip_tags(substr($data['body'], 0, 250)));
    //                 }
    //                 $values = array(
    //                     'title' => $this->convertoUTF8($rs->fields['post_title']),
    //                     'category' => $this->matchCategory($rs->fields['term_taxonomy_id']),
    //                     'with_comment' => 1,
    //                     'available' => 1,
    //                     'content_status' => 1,
    //                     'frontpage' => 0,
    //                     'in_home' => 0,
    //                     'title_int' => $this->convertoUTF8($rs->fields['post_title']),
    //                     'metadata' => \Onm\StringUtils::get_tags($this->convertoUTF8($rs->fields['post_title'])),
    //                     'subtitle' => '',
    //                     'slug' => $rs->fields['post_name'],
    //                     'agency' => '',
    //                     'summary' => $summary,
    //                     'description' => strip_tags(substr($summary, 0,150)),
    //                     'body' => $data['body'],
    //                     'posic' => 0,
    //                     'id' => 0,
    //                     'img1' => $data['img'],
    //                     'img2' => $data['img'],
    //                     'img2_footer' => $data['footer'],
    //                     'fk_video' => '',
    //                     'fk_video2' => '',
    //                     'footer_video2' => '',
    //                     'created' => $rs->fields['post_date_gmt'],
    //                     'starttime' => $rs->fields['post_date_gmt'],
    //                     'changed' => $rs->fields['post_modified_gmt'],
    //                     'fk_user' => $this->elementIsImported($rs->fields['post_author'], 'user'),
    //                     'fk_author' => $this->elementIsImported($rs->fields['post_author'], 'user'),
    //                     'fk_publisher' => $this->elementIsImported($rs->fields['post_author'], 'user'),
    //                     'fk_publisher' => $this->elementIsImported($rs->fields['post_author'], 'user'),
    //                 );

    //                 $article      = new \Article();
    //                 $newArticleID = $article->create($values);

    //                 if (!empty($newArticleID)) {
    //                     $this->createTranslation($originalArticleID, $newArticleID, 'article', $rs->fields['post_name']);
    //                 //  $this->output->writeln('-'. $originalArticleID.'->'.
    //                 //         $newArticleID. ' article ok');
    //                 $this->output->write('.');
    //                 } else {
    //                     $this->output->writeln('Problem inserting article '.$originalArticleID.
    //                         ' - '. $rs->fields['post_name'] .'\n');
    //                 }
    //             }
    //             $current++;
    //             $rs->MoveNext();
    //         }
    //         $this->output->writeln('Imported  '.$current.' articles \n');

    //     $rs->Close();
    //     }
    //     return true;
    // }

    /**
     * Read images data and insert this in new database
     *
     * @return void
     **/
    protected function importImages($prefix = null)
    {
        if (empty($prefix)) {
           $prefix = PREFIX;
        }
        $settings = array( 'image_thumb_size'=>'140',
                            'image_inner_thumb_size'=>'470',
                            'image_front_thumb_size'=>'350');
        foreach ($settings as $key => $value) {
            s::set($key, $value);
        }
        $sql = "SELECT * FROM `".$prefix."posts` WHERE ".
            "`post_type` = 'attachment'  AND post_status !='trash' ";

        $request = $GLOBALS['application']->connOrigin->Prepare($sql);
        $rs      = $GLOBALS['application']->connOrigin->Execute($request);

        $oldID = $this->elementIsImported('fotos', 'category');
        if (empty($oldID)) {
            $IDCategory ='1'; //fotografias
        } else {
           $IDCategory = $this->matchCategory($oldID); //assign category 'Fotos' for media elements
        }
        if (!$rs) {
            $this->output->writeln($GLOBALS['application']->connOrigin->ErrorMsg());
        } else {

            $totalRows = $rs->_numOfRows;
            $current   = 1;
            $photo     = new \Photo();

            while (!$rs->EOF) {
                if(!empty($rs->fields['guid'])) {
                    if ($this->elementIsImported($rs->fields['ID'], 'image')) {
                        $this->output->writeln("[{$current}/{$totalRows}] Image already imported");
                    } else {

                        $originalImageID = $rs->fields['ID'];

                        ///http://mundiario.com/wp-content/uploads/2013/06/Brasil-360x225.png
                        //http://mundiario.com/galicia/files/2013/07/6696140347_824d45603a_z-360x225.jpg
                        //http://mundiario.com/emprendedores/files/2013/07/6696140347_824d45603a_z-360x225.jpg
                        $local_file = str_replace(ORIGINAL_URL, ORIGINAL_MEDIA, $rs->fields['guid']);

                        $imageData = array(
                            'title' => $this->convertoUTF8(strip_tags($rs->fields['post_title'])),
                            'category' => $IDCategory,
                            'fk_category' => $IDCategory,
                            'category_name'=> '',
                            'content_status' => 1,
                            'frontpage' => 0,
                            'in_home' => 0,
                            'metadata' => \Onm\StringUtils::get_tags($this->convertoUTF8($rs->fields['post_name'].$rs->fields['post_excerpt'])),
                            'description' => $this->convertoUTF8(strip_tags(substr($rs->fields['post_excerpt'], 0, 150))),
                            'id' => 0,
                            'created' => $rs->fields['post_date_gmt'],
                            'starttime' => $rs->fields['post_date_gmt'],
                            'changed' => $rs->fields['post_modified_gmt'],
                            'fk_user' =>  $this->elementIsImported($rs->fields['post_author'], 'user'),
                            'fk_author' =>  $this->elementIsImported($rs->fields['post_author'], 'user'),
                            'fk_publisher' => $this->elementIsImported($rs->fields['post_author'], 'user'),
                            'fk_user_last_editor' => $this->elementIsImported($rs->fields['post_author'], 'user'),
                            'local_file' => $local_file,
                            'author_name' => '',
                        );

                        $date = new \DateTime($rs->fields['post_date_gmt']);
                        $imageID = @$photo->createFromLocalFile($imageData, $date->format('/Y/m/d/'));

                        if (!empty($imageID)) {
                            $this->createTranslation($originalImageID, $imageID, 'image', $rs->fields['post_name']);
                            // $this->output->writeln('- Image '. $imageID. ' ok');
                            $this->output->write('.');
                        } else {
                            $imageData['local_file'] = str_replace(ORIGINAL_URL, ORIGINAL_MEDIA_COMMON, $rs->fields['guid']);

                            $imageID = @$photo->createFromLocalFile($imageData, $date->format('/Y/m/d/'));
                            if (!empty($imageID)) {
                                $this->createTranslation($originalImageID, $imageID, 'image', $rs->fields['post_name']);
                                // $this->output->writeln('- Image '. $imageID. ' ok');
                            } else {
                                $this->output->write('.');
                                $this->output->writeln('Problem image '.$originalImageID.
                                    "-". $rs->fields['guid'] .' -> '.$imageData['local_file'] ."\n");
                            }
                        }
                    }
                }
                $current++;
                $rs->MoveNext();
            }
            $this->output->writeln("Importer Images Finished");
            $rs->Close();
        }
    }


    protected function importGalleries()
    {
        $sql = "SELECT * FROM `".PREFIX."posts` WHERE ".
            "`post_content` LIKE '%gallery%'  AND post_status !='trash' ";
         /*[gallery link="file" ids="8727,8728,8729,8730,8731,8732"]*/

        $request    = $GLOBALS['application']->connOrigin->Prepare($sql);
        $rs         = $GLOBALS['application']->connOrigin->Execute($request);
        $oldID = $this->elementIsImported('fotos', 'category');

        if (empty($oldID)) {
            $IDCategory ='3'; //galleries
        } else {
           $IDCategory = $this->matchCategory($oldID); //assign category 'Fotos' for media elements
        }


        if (!$rs) {
            $this->output->writeln($GLOBALS['application']->connOrigin->ErrorMsg());
        } else {

            $totalRows = $rs->_numOfRows;
            $current   = 1;
            $album     = new \Album();

            while (!$rs->EOF) {
                 $originalID = $rs->fields['ID'];
                if ($this->elementIsImported($originalID, 'gallery')) {
                    $this->output->writeln("[{$current}/{$totalRows}] Gallery already imported");
                } else {

                    preg_match_all('/\[gallery.*?ids="(.*)".*?\]/', $rs->fields['post_content'], $result);

                    if (!empty($result[0]) ) {
                        $ids = array();
                        $originIds = explode(',', $result[1][0]);
                        foreach ($originIds as $id) {
                            $ids[] =$this->elementIsImported($id, 'image');
                        }

                        $newBody = preg_replace('/\[gallery.*?ids="(.*)".*?\]/', '', $rs->fields['post_content']);
                        $newBody = $this->convertoUTF8(strip_tags($newBody, '<p><a><br>'));

                        $data = array(
                            'title'          => $this->convertoUTF8($rs->fields['post_title']),
                            'category'       => $IDCategory,
                            'with_comment'   => 1,
                            'content_status' => 1,
                            'available'      => 1,
                            'metadata'       => \Onm\StringUtils::get_tags($this->convertoUTF8($rs->fields['post_title'])),
                            'subtitle'       => '',
                            'agency'         => '',
                            'summary'        => $newBody,
                            'fuente'         => '',
                            'category_name'  => 'fotos',
                            'description'    => $newBody,
                            'created'        => $rs->fields['post_date_gmt'],
                            'starttime'      => $rs->fields['post_date_gmt'],
                            'changed'        => $rs->fields['post_modified_gmt'],
                            'fk_user'        => $this->elementIsImported($rs->fields['post_author'], 'user'),
                            'fk_author'      => $this->elementIsImported($rs->fields['post_author'], 'user'),
                            'fk_publisher'   => $this->elementIsImported($rs->fields['post_author'], 'user'),
                            'fk_user_last_editor' => $this->elementIsImported($rs->fields['post_author'], 'user'),
                            'slug'           => $rs->fields['post_name'],
                            'album_photos_id' => $ids,
                            'album_photos_footer'=> null,
                            'album_frontpage_image' => $ids[0],
                        );

                        $album->cover_id = $ids[0];

                        $result  = $album->create($data);
                        $albumID = $result->id;
                        if (!empty($albumID)) {
                            $this->createTranslation($originalID, $albumID, 'gallery', $rs->fields['post_name']);
                            //$this->updateFields('`available` ='.$rs->fields['available'], $rs->fields['pk_content']);
                         //   $this->output->writeln('- Gallery '. $albumID. ' ok');
                        } else {
                            $this->output->writeln('Problem inserting album '.$originalID.' - '.$rs->fields['post_name'] ."\n");
                        }
                    }
                }

                $current++;
                $rs->MoveNext();
            }
        }
        $rs->Close(); # optional
        $this->output->writeln("Importer Galleries Finished");

    }

    /**
     * update some fields in content table
     *
     * @param int $contentId the content id
     * @param string $params new values for the content table
     *
     * @return void
     **/
    protected function updateFields($contentID, $params)
    {
        if (isset($contentID) && isset($params)) {
            $sql    = 'UPDATE `contents` SET {$params}  WHERE pk_content=?';
            $values = array($params, $contentID);

            $stmt = $GLOBALS['application']->conn->Prepare($sql);
            $rss  = $GLOBALS['application']->conn->Execute($stmt, $values);
            if (!$rss) {
                $this->output->writeln($GLOBALS['application']->conn->ErrorMsg());
            }

        } else {
            //$this->output->writeln("Please provide a contentID and views to update it.");
        }
    }

    /**
     * Clear body for
     *
     * @return string
     **/
    protected function getOnmIdImage($guid)
    {
        $sql = "SELECT ID FROM `".PREFIX."posts` WHERE ".
            "`post_type` = 'attachment'  AND post_status !='trash' ".
            " AND guid = '".$guid."'";


        // Fetch the list of Opinions available for one author in EditMaker
        $request = $GLOBALS['application']->connOrigin->Prepare($sql);
        $rs      = $GLOBALS['application']->connOrigin->Execute($request);

        $imageID='';
        if (!$rs || empty($rs->fields['ID'])) {
            $sql = "SELECT ID FROM `wp_posts` WHERE ".
            "`post_type` = 'attachment'  AND post_status !='trash' ".
            " AND guid LIKE '%".$guid."%'";
            $request = $GLOBALS['application']->connOrigin->Prepare($sql);
            $rs      = $GLOBALS['application']->connOrigin->Execute($request);
            if (!$rs->fields['ID']) {
                // $this->output->writeln('- Image '. $guid. ' fault');
            } else {
                $imageID = $this->elementIsImported($rs->fields['ID'], 'image');
            }

        } else {
            $imageID = $this->elementIsImported($rs->fields['ID'], 'image');

        }

        return $imageID;

    }

    protected function clearLabelsInBodyArticle($body)
    {

        /*[gallery link="file" ids="8727,8728,8729,8730,8731,8732"]
        [caption id="attachment_9084" align="alignnone" width="400"]<a href="http://mundiario.com/wp-content/uploads/2013/06/alicante-2.jpg"><img class="size-full wp-image-9084" alt="EstaciÃ³n de Alicante, desde ahora enlazada por AVE." src="http://mundiario.com/wp-content/uploads/2013/06/alicante-2.jpg" width="400" height="225" /></a> EstaciÃ³n de Alicante, desde ahora enlazada por AVE.[/caption]
        <a href="http://mundiario.com/wp-content/uploads/2013/05/368875884_b4b5266888_z.jpg"><img class="alignnone size-medium wp-image-7398" alt="Angela Merkel - World Economic Forum Annual Meeting Davos 2007" src="http://mundiario.com/wp-content/uploads/2013/05/368875884_b4b5266888_z-420x275.jpg" width="420" height="275" /></a>
        */
        $result = array();
        #Deleted [caption id="attachment_2302" align="aligncenter" width="300" caption="El partido ultra Jobbik siembra el terror entre las minorías y los extranjeros en Hungría."][/caption]
        //Allow!!<a title="Kobe Bryant" href="http://www.flickr.com/photos/42161969@N03/4067656449/" target="_blank"><img title="Kobe Bryant" alt="Kobe Bryant" src="http://farm3.staticflickr.com/2493/4067656449_a576ba8a59.jpg" /></a>


        $newBody = $body;
        $img     = '';
        $gallery = '';
        $footer  = '';
        $photo     = new \Photo();
        $allowed = '<i><b><p><a><br><ol><ul><li><strong><em>';
        $patern  = '@<a .*?href=".+?".*?><img .*?src="?('.preg_quote(ORIGINAL_URL).'.+?)".*?><\/a>@';
        preg_match_all($patern, $body, $result);
        if (!empty($result[1])) {
            $guid    = $result[1][0];
            $img     = $this->getOnmIdImage($guid);
            $newBody = $body;
            if (empty($img)) {


                //-420x278.jpg
                preg_match_all ( "@(.*)-[0-9]{3,4}x[0-9]{3,4}.(.*)@", $guid, $result);
                if (!empty($result[1]) ) {

                    $newGuid = $result[1][0].".".$result[2][0];
                    var_dump($newGuid);
                    $img     = $this->getOnmIdImage($newGuid);

                    if (empty($img)) {
                        $this->output->writeln('- Image from Body '. $guid. ' fault');
                    }
                }
                $date = new \DateTime();
                $date = $date->format('Y-m-d H:i:s');
                $local_file = str_replace(ORIGINAL_URL, ORIGINAL_MEDIA, $guid);
                $oldID = $this->elementIsImported('fotos', 'category');
                if(empty($oldID)) {
                    $oldID ='1';
                }
                $IDCategory = $this->matchCategory($oldID); //assign category 'Fotos' for media elements

                $imageData = array(
                        'title' => $this->convertoUTF8(strip_tags($guid)),
                        'category' => $IDCategory,
                        'fk_category' => $IDCategory,
                        'category_name'=> '',
                        'content_status' => 1,
                        'frontpage' => 0,
                        'in_home' => 0,
                        'metadata' => \Onm\StringUtils::get_tags($this->convertoUTF8($guid)),
                        'description' => \Onm\StringUtils::get_tags($this->convertoUTF8($guid)),
                        'id' => 0,
                        'created' => $rs->fields['post_date_gmt'],
                        'starttime' => $rs->fields['post_date_gmt'],
                        'changed' => $rs->fields['post_modified_gmt'],
                        'fk_user' =>  $this->elementIsImported(7, 'user'),
                        'fk_author' =>  $this->elementIsImported(7, 'user'),
                        'fk_publisher' => $this->elementIsImported(7, 'user'),
                        'fk_user_last_editor' => $this->elementIsImported(7, 'user'),
                        'local_file' => $local_file,
                        'author_name' => '',
                    );

                $img  = $photo->createFromLocalFile($imageData);
                $this->output->writeln('- Image from Body inserted'. $img. ' ');
            }
            $newBody = preg_replace($patern, '', $body);
        //    $newBody = $this->convertoUTF8(strip_tags($newBody, $allowed));
        }

        if (empty($img)) {
            preg_match_all('@\[caption .*?id="attachment_(.*)" align=.*?\].* alt="?(.*?)".*?\[\/caption\]@', $body, $result);
            if (!empty($result[1]) ) {
                $id      = $result[1][0];
                $img     = $this->elementIsImported($id, 'image');
                $footer  = $result[2][0];

                $newBody = preg_replace('/\[caption .*?\].*?\[\/caption\]/', '', $body);
              //  $newBody = $this->convertoUTF8(strip_tags($newBody, $allowed));
            }
        }

        preg_match_all('@\[gallery.*?ids="(.*)".*?\]@', $body, $result);
        if (!empty($result[0]) ) {
            $id      = $result[1][0];
            $gallery = $this->elementIsImported($id, 'gallery');
            $newBody = preg_replace('/\[gallery.*?ids="(.*)".*?\]/', '', $body);
       //     $newBody = $this->convertoUTF8(strip_tags($newBody, $allowed));
        }

        $str = preg_replace(array("/([\r\n])+/i", "/([\n]{2,})/i", "/([\n]{2,})/i", "/(\n)/i"), array('</p><p>', '</p><p>', '<br>', '<br>'), $newBody);
        $newBody = '<p>'.($str).'</p>';

        return array('img' => $img, 'body' => $newBody, 'gallery' => $gallery, 'footer' => $footer);

    }

       /**
     * Process an uploaded photo for user
     *
     * @param Symfony\Component\HttpFoundation\File\UploadedFile $file the uploaded file
     * @param string $userName the user real name
     *
     * @return Response the response object
     **/
    protected function uploadUserAvatar($file, $userName)
    {
        // Generate image path and upload directory
        $userNameNormalized      = \Onm\StringUtils::normalize_name($userName);
        $relativeAuthorImagePath ="/authors/".$userName;
        $uploadDirectory         =  MEDIA_PATH."/images".$relativeAuthorImagePath;

        // Get original information of the uploaded image
        $originalFileName = $file;
        $originalFileData = pathinfo($originalFileName);
        $fileExtension    = strtolower($originalFileData['extension']);

        // Generate new file name
        $currentTime = gettimeofday();
        $microTime   = intval(substr($currentTime['usec'], 0, 5));
        $newFileName = date("YmdHis").$microTime.".".$fileExtension;

        // Check upload directory
        if (!is_dir($uploadDirectory)) {
            \FilesManager::createDirectory($uploadDirectory);
        }

        // Upload file
        $fileCopied = @copy($file, $uploadDirectory."/".$newFileName);
        $photoId = 0;
        if ($fileCopied) {
            // Get all necessary data for the photo
            $infor = new \MediaItem($uploadDirectory.'/'.$newFileName);
            $data  = array(
                'title'       => $originalFileName,
                'name'        => $newFileName,
                'user_name'   => $newFileName,
                'path_file'   => $relativeAuthorImagePath,
                'nameCat'     => $userName,
                'category'    => '',
                'created'     => $infor->atime,
                'changed'     => $infor->mtime,
                'size'        => round($infor->size/1024, 2),
                'width'       => $infor->width,
                'height'      => $infor->height,
                'type'        => $infor->type,
                'fk_author'   => $this->elementIsImported(7, 'user'),
                'author_name' => '',
            );

            // Create new photo
            $photo = new \Photo();
            $photoId = $photo->create($data);

        } else {
            // $this->output->writeln('- No photo move -',"{$file}, '-> '.{$uploadDirectory}."/".{$newFileName}");
        }
        return $photoId;
    }

    /**
     * Converts a given string to UTF-8 codification
     *
     * @return string
     **/
    protected function convertoUTF8($string)
    {
       // return mb_convert_encoding($string, 'UTF-8');
       return $string;
    }


     /**
     * update some fields in content table
     *
     * @param int $contentId the content id
     * @param string $params new values for the content table
     *
     * @return void
     **/
    public  function updateBody()
    {
        $sql = "SELECT body, pk_article FROM articles WHERE body != ''";
        $rs = $GLOBALS['application']->conn->Execute($sql);

        $values= array();
        while (!$rs->EOF) {

            $newBody = preg_replace(
                array("/([\r\n])+/i", "/([\n]{2,})/i", "/([\n]{2,})/i", "/(\n)/i"),
                array('</p><p>', '</p><p>', '<br>', '<br>'),
                $rs->fields['body']
            );
            $newBody = '<p>'.($newBody).'</p>';

            $values[] =  array(
                $newBody,
                $rs->fields['pk_article'],
            );

            $rs->MoveNext();
        }

        if (!empty($values)) {
            $sql    = 'UPDATE `articles` SET body =?  WHERE pk_article=?';

            $stmt = $GLOBALS['application']->conn->Prepare($sql);
            $rss  = $GLOBALS['application']->conn->Execute($stmt, $values);
            if (!$rss) {
                $this->output->writeln($GLOBALS['application']->conn->ErrorMsg());
            }

        } else {
            //$this->output->writeln("Please provide a contentID and views to update it.");
        }

    }


    /***** functions fixing mundiario  fails */

    // public function updateMetadatas()
    // {

    //     $sql = "SELECT pk_content, metadata, title FROM contents ";
    //     $rs = $GLOBALS['application']->conn->Execute($sql);

    //     $values= array();
    //     while (!$rs->EOF) {

    //         $tags = StringUtils::get_tags($rs->fields['metadata']);
    //         if(empty($tags)) {
    //             $tags = StringUtils::get_tags($rs->fields['title']);
    //         }

    //         $values[] =  array(
    //             $tags,
    //             $rs->fields['pk_content'],
    //         );

    //         $rs->MoveNext();
    //     }

    //     if (!empty($values)) {
    //         $sql    = 'UPDATE `contents` SET metadata =?  WHERE pk_content=?';

    //         $stmt = $GLOBALS['application']->conn->Prepare($sql);
    //         $rss  = $GLOBALS['application']->conn->Execute($stmt, $values);
    //         if (!$rss) {
    //             $this->output->writeln($GLOBALS['application']->conn->ErrorMsg());
    //         }

    //     } else {
    //         //$this->output->writeln("Please provide a contentID and views to update it.");
    //     }
    // }


     /* create new video */
    // public function createVideo($video)
    // {
    //     $newVideoID = null;

    //     preg_match(
    //         '%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i',
    //         $video,
    //         $match
    //     );

    //     $oldID = $this->elementIsImported('videos', 'category');

    //     if (empty($oldID)) {
    //         $IDCategory ='6'; //videos
    //     } else {
    //         $IDCategory = $this->matchCategory($oldID); //assign category 'videos' for media elements
    //     }

    //     if (!empty($match[1])) {

    //         $url= "http://www.youtube.com/watch?v=".$match[1] ;

    //         if ($url) {
    //             try {
    //                 $videoP = new \Panorama\Video($url);
    //                 $information = $videoP->getVideoDetails();

    //                 $values = array(
    //                     'file_path'      => $url,
    //                     'video_url'      => $url,
    //                     'category'       => $IDCategory,
    //                     'available'      => 1,
    //                     'content_status' => 1,
    //                     'title'          => $information['title'],
    //                     'metadata'       => StringUtils::get_tags($information['title']),
    //                     'description'    => $information['title'].' video '.$url,
    //                     'author_name'    => $url,
    //                 );

    //             } catch (\Exception $e) {
    //                $this->output->writeln("\n 1 Can't get video information. Check the $url");
    //                 return;
    //             }

    //             $video = new \Video();
    //             $values['information'] = $information;

    //             try {
    //                 $newVideoID = $video->create($values);
    //             } catch (\Exception $e) {

    //                 $this->output->writeln("1 Problem with video: {$e->getMessage()} {$url}  ");
    //             }

    //             if (empty($newVideoID)) {
    //                 $this->output->writeln("2 Problem with video: {$url}  ");
    //             }

    //         } else {
    //             $this->output->writeln("There was an error while uploading the form, not all the required data was sent.");

    //         }
    //     }

    //     $this->output->writeln("new id {$newVideoID} [DONE]");
    //     return $newVideoID;
    // }

    // public function importVideos()
    // {

    //     $sql = "SELECT * FROM `".PREFIX."postmeta` WHERE ".
    //         "`meta_key` = 'usn_videolink' ";

    //     $request = $GLOBALS['application']->connOrigin->Prepare($sql);
    //     $rs =$GLOBALS['application']->connOrigin->Execute($request);

    //     if (!$rs) {
    //         $this->output->writeln($GLOBALS['application']->connOrigin->ErrorMsg());

    //     } else {

    //         $totalRows = $rs->_numOfRows;
    //         $current = 1;
    //         while (!$rs->EOF) {

    //             $videoID = $this->createVideo($rs->fields['meta_value']);
    //             if(!empty($videoID)) {
    //                 $this->createTranslation($rs->fields['post_id'], $videoID, 'youtube', $rs->fields['meta_value']);
    //             }
    //             $current++;
    //             $rs->MoveNext();
    //         }
    //         $rs->Close(); # optional
    //     }

    // }

    // public function importbodyArticles()
    // {
    //     //check if body is empty & get from wp
    //     //check if /files/emprendedores or /files/galicia
    //     $sql2 = "SELECT  pk_content_old, pk_content FROM `articles`, `translation_ids` "
    //      ." WHERE pk_article = pk_content AND (body = '' OR img1 = 0 )";

    //     $request = $GLOBALS['application']->conn->Prepare($sql2);
    //     $rs2     = $GLOBALS['application']->conn->Execute($request);
    //     if (!$rs2) {
    //         $this->output->writeln('- sql '.$sql2);
    //         $this->output->writeln($GLOBALS['application']->conn->ErrorMsg());
    //     }

    //     $items = $rs2->getArray();
    //     $this->output->writeln('- hay '.count($items));

    //     $pks   = array();
    //     foreach ($items as $item) {
    //         $pk_content_old       = $item['pk_content_old'];
    //         $pks[$pk_content_old] = $item['pk_content'];
    //     }
    //     $values = array();

    //     $sql = "SELECT * FROM `".PREFIX."posts` WHERE ".
    //         "ID IN (".implode(', ', array_keys($pks)).")";

    //     // Fetch the list of Opinions available for one author in EditMaker
    //     $request = $GLOBALS['application']->connOrigin->Prepare($sql);
    //     $rs      = $GLOBALS['application']->connOrigin->Execute($request);


    //     if (!$rs) {
    //         $this->output->writeln($GLOBALS['application']->connOrigin->ErrorMsg());
    //     } else {
    //         while (!$rs->EOF) {
    //             $data = $this->clearLabelsInBodyArticle($rs->fields['post_excerpt']);
    //             $data2 = $this->clearLabelsInBodyArticle($rs->fields['post_content']);
    //             $newBody = preg_replace(
    //                 array("/([\r\n])+/i", "/([\n]{2,})/i", "/([\n]{2,})/i", "/(\n)/i"),
    //                 array('</p><p>', '</p><p>', '<br>', '<br>'),
    //                 $data2['body']
    //             );
    //             if(empty($data['img'])) {
    //                $data['img'] = $data2['img'];
    //             }
    //             $id = $rs->fields['ID'];
    //             $values[] = array(
    //                 $data['img'],
    //                 $newBody,
    //                 $pks[$id]
    //             );
    //             $this->output->write(".");

    //             if(!empty($data['img'])) {
    //          //       $this->output->writeln(" - img- ".$data['img']." - ".$pks[$id]." -".substr($newBody, 0, 50));
    //             }
    //             $rs->MoveNext();
    //         }
    //         $rs->Close(); # optional
    //     }

    //     $this->output->writeln('- updating '.count($values));
    //     if (!empty($values)) {
    //         $sql    = 'UPDATE `articles` SET img1=?, body =?  WHERE pk_article=?';

    //         $stmt = $GLOBALS['application']->conn->Prepare($sql);
    //         $rss  = $GLOBALS['application']->conn->Execute($stmt, $values);
    //         if (!$rss) {
    //             $this->output->writeln($GLOBALS['application']->conn->ErrorMsg());
    //         }

    //     } else {

    //     }
    // }

    // public function importOtherImages()
    // {
    //     //check if body is empty & get from wp
    //     //check if /files/emprendedores or /files/galicia
    //     $sql2 = "SELECT  pk_content_old, pk_content FROM `articles`, `translation_ids` "
    //      ." WHERE pk_article = pk_content AND img1 = 0 ";

    //     $request = $GLOBALS['application']->conn->Prepare($sql2);
    //     $rs2     = $GLOBALS['application']->conn->Execute($request);
    //     if (!$rs2) {
    //         $this->output->writeln('- sql '.$sql2);
    //         $this->output->writeln($GLOBALS['application']->conn->ErrorMsg());
    //     }

    //     $items = $rs2->getArray();
    //     $this->output->writeln('- hay '.count($items));

    //     $pks   = array();
    //     foreach ($items as $item) {
    //         $pk_content_old       = $item['pk_content_old'];
    //         $pks[$pk_content_old] = $item['pk_content'];
    //     }


    //     $sql = "SELECT * FROM `".PREFIX."postmeta` WHERE ".
    //         "post_id IN (".implode(', ', array_keys($pks)).") AND `meta_key` = '_thumbnail_id'";

    //     // Fetch the list of Opinions available for one author in EditMaker
    //     $request = $GLOBALS['application']->connOrigin->Prepare($sql);
    //     $rs      = $GLOBALS['application']->connOrigin->Execute($request);

    //     $items    = $rs->getArray();
    //     $values   = array();
    //     foreach ($items as $item) {

    //         $id  = $item['post_id'];
    //         $img = $this->elementIsImported($item['meta_value'], 'image');
    //         if (!empty($img)) {
    //             $values[] = array(
    //                 $img,
    //                 $pks[$id]
    //             );
    //             $this->output->writeln($id. "->".$pks[$id]."  thumbnail - width {$img} - " );
    //         }
    //     }

    //     if (!empty($values)) {
    //         $sql    = 'UPDATE `articles` SET img1=?  WHERE pk_article = ?';

    //         $stmt = $GLOBALS['application']->conn->Prepare($sql);
    //         $rss  = $GLOBALS['application']->conn->Execute($stmt, $values);
    //         if (!$rss) {
    //             $this->output->writeln($GLOBALS['application']->conn->ErrorMsg());
    //         }

    //     }
    // }
}
