<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\Migrator\Saver;

use Common\Migration\Component\Tracker\SimpleIdTracker;
use Onm\Exception\UserAlreadyExistsException;
use Onm\Settings as s;
use Onm\StringUtils;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Filesystem\Filesystem;

class MigrationSaver
{
    /**
     * Database connection to use while getting data from source.
     *
     * @var Common\ORM\Core\Connection
     */
    protected $conn;

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

                if (is_array($values['photo'])) {
                    foreach ($values['photo'] as $id) {
                        $albums[$values['album']]['album_photos_id'][] = $id;
                        $albums[$values['album']]['album_photos_footer'][] =
                            $values['footer'];
                    }
                } else {
                    $albums[$values['album']]['album_photos_id'][] =
                        $values['photo'];

                    $albums[$values['album']]['album_photos_footer'][] =
                        $values['footer'];
                }
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
                    $articleId = $this->findContent($values['title']);

                    if (!empty($articleId)) {
                        throw new UserAlreadyExistsException();
                    }

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
            } catch (UserAlreadyExistsException $e) {
                $articleId = $this->findContent($values['title']);

                $this->createTranslation(
                    $values[$schema['translation']['field']],
                    $articleId,
                    $schema['translation']['name'],
                    ''
                );

                $this->stats[$name]['already_imported']++;
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

            $items = [];
            if (is_array($values['local_file'])) { // Inline attachments
                foreach ($values['local_file'] as $key => $fileName) {
                    $info  = pathinfo($fileName);
                    $value = array_merge($values, [
                        'source_path'       => $values['source_path'] . $fileName,
                        'target_path'       => $values['target_path'] . $info['basename'],
                        'path'              => $values['path'] . $info['basename'],
                        'title'             => $info['basename'],
                        'slug'              => $fileName,
                        'id'                => $values['id'].'-'.$key
                    ]);

                    $items[] = $value;
                }
            } else {
                $items[] = $values;
            }

            try {
                // Inline attachments
                foreach ($items as $key => $i) {
                    if ($this->matchTranslation(
                        $i[$schema['translation']['field']],
                        $schema['translation']['name']
                    ) === false
                    ) {
                        $fs = new Filesystem();
                        $fs->copy($i['source_path'], $i['target_path']);

                        $attachment = new \Attachment();
                        $id = $attachment->create($i);

                        if ($id) {
                            $slug = array_key_exists('slug', $schema['translation']) ?
                                $i[$schema['translation']['slug']] : $i['slug'];

                            $this->createTranslation(
                                $i[$schema['translation']['field']],
                                $attachment->id,
                                $schema['translation']['name'],
                                $slug
                            );

                            $this->stats[$name]['imported']++;
                        } else {
                            $this->stats[$name]['already_imported']++;
                        }
                    } else {
                        $this->stats[$name]['already_imported']++;
                    }
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
                $categoryName = StringUtils::normalizeName(
                    strtolower($values['name'])
                );

                $categoryId = $this->findCategory($categoryName);

                if (empty($categoryId)) {
                    $categoryId = $this->findCategory($values['title']);
                }

                $slug = array_key_exists('slug', $schema['translation']) ?
                        $values[$schema['translation']['slug']] : '';

                if ($categoryId === false) {
                    $category = new \ContentCategory();
                    $category->create($values);
                    $categoryId = $category->pk_content_category;

                    $this->createTranslation(
                        $values[$schema['translation']['field']],
                        $categoryId,
                        $schema['translation']['name'],
                        $slug
                    );

                    $this->stats[$name]['imported']++;
                } else {
                    $this->createTranslation(
                        $values[$schema['translation']['field']],
                        $categoryId,
                        $schema['translation']['name'],
                        $slug
                    );

                    $this->stats[$name]['already_imported']++;
                }
            } catch (\Exception $e) {
                $this->stats[$name]['error']++;
            }
        }

        // Reload category array cache
        $this->reloadCategoryArray();
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
                'id'           => 0,
                'content_id'   => 0,
                'author'       => '',
                'author_email' => '',
                'author_url'   => '',
                'author_ip'    => '',
                'date'         => date('now'),
                'body'         => '',
                'status'       => 'pending',
                'agent'        => '',
                'type'         => '',
                'parent_id'    => 0,
                'user_id'      => 0,
                'content_type_referenced' => ''
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
     * Save the content views.
     *
     * @param string $name   Schema name.
     * @param array  $schema Database schema.
     * @param array  $data   Content views to save.
     */
    public function saveContentViews($name, $schema, $data)
    {
        $vm = getService('content_views_repository');

        foreach ($data as $item) {
            $values = [ 'pk_fk_content' => 0, 'views' => 0 ];

            $values = $this->merge($values, $item, $schema);

            try {
                if ($this->matchTranslation(
                    $values[$schema['translation']['field']],
                    $schema['translation']['name']
                ) === false
                ) {
                    if ($values['pk_fk_content'] !== false) {
                        $views = $vm->getViews($values['pk_fk_content']);
                        $vm->setViews($values['pk_fk_content'], $values['views'] + $views);

                        $this->createTranslation(
                            $values[$schema['translation']['field']],
                            $values['pk_fk_content'],
                            $schema['translation']['name'],
                            ''
                        );

                        $this->stats[$name]['imported']++;
                    } else {
                        $this->stats[$name]['error']++;
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
                    $opinionID = $this->findContent($values['title']);

                    if (!empty($opinionID)) {
                        throw new UserAlreadyExistsException();
                    }

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
            } catch (UserAlreadyExistsException $e) {
                $opinionID = $this->findContent($values['title']);

                $this->createTranslation(
                    $values[$schema['translation']['field']],
                    $opinionID,
                    $schema['translation']['name'],
                    ''
                );

                $this->stats[$name]['already_imported']++;
            } catch (\Exception $e) {
                $this->stats[$name]['error']++;
            }
        }
    }

    /**
     * Saves the polls.
     *
     * @param string $name   Schema name.
     * @param array  $schema Database schema.
     * @param array  $data   Polls to save.
     */
    public function savePolls($name, $schema, $data)
    {
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
                'visualization'       => 0,
                'subtitle'            => '',
                'used_ips'            => null,
                'total_votes'         => 0,
                'item'                => []
            );

            $values = $this->merge($values, $item, $schema);

            try {
                if ($this->matchTranslation(
                    $values[$schema['translation']['field']],
                    $schema['translation']['name']
                ) === false
                ) {
                    $pollId = $this->findContent($values['title']);

                    if (!empty($pollId)) {
                        throw new UserAlreadyExistsException();
                    }

                    $poll = new \Poll();
                    $poll->create($values);
                    $slug = array_key_exists('slug', $schema['translation']) ?
                        $values[$schema['translation']['slug']] : '';

                    $this->createTranslation(
                        $values[$schema['translation']['field']],
                        $poll->id,
                        $schema['translation']['name'],
                        $slug
                    );

                    $this->stats[$name]['imported']++;
                } else {
                    $this->stats[$name]['already_imported']++;
                }
            } catch (UserAlreadyExistsException $e) {
                $id = $this->findContent($values['title']);

                $this->createTranslation(
                    $values[$schema['translation']['field']],
                    $id,
                    $schema['translation']['name'],
                    ''
                );

                $this->stats[$name]['already_imported']++;
            } catch (\Exception $e) {
                $this->stats[$name]['error']++;
            }
        }
    }

    /**
     * Saves the poll items.
     *
     * @param string $name   Schema name.
     * @param array  $schema Database schema.
     * @param array  $data   Polls to save.
     */
    public function savePollItems($name, $schema, $data)
    {
        $items = [];
        foreach ($data as $item) {
            $values = array(
                'fk_pk_poll' => 0,
                'item'       => '',
                'metadata'   => '',
                'votes'      => 0
            );

            $values = $this->merge($values, $item, $schema);

            if (empty($items[$values['fk_pk_poll']])) {
                $items[$values['fk_pk_poll']] = [];
            }

            if (!empty($values['fk_pk_poll'])) {
                $items[$values['fk_pk_poll']][] = $values;
            }
        }

        foreach ($items as $id => $values) {
            $poll = new \Poll($id);

            $data = [
                'title'               => $poll->title,
                'description'         => $poll->description,
                'body'                => $poll->body,
                'starttime'           => $poll->starttime,
                'endtime'             => $poll->endtime,
                'created'             => $poll->created,
                'changed'             => $poll->changed,
                'metadata'            => $poll->metadata,
                'content_status'      => $poll->content_status,
                'fk_category'         => $poll->fk_category,
                'fk_user'             => $poll->fk_user,
                'fk_author'           => $poll->fk_author,
                'fk_user_last_editor' => $poll->fk_user_last_editor,
                'posic'               => $poll->posic,
                'frontpage'           => $poll->frontpage,
                'slug'                => $poll->slug,
                'available'           => $poll->available,
                'category'            => $poll->category,
                'id'                  => $poll->id,
                'visualization'       => $poll->visualization,
                'subtitle'            => $poll->subtitle,
                'used_ips'            => $poll->used_ips,
                'total_votes'         => $poll->total_votes,
                'item'                => $values,
            ];

            $poll->update($data);

            $this->stats[$name]['imported'] += count($values);
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
                // 'created'             => null,
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

            $items = [];
            if (is_array($values['local_file'])) { // Inline photos
                foreach ($values['local_file'] as $key => $fileName) {
                    $info  = pathinfo($fileName);
                    $value = array_merge($values, [
                        'local_file'        => $values['path'] . $fileName,
                        'match'             => $fileName,
                        'extension'         => $info['extension'],
                        'original_filename' => $info['basename'],
                        'title'             => $values['title'],
                        'id'                => $values['id'].'-'.$key
                    ]);

                    unset($value['article']);
                    $items[] = $value;
                }
            } else {
                $info  = pathinfo($values['original_filename']);
                $value = array_merge($values, [
                    'local_file'        => $values['path'] . $values['local_file'],
                    'extension'         => $info['extension'],
                    'original_filename' => $info['basename'],
                    'title'             => $values['title']
                ]);

                $items[] = $value;
            }

            try {
                // Inline images
                foreach ($items as $key => $i) {
                    $photo = new \Photo();
                    $id = null;

                    if ($this->matchTranslation(
                        $i[$schema['translation']['field']],
                        $schema['translation']['name']
                    ) === false
                    ) {
                        $id = $this->findPhoto($i['title']);

                        if ($id !== false) {
                            throw new UserAlreadyExistsException();
                        }

                        $uploadPath = null;
                        if (array_key_exists('upload_images_path', $i)) {
                            $uploadPath = $i['upload_images_path'];
                        }

                        if (is_file($i['local_file'])) {
                            $id = $photo->createFromLocalFile(
                                $i,
                                $i['directory'],
                                $uploadPath
                            );
                            $slug = array_key_exists('slug', $schema['translation'])
                                ? $i[$schema['translation']['slug']] : '';

                            // Update article img3 and img2_footer
                            if (isset($i['article'])
                                && $i['article'] !== false
                                && array_key_exists('img2_footer', $i)
                            ) {
                                $this->updateArticlePhoto(
                                    $i['article'],
                                    $id,
                                    $i['img2_footer']
                                );
                            }

                            // Update article img1 and img1_footer
                            if (isset($i['article'])
                                && $i['article'] !== false
                                && array_key_exists('img1_footer', $i)
                            ) {
                                $this->updateArticleFrontpagePhoto(
                                    $i['article'],
                                    $id,
                                    $i['img1_footer']
                                );
                            }

                            // Update opinion img2 and img2_footer
                            if (isset($i['opinion'])
                                && $i['opinion'] !== false
                                && array_key_exists('img2_footer', $i)
                            ) {
                                $this->updateOpinionPhoto(
                                    $i['opinion'],
                                    $id,
                                    $i['img2_footer']
                                );
                            }

                            // Update opinion img1 and img1_footer
                            if (isset($i['opinion'])
                                && $i['opinion'] !== false
                                && array_key_exists('img1_footer', $i)
                            ) {
                                $this->updateOpinionFrontpagePhoto(
                                    $i['opinion'],
                                    $id,
                                    $i['img1_footer']
                                );
                            }

                            $this->createTranslation(
                                $i[$schema['translation']['field']],
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
                    unset($photo);
                    gc_collect_cycles();
                }
            } catch (UserAlreadyExistsException $e) {
                $id = $this->findPhoto($values['title']);

                $this->createTranslation(
                    $values[$schema['translation']['field']],
                    $id,
                    $schema['translation']['name'],
                    ''
                );

                $this->stats[$name]['already_imported']++;
            } catch (\Exception $e) {
                $this->stats[$name]['error']++;
            }
        }
    }

    /**
     * Save the related contents.
     *
     * @param string $name   Schema name.
     * @param array  $schema Database schema.
     * @param array  $data   Related contents to save.
     */
    public function saveRelatedContents($name, $schema, $data)
    {
        foreach ($data as $item) {
            $values = [
                'pk_content1'  => 0,
                'pk_content2'  => 0,
                'relationship' => '',
                'text'         => '',
                'position'     => 0,
                'posinterior'  => 0,
                'verportada'   => 0,
                'verinterior'  => 0
            ];

            $values = $this->merge($values, $item, $schema);

            try {
                if ($values['pk_content1'] !== false && $values['pk_content2'] !== false) {
                    $this->updateRelated($values);

                    $this->stats[$name]['imported']++;
                } else {
                    $this->stats[$name]['error']++;
                }
            } catch (\Exception $e) {
                $this->stats[$name]['error']++;
            }
        }
    }

    /**
     * Saves the static pages.
     *
     * @param string $name   Schema name.
     * @param array  $schema Database schema.
     * @param array  $data   Static pages to save.
     */
    public function saveStaticPages($name, $schema, $data)
    {
        $em        = getService('orm.manager');
        $converter = $em->getConverter('Content');

        foreach ($data as $item) {
            $values = $this->merge([], $item, $schema);

            try {
                $oldId = $values[$schema['translation']['field']];
                $slug  = array_key_exists('slug', $schema['translation']) ?
                        $values[$schema['translation']['slug']] : '';

                unset($values[$schema['translation']['field']]);

                if ($this->matchTranslation($oldId, $schema['translation']['name']) === false) {
                    $page = new \Common\ORM\Entity\Content($converter->objectify($values));
                    $page->content_type_name = 'static_page';
                    $page->fk_content_type = 13;
                    $page->content_status  = 1;

                    $em->persist($page);
                    $this->stats[$name]['imported']++;

                    $this->createTranslation(
                        $oldId,
                        $page->pk_content,
                        $schema['translation']['name'],
                        $slug
                    );
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
                'url'           => '',
                'bio'           => '',
                'avatar_img_id' => 0,
                'email'         => null,
                'name'          => null,
                'type'          => 0,
                'token'         => null,
                'activated'     => 0,
                'id_user_group' => array('3'),
            );

            if (array_key_exists('id_user_group', $item)) {
                $item['id_user_group'] = explode(',', $item['id_user_group']);

                if (array_key_exists('params', $schema['fields']['id_user_group'])) {
                    foreach ($item['id_user_group'] as $key => $group) {
                        $newGroup = $this->matchTranslation(
                            $group,
                            $schema['fields']['id_user_group']['params']['translation']
                        );

                        if (!empty($newGroup)) {
                            $item['id_user_group'][$key] = $newGroup;
                        }
                    }
                }
            }

            if (array_key_exists('ids_category', $item)) {
                $item['ids_category'] = explode(',', $item['ids_category']);

                foreach ($item['ids_category'] as $key => $category) {
                    $newCategory = $this->matchTranslation(
                        $category,
                        $schema['fields']['ids_category']['params']['translation']
                    );

                    if (!empty($category)) {
                        $item['ids_category'][$key] = $newCategory;
                    }
                }
            }

            $values = $this->merge($values, $item, $schema);

            try {
                $slug = array_key_exists('slug', $schema['translation']) ?
                        $values[$schema['translation']['slug']] : '';

                if ($this->matchTranslation(
                    $values[$schema['translation']['field']],
                    $schema['translation']['name']
                ) === false
                ) {
                    $userId = $this->findUser($values['username']);

                    if ($userId === false) {
                        $userId = $this->findUser($values['email']);
                    }

                    if ($userId !== false) {
                        throw new UserAlreadyExistsException();
                    }

                    $user = new \User();
                    $user->create($values);
                    $slug = array_key_exists('slug', $schema['translation'])
                        ? $values[$schema['translation']['slug']] : '';
                    $userId = $user->id;

                    if (array_key_exists('path_img', $values)
                        && file_exists($values['path_img'])
                    ) {
                        $file = new File($values['path_img'], $values['image']);
                        $values['avatar_img_id'] = $user->uploadUserAvatar($file, $values['username']);
                        $values['id'] = $userId;
                        $values['passwordconfirm'] = $values['password'];
                        $user->update($values);
                    }

                    $this->createTranslation(
                        $values[$schema['translation']['field']],
                        $userId,
                        $schema['translation']['name'],
                        $slug
                    );

                    $this->stats[$name]['imported']++;
                }
            } catch (UserAlreadyExistsException $e) {
                $userId = $this->findUser($values['username']);

                if (empty($userId)) {
                    $userId = $this->findUser($values['email']);
                }

                $this->createTranslation(
                    $values[$schema['translation']['field']],
                    $userId,
                    $schema['translation']['name'],
                    ''
                );

                $this->stats[$name]['already_imported']++;
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
                $slug = array_key_exists('slug', $schema['translation']) ?
                    $values[$schema['translation']['slug']] : '';

                if ($this->matchTranslation(
                    $values[$schema['translation']['field']],
                    $schema['translation']['name']
                ) === false
                ) {
                    $videoId = $this->findVideo($values['video_url']);

                    if ($videoId !== false) {
                        throw new UserAlreadyExistsException();
                    }

                    $params = getService('service_container')->getParameter('panorama');
                    $videoP = new \Panorama\Video($values['video_url'], $params);
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
                        $values[$schema['translation']['field']],
                        $video->id,
                        $schema['translation']['name'],
                        $slug
                    );

                    $this->stats[$name]['imported']++;
                } else {
                    $this->createTranslation(
                        $values[$schema['translation']['field']],
                        $videoId,
                        $schema['translation']['name'],
                        $slug
                    );

                    $this->stats[$name]['already_imported']++;
                }
            } catch (UserAlreadyExistsException $e) {
                $videoId = $this->findVideo($values['video_url']);

                $this->createTranslation(
                    $values[$schema['translation']['field']],
                    $videoId,
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
     * Saves the polls.
     *
     * @param string $name   Schema name.
     * @param array  $schema Database schema.
     * @param array  $data   Polls to save.
     */
    public function saveWidgets($name, $schema, $data)
    {
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
                'content'             => '',
                'renderlet'           => ''
            );

            $values = $this->merge($values, $item, $schema);

            try {
                if ($this->matchTranslation(
                    $values[$schema['translation']['field']],
                    $schema['translation']['name']
                ) === false
                ) {
                    $widgetId = $this->findContent($values['title']);

                    if (!empty($widgetId)) {
                        throw new UserAlreadyExistsException();
                    }

                    $widget = new \Widget();
                    $widget->create($values);
                    $slug = array_key_exists('slug', $schema['translation']) ?
                        $values[$schema['translation']['slug']] : '';

                    $this->createTranslation(
                        $values[$schema['translation']['field']],
                        $widget->id,
                        $schema['translation']['name'],
                        $slug
                    );

                    $this->stats[$name]['imported']++;
                } else {
                    $this->stats[$name]['already_imported']++;
                }
            } catch (UserAlreadyExistsException $e) {
                $id = $this->findContent($values['title']);

                $this->createTranslation(
                    $values[$schema['translation']['field']],
                    $id,
                    $schema['translation']['name'],
                    ''
                );

                $this->stats[$name]['already_imported']++;
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
        if (!defined('CACHE_PREFIX')) {
            define('CACHE_PREFIX', $this->settings['migration']['instance']);
        }

        if (!defined('INSTANCE_UNIQUE_NAME')) {
            define('INSTANCE_UNIQUE_NAME', $this->settings['migration']['instance']);
        }

        if (!defined('MEDIA_IMG_PATH')) {
            define(
                'MEDIA_IMG_PATH',
                SITE_PATH . "media" . DIRECTORY_SEPARATOR . INSTANCE_UNIQUE_NAME
                . DIRECTORY_SEPARATOR . "images"
            );
        }

        if (!defined('MEDIA_PATH')) {
            define('MEDIA_PATH', SITE_PATH . "media" . DIRECTORY_SEPARATOR
                . INSTANCE_UNIQUE_NAME . DIRECTORY_SEPARATOR);
        }

        $this->conn = getService('orm.manager')->getConnection('instance');
        $this->conn->selectDatabase($this->settings['migration']['target']);

        $this->tracker = new SimpleIdTracker($this->conn);
        $this->tracker->load();

        if (array_key_exists('user', $this->settings['migration'])) {
            // TODO: Remove ASAP
            getService('session')->set(
                'user',
                json_decode(json_encode([
                    'id'       => $this->settings['migration']['user']['id'],
                    'username' => $this->settings['migration']['user']['username']
                ]))
            );
        } else {
            // TODO: Remove ASAP
            getService('session')->set(
                'user',
                json_decode(json_encode([ 'id' => 0, 'username' => 'console' ]))
            );
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
        $fm = getService('data.manager.filter');

        return $fm->filter('tags', $string);
    }

    /**
     * Convert a given string to slug.
     *
     * @return string
     */
    protected function convertToSlug($string)
    {
        return \Onm\StringUtils::getTitle($string);
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

        $rss  = $this->conn->executeQuery($sql, $values);

        if (!$rss) {
            $this->output->writeln('createTranslation: check error log');
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
                            "/(\n)/i",
                            "/\[caption.*?\].*?(<img.*?\/?>).*?\[\/caption\]/"
                        ),
                        array('</p><p>', '</p><p>', '<br>', '<br>', '${1}'),
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
                    if (!empty($matches) && array_key_exists(1, $matches)) {
                        $field = $matches[1];
                    }
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
                case 'preg_match_all':
                    preg_match_all($params['pattern'], $field, $matches);

                    $field = $matches[0];
                    break;
                case 'map':
                    $field = $this->convertToMap($field, $params['map']);
                    break;
                case 'metadata':
                    $field = $this->convertToMetadata($field);
                    break;
                case 'merge':
                    if (is_array($field) && count($field) > 0) {
                        $value = '';
                        foreach ($field as $v) {
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
                    $offset = array_key_exists('offset', $params) ? $params['offset'] : 0;
                    $delimiter = $params['delimiter'];
                    $start = array_key_exists('start', $params) ? $params['start'] : 0;
                    $pos = (array_key_exists('strrpos', $params)
                        && $params['strrpos']) ?
                        strrpos($field, $delimiter, $offset) + 1 :
                        strpos($field, $delimiter, $offset);

                    if ($pos !== false) {
                        $field = substr($field, $start, $pos);
                    } else {
                        $field = '';
                    }
                    break;
                case 'substrr':
                    $offset = array_key_exists('offset', $params) ? $params['offset'] : 0;
                    $delimiter = $params['delimiter'];
                    $pos = (array_key_exists('strrpos', $params)
                        && $params['strrpos']) ?
                        strrpos($field, $delimiter, $offset) + 1 :
                        strpos($field, $delimiter, $offset);

                    if ($pos !== false) {
                        $field = substr($field, $pos);
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
                case 'translation_from_slug':
                    if (!empty($field)) {
                        list($type, $field) =
                            \ContentManager::getOriginalIdAndContentTypeFromSlug($field);
                    }
                    break;
                case 'replace_body_images':
                    preg_match_all($params['img_pattern'], $field, $matches);

                    foreach ($matches[1] as $value) {
                        $filename = $value;
                        if ($params['img_basename'] == true) {
                            $filename = pathinfo($value)['basename'];
                        }
                        list($type, $id) =
                            \ContentManager::getOriginalIdAndContentTypeFromSlug($filename);

                        $photo = new \Photo($id);
                        $photoUri = $params['media_path']. $photo->path_img;
                        $field = str_replace($value, $photoUri, $field);
                    }
                    break;
                case 'replace_body_files':
                    preg_match_all($params['file_pattern'], $field, $matches);

                    foreach ($matches[1] as $value) {
                        $filename = $value;
                        if ($params['file_basename'] == true) {
                            $filename = pathinfo($value)['basename'];
                        }
                        list($type, $id) =
                            \ContentManager::getOriginalIdAndContentTypeFromSlug($filename);

                        $file = new \Attachment($id);
                        $field = str_replace($value, $file->uri, $field);
                    }
                    break;
                case 'username':
                    $field = \Onm\StringUtils::getTitle(
                        $field,
                        true,
                        $params['separator']
                    );
                    break;
                case 'utf8':
                    $field = $this->convertToUtf8($field);
                    break;
                case 'md5':
                    if ($params['string']) {
                        if ($params['string']['position'] == 'before') {
                            $field = md5($params['string']['value'].$field);
                        } else {
                            $field = md5($field.$params['string']['value']);
                        }
                    } else {
                        $field = md5($field);
                    }

                    break;
                case 'youtube':
                    $field = $this->convertToYoutube($field);
                    break;
                case 'strip_tags':
                    $field = strip_tags($field);
                    break;
                case 'summary':
                    $field = StringUtils::getNumWords($field, 50);
                    break;
                case 'html_entity_decode':
                    $field = html_entity_decode($field);
                    break;
                case 'replace':
                    $field = preg_replace($params['pattern'], $params['replacement'], $field);
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

        $this->conn->executeQuery($sql, $values);
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

        $this->conn->executeQuery($sql, $values);
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

        $this->conn->executeQuery($sql, $values);
    }

    /**
     * Updates img1 and im1_footer fields from an opinion.
     *
     * @param integer $id     Opinion id.
     * @param integer $photo  Photo id.
     * @param string  $footer Footer value for the photo.
     */
    protected function updateOpinionFrontpagePhoto($id, $photo, $footer)
    {
        $sql = "INSERT INTO contentmeta (`fk_content`, `meta_name`, `meta_value`)"
              ." VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE `meta_value`=?";
        $values = array($id, 'img1', $photo, $photo);

        $this->conn->executeQuery($sql, $values);

        $sql = "INSERT INTO contentmeta (`fk_content`, `meta_name`, `meta_value`)"
              ." VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE `meta_value`=?";
        $values = array($id, 'img1_footer', $footer, $footer);

        $this->conn->executeQuery($sql, $values);
    }

    /**
     * Updates img2 and img2_footer fields from an opinion.
     *
     * @param integer $id     Opinin id.
     * @param integer $photo  Photo id.
     * @param string  $footer Footer value for the photo.
     */
    protected function updateOpinionPhoto($id, $photo, $footer)
    {
        $sql = "INSERT INTO contentmeta (`fk_content`, `meta_name`, `meta_value`)"
              ." VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE `meta_value`=?";
        $values = array($id, 'img2', $photo, $photo);

        $this->conn->executeQuery($sql, $values);

        $sql = "INSERT INTO contentmeta (`fk_content`, `meta_name`, `meta_value`)"
              ." VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE `meta_value`=?";
        $values = array($id, 'img2_footer', $footer, $footer);

        $this->conn->executeQuery($sql, $values);
    }

    /**
     * Updates fk_video2 and footer_video2 fields from an article.
     *
     * @param array $values Values.
     */
    protected function updateRelated($values)
    {
        $sql = "DELETE FROM related_contents WHERE `pk_content1`=? AND `pk_content2`=?";

        $rss  = $this->conn->executeQuery($sql, [$values['pk_content1'],$values['pk_content2']]);

        if (!$rss) {
            $this->output->writeln('Delete - updateRelated: Check error log');
        }

        $sql = "INSERT INTO related_contents  SET `pk_content1`=?, `pk_content2`=?,"
            ."`relationship`=?, `text`=?, `position`=?, `posinterior`=?, `verportada`=?,"
            ."`verinterior`=?";

        $values = [
            $values['pk_content1'],
            $values['pk_content2'],
            $values['relationship'],
            $values['text'],
            $values['position'],
            $values['posinterior'],
            $values['verportada'],
            $values['verinterior'],
        ];

        $rss = $this->conn->executeQuery($sql, $values);

        if (!$rss) {
            $this->output->writeln('Insert - updateRelated: check error log');
        }
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

        $this->conn->executeQuery($sql, $values);
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

        $rs  = $this->conn->executeQuery($sql);

        if (!$rs) {
            $this->output->writeln('createTranslation: check error log');
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
     * Finds the content id for a given title.
     *
     * @param  string  $title Content title.
     * @return integer        Content id.
     */
    private function findContent($title)
    {
        $title = str_replace([ '\'', '"'], [ '\\\'', '\\"'], $title);
        $sql = "SELECT pk_content FROM contents WHERE title='$title'";

        $rs = $this->conn->fetchAll($sql);

        if ($rs && count($rs) == 1
            && array_key_exists('pk_content', $rs[0])
        ) {
            return $rs[0]['pk_content'];
        }

        return false;
    }

    /**
     * Finds the category id for a given normalized name.
     *
     * @param  string  $name Category name.
     * @return integer       Category id.
     */
    private function findCategory($name)
    {
        $name = str_replace([ '\'', '"'], [ '\\\'', '\\"'], $name);
        $sql = "SELECT pk_content_category FROM content_categories"
            . " WHERE name='$name' OR title='$name'";

        $rs = $this->conn->fetchAll($sql);

        if ($rs && count($rs) == 1
            && array_key_exists('pk_content_category', $rs[0])
        ) {
            return $rs[0]['pk_content_category'];
        }

        return false;
    }

    /**
     * Reloads categories array.
     *
     * @param  string  $name Category name.
     * @return integer       Category id.
     */
    private function reloadCategoryArray()
    {
        $ccm = \ContentCategoryManager::get_instance();
        $ccm->reset();
        $ccm->findAll();
    }

    /**
     * Finds the photo id for a given title.
     *
     * @param  string  $title Photo title.
     * @return mixed          Photo id if photo was found. Otherwise, return
     *                        false.
     */
    private function findPhoto($title)
    {
        if (!empty($title)) {
            $title = str_replace([ '\'', '"'], [ '\\\'', '\\"'], $title);
            $sql = "SELECT pk_content FROM contents WHERE content_type_name='photo' AND title = '$title'";

            $rs = $this->conn->fetchAll($sql);

            if ($rs && count($rs) == 1 && array_key_exists('pk_content', $rs[0])) {
                return $rs[0]['pk_content'];
            }
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
        $sql = "SELECT id FROM users WHERE name = '$name' OR username = '$name'"
            . " OR email = '$name'";

        $rs = $this->conn->fetchAll($sql);

        if ($rs && count($rs) == 1 && array_key_exists('id', $rs[0])) {
            return $rs[0]['id'];
        }

        return false;
    }

    /**
     * Finds the video id for a given video url.
     *
     * @param  string  $url Video url.
     * @return mixed        Video url if user was found. Otherwise, return
     *                       false.
     */
    private function findVideo($url)
    {
        $sql = "SELECT pk_video FROM videos WHERE video_url = '$url'";

        $rs = $this->conn->fetchAll($sql);

        if ($rss && count($rs) == 1 && array_key_exists('pk_video', $rs[0])) {
            return $rs[0]['pk_video'];
        }

        return false;
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
}
