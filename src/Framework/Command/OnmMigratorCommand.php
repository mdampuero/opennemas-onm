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

        $this->displayConfigurationInfo();
        $this->configureMigrator();

        $this->prepareDatabase();
        $this->import();

        $this->displayFinalInfo();
    }

    /**
     * Configures the current migrator.
     */
    protected function configureMigrator()
    {
        // define('DS', DIRECTORY_SEPARATOR);
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
    protected function convertToUTF8($string)
    {
        return utf8_encode($string);
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
     */
    protected function displayFinalInfo()
    {
        // $this->output->writeln(
        //     '<fg=yellow>*** ONM Migrator Stats ***</fg=yellow>'
        // );

        // foreach ($this->stats as $section => $stats) {
        //     $this->displaySectionResults($section, $stats);
        // }

        $this->output->writeln(
            '<fg=yellow>*** ONM Importer finished ***</fg=yellow>'
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
     * Gets all source fields from database for each entity.
     *
     * @param  array $schema Database schema.
     * @return array         Array of fields used to create new entities.
     */
    protected function getSource($schema)
    {
        $data = array();
        $this->stats[$schema['target']]['already_imported'] = 0;

        // Select all ids
        $sql = 'SELECT ' . $schema['table_origin'] . '.'
            . $schema['id_origin'] . ' FROM ' . $schema['table_origin']
            . ' ORDER BY ' . $schema['table_origin'] . '.'
            . $schema['id_origin'] . ' LIMIT 0,30';

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
                $id[$schema['id_origin']],
                $schema['translation']
            )) {

                // Build sql statement 'SELECT' chunk
                $sql = 'SELECT ';
                $i = 0;
                foreach ($schema['fields'] as $key => $field) {
                    if (isset($field['type']) &&
                            in_array('constant', $field['type'])) {
                        $sql .= '\'' . $field['value'] . '\'' . ' AS ' . $key;
                    } else {
                        $sql = $sql . $field['table_origin'] . '.'
                            . $field['field_origin'] . ' AS ' . $key;
                    }

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
                $sql.= ' WHERE ('
                        . $schema['table_origin'] . '.'
                        . $schema['id_origin'] . '='
                        . $id[$schema['id_origin']] . ')';

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
                    foreach ($schema['conditions'] as $condition) {
                        $sql .= ' AND (';

                        foreach ($condition as $key => $value) {
                            $sql .= $value['table'] . '.' . $value['field']
                                . '=\'' . $value['value'] . '\'';

                            if ($key < count($condition) - 1) {
                                $sql .= ' OR ';
                            }
                        }

                        $sql .= ')';
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
                $this->stats[$schema['target']]['already_imported']++;
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

        foreach ($this->settings['database']['schemas'] as $schema) {
            $this->output->writeln(
                "\n<fg=yellow>Migrating from <fg=red>"
                . $schema['table_origin'] . '</fg=red> to <fg=green>'
                . $schema['target']
                . "</fg=green>...</fg=yellow>"
            );


            $this->stats[$schema['target']]['already_imported'] = 0;
            $this->stats[$schema['target']]['error']            = 0;
            $this->stats[$schema['target']]['imported']         = 0;
            $this->stats[$schema['target']]['start']            = time();

            $data = $this->getSource($schema);

            switch ($schema['target']) {
                case 'article':
                    $this->saveArticles($schema, $data);
                    break;
                case 'category':
                    $this->saveCategories($schema, $data);
                    break;
                case 'album':
                    $this->saveAlbums($schema, $data);
                    break;
                case 'album_photos':
                    $this->saveAlbumPhotos($schema, $data);
                    break;
                case 'photo':
                    $this->savePhotos($schema, $data);
                    break;
                case 'opinion':
                    $this->saveOpinions($schema, $data);
                    break;
                case 'user':
                    $this->saveUsers($schema, $data);
                    break;
                case 'user_group':
                    $this->saveUserGroups($schema, $data);
                    break;
                case 'video':
                    // $this->importVideos();
                    // $this->importbodyArticles();
                    // $this->importOtherImages();

                    // $this->updateBody();

                    // $this->importGalleries();
                    break;
                default:
                    break;
            }

            $this->stats[$schema['target']]['end'] = time();

            $this->displaySectionResults(
                $schema['target'],
                $this->stats[$schema['target']]
            );
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
     * Returns the new media id.
     *
     * @param int $id Old media id.
     */
    protected function matchMedia($id)
    {
        if (array_key_exists($id, $this->translations['media'])) {
            return $this->translations['media'][$id];
        } else {
            return 0;
        }
    }

    /**
     * Returns the new photo id.
     *
     * @param int $id Old photo id.
     */
    protected function matchPhoto($id)
    {
        if (array_key_exists($id, $this->translations['photo'])) {
            return $this->translations['photo'][$id];
        } else {
            return 0;
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
        if (array_key_exists($type, $this->translations)
                && array_key_exists($id, $this->translations[$type])) {
            return $this->translations[$type][$id];
        }

        return false;
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
                case 'author_translation':
                    $field = $this->matchAuthor($field);
                    break;
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
                case 'category_translation':
                    $field = $this->matchCategory($field);
                    break;
                // case 'filename': // Saves the media
                //     $this->importFile($pathOrigin, $filenameOrigin, $pathFinal, $filenameFinal) {

                //     }
                //     $field = 0;
                //     break;
                case 'copy_file':
                    // $this->copyFile($params['old'], $params['new']);
                    break;
                case 'date':
                    $field = date($params['format'], strtotime($field));
                    break;
                case 'media_translation':
                    $field = $this->matchMedia($field);
                    break;
                case 'photo':
                    // $params['fields'];
                    // $field = $this->importPhoto($params['fields']);
                    break;
                case 'photo_translation':
                    $field = $this->matchPhoto($field);
                    break;
                case 'raw': // Remove spaces at beginning and end
                    $field = trim($field);
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
                    $field = $this->convertToUTF8($field);
                    break;
                default:
                    $field = $field;
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
     * @param  array    $schema Database schema.
     * @param  array    $data   Photos to save.
     */
    protected function saveAlbumPhotos($schema, $data)
    {
        $albums = array();

        foreach ($data as $item) {
            $values = array(
                'album'       => 0,
                'photo'       => 0,
                'description' => '',
                'position'    => 0
            );

            // Parse and translate old ids
            foreach ($item as $key => $value) {
                // Parse only if it isn't an old id to translate
                $parsed = $this->parseField(
                    $value,
                    $schema['fields'][$key]['type'],
                    isset($schema['fields'][$key]['params']) ?
                    $schema['fields'][$key]['params'] : null
                );

                // Overwrite only if it has a default value
                if ($parsed !== false && array_key_exists($key, $values)) {
                    $values[$key] = $parsed;
                }
            }

            // Group photos by album
            if (!isset($albums[$values['album']]['album_photos_id'])) {
                $albums[$values['album']]['album_photos_id'] = array();
            }

            if (!isset($albums[$values['album']]['album_photos_footer'])) {
                $albums[$values['album']]['album_photos_footer'] = array();
            }

            if (!$this->elementIsImported(
                $values['photo'],
                $schema['translation']
            )) {

                $albums[$values['album']]['album_photos_id'][] =
                    $values['photo'];

                $albums[$values['album']]['album_photos_footer'][] =
                    $values['description'];
            } else {
                $this->stats[$schema['target']]['already_imported']++;
            }
        }

        foreach ($albums as $id => $photos) {
            try {
                $album = new \Album();
                $album->read($id);

                if ($album->id == 0) {
                    $album->saveAttachedPhotos($photos);

                    foreach ($photos['album_photos_id'] as $photo) {
                        $this->createTranslation(
                            $photo,
                            0,
                            $schema['translation']
                        );

                        $this->stats[$schema['target']]['imported']++;
                    }
                }
            } catch (\Exception $e) {
                $this->stats[$schema['target']]['error']++;
            }
        }
    }

    /**
     * Saves the albums.
     *
     * @param  array    $schema Database schema.
     * @param  array    $data   Albums to save.
     */
    protected function saveAlbums($schema, $data)
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
                $album = new \Album();
                $album->create($values);

                $id = $album->id;
                $this->createTranslation(
                    $item['id'],
                    $id,
                    $schema['translation']
                );

                $this->stats[$schema['target']]['imported']++;
            } catch (\Exception $e) {
                $this->stats[$schema['target']]['error']++;
            }
        }
    }

    /**
     * Saves the articles.
     *
     * @param  array $schema Database schema.
     * @param  array $data   Users to save.
     */
    protected function saveArticles($schema, $data)
    {
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

                foreach ($item as $key => $value) {
                    $parsed = $this->parseField(
                        $value,
                        $schema['fields'][$key]['type'],
                        isset($schema['fields'][$key]['params']) ?
                        $schema['fields'][$key]['params'] : null
                    );

                    // Overwrite only if it has a default value
                    if (array_key_exists($key, $values)) {
                        $values[$key] = $parsed;
                    }
                }

                if (isset($schema['merge'])) {
                    foreach ($schema['merge'] as $target => $origin) {
                        $merged = '';
                        foreach ($origin as $field) {
                            $merged .= $values[$field] . ' ';
                        }

                        $values[$target] = $merged;
                    }
                }

                try {
                    $article = new \Article();
                    $article->create($values);

                    $id = $article->id;
                    $this->createTranslation(
                        $item['id'],
                        $id,
                        $schema['translation']
                    );

                    $this->stats[$schema['target']]['imported']++;
                } catch (\Exception $e) {
                    $this->stats[$schema['target']]['error']++;
                }
            } else {
                $this->stats[$schema['target']]['already_imported']++;
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
                    $this->createTranslation(
                        $item['id'],
                        $id,
                        $schema['translation']
                    );

                    $this->stats[$schema['target']]['imported']++;
                } catch (\Exception $e) {
                    $this->stats[$schema['target']]['error']++;
                }
            } else {
                $this->stats[$schema['target']]['already_imported']++;
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
                    'body'           => '',
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
                        }
                    }
                }

                try {
                    $opinion = new \Opinion();
                    $opinion->create($values);

                    $id = $opinion->id;
                    $this->createTranslation(
                        $item['id'],
                        $id,
                        $schema['translation']
                    );

                    $this->stats[$schema['target']]['imported']++;
                } catch (\Exception $e) {
                    $this->stats[$schema['target']]['error']++;
                }
            } else {
                $this->stats[$schema['target']]['already_imported']++;
            }
        }
    }

    /**
     * Saves the photos.
     *
     * @param  array $schema Database schema.
     * @param  array $data   User groups to save.
     */
    protected function savePhotos($schema, $data)
    {
        foreach ($data as $item) {
            if (!$this->elementIsImported($item['id'], $schema['translation'])) {
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
                    'extension'           => '',
                    'directory'           => '',
                    'origin_path'         => ''
                );

                foreach ($item as $key => $value) {
                    // Parse only if it isn't an old id to translate
                    if ($schema['fields'][$key]['type'] != 'translation') {
                        $parsed = $this->parseField(
                            $value,
                            $schema['fields'][$key]['type'],
                            isset($schema['fields'][$key]['params']) ?
                            $schema['fields'][$key]['params'] : null
                        );

                        // Overwrite only if it has a default value
                        if (array_key_exists($key, $values)) {
                            $values[$key] = $parsed;
                        }
                    }
                }

                if (isset($schema['merge'])) {
                    foreach ($schema['merge'] as $target => $origin) {
                        $i = count($origin['fields']) - 1;
                        $merged = '';
                        foreach ($origin['fields'] as $field) {
                            $merged .= $values[$field];

                            if ($origin['separator'] && $i > 0) {
                                $merged .= $origin['separator'];
                            }

                            $i--;
                        }

                        $values[$target] = $merged;
                    }
                }

                try {
                    $photo = new \Photo();
                    $id = null;

                    if (is_file($values['local_file'])) {
                        $id = $photo->createFromLocalFile(
                            $values,
                            $values['directory']
                        );
                    } else {
                        $id = $photo->create($values);
                    }

                    $this->createTranslation(
                        $item['id'],
                        $id,
                        $schema['translation']
                    );
                    $this->stats[$schema['target']]['imported']++;
                } catch (\Exception $e) {
                    $this->stats[$schema['target']]['error']++;
                }
            } else {
                $this->stats[$schema['target']]['already_imported']++;
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
        foreach ($data as $item) {
            if (!$this->elementIsImported($item['id'], 'user_group')) {
                // Default values
                $values = array(
                    'name'       => null,
                    'privileges' => array()
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
                    $user   = new \UserGroup();
                    $user->create($values);

                    $id = $user->id;
                    $this->createTranslation(
                        $item['id'],
                        $id,
                        $schema['translation']
                    );

                    $this->stats[$schema['target']]['imported']++;
                } catch (\Exception $e) {
                    echo $e;
                    $this->stats[$schema['target']]['error']++;
                }
            } else {
                $this->stats[$schema['target']]['already_imported']++;
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
                            $schema['fields'][$key]['type'],
                            isset($schema['fields'][$key]['params']) ?
                            $schema['fields'][$key]['params'] : null
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
                    $this->createTranslation(
                        $item['id'],
                        $id,
                        $schema['translation']
                    );

                    $this->stats[$schema['target']]['imported']++;
                } catch (\Exception $e) {
                    echo $e;
                    $this->stats[$schema['target']]['error']++;
                }
            } else {
                $this->stats[$schema['target']]['already_imported']++;
            }
        }
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
}
