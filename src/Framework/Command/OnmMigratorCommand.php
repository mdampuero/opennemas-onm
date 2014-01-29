<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */
namespace Framework\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Parser;

use Onm\Settings as s;
use Onm\StringUtils;

class OnmMigratorCommand extends ContainerAwareCommand
{
    /**
     * If true, debug messages will be shown during importing.
     *
     * @var boolean
     */
    protected $debug;

    /**
     * Array of database settings to use in migration process.
     *
     * @var array
     */
    protected $settings;

    /**
     * Array to save some results during the migration process.
     *
     * @var array
     */
    protected $stats = array();

    /**
     * Array of database translations
     *
     * @var array
     */
    protected $translations;

    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this
            ->setName('migrate:onm')
            ->setDescription('Migrate a region database to Openemas')
            ->setHelp(
                "Migrates an existing database to a openenmas database."
            )
            ->addArgument(
                'conf-file',
                InputArgument::REQUIRED,
                'Describes origin database and how to import from it.'
            )
            ->addOption(
                'debug',
                false,
                InputOption::VALUE_NONE,
                'If set, the command will be run in debug mode.'
            );
    }

    /**
     * Configures the current migrator.
     */
    protected function configureMigrator()
    {
        define('DS', DIRECTORY_SEPARATOR);
        define('CACHE_PREFIX', $this->settings['database']['instance']);

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
        define('FILE_DIR', INSTANCE_UNIQUE_NAME.'/files');

        define('IMG_DIR', "images");
        define(
            'MEDIA_PATH',
            SITE_PATH . "media" . DIRECTORY_SEPARATOR . INSTANCE_UNIQUE_NAME
        );

        $this->originConnection = new \Onm\DatabaseConnection(
            getContainerParameter('database')
        );
        $this->originConnection->selectDatabase(
            $this->settings['database']['source']
        );

        $this->targetConnection = getService('db_conn');
        $this->targetConnection->selectDatabase(
            $this->settings['database']['target']
        );

        \Application::load();
        \Application::initDatabase($this->targetConnection);

        $_SESSION['username'] = 'script';
        $_SESSION['userid']   = 11;
    }

    /**
     * Returns true if $string is equal to all values in $params.
     *
     * @param  string  $string String to convert.
     * @param  string  $params Values to compare.
     * @return boolean
     */
    protected function convertToBoolean($string, $params)
    {
        foreach ($params['value'] as $value) {
            if ($string != $value) {
                return false;
            }
        }

        return true;
    }

    /**
     * Returns the new value according to the given mapping.
     *
     * @param  array $string String to convert.
     * @param  array $params Mapping to use while converting.
     * @return mixed
     */
    protected function convertToMap($string, $params)
    {
        if (isset($params['map']) && isset($params['map'][$string])) {
            return $params['map'][$string];
        }

        return false;
    }

    /**
     * Returns the string of keywords separated by commas.
     *
     * @param  string $string String to converto to metadata.
     * @return string         Keywords separated by commas.
     */
    protected function convertToMetadata($string)
    {
        return \Onm\StringUtils::get_tags($string);
    }

    /**
     * Convert a given string to slug.
     *
     * @return string
     */
    protected function convertToSlug($string)
    {
        return \Onm\StringUtils::get_title($string);
    }

    /**
     * Converts a given string to UTF-8 codification
     *
     * @return string
     */
    protected function convertToUtf8($string)
    {
        return utf8_encode($string);
    }

    /**
     * Returns a YouTube URL.
     *
     * @param  string $string Video id.
     * @return string
     */
    protected function convertToYoutube($string)
    {
        return 'http://www.youtube.com/watch?v=' . $string;
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
     * Displays a message before starting migration.
     */
    protected function displayConfigurationInfo()
    {
        $info = '';
        if (isset($this->settings['database']['instance'])) {
            $info .= "Instance: " . $this->settings['database']['instance'];
        }

        if (isset($this->settings['database']['url'])) {
            $info .= "\nSite url: " . $this->settings['database']['url'];
        }

        $this->output->writeln($info);
    }

    /**
     * Displays a message when ONM Migrator finishes the migration.
     *
     * @param integer
     */
    protected function displayFinalInfo($time)
    {
        $this->output->writeln(
            '<fg=yellow>*** ONM Migrator Stats ***</fg=yellow>'
        );

        foreach ($this->stats as $section => $stats) {
            $this->displaySectionResults($section, $stats);
        }

        $this->output->writeln(
            '<fg=yellow>*** ONM Importer finished in '
            . $time . ' secs. ***</fg=yellow>'
        );
    }

    /**
     * Display results after importing a section.
     *
     * @param string $section Section imported
     * @param array  $stats   Results after importing $section.
     */
    protected function displaySectionResults($section, $stats)
    {
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
        if (array_key_exists($type, $this->translations)
                && array_key_exists($oldId, $this->translations[$type])) {
            return $this->translations[$type][$oldId];
        }

        return false;
    }

    /**
     * Executes the current command.
     *
     * @param InputInterface  $input  An InputInterface instance
     * @param OutputInterface $output An OutputInterface instance
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $start = time();
        $this->output = $output;
        $this->output->writeln(
            '<fg=yellow>*** Starting ONM Migrator ***</fg=yellow>'
        );

        $path = $input->getArgument('conf-file');
        $this->debug = $input->getOption('debug');
        $yaml = new Parser();
        $this->settings = $yaml->parse(file_get_contents($path));

        $basePath = APPLICATION_PATH;
        chdir($basePath);

        $this->displayConfigurationInfo();
        $this->configureMigrator();

        $this->prepareDatabase();
        $this->import();

        $end = time();
        $this->displayFinalInfo($end - $start);
    }

    /**
     * Gets all source fields from database for each entity.
     *
     * @param  string $name   Schema name.
     * @param  array  $schema Database schema.
     * @return array          Array of fields used to create new entities.
     */
    protected function getSource($name, $schema)
    {
        $data = array();
        $this->stats[$name]['already_imported'] = 0;

        // Select all ids
        $sql = 'SELECT ' . $schema['source']['table'] . '.'
            . $schema['source']['id'] . ' FROM ' . $schema['source']['table']
            . ' WHERE 1';

        // Add logical comparisons to 'WHERE' chunk
        if (isset($schema['filters']) && count($schema['filters']) > 0) {
            $sql .= ' AND ' . $this->parseCondition($schema['filters']);
        }

        $sql .= ' ORDER BY ' . $schema['source']['table'] . '.'
            . $schema['source']['id'];

        $request = $this->originConnection->Prepare($sql);
        $rs      = $this->originConnection->Execute($request);
        $ids     = $rs->getArray();

        $total = count($ids);
        $current = 1;
        foreach ($ids as $id) {
            if ($this->debug) {
                $this->output->writeln(
                    '   Processing item ' . $current++ . ' of ' . $total . '...'
                );
            }

            if (!$this->elementIsImported(
                $id[$schema['source']['id']],
                $schema['translation']['name']
            )) {

                // Build sql statement 'SELECT' chunk
                $sql = 'SELECT ';
                $i = 0;
                foreach ($schema['fields'] as $key => $field) {
                    if (isset($field['type']) &&
                            in_array('constant', $field['type'])) {
                        $sql .= '\'' . $field['value'] . '\'' . ' AS ' . $key;

                    } else if (isset($field['type']) &&
                            in_array('subselect', $field['type'])) {
                        $params = $field['params']['subselect'];

                        $sql .= '(SELECT ' . $params['table'] . '.'
                            . $params['field'] . ' FROM ' . $params['table']
                            . ' WHERE ' . $params['table'] . '.' . $params['id']
                            . '=' . $id[$schema['source']['id']];

                        // Add logical comparisons to 'WHERE' chunk
                        if (isset($params['conditions'])
                                && count($params['conditions']) > 0) {
                            $sql .= ' AND '
                                . $this->parseCondition($params['conditions']);
                        }

                        $sql .= ')' . ' AS ' . $key;

                    } else {
                        $sql .= $field['table'] . '.' . $field['field']
                            . ' AS ' . $key;
                    }

                    if ($i < count($schema['fields']) - 1) {
                        $sql .= ',';
                    }

                    $i++;
                }

                // Build sql statement 'FROM' chunk
                $sql .= ' FROM ';
                foreach ($schema['tables'] as $key => $table) {
                    $sql .= $table['table'];

                    if (isset($table['alias'])) {
                        $sql .= ' AS ' . $table['alias'];
                    }

                    if ($key < count($schema['tables']) - 1) {
                        $sql .= ', ';
                    }
                }

                // Build sql statement 'WHERE' chuck
                $sql.= ' WHERE ('
                        . (isset($schema['source']['alias']) ?
                            $schema['source']['alias'] :
                            $schema['source']['table']) . '.'
                        . $schema['source']['id'] . '='
                        . $id[$schema['source']['id']] . ')';

                if (isset($schema['relations'])
                        && count($schema['relations']) > 0) {

                    foreach ($schema['relations'] as $key => $relation) {
                        if ($key < count($schema['relations'])) {
                            $sql .= ' AND (';
                        }
                        $sql .= $relation['table1'] . '.' . $relation['id1'] .
                            '=' . $relation['table2'] . '.' . $relation['id2']
                            . ')';
                    }
                }

                // Add logical comparisons to 'WHERE' chunk
                if (isset($schema['conditions'])
                        && count($schema['conditions']) > 0) {
                    $sql .= ' AND '
                        . $this->parseCondition($schema['conditions']);
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

                                foreach ($value as $field) {
                                    $result[$key][] = $result[$field];
                                    unset($result[$field]);
                                }
                            }
                        }

                        $data[] = $result;
                    }
                }
            } else {
                $this->stats[$name]['already_imported']++;
            }
        }

        return $data;
    }

    /**
     * Import from origin database to final database
     */
    protected function import()
    {
        $this->loadTranslations();

        foreach ($this->settings['database']['schemas'] as $key => $schema) {
            $this->output->writeln(
                "\n<fg=yellow>Migrating from <fg=red>"
                . $schema['source']['table'] . '</fg=red> to <fg=green>'
                . $schema['target']
                . "</fg=green>...</fg=yellow>"
            );

            $this->stats[$key]['already_imported'] = 0;
            $this->stats[$key]['error']            = 0;
            $this->stats[$key]['imported']         = 0;
            $this->stats[$key]['start']            = time();

            $data = $this->getSource($key, $schema);

            switch ($schema['target']) {
                case 'album':
                    $this->saveAlbums($key, $schema, $data);
                    break;
                case 'album_photos':
                    $this->saveAlbumPhotos($key, $schema, $data);
                    break;
                case 'article':
                    $this->saveArticles($key, $schema, $data);
                    break;
                case 'attachment':
                    $this->saveAttachments($key, $schema, $data);
                    break;
                case 'category':
                    $this->saveCategories($key, $schema, $data);
                    break;
                case 'comment':
                    $this->saveComments($key, $schema, $data);
                    break;
                case 'photo':
                    $this->savePhotos($key, $schema, $data);
                    break;
                case 'opinion':
                    $this->saveOpinions($key, $schema, $data);
                    break;
                case 'user':
                    $this->saveUsers($key, $schema, $data);
                    break;
                case 'user_group':
                    $this->saveUserGroups($key, $schema, $data);
                    break;
                case 'video':
                    $this->saveVideos($key, $schema, $data);
                    break;
                default:
                    break;
            }

            $this->stats[$key]['end'] = time();

            $this->displaySectionResults(
                $key,
                $this->stats[$key]
            );
        }
    }

    /**
     * Fetches all translations.
     */
    protected function loadTranslations()
    {
        $sql = 'SELECT * FROM translation_ids';
        $this->translations = array();

        $rs = $this->targetConnection->Execute($sql);
        $translations = $rs->GetArray();

        foreach ($translations as $translation) {
            $this->translations[$translation['type']]
                [$translation['pk_content_old']] = $translation['pk_content'];
        }
    }

    /**
     * Returns the new object id.
     *
     * @param  int   $id Old object id.
     * @return mixed     If the translation exists return the new object id.
     *                   Otherwise, returns false.
     */
    protected function matchTranslation($id, $type)
    {
        if ($id && $type && array_key_exists($type, $this->translations)
                && array_key_exists($id, $this->translations[$type])) {
            return $this->translations[$type][$id];
        }

        return false;
    }

    /**
     * Parses the values in $values and merges $default and $values according to
     * database schema.
     *
     * @param  array $default Default values.
     * @param  array $values  Values retrieved from database.
     * @param  array $schema  Database schema.
     * @return array          Merged array.
     */
    protected function merge($default, $values, $schema)
    {
        foreach ($values as $key => $value) {
            $params = isset($schema['fields'][$key]['params']) ?
                $schema['fields'][$key]['params'] : null;

            // Get the values from item and append them to parameters array
            if (in_array('select', $schema['fields'][$key]['type'])) {
                $fields = $schema['fields'][$key]['params']['select']['fields'];
                foreach ($fields as $field) {
                    $params[$field] = $values[$field];
                }
            }

            $values[$key] = $this->parseField(
                $value,
                $schema['fields'][$key]['type'],
                $params
            );
        }

        $default = array_merge($default, $values);

        if (isset($schema['merge'])) {
            foreach ($schema['merge'] as $target => $origin) {
                $merged = '';
                $i = count($origin['fields']) - 1;
                foreach ($origin['fields'] as $field) {
                    $merged .= $default[$field] . ($i > 0 ? $origin['separator'] : '');
                    $i--;
                }

                $default[$target] = $merged;
            }
        }

        return $default;
    }

    /**
     * Parses and returns the field.
     *
     * @param  string $field Field to parse.
     * @param  array  $types Array of types.
     * @return mixed         The field after parsing.
     */
    protected function parseField($field, $types, $params = null)
    {
        foreach ($types as $type) {
            switch ($type) {
                case 'body': // Replaces the content of the field
                    $field = '<p>'. preg_replace(
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
                case 'date':
                    $field = date($params['format'], strtotime($field));
                    break;
                case 'map':
                    $field = $this->convertToMap($field, $params['map']);
                    break;
                case 'raw': // Remove spaces at beginning and end
                    $field = trim($field);
                    break;
                case 'select':
                    $i = 0;
                    $next = true;
                    while ($i < count($params['select']['fields']) && $next) {
                        $i++;
                        $key = $params['select']['fields'][$i];
                        $field = $params[$key];

                        switch ($params['select']['operator']) {
                            case '!=':
                                if ($field != $params['select']['value']) {
                                    $next = false;
                                }
                                break;
                            case '==':
                                if ($field == $params['select']['value']) {
                                    $next = false;
                                }
                                break;
                            case '>':
                                if ($field > $params['select']['value']) {
                                    $next = false;
                                }
                                break;
                            case '<':
                                if ($field < $params['select']['value']) {
                                    $next = false;
                                }
                                break;
                        }
                    }

                    // Condition not satisfied by any field
                    if ($i >= count($params['select']['fields'])) {
                        $field = null;
                    }

                    break;
                case 'slug':
                    $field = $this->convertToSlug($field);
                    break;
                case 'substr':
                    $field = substr(
                        $field,
                        0,
                        strpos($field, $params['delimiter'])
                    );
                    break;
                case 'substrr':
                    if (strrpos($field, $params['delimiter'])) {
                        $field = substr(
                            $field,
                            strrpos($field, $params['delimiter']) + 1
                        );
                    } else {
                        $field = '';
                    }
                    break;
                case 'timestamp':
                    $field = date('Y-m-d H:i:s', $field);
                    break;
                case 'translation':
                    $field = $this->matchTranslation(
                        $field,
                        $params['translation']
                    );
                    break;
                case 'utf8':
                    $field = $this->convertToUtf8($field);
                    break;
                case 'youtube':
                    $field = $this->convertToYoutube($field);
                    break;
                default:
                    if (method_exists($this, 'convertTo' . $type)) {
                        $field = call_user_func(
                            array($this, 'convertTo' . $type),
                            $field,
                            $params
                        );
                    }
                    break;
            }
        }

        return $field;
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
     * Saves the photos related to an existing album.
     *
     * @param  array $name   Schema name.
     * @param  array $schema Database schema.
     * @param  array $data   Photos to save.
     */
    protected function saveAlbumPhotos($name, $schema, $data)
    {
        $albums = array();

        foreach ($data as $item) {
            $values = array(
                'album'       => 0,
                'photo'       => 0,
                'description' => '',
                'position'    => 0
            );

            $values = $this->merge($values, $item, $schema);

            if ($values['photo'] !== false) {
                // Group photos by album
                if (!isset($albums[$values['album']]['album_photos_id'])) {
                    $albums[$values['album']]['album_photos_id'] = array();
                }

                if (!isset($albums[$values['album']]['album_photos_footer'])) {
                    $albums[$values['album']]['album_photos_footer'] = array();
                }

                if (!$this->elementIsImported(
                    $values['photo'],
                    $schema['translation']['name']
                )) {

                    $albums[$values['album']]['album_photos_id'][] =
                        $values['photo'];

                    $albums[$values['album']]['album_photos_footer'][] =
                        $values['description'];
                } else {
                    $this->stats[$name]['already_imported']++;
                }
            }
        }

        foreach ($albums as $id => $photos) {
            try {
                $album = new \Album();
                $album->read($id);

                if ($album->id != 0) {
                    $album->saveAttachedPhotos($photos);

                    foreach ($photos['album_photos_id'] as $photo) {
                        $this->createTranslation(
                            $photo,
                            $album->id,
                            $schema['translation']['name']
                        );

                        $this->stats[$name]['imported']++;
                    }
                }
            } catch (\Exception $e) {
                $this->stats[$name]['error']++;
            }
        }
    }

    /**
     * Saves the albums.
     *
     * @param string $name   Schema name.
     * @param array  $schema Database schema.
     * @param array  $data   Albums to save.
     */
    protected function saveAlbums($name, $schema, $data)
    {
        foreach ($data as $item) {
            $values = array(
                'title'                 => null,
                'available'             => 1,
                'content_status'        => 1,
                'category'              => 20,
                'frontpage'             => 0,
                'in_home'               => 0,
                'metadata'              => null,
                'slug'                  => null,
                'description'           => null,
                'body'                  => '',
                'posic'                 => 0,
                'created'               => null,
                'starttime'             => null,
                'changed'               => null,
                'fk_user'               => null,
                'fk_publisher'          => null,
                'album_frontpage_image' => 0,
                'agency'                => ''
            );

            $values = $this->merge($values, $item, $schema);

            try {
                $album = new \Album();
                $album->create($values);

                $this->createTranslation(
                    $item[$schema['translation']['field']],
                    $album->id,
                    $schema['translation']['name']
                );

                $this->stats[$name]['imported']++;
            } catch (\Exception $e) {
                $this->stats[$name]['error']++;
            }
        }

        // Update translation to point to the new id
        if (array_key_exists('remap', $schema['translation'])) {
            foreach ($schema['translation']['remap'] as $oldId => $oldTarget) {
                try {
                    $album = new \Album();
                    $album->remove(
                        $this->matchTranslation(
                            $oldId,
                            $schema['translation']['name']
                        )
                    );

                    $this->updateTranslation(
                        $oldId,
                        $this->matchTranslation(
                            $oldTarget,
                            $schema['translation']['name']
                        ),
                        $schema['translation']['name']
                    );

                    $this->stats[$name]['imported']--;
                } catch (Exception $e) {
                    $this->stats[$name]['error']++;
                }
            }
        }
    }

    /**
     * Saves the articles.
     *
     * @param string $name   Schema name.
     * @param array  $schema Database schema.
     * @param array  $data   Users to save.
     */
    protected function saveArticles($name, $schema, $data)
    {
        foreach ($data as $item) {
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
                'body'           => '',
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

            $values = $this->merge($values, $item, $schema);

            try {
                $article = new \Article();
                $article->create($values);

                $this->createTranslation(
                    $item[$schema['translation']['field']],
                    $article->id,
                    $schema['translation']['name']
                );

                $this->stats[$name]['imported']++;
            } catch (\Exception $e) {
                $this->stats[$name]['error']++;
            }
        }

        // Update translation to point to the new id
        if (array_key_exists('remap', $schema['translation'])) {
            foreach ($schema['translation']['remap'] as $oldId => $oldTarget) {
                try {
                    $article = new \Article();
                    $article->remove(
                        $this->matchTranslation(
                            $oldId,
                            $schema['translation']['name']
                        )
                    );

                    $this->updateTranslation(
                        $oldId,
                        $this->matchTranslation(
                            $oldTarget,
                            $schema['translation']['name']
                        ),
                        $schema['translation']['name']
                    );

                    $this->stats[$name]['imported']--;
                } catch (Exception $e) {
                    $this->stats[$name]['error']++;
                }
            }
        }
    }

    /**
     * Saves the attachments.
     *
     * @param string $name   Schema name.
     * @param array  $schema Database schema.
     * @param array  $data   Attachments to save.
     */
    protected function saveAttachments($name, $schema, $data)
    {
        foreach ($data as $item) {
            $values = array(
                'pk_attachment' => 0,
                'title'         => '',
                'path'          => '',
                'category'      => 20
            );

            $values = $this->merge($values, $item, $schema);

            try {
                $attachment = new \Attachment();
                $attachment->create($values);

                $this->createTranslation(
                    $item[$schema['translation']['field']],
                    $attachment->id,
                    $schema['translation']['name']
                );

                $this->stats[$name]['imported']++;
            } catch (\Exception $e) {
                $this->stats[$name]['error']++;
            }
        }

        // Update translation to point to the new id
        if (array_key_exists('remap', $schema['translation'])) {
            foreach ($schema['translation']['remap'] as $oldId => $oldTarget) {
                try {
                    $attachment = new \Attachment();
                    $attachment->remove(
                        $this->matchTranslation(
                            $oldId,
                            $schema['translation']['name']
                        )
                    );

                    $this->updateTranslation(
                        $oldId,
                        $this->matchTranslation(
                            $oldTarget,
                            $schema['translation']['name']
                        ),
                        $schema['translation']['name']
                    );

                    $this->stats[$name]['imported']--;
                } catch (Exception $e) {
                    $this->stats[$name]['error']++;
                }
            }
        }
    }

    /**
     * Saves the categories.
     *
     * @param string $name   Schema name.
     * @param array  $schema Database schema.
     * @param array  $data   Categories to save.
     */
    protected function saveCategories($name, $schema, $data)
    {
        foreach ($data as $item) {
            $values = array(
                'name'              => '',
                'title'             => '',
                'inmenu'            => 1,
                'posmenu'           => 10,
                'internal_category' => 1,
                'subcategory'       => 0,
                'logo_path'         => null,
                'params'            => array(),
                'color'             => null
            );

            $values = $this->merge($values, $item, $schema);

            try {
                $category = new \ContentCategory();
                $category->create($values);

                $this->createTranslation(
                    $item[$schema['translation']['field']],
                    $category->pk_content_category,
                    $schema['translation']['name']
                );

                $this->stats[$name]['imported']++;
            } catch (\Exception $e) {
                $this->stats[$name]['error']++;
            }
        }

        // Update translation to point to the new id
        if (array_key_exists('remap', $schema['translation'])) {
            foreach ($schema['translation']['remap'] as $oldId => $oldTarget) {
                try {
                    $category = new \ContentCategory();
                    $category->delete(
                        $this->matchTranslation(
                            $oldId,
                            $schema['translation']['name']
                        )
                    );

                    $this->updateTranslation(
                        $oldId,
                        $this->matchTranslation(
                            $oldTarget,
                            $schema['translation']['name']
                        ),
                        $schema['translation']['name']
                    );

                    $this->stats[$name]['imported']--;
                } catch (Exception $e) {
                    $this->stats[$name]['error']++;
                }
            }
        }
    }

    /**
     * Save the comments.
     *
     * @param string $name   Schema name.
     * @param array  $schema Database schema.
     * @param array  $data   Comments to save.
     */
    protected function saveComments($name, $schema, $data)
    {
        foreach ($data as $item) {
            $values = array(
                'pk_content'   => 0,
                'content_id'   => 0,
                'author'       => '',
                'author_email' => '',
                'author_ip'    => '',
                'date'         => date('now'),
                'body'         => '',
                'status'       => 'pending',
                'agent'        => '',
                'type'         => '',
                'parent'       => 0,
                'user_id'      => 0,
            );

            $values = $this->merge($values, $item, $schema);

            try {
                $comment = new \Comment();
                $comment->create($values);

                $this->createTranslation(
                    $item[$schema['translation']['field']],
                    $comment->id,
                    $schema['translation']['name']
                );

                $this->stats[$name]['imported']++;
            } catch (\Exception $e) {
                $this->stats[$name]['error']++;
            }
        }

        // Update translation to point to the new id
        if (array_key_exists('remap', $schema['translation'])) {
            foreach ($schema['translation']['remap'] as $oldId => $oldTarget) {
                try {
                    $comment = new \Comment();
                    $comment->remove(
                        $this->matchTranslation(
                            $oldId,
                            $schema['translation']['name']
                        )
                    );

                    $this->updateTranslation(
                        $oldId,
                        $this->matchTranslation(
                            $oldTarget,
                            $schema['translation']['name']
                        ),
                        $schema['translation']['name']
                    );

                    $this->stats[$name]['imported']--;
                } catch (Exception $e) {
                    $this->stats[$name]['error']++;
                }
            }
        }
    }

    /**
     * Save the opinions.
     *
     * @param string $name   Schema name.
     * @param array  $schema Database schema.
     * @param array  $data   Opinions to save.
     */
    protected function saveOpinions($name, $schema, $data)
    {
        foreach ($data as $item) {
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
                'body'           => '',
                'posic'          => 0,
                'created'        => null,
                'starttime'      => null,
                'changed'        => null,
                'fk_user'        => null,
                'fk_publisher'   => null
            );

            $values = $this->merge($values, $item, $schema);

            try {
                $opinion = new \Opinion();
                $opinion->create($values);

                $this->createTranslation(
                    $item[$schema['translation']['field']],
                    $opinion->id,
                    $schema['translation']['name']
                );

                $this->stats[$name]['imported']++;
            } catch (\Exception $e) {
                $this->stats[$name]['error']++;
            }
        }

        // Update translation to point to the new id
        if (array_key_exists('remap', $schema['translation'])) {
            foreach ($schema['translation']['remap'] as $oldId => $oldTarget) {
                try {
                    $opinion = new \Opinion();
                    $opinion->remove(
                        $this->matchTranslation(
                            $oldId,
                            $schema['translation']['name']
                        )
                    );

                    $this->updateTranslation(
                        $oldId,
                        $this->matchTranslation(
                            $oldTarget,
                            $schema['translation']['name']
                        ),
                        $schema['translation']['name']
                    );

                    $this->stats[$name]['imported']--;
                } catch (Exception $e) {
                    $this->stats[$name]['error']++;
                }
            }
        }
    }

    /**
     * Saves the photos.
     *
     * @param string $name   Schema name.
     * @param array  $schema Database schema.
     * @param array  $data   User groups to save.
     */
    protected function savePhotos($name, $schema, $data)
    {
        $settings = array (
            'image_thumb_size'=>'140',
            'image_inner_thumb_size'=>'470',
            'image_front_thumb_size'=>'350'
        );

        foreach ($settings as $key => $value) {
            s::set($key, $value);
        }

        foreach ($data as $item) {
            $values = array(
                'title'               => null,
                'description'         => null,
                'body'                => '',
                'starttime'           => null,
                'endtime'             => null,
                'created'             => null,
                'changed'             => null,
                'metadata'            => null,
                'content_status'      => 1,
                'fk_category'         => 20,
                'fk_user'             => null,
                'fk_author'           => null,
                'fk_user_last_editor' => null,
                'posic'               => 0,
                'frontpage'           => 0,
                'slug'                => null,
                'available'           => 1,
                'category'            => 20,
                'id'                  => 0,
                'name'                => '',
                'path_file'           => '',
                'size'                => null,
                'width'               => null,
                'height'              => null,
                'author_name'         => '',
                'category_name'       => '',
                'nameCat'             => '',
                'local_file'          => null,
                'address'             => '',
                'extension'           => '',
                'directory'           => '',
                'origin_path'         => ''
            );

            $values = $this->merge($values, $item, $schema);

            try {
                $photo = new \Photo();
                $id = null;

                if (is_file($values['local_file'])) {
                    $id = $photo->createFromLocalFile(
                        $values,
                        $values['directory']
                    );

                    // Update article img2 and img2_footer
                    if (isset($values['article'])
                            && $values['article'] !== false) {
                        $this->updateArticlePhoto(
                            $values['article'],
                            $id,
                            $values['img2_footer']
                        );
                    }

                    $this->createTranslation(
                        $item[$schema['translation']['field']],
                        $id,
                        $schema['translation']['name']
                    );

                    $this->stats[$name]['imported']++;
                }
            } catch (\Exception $e) {
                $this->stats[$name]['error']++;
            }
        }

        // Update translation to point to the new id
        if (array_key_exists('remap', $schema['translation'])) {
            foreach ($schema['translation']['remap'] as $oldId => $oldTarget) {
                try {
                    $photo = new \Photo();
                    $photo->remove(
                        $this->matchTranslation(
                            $oldId,
                            $schema['translation']['name']
                        )
                    );

                    $this->updateTranslation(
                        $oldId,
                        $this->matchTranslation(
                            $oldTarget,
                            $schema['translation']['name']
                        ),
                        $schema['translation']['name']
                    );

                    $this->stats[$name]['imported']--;
                } catch (Exception $e) {
                    $this->stats[$name]['error']++;
                }
            }
        }
    }

    /**
     * Saves the user groups.
     *
     * @param string $name   Schema name.
     * @param array $schema  Database schema.
     * @param array $data    User groups to save.
     */
    protected function saveUserGroups($name, $schema, $data)
    {
        foreach ($data as $item) {
            $values = array(
                'name'       => null,
                'privileges' => array()
            );

            $values = $this->merge($values, $item, $schema);

            try {
                $group   = new \UserGroup();
                $group->create($values);

                $this->createTranslation(
                    $item[$schema['translation']['field']],
                    $group->id,
                    $schema['translation']['name']
                );

                $this->stats[$name]['imported']++;
            } catch (\Exception $e) {
                echo $e;
                $this->stats[$name]['error']++;
            }
        }

        // Update translation to point to the new id
        if (array_key_exists('remap', $schema['translation'])) {
            foreach ($schema['translation']['remap'] as $oldId => $oldTarget) {
                try {
                    $group = new \UserGroup();
                    $group->delete(
                        $this->matchTranslation(
                            $oldId,
                            $schema['translation']['name']
                        )
                    );

                    $this->updateTranslation(
                        $oldId,
                        $this->matchTranslation(
                            $oldTarget,
                            $schema['translation']['name']
                        ),
                        $schema['translation']['name']
                    );

                    $this->stats[$name]['imported']--;
                } catch (Exception $e) {
                    $this->stats[$name]['error']++;
                }
            }
        }
    }

    /**
     * Saves the users.
     *
     * @param string $name   Schema name.
     * @param array  $schema Database schema.
     * @param array  $data   Users to save.
     */
    protected function saveUsers($name, $schema, $data)
    {
        foreach ($data as $item) {
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

            $values = $this->merge($values, $item, $schema);

            try {
                $user = new \User();
                $user->create($values);

                $this->createTranslation(
                    $item[$schema['translation']['field']],
                    $user->id,
                    $schema['translation']['name']
                );

                $this->stats[$name]['imported']++;
            } catch (\Exception $e) {
                $this->stats[$name]['error']++;
            }
        }

        // Update translation to point to the new id
        if (array_key_exists('remap', $schema['translation'])) {
            foreach ($schema['translation']['remap'] as $oldId => $oldTarget) {
                try {
                    $user = new \User();
                    $user->delete(
                        $this->matchTranslation(
                            $oldId,
                            $schema['translation']['name']
                        )
                    );

                    $this->updateTranslation(
                        $oldId,
                        $this->matchTranslation(
                            $oldTarget,
                            $schema['translation']['name']
                        ),
                        $schema['translation']['name']
                    );

                    $this->stats[$name]['imported']--;
                } catch (Exception $e) {
                    $this->stats[$name]['error']++;
                }
            }
        }
    }

    /**
     * Saves the videos.
     *
     * @param string $name   Schema name.
     * @param array  $schema Database schema.
     * @param array  $data   Users to save.
     */
    protected function saveVideos($name, $schema, $data)
    {
        foreach ($data as $item) {
            $values = array(
                'file_path'      => '',
                'video_url'      => '',
                'category'       => 20,
                'available'      => 1,
                'content_status' => 1,
                'information'    => '',
                'title'          => '',
                'metadata'       => '',
                'description'    => '',
                'author_name'    => '',
            );

            $values = $this->merge($values, $item, $schema);

            try {
                $videoP = new \Panorama\Video($values['video_url']);
                $values['information'] = $videoP->getVideoDetails();

                foreach ($values['information'] as $key => $value) {
                    // Overwrite only if it has a default value
                    if (array_key_exists($key, $values)) {
                        $values[$key] = $value;
                    }
                }

                $video = new \Video();
                $video->create($values);

                // Update article img2 and img2_footer
                if (isset($values['article'])
                        && $values['article'] !== false) {
                    $this->updateArticleVideo(
                        $values['article'],
                        $video->id,
                        $values['video2_footer']
                    );
                }

                $this->createTranslation(
                    $item[$schema['translation']['field']],
                    $video->id,
                    $schema['translation']['name']
                );

                $this->stats[$name]['imported']++;
            } catch (\Exception $e) {
                $this->stats[$name]['error']++;
            }
        }

        // Update translation to point to the new id
        if (array_key_exists('remap', $schema['translation'])) {
            foreach ($schema['translation']['remap'] as $oldId => $oldTarget) {
                try {
                    $video = new \Video();
                    $video->remove(
                        $this->matchTranslation(
                            $oldId,
                            $schema['translation']['name']
                        )
                    );

                    $this->updateTranslation(
                        $oldId,
                        $this->matchTranslation(
                            $oldTarget,
                            $schema['translation']['name']
                        ),
                        $schema['translation']['name']
                    );

                    $this->stats[$name]['imported']--;
                } catch (Exception $e) {
                    $this->stats[$name]['error']++;
                }
            }
        }
    }

    /**
     * Updates img2 and im2_footer fields from an article.
     *
     * @param integer $id     Article id.
     * @param integer $photo  Photo id.
     * @param string  $footer Footer value for the photo.
     */
    protected function updateArticlePhoto($id, $photo, $footer)
    {
        $sql = "UPDATE articles  SET `img2`=?, `img2_footer`=?"
            ."WHERE pk_article=?";

        $values = array($photo, $footer, $id);

        $stmt = $this->targetConnection->Prepare($sql);
        $rss  = $this->targetConnection->Execute($stmt, $values);
    }

    /**
     * Updates fk_video2 and footer_video2 fields from an article.
     *
     * @param integer $id     Article id.
     * @param integer $video  Video id.
     * @param string  $footer Footer value for the photo.
     */
    protected function updateArticleVideo($id, $video, $footer)
    {
        var_dump($id);
        $sql = "UPDATE articles  SET `fk_video2`=?, `footer_video2`=?"
            ."WHERE pk_article=?";

        $values = array($video, $footer, $id);

        $stmt = $this->targetConnection->Prepare($sql);
        $rss  = $this->targetConnection->Execute($stmt, $values);
    }

    /**
     * Updates a translation entry.
     *
     * @param integer $old  Old content id used in old database.
     * @param integer $new  New content id.
     * @param string  $type Type of the translation.
     */
    protected function updateTranslation($old, $new, $type)
    {
        $sql = 'UPDATE translation_ids SET `pk_content`=' . $new
            . ' WHERE `pk_content_old`=' . $old . ' AND `type`=\''
            . $type . '\'';

        $rs  = $this->targetConnection->Execute($sql);

        if (!$rs) {
            $this->output->writeln(
                'createTranslation: ' . $this->targetConnection->ErrorMsg()
            );
        }

        $this->translations[$type][$old] = $new;
    }

    private function clearTags($body)
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
     * Parses the given condition and returns the equivalent SQL.
     *
     * @param  array $condition Condition to parse.
     * @return string           SQL string.
     */
    private function parseCondition($condition)
    {
        $sql = '';
        if (array_keys($condition) !== range(0, count($condition) - 1)) {
            // Associative array
            $sql .= $condition['table'] . '.' . $condition['field']
                . (array_key_exists('operator', $condition) ?
                    $condition['operator'] : '=')
                . '\'' . $condition['value'] . '\'';
        } else {
            // Non-associative array
            $sql .= '(';
            foreach ($condition as $key => $value) {
                $sql .= $this->parseCondition($value);
                if ($key < count($condition) - 1) {
                    if (array_keys($value) !== range(0, count($value) - 1)) {
                        $sql .= ' OR ';
                    } else {
                        $sql .= ' AND ';
                    }
                } else {
                    $sql .= ')';
                }
            }
        }

        return $sql;
    }
}
