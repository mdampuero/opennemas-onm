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

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Parser;

use Onm\StringUtils;
use Onm\Settings as s;

class OnmMigratorCommand extends ContainerAwareCommand
{
    /**
     * Array of database settings to use in migration process.
     *
     * @var array
     */
    protected $settings;

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
    protected $stats = array();


    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this
            ->setName('migrate:region')
            ->setDescription('Migrate a region database to Openemas')
            ->setHelp(
                "\nThe <info>migrate:region</info> command migrates one region "
                . "DB to new openenmas database.\n\n<info>php bin/console migra"
                . "te:region originDB finalDB</info>"
            )
            ->addArgument(
                'conf-file',
                InputArgument::REQUIRED,
                'conf-file'
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

        $path = $input->getArgument('conf-file');
        $yaml = new Parser();
        $this->settings = $yaml->parse(file_get_contents($path));

        $basePath = APPLICATION_PATH;
        chdir($basePath);

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
        $info = "\n<fg=yellow>Migrating from <fg=red>"
            . $this->settings['database']['origin'] . '</fg=red> to <fg=green>'
            . $this->settings['database']['final']
            . "</fg=green>...</fg=yellow>\n";

        if (isset($this->settings['database']['instance'])) {
            $info .= "\n   Instance:        "
                . $this->settings['database']['instance'];
        }

        if (isset($this->settings['url'])) {
            $info .= "\n   Site url:        " . $this->settings['url'];
        }

        if (isset($this->settings['media_dir'])) {
            $info .= "\n   Media dir:        "
                . $this->settings['database']['instance']
                . '   Media directory: '
                . $this->settings['database']['media_dir'] . "\n";
        }

        $info = "\n   Database origin: " . $this->settings['database']['origin']
            . "\n   Database final:  " . $this->settings['database']['final']
            . "\n   Database host:   " . $this->settings['database']['host']
            . "\n   Database type:   " . $this->settings['database']['type']
            . "\n   Database user:   " . $this->settings['database']['user']
            . "\n   Database pass:   " . $this->settings['database']['password'];

        $this->output->writeln($info);
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
        $this->output->writeln(
            '<fg=yellow>*** ONM Migrator Stats ***</fg=yellow>'
        );

        foreach ($this->stats as $section => $stats) {
            $this->output->writeln(
                ucwords($section) . " ("
                . ($stats['end'] - $stats['start']) . " secs.)"
            );

            $this->output->writeln(
                "<fg=green>Imported: " . $stats['imported']
                . "</fg=green><fg=yellow>\tAlready imported: "
                . $stats['already_imported'] . "</fg=yellow><fg=red>\tError: "
                . $stats['error'] . "</fg=red>\n"
            );
        }
    }

    /**
     * Configures the current migrator.
     */
    protected function configureMigrator()
    {
        define('ORIGINAL_URL', $this->settings['database']['url']);
        define('ORIGINAL_MEDIA', $this->settings['database']['media_dir']);
        define('ORIGINAL_MEDIA_COMMON', '/opt/backup_opennemas/mundiario/wp-content/uploads/');

        define('CACHE_PREFIX', $this->settings['database']['instance']);

        define('BD_HOST', $this->settings['database']['host']);
        define('BD_USER', $this->settings['database']['user']);
        define('BD_PASS', $this->settings['database']['password']);
        define('BD_TYPE', $this->settings['database']['type']);
        define('BD_DATABASE', $this->settings['database']['final']);
        define('ORIGIN_BD_DATABASE', $this->settings['database']['origin']);
        define('PREFIX', $this->settings['database']['prefix']);

        // Initialize internal constants for logger
        // Logger
        define(
            'SYS_LOG_PATH',
            realpath(
                SITE_PATH . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR
                . "tmp/logs"
            )
        );
        define('INSTANCE_UNIQUE_NAME', $this->settings['database']['instance']);

        define('IMG_DIR', "images");
        define(
            'MEDIA_PATH',
            SITE_PATH . "media" . DIRECTORY_SEPARATOR . INSTANCE_UNIQUE_NAME
        );

        $this->originConnection = new \Onm\DatabaseConnection(
            getContainerParameter('database')
        );
        $this->originConnection->selectDatabase(
            $this->settings['database']['origin']
        );

        $this->targetConnection = getService('db_conn');
        $this->targetConnection->selectDatabase(
            $this->settings['database']['final']
        );

        \Application::load();
        \Application::initDatabase($this->targetConnection);

        $_SESSION['username'] = 'script';
        $_SESSION['userid']   = 11;
    }

    /**
     * Prepare database for importing.
     *
     * @note This method is called after collecting arguments and before
     *       importing.
     */
    protected function prepareDatabase()
    {
    }

    /**
     * Import from origin database to final database
     */
    protected function import()
    {
        $this->loadTranslations();

        foreach ($this->settings['database']['schemas'] as $schema) {
            switch ($schema['type']) {
                case 'article':
                    $this->importArticles($schema);
                    break;
                case 'category':
                    $this->importCategories($schema);
                    break;
                case 'opinion':
                    $this->importOpinions($schema);
                    break;
                case 'user':
                    $this->importUsers($schema);
                    break;
                case 'user_group':
                    // $this->importUserGroups($schema);
                    break;
                case 'video':
                    // $this->importVideos();
                    break;
                case 'image':
                    // $this->importImages();
                default:
                    break;
            }
        }

        // $this->importbodyArticles();
        // $this->importOtherImages();

        // $this->updateBody();

        // $this->importGalleries();
    }

    /**
     * Imports articles from origin database
     *
     * @param array $schema Database schema to get the data to import.
     */
    protected function importArticles($schema)
    {
        $this->stats['articles']['start'] = time();
        $data = $this->getSource($schema);

        $this->saveArticles($schema, $data);
        $this->stats['articles']['end'] = time();
    }

    /**
     * Imports categories from origin database.
     *
     * @param array $schema Database schema to get the data to import.
     */
    protected function importCategories($schema)
    {
        $this->stats['categories']['start'] = time();
        $data = $this->getSource($schema);

        $this->saveCategories($schema, $data);
        $this->stats['categories']['end'] = time();
    }

    /**
     * Imports opinions from origin database.
     */
    protected function importOpinions($schema)
    {
        $this->stats['opinions']['start'] = time();
        $data = $this->getSource($schema);

        $this->saveOpinions($schema, $data);
        $this->stats['opinions']['end'] = time();
    }

    /**
     * Imports users from origin database.
     *
     * @param array $schema Database schema to get the data to import.
     */
    protected function importUsers($schema)
    {
        $this->stats['users']['start'] = time();
        $data = $this->getSource($schema);

        $this->saveUsers($schema, $data);
        $this->stats['users']['end'] = time();
    }

    /**
     * Imports user groups from origin database.
     *
     * @param array $schema Database schema to get the data to import.
     */
    protected function importUserGroups($schema)
    {
        $this->stats['user_groups']['start'] = time();
        $data = $this->getSource($schema);

        $this->saveUserGroups($schema, $data);
        $this->stats['user_groups']['end'] = time();
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
        $sql = 'SELECT ' . $schema['table_origin'] . '.'
            . $schema['id_origin']
            . ' FROM ' . $schema['table_origin']
            . ' LIMIT 1,10';

        $request = $this->originConnection->Prepare($sql);
        $rs      = $this->originConnection->Execute($request);
        $ids     = $rs->getArray();

        $total = count($ids);
        foreach ($ids as $id) {
            // $this->output->writeln('Processing article '. $x + 1 . ' of '. $total);

            // Build sql statement 'SELECT' chunk
            $sql = 'SELECT ';
            $i = 0;
            foreach ($schema['fields'] as $key => $field) {
                $sql = $sql . $field['table_origin'] . '.'
                    . $field['field_origin'] . ' AS ' . $key;

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
                    . $schema['table_origin'] . '.'
                    . $schema['id_origin'] . '='
                    . $id[$schema['id_origin']];

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

            // Execute sql and save in array
            $request = $this->originConnection->Prepare($sql);
            $rs      = $this->originConnection->Execute($request);
            $results = $rs->getArray();

            if (count($results) > 0) {
                foreach ($results as $result) {
                    if (isset($schema['collections'])) {
                        foreach ($schema['collections'] as $key => $value) {
                            $result[$key] = array();

                            foreach ($schema['collections'][$key] as $field) {
                                $result[$key][] = $result[$field];
                                unset($result[$field]);
                            }
                        }
                    }

                    $data[] = $result;
                }
            }
        }

        return $data;
    }

    /**
     * Saves the articles.
     *
     * @param  array $schema Database schema.
     * @param  array $data   Users to save.
     */
    protected function saveArticles($schema, $data)
    {
        $this->stats['articles']['already_imported'] = 0;
        $this->stats['articles']['error']            = 0;
        $this->stats['articles']['imported']         = 0;

        foreach ($data as $item) {
            if (!$this->elementIsImported($item['id'], 'article')) {
                $values = array(
                    'title'          => null,
                    'with_comment'   => 1,
                    'available'      => 1,
                    'content_status' => 1,
                    'category'       => 20,
                    'frontpage'      => 0,
                    'in_home'        => 0,
                    'title_int'      => null,
                    'metadata'       => null,
                    'subtitle'       => null,
                    'slug'           => null,
                    'agency'         => null,
                    'summary'        => null,
                    'description'    => null,
                    'body'           => null,
                    'posic'          => 0,
                    'id'             => 0,
                    'img1'           => null,
                    'img2'           => null,
                    'img1_footer'    => null,
                    'img2_footer'    => null,
                    'fk_video'       => null,
                    'fk_video2'      => null,
                    'footer_video2'  => null,
                    'created'        => null,
                    'starttime'      => null,
                    'changed'        => null,
                    'fk_user'        => null,
                    'fk_author'      => null,
                    'fk_publisher'   => null,
                );

                foreach ($item as $key => $value) {
                    $parsed = $this->parseField(
                        $value,
                        $schema['fields'][$key]['type']
                    );

                    // Overwrite only if it has a default value
                    if (array_key_exists($key, $values)) {
                        $values[$key] = $parsed;
                    }
                }

                try {
                    $article = new \Article();
                    $article->create($values);

                    $id = $article->id;
                    $this->createTranslation($item['id'], $id, 'article');

                    $this->stats['articles']['imported']++;
                } catch (\Exception $e) {
                    $this->stats['articles']['error']++;
                }
            } else {
                $this->stats['articles']['already_imported']++;
            }
        }
    }

    /**
     * Saves the categories.
     *
     * @param  array    $schema Database schema.
     * @param  array    $data   Categories to save.
     */
    protected function saveCategories($schema, $data)
    {
        $this->stats['categories']['already_imported'] = 0;
        $this->stats['categories']['error']            = 0;
        $this->stats['categories']['imported']         = 0;

        foreach ($data as $item) {
            if (!$this->elementIsImported($item['id'], 'category')) {
                $values = array(
                    'name'              => '',
                    'title'             => '',
                    'inmenu'            => 0,
                    'posmenu'           => 10,
                    'internal_category' => 0,
                    'subcategory'       => 0,
                    'logo_path'         => null,
                    'params'            => null,
                    'color'             => null
                );

                foreach ($item as $key => $value) {
                    // Parse only if it isn't an old id to translate
                    if ($schema['fields'][$key]['type'] != 'translation') {
                        $parsed = $this->parseField(
                            $value,
                            $schema['fields'][$key]['type']
                        );

                        // Overwrite only if it has a default value
                        if (array_key_exists($key, $values)) {
                            $values[$key] = $parsed;
                        }
                    }
                }

                try {
                    $category = new \ContentCategory();
                    $category->create($values);

                    $id = $category->pk_content_category;
                    $this->createTranslation($item['id'], $id, 'category');

                    $this->stats['categories']['imported']++;
                } catch (\Exception $e) {
                    $this->stats['categories']['error']++;
                }
            } else {
                $this->stats['categories']['already_imported']++;
            }
        }
    }

    /**
     * Save the opinions.
     *
     * @param array $schema Database schema.
     * @param array $data   Opinions to save.
     */
    protected function saveOpinions($schema, $data)
    {
        $this->stats['opinions']['already_imported'] = 0;
        $this->stats['opinions']['error']            = 0;
        $this->stats['opinions']['imported']         = 0;

        foreach ($data as $item) {
            if (!$this->elementIsImported($item['id'], 'opinion')) {
                $values = array(
                    'fk_author'      => null,
                    'fk_author_img'  => null,
                    'with_comment'   => 1,
                    'type_opinion'   => 1,
                    'title'          => null,
                    'available'      => 1,
                    'content_status' => 1,
                    'category'       => 20,
                    'frontpage'      => 0,
                    'in_home'        => 0,
                    'metadata'       => null,
                    'slug'           => null,
                    'description'    => null,
                    'body'           => null,
                    'posic'          => 0,
                    'created'        => null,
                    'starttime'      => null,
                    'changed'        => null,
                    'fk_user'        => null,
                    'fk_publisher'   => null
                );

                foreach ($item as $key => $value) {
                    // Parse only if it isn't an old id to translate
                    if ($schema['fields'][$key]['type'] != 'translation') {
                        $parsed = $this->parseField(
                            $value,
                            $schema['fields'][$key]['type']
                        );

                        // Overwrite only if it has a default value
                        if (array_key_exists($key, $values)) {
                            $values[$key] = $parsed;
                            $this->output->writeln("overwrite " . $key);
                        }
                    }
                }

                try {
                    $opinion = new \Opinion();
                    $opinion->create($values);

                    $id = $opinion->id;
                    $this->createTranslation($item['id'], $id, 'opinion');

                    $this->stats['opinions']['imported']++;
                } catch (\Exception $e) {
                    $this->stats['opinions']['error']++;
                }
            } else {
                $this->stats['opinions']['already_imported']++;
            }
        }
    }

    /**
     * Saves the users.
     *
     * @param  array $schema Database schema.
     * @param  array $data   Users to save.
     */
    protected function saveUsers($schema, $data)
    {
        $this->stats['users']['already_imported'] = 0;
        $this->stats['users']['error']            = 0;
        $this->stats['users']['imported']         = 0;

        foreach ($data as $item) {
            if (!$this->elementIsImported($item['id'], 'author')) {
                // Default values
                $values = array(
                    'username'      => '',
                    'password'      => null,
                    'sessionexpire' => '30',
                    'url'           => '',
                    'bio'           => '',
                    'avatar_img_id' => 0,
                    'email'         => null,
                    'name'          => null,
                    'type'          => 0,
                    'deposit'       => 0,
                    'token'         => null,
                    'activated'     => 1,
                    'id_user_group' => array('3'),
                );

                foreach ($item as $key => $value) {
                    // Parse only if it isn't an old id to translate
                    if ($schema['fields'][$key]['type'] != 'translation') {
                        $parsed = $this->parseField(
                            $value,
                            $schema['fields'][$key]['type']
                        );

                        // Overwrite only if it has a default value
                        if (array_key_exists($key, $values)) {
                            $values[$key] = $parsed;
                        }
                    }
                }

                try {
                    $user   = new \User();
                    $user->create($values);

                    $id = $user->id;
                    $this->createTranslation($item['id'], $id, 'author');

                    $this->stats['users']['imported']++;
                } catch (\Exception $e) {
                    echo $e;
                    $this->stats['users']['error']++;
                }
            } else {
                $this->stats['users']['already_imported']++;
            }
        }
    }

    /**
     * Saves the user groups.
     *
     * @param  array $schema Database schema.
     * @param  array $data   User groups to save.
     */
    protected function saveUserGroups($schema, $data)
    {
        $this->stats['user_groups']['already_imported'] = 0;
        $this->stats['user_groups']['error']            = 0;
        $this->stats['user_groups']['imported']         = 0;

        foreach ($data as $item) {
            if (!$this->elementIsImported($item['id'], 'user_group')) {
                // Default values
                $values = array(
                    'name' => null,
                );

                foreach ($item as $key => $value) {
                    // Parse only if it isn't an old id to translate
                    if ($schema['fields'][$key]['type'] != 'translation') {
                        $parsed = $this->parseField(
                            $value,
                            $schema['fields'][$key]['type']
                        );

                        // Overwrite only if it has a default value
                        if (array_key_exists($key, $values)) {
                            $values[$key] = $parsed;
                        }
                    }
                }

                try {
                    $user   = new \User();
                    $user->create($values);

                    $id = $user->id;
                    $this->createTranslation($item['id'], $id, 'author');

                    $this->stats['user_groups']['imported']++;
                } catch (\Exception $e) {
                    echo $e;
                    $this->stats['user_groups']['error']++;
                }
            } else {
                $this->stats['user_groups']['already_imported']++;
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
        $sql = 'INSERT INTO translation_ids(`pk_content_old`, `pk_content`, '
            . '`type`) VALUES (?,?,?)';
        $values = array($old, $new, $type);

        $stmt = $this->targetConnection->Prepare($sql);
        $rss  = $this->targetConnection->Execute($stmt, $values);

        if (!$rss) {
            $this->output->writeln(
                'createTranslation: ' . $this->targetConnection->ErrorMsg()
            );
        }

        $this->translations[$type][$old] = $new;
    }

    /**
     * Parses and returns the field.
     *
     * @param  string $field Field to parse.
     * @param  string $type  Type of the field.
     * @return mixed         The field after parsing.
     */
    protected function parseField($field, $type)
    {
        switch ($type) {
            case 'raw': // Remove spaces at beginning and end
                return trim($field);
                break;
            case 'utf8':
                return $this->convertoUTF8($field);
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
            case 'category_translation':
                return $this->matchCategory($field);
                break;
            case 'author_translation':
                return $this->matchAuthor($field);
            case 'timestamp':
                return date('Y-m-d H:i:s', $field);
                break;
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

        $rs = $this->targetConnection->Execute($sql);
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
            return $this->translations['category'][$id];
        } else {
            return 20;
        }
    }

    /**
     * Returns the new author id.
     *
     * @param int $id Old author id.
     */
    protected function matchAuthor($id)
    {
        if (array_key_exists($id, $this->translations['author'])) {
            return $this->translations['author'][$id];
        } else {
            return 0;
        }
    }

    /**
     * Read the correspondence between identifiers
     *
     * @param  integer $oldId Element id (origin database).
     * @param  string  $type  Element type.
     * @return mixed          The new element id if it's already imported.
     *                        Otherwise, return false.
     */
    protected function elementIsImported($oldId, $type)
    {
        if (isset($this->translations[$type][$oldId])) {
            return $this->translations[$type][$oldId];
        }

        return false;
    }

    /**
     * Converts a given string to UTF-8 codification
     *
     * @return string
     **/
    protected function convertoUTF8($string)
    {
        return mb_convert_encoding($string, 'UTF-8');
       // return $string;
    }




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

        $request = $this->targetConnection->Prepare($sql);
        $rs      = $this->targetConnection->Execute($request);

        $oldID = $this->elementIsImported('fotos', 'category');
        if (empty($oldID)) {
            $IDCategory ='1'; //fotografias
        } else {
           $IDCategory = $this->matchCategory($oldID); //assign category 'Fotos' for media elements
        }
        if (!$rs) {
            $this->output->writeln($this->targetConnection->ErrorMsg());
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

        $request    = $this->targetConnection->Prepare($sql);
        $rs         = $this->targetConnection->Execute($request);
        $oldID = $this->elementIsImported('fotos', 'category');

        if (empty($oldID)) {
            $IDCategory ='3'; //galleries
        } else {
           $IDCategory = $this->matchCategory($oldID); //assign category 'Fotos' for media elements
        }


        if (!$rs) {
            $this->output->writeln($this->targetConnection->ErrorMsg());
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

            $stmt = $this->targetConnection->Prepare($sql);
            $rss  = $this->targetConnection->Execute($stmt, $values);
            if (!$rss) {
                $this->output->writeln($this->targetConnection->ErrorMsg());
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
        $request = $this->targetConnection->Prepare($sql);
        $rs      = $this->targetConnection->Execute($request);

        $imageID='';
        if (!$rs || empty($rs->fields['ID'])) {
            $sql = "SELECT ID FROM `wp_posts` WHERE ".
            "`post_type` = 'attachment'  AND post_status !='trash' ".
            " AND guid LIKE '%".$guid."%'";
            $request = $this->targetConnection->Prepare($sql);
            $rs      = $this->targetConnection->Execute($request);
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
        $result = array();

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
                    // var_dump($newGuid);
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

    //     $request = $this->targetConnection->Prepare($sql);
    //     $rs =$this->targetConnection->Execute($request);

    //     if (!$rs) {
    //         $this->output->writeln($this->targetConnection->ErrorMsg());

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

    //     $request = $this->targetConnection->Prepare($sql2);
    //     $rs2     = $this->targetConnection->Execute($request);
    //     if (!$rs2) {
    //         $this->output->writeln('- sql '.$sql2);
    //         $this->output->writeln($this->targetConnection->ErrorMsg());
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
    //     $request = $this->targetConnection->Prepare($sql);
    //     $rs      = $this->targetConnection->Execute($request);


    //     if (!$rs) {
    //         $this->output->writeln($this->targetConnection->ErrorMsg());
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

    //         $stmt = $this->targetConnection->Prepare($sql);
    //         $rss  = $this->targetConnection->Execute($stmt, $values);
    //         if (!$rss) {
    //             $this->output->writeln($this->targetConnection->ErrorMsg());
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

    //     $request = $this->targetConnection->Prepare($sql2);
    //     $rs2     = $this->targetConnection->Execute($request);
    //     if (!$rs2) {
    //         $this->output->writeln('- sql '.$sql2);
    //         $this->output->writeln($this->targetConnection->ErrorMsg());
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
    //     $request = $this->targetConnection->Prepare($sql);
    //     $rs      = $this->targetConnection->Execute($request);

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

    //         $stmt = $this->targetConnection->Prepare($sql);
    //         $rss  = $this->targetConnection->Execute($stmt, $values);
    //         if (!$rss) {
    //             $this->output->writeln($this->targetConnection->ErrorMsg());
    //         }

    //     }
    // }
}
