<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Diego Blanco Estévez <diego@openhost.es>
 *
 */
namespace Framework\Migrator\Saver;

use Onm\DatabaseConnection;
use Onm\Settings as s;
use Onm\StringUtils;

class MigrationSaver
{
    /**
     * Database connection to use while getting data from source.
     *
     * @var Onm\DatabaseConnection
     */
    protected $connection;

    /**
     * If true, debug messages will be shown during importing.
     *
     * @var boolean
     */
    protected $debug;

    /**
     * Logger to use during migration process.
     *
     * @var Monolog\Logger
     */
    protected $logger;

    /**
     * Array of settings to use during migration process.
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
     * Constructs a new Migration saver.
     *
     * @param Logger $logger
     * @param array  $settings
     * @param array  $translations Array of translations.
     * @param array  $stats
     * @param array  $debug
     */
    public function __construct(
        $logger,
        $settings,
        &$translations,
        &$stats,
        $output,
        $debug = false
    ) {
        $this->debug        = $debug;
        $this->logger       = $logger;
        $this->settings     = $settings;
        $this->stats        = &$stats;
        $this->output       = $output;
        $this->translations = &$translations;

        $this->configure();
    }

    /*
     * Update translation to point to the new id
     *
     * @param string $name   Schema name.
     * @param array  $schema Source schema.
     */
    public function premapTranslations($name, $schema)
    {
        if (array_key_exists('premap', $schema['translation'])) {
            foreach ($schema['translation']['premap'] as $oldId => $oldTarget) {
                try {
                    $item = null;
                    $id   = $this->matchTranslation(
                        $oldId,
                        $schema['translation']['name']
                    );

                    switch ($schema['target']) {
                        case 'category':
                            $item = new \ContentCategory();
                            break;
                        case 'user':
                            $item = new \User();
                            break;
                        case 'user_group':
                            $item = new \UserGroup();
                            break;
                        default:
                            $item = new \Content();
                            break;
                    }

                    if ($item) {
                        $item->delete($id);
                    }

                    $this->updateTranslation(
                        $oldId,
                        $this->matchTranslation(
                            $oldTarget,
                            $schema['translation']['name']
                        ),
                        $schema['translation']['name']
                    );

                } catch (Exception $e) {
                    $this->stats[$name]['error']++;
                }
            }
        }
    }

    /*
     * Update translation to point to the new id
     *
     * @param string $name   Schema name.
     * @param array  $schema Source schema.
     */
    public function postmapTranslations($name, $schema)
    {
        if (array_key_exists('postmap', $schema['translation'])) {
            foreach ($schema['translation']['postmap'] as $oldId => $target) {
                try {
                    $item = null;
                    $id   = $this->matchTranslation(
                        $oldId,
                        $schema['translation']['name']
                    );

                    switch ($schema['target']) {
                        case 'category':
                            $item = new \ContentCategory();
                            break;
                        case 'user':
                            $item = new \User();
                            break;
                        case 'user_group':
                            $item = new \UserGroup();
                            break;
                        default:
                            $item = new \Content();
                            break;
                    }

                    if ($item) {
                        $item->delete($id);
                    }

                    $this->updateTranslation(
                        $oldId,
                        $target,
                        $schema['translation']['name']
                    );

                } catch (Exception $e) {
                    $this->stats[$name]['error']++;
                }
            }
        }
    }

    /**
     * Saves the photos related to an existing album.
     *
     * @param  array $name   Schema name.
     * @param  array $schema Database schema.
     * @param  array $data   Photos to save.
     */
    public function saveAlbumPhotos($name, $schema, $data)
    {
        $albums = array();

        foreach ($data as $item) {
            $values = array(
                'album'    => 0,
                'photo'    => 0,
                'footer'   => '',
                'position' => 0
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

                $albums[$values['album']]['album_photos_id'][] =
                    $values['photo'];

                $albums[$values['album']]['album_photos_footer'][] =
                    $values['footer'];
            } else {
                $this->stats[$name]['error']++;
            }
        }

        foreach ($albums as $id => $photos) {
            try {
                if ($id != 0) {
                    $album = new \Album();
                    $album->read($id);

                    $album->saveAttachedPhotos($photos);
                    $slug = array_key_exists('slug', $schema['translation']) ?
                        $values[$schema['translation']['slug']] : '';

                    foreach ($photos['album_photos_id'] as $key => $photo) {
                        if ($this->matchTranslation(
                            $photo,
                            $schema['translation']['name']
                        ) === false
                        ) {
                            if ($key == 0) {
                                $this->updateAlbumCover($id, $photo);
                            }

                            $this->createTranslation(
                                $photo,
                                $album->id,
                                $schema['translation']['name'],
                                $slug
                            );

                            $this->stats[$name]['imported']++;
                        } else {
                            $this->stats[$name]['already_imported']++;
                        }
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
    public function saveAlbums($name, $schema, $data)
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
                if ($this->matchTranslation(
                    $values[$schema['translation']['field']],
                    $schema['translation']['name']
                ) === false
                ) {
                    $album = new \Album();
                    $album->create($values);
                    $slug = array_key_exists('slug', $schema['translation']) ?
                        $values[$schema['translation']['slug']] : '';

                    $this->createTranslation(
                        $values[$schema['translation']['field']],
                        $album->id,
                        $schema['translation']['name'],
                        $slug
                    );

                    $this->stats[$name]['imported']++;
                } else {
                    $this->stats[$name]['already_imported']++;
                }
            } catch (\Exception $e) {
                $this->stats[$name]['error']++;
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
    public function saveArticles($name, $schema, $data)
    {
        foreach ($data as $item) {
            $values = array(
                'title'          => null,
                'with_comment'   => 0,
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
                if ($this->matchTranslation(
                    $values[$schema['translation']['field']],
                    $schema['translation']['name']
                ) === false
                ) {
                    $article = new \Article();
                    $article->create($values);
                    $slug = array_key_exists('slug', $schema['translation']) ?
                        $values[$schema['translation']['slug']] : '';

                    $this->createTranslation(
                        $values[$schema['translation']['field']],
                        $article->id,
                        $schema['translation']['name'],
                        $slug
                    );

                    $this->stats[$name]['imported']++;
                } else {
                    $this->stats[$name]['already_imported']++;
                }
            } catch (\Exception $e) {
                $this->stats[$name]['error']++;
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
    public function saveAttachments($name, $schema, $data)
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
                if ($this->matchTranslation(
                    $values[$schema['translation']['field']],
                    $schema['translation']['name']
                ) === false
                ) {
                    $attachment = new \Attachment();
                    $attachment->create($values);
                    $slug = array_key_exists('slug', $schema['translation']) ?
                        $values[$schema['translation']['slug']] : '';

                    $this->createTranslation(
                        $values[$schema['translation']['field']],
                        $attachment->id,
                        $schema['translation']['name'],
                        $slug
                    );

                    $this->stats[$name]['imported']++;
                } else {
                    $this->stats[$name]['already_imported']++;
                }
            } catch (\Exception $e) {
                $this->stats[$name]['error']++;
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
    public function saveCategories($name, $schema, $data)
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
                $categoryName = StringUtils::normalize_name(
                    strtolower($values['name'])
                );

                $categoryId = $this->findCategory($categoryName);
                $slug = array_key_exists('slug', $schema['translation']) ?
                        $values[$schema['translation']['slug']] : '';

                if ($categoryId === false) {
                    $category = new \ContentCategory();
                    $category->create($values);
                    $categoryId = $category->pk_content_category;
                }

                $this->createTranslation(
                    $values[$schema['translation']['field']],
                    $categoryId,
                    $schema['translation']['name'],
                    $slug
                );

                $this->stats[$name]['already_imported']++;
            } catch (\Exception $e) {
                $this->stats[$name]['error']++;
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
    public function saveComments($name, $schema, $data)
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
                if ($this->matchTranslation(
                    $values[$schema['translation']['field']],
                    $schema['translation']['name']
                ) === false
                ) {
                    $comment = new \Comment();
                    $comment->create($values);
                    $slug = array_key_exists('slug', $schema['translation']) ?
                        $values[$schema['translation']['slug']] : '';

                    $this->createTranslation(
                        $values[$schema['translation']['field']],
                        $comment->id,
                        $schema['translation']['name'],
                        $slug
                    );

                    $this->stats[$name]['imported']++;
                } else {
                    $this->stats[$name]['already_imported']++;
                }
            } catch (\Exception $e) {
                $this->stats[$name]['error']++;
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
    public function saveOpinions($name, $schema, $data)
    {
        foreach ($data as $item) {
            $values = array(
                'fk_author'      => 0,
                'fk_author_img'  => 0,
                'with_comment'   => 1,
                'type_opinion'   => 0,
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
                if ($this->matchTranslation(
                    $values[$schema['translation']['field']],
                    $schema['translation']['name']
                ) === false
                ) {
                    $opinion = new \Opinion();
                    $opinion->create($values);
                    $slug = array_key_exists('slug', $schema['translation']) ?
                        $values[$schema['translation']['slug']] : '';

                    $this->createTranslation(
                        $values[$schema['translation']['field']],
                        $opinion->id,
                        $schema['translation']['name'],
                        $slug
                    );

                    $this->stats[$name]['imported']++;
                } else {
                    $this->stats[$name]['already_imported']++;
                }
            } catch (\Exception $e) {
                $this->stats[$name]['error']++;
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
    public function savePhotos($name, $schema, $data)
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

                if ($this->matchTranslation(
                    $values[$schema['translation']['field']],
                    $schema['translation']['name']
                ) === false
                ) {
                    if (is_file($values['local_file'])) {
                        $id = $photo->createFromLocalFile(
                            $values,
                            $values['directory']
                        );
                        $slug = array_key_exists('slug', $schema['translation'])
                            ? $values[$schema['translation']['slug']] : '';

                        // Update article img2 and img2_footer
                        if (isset($values['article'])
                            && $values['article'] !== false
                        ) {
                            $this->updateArticlePhoto(
                                $values['article'],
                                $id,
                                isset($values['img2_footer']) ?
                                $values['img2_footer'] : ''
                            );
                        }

                        // Update article img1 and img1_footer
                        if (isset($values['article'])
                            && $values['article'] !== false
                        ) {
                            $this->updateArticleFrontpagePhoto(
                                $values['article'],
                                $id,
                                isset($values['img1_footer']) ?
                                $values['img1_footer'] : ''
                            );
                        }

                        $this->createTranslation(
                            $values[$schema['translation']['field']],
                            $id,
                            $schema['translation']['name'],
                            $slug
                        );

                        $this->stats[$name]['imported']++;
                    } else {
                        $this->stats[$name]['not_found']++;
                    }
                } else {
                    $this->stats[$name]['already_imported']++;
                }
            } catch (\Exception $e) {
                $this->stats[$name]['error']++;
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
    public function saveUserGroups($name, $schema, $data)
    {
        foreach ($data as $item) {
            $values = array(
                'name'       => null,
                'privileges' => array()
            );

            $values = $this->merge($values, $item, $schema);

            try {
                if ($this->matchTranslation(
                    $values[$schema['translation']['field']],
                    $schema['translation']['name']
                ) === false
                ) {
                    $group   = new \UserGroup();
                    $group->create($values);
                    $slug = array_key_exists('slug', $schema['translation']) ?
                        $values[$schema['translation']['slug']] : '';

                    $this->createTranslation(
                        $values[$schema['translation']['field']],
                        $group->id,
                        $schema['translation']['name'],
                        $slug
                    );

                    $this->stats[$name]['imported']++;
                } else {
                    $this->stats[$name]['already_imported']++;
                }
            } catch (\Exception $e) {
                echo $e;
                $this->stats[$name]['error']++;
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
    public function saveUsers($name, $schema, $data)
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
                'activated'     => 0,
                'id_user_group' => array('3'),
            );

            $values = $this->merge($values, $item, $schema);

            try {
                $userId = $this->findUser($values['username']);
                $slug = array_key_exists('slug', $schema['translation']) ?
                        $values[$schema['translation']['slug']] : '';

                if ($this->matchTranslation(
                    $values[$schema['translation']['field']],
                    $schema['translation']['name']
                ) === false
                ) {
                    if ($userId === false) {
                        $user = new \User();
                        $user->create($values);
                        $slug = array_key_exists('slug', $schema['translation'])
                            ? $values[$schema['translation']['slug']] : '';
                        $userId = $user->id;
                    }

                    $this->createTranslation(
                        $values[$schema['translation']['field']],
                        $userId,
                        $schema['translation']['name'],
                        $slug
                    );

                    $this->stats[$name]['imported']++;
                } else {
                    $this->stats[$name]['already_imported']++;
                }
            } catch (\Exception $e) {
                $this->stats[$name]['error']++;
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
    public function saveVideos($name, $schema, $data)
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
                if ($this->matchTranslation(
                    $values[$schema['translation']['field']],
                    $schema['translation']['name']
                ) === false
                ) {
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
                    $slug = array_key_exists('slug', $schema['translation']) ?
                        $values[$schema['translation']['slug']] : '';

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
                        $values[$schema['translation']['field']],
                        $video->id,
                        $schema['translation']['name'],
                        $slug
                    );

                    $this->stats[$name]['imported']++;
                } else {
                    $this->stats[$name]['already_imported']++;
                }
            } catch (\Exception $e) {
                $this->stats[$name]['error']++;
            }
        }
    }

    /**
     * Configures the saver.
     */
    protected function configure()
    {
        define('CACHE_PREFIX', $this->settings['migration']['instance']);
        define('INSTANCE_UNIQUE_NAME', $this->settings['migration']['instance']);

        define(
            'MEDIA_PATH',
            SITE_PATH . "media" . DIRECTORY_SEPARATOR . INSTANCE_UNIQUE_NAME
        );

        // Initialize target database
        $this->targetConnection = getService('db_conn');
        $this->targetConnection->selectDatabase(
            $this->settings['migration']['target']
        );

        \Application::load();
        \Application::initDatabase($this->targetConnection);

        $this->originConnection = new DatabaseConnection(
            getContainerParameter('database')
        );
        $this->originConnection->selectDatabase(
            $this->settings['migration']['source']
        );

        if (array_key_exists('user', $this->settings['migration'])) {
            $_SESSION['userid'] = $this->settings['migration']['user']['id'];
            $_SESSION['username'] =
                $this->settings['migration']['user']['username'];
        } else {
            $_SESSION['userid'] = 999;
            $_SESSION['username'] = 'ONM';
        }
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
        if (isset($params) && isset($params[$string])) {
            return $params[$string];
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
     * @param string  $slug Old content slug.
     */
    protected function createTranslation($old, $new, $type, $slug = null)
    {
        $sql = 'INSERT INTO translation_ids(`pk_content_old`, `pk_content`, '
            . '`type`, `slug`) VALUES (?,?,?,?)';
        $values = array($old, $new, $type, $slug);

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
     * Returns the new object id.
     *
     * @param  int   $id Old object id.
     * @return mixed     If the translation exists return the new object id.
     *                   Otherwise, returns false.
     */
    protected function matchTranslation($id, $type)
    {
        if (!is_null($id) && $id !== false && $type
            && array_key_exists($type, $this->translations)
            && array_key_exists($id, $this->translations[$type])
        ) {
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
        // Pre-filter (pre-select, pre-merge)
        if (array_key_exists('pre-filters', $schema)) {
            $values = $this->filterItem(
                $values,
                $schema['pre-filters']['type'],
                $schema['pre-filters']['params']
            );
        }

        // Parse fields
        foreach ($values as $key => $value) {
            $params = isset($schema['fields'][$key]['params']) ?
                $schema['fields'][$key]['params'] : null;

            $values[$key] = $this->parseField(
                $value,
                $schema['fields'][$key]['type'],
                $params
            );
        }

        $default = array_merge($default, $values);

        // Pre-filter (select, merge)
        if (array_key_exists('post-filters', $schema)) {
            $default = $this->filterItem(
                $default,
                $schema['post-filters']['type'],
                $schema['post-filters']['params']
            );
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
                case 'embed':
                    $pattern = '~(?:http|https|)(?::\/\/|)(?:www.|)(?:youtu\.be\
                        /|youtube\.com(?:\/embed\/|\/v\/|\/watch\?v=|\/ytscreeni
                        ngroom\?v=|\/feeds\/api\/videos\/|\/user\S*[^\w\-\s]|\S*
                        [^\w\-\s]))([\w\-]{11})[a-z0-9;:@?&%=+\/\$_.-]*~i';

                    preg_match($pattern, $field, $matches);

                    $field = $matches[1];
                    break;
                case 'findUser':
                    $id = $this->findUser($field);
                    if ($id !== false) {
                        $field = $id;
                    }
                    break;
                case 'html':
                    $field = htmlentities($field, ENT_IGNORE, 'UTF-8');
                    break;
                case 'map':
                    $field = $this->convertToMap($field, $params['map']);
                    break;
                case 'metadata':
                    $field = \Onm\StringUtils::get_tags($field);
                    break;
                case 'merge':
                    if (is_array($field) && count($field) > 0) {
                        $value = '';
                        foreach ($field as $key => $v) {
                            $value .= $v . ',';
                        }
                        $field = rtrim($value, ',');
                    }
                    break;
                case 'raw':
                    $field = trim($field);
                    break;
                case 'slug':
                    $field = $this->convertToSlug($field);
                    break;
                case 'substr':
                    $offset = array_key_exists('offset', $params['substr']) ?
                        $params['substr']['offset'] : 0;
                    $delimiter = $params['substr']['delimiter'];

                    if (strpos($field, $delimiter, $offset) !== false) {
                        $field = substr(
                            $field,
                            0,
                            strpos($field, $delimiter, $offset)
                        );
                    } else {
                        $field = '';
                    }
                    break;
                case 'substrr':
                    $offset = array_key_exists('offset', $params['substrr']) ?
                        $params['substrr']['offset'] : 0;
                    $delimiter = $params['substrr']['delimiter'];

                    if (strrpos($field, $delimiter, $offset) !== false) {
                        $field = substr(
                            $field,
                            strrpos($field, $delimiter, $offset) + 1
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
                case 'username':
                    $field = \Onm\StringUtils::get_title(
                        $field,
                        true,
                        $params['separator']
                    );
                    break;
                case 'utf8':
                    $field = $this->convertToUtf8($field);
                    break;
                case 'youtube':
                    $field = $this->convertToYoutube($field);
                    break;
                default:
                    if (method_exists($this, $type . 'Filter')) {
                        $field = call_user_func(
                            array($this, $type . 'Filter'),
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
     * Updates the album cover photo.
     *
     * @param integer $album album id.
     * @param integer $photo Photo id.
     */
    protected function updateAlbumCover($album, $photo)
    {
        $sql = "UPDATE albums  SET `cover_id`=? WHERE pk_album=?";

        $values = array($photo, $album);

        $stmt = $this->targetConnection->Prepare($sql);
        $rss  = $this->targetConnection->Execute($stmt, $values);
    }

    /**
     * Updates img1 and img1_footer fields from an article.
     *
     * @param integer $id     Article id.
     * @param integer $photo  Photo id.
     * @param string  $footer Footer value for the photo.
     */
    protected function updateArticleFrontpagePhoto($id, $photo, $footer)
    {
        $sql = "UPDATE articles  SET `img1`=?, `img1_footer`=?"
            ."WHERE pk_article=?";

        $values = array($photo, $footer, $id);

        $stmt = $this->targetConnection->Prepare($sql);
        $rss  = $this->targetConnection->Execute($stmt, $values);
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

    /**
     * Applies general item filters.
     *
     * @param array $values  Array of values to filter.
     * @param array $filters Array of filters to apply.
     * @param array $params  Array of parameters used to filter.
     */
    private function filterItem($values, $filters, $params)
    {
        foreach ($filters as $filter) {
            switch ($filter) {
                case 'select':
                    $values = $this->selectFilter($values, $params['select']);
                    break;
                case 'merge':
                    $values = $this->mergeFilter($values, $params['merge']);
                    break;
            }
        }

        return $values;
    }

    /**
     * Finds the category id for a given normalized name.
     *
     * @param  string  $name Category name.
     * @return integer       Category id.
     */
    private function findCategory($name)
    {
        $sql = "SELECT pk_content_category FROM content_categories"
            . " WHERE name='" . $name . "'";

        $rs = $this->targetConnection->Execute($sql);
        $rss = $rs->getArray();

        if ($rss && count($rss) == 1
            && array_key_exists('pk_content_category', $rss[0])
        ) {
            return $rss[0]['pk_content_category'];
        }

        return false;
    }

    /**
     * Finds the user's id for a given normalized name.
     *
     * @param  string  $name User's name.
     * @return mixed         User's id if user was found. Otherwise, return
     *                       false.
     */
    private function findUser($name)
    {
        $sql = "SELECT id FROM users WHERE name LIKE '%" . $name . "%'";

        $rs = $this->targetConnection->Execute($sql);
        $rss = $rs->getArray();

        if ($rss && count($rss) == 1 && array_key_exists('id', $rss[0])) {
            return $rss[0]['id'];
        }

        return 0;
    }

    /**
     * Merges two or more fields in $values into another field.
     *
     * @param  array $values Array of values.
     * @param  array $params Array of parameters used to merge fields.
     * @return array         Filtered array of values
     */
    private function mergeFilter($values, $params)
    {
        foreach ($params as $filter) {
            $merged = '';
            foreach ($filter['fields'] as $source) {
                $merged .=  $values[$source] . $filter['separator'];
            }

            $values[$filter['target']] = rtrim($merged, $filter['separator']);
        }

        return $values;
    }

    /**
     * Returns a array of values after applying select filter.
     *
     * @param  array $values Array of values.
     * @param  array $params Array of parameters used to select.
     * @return array         Filtered array of values.
     */
    private function selectFilter($values, $params)
    {
        foreach ($params as $filter) {
            $i    = 0;
            $next = true;
            while ($i < count($filter['fields']) && $next) {
                $key = $filter['fields'][$i++];
                $selected = $values[$key];

                switch ($filter['operator']) {
                    case '!=':
                        if ($selected != $filter['value']) {
                            $next = false;
                        }
                        break;
                    case '==':
                        if ($selected == $filter['value']) {
                            $next = false;
                        }
                        break;
                    case '>':
                        if ($selected > $filter['value']) {
                            $next = false;
                        }
                        break;
                    case '<':
                        if ($selected < $filter['value']) {
                            $next = false;
                        }
                        break;
                }
            }

            // Condition not satisfied by any field
            if ($next && $i >= count($filter['fields'])) {
                if (array_key_exists('default', $filter)) {
                    $values[$filter['target']] = $filter['default'];
                } else {
                    $values[$filter['target']] = null;
                }
            } else {
                $values[$filter['target']] = $selected;
            }
        }

        return $values;
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
                preg_match_all("@(.*)-[0-9]{3,4}x[0-9]{3,4}.(.*)@", $guid, $result);
                if (!empty($result[1])) {

                    $newGuid = $result[1][0].".".$result[2][0];
                    $img     = $this->getOnmIdImage($newGuid);

                    if (empty($img)) {
                        $this->output->writeln('- Image from Body '. $guid. ' fault');
                    }
                }
                $date = new \DateTime();
                $date = $date->format('Y-m-d H:i:s');
                $local_file = str_replace(ORIGINAL_URL, ORIGINAL_MEDIA, $guid);
                $oldID = $this->elementIsImported('fotos', 'category');
                if (empty($oldID)) {
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
}
