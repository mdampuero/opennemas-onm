<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\Import\Importer;

use Framework\Import\Repository\LocalRepository;

class Importer
{
    /**
     * The importer configuration.
     *
     * @var array
     */
    protected $config = [];

    /**
     * The service container.
     *
     * @var ServiceContainer
     */
    protected $container;

    /**
     * The local repository.
     *
     * @var LocalRepository
     */
    protected $repository;

    /**
     * Initializes the Importer.
     *
     * @param array $config The importer configuration.
     */
    public function __construct($container, $config = [])
    {
        $this->container  = $container;
        $this->repository = new LocalRepository();

        $this->configure($config);
    }

    /**
     * Configures the Importer.
     *
     * @param array $config The importer configuration.
     */
    public function configure($config)
    {
        $this->config = array_merge($this->config, $config);
    }

    /**
     * Check if auto-import mode is enabled.
     *
     * @return boolean True if the auto-import mode is enabled. Otherwise,
     *                 returns false.
     */
    public function autoImport()
    {
        if (array_key_exists('auto_import', $this->config)
            && $this->config['auto_import']
        ) {
            return true;
        }

        return false;
    }

    /**
     * Returns the category basing on the configuration.
     *
     * @param integer $category The category for contents.
     *
     * @return integer The category for contents.
     */
    public function getCategory($category = null)
    {
        if ($this->autoImport()) {
            return $this->config['category'];
        }

        $category = $this->getSimilarCategory($category);

        if (!empty($category)) {
            return $category;
        }

        return 20;
    }

    /**
     * Imports a resource.
     *
     * @param string  $id       The resource id.
     * @param integer $category The category id.
     * @param string  $target   The content type name to import resource as.
     * @param integer $author   The author id.
     * @param integer $enabled  The enabled flag value.
     *
     * @return integer The content id.
     */
    public function import($resource, $category = null, $target = 'Article', $author = null, $enabled = 0)
    {
        $category = $this->getCategory($category);

        $data = $this->getData($resource, $category, $author, $enabled, $target);

        if ($resource->type === 'photo') {
            $photo = new \Photo();
            $id = $photo->createFromLocalFile($data);

            return $id;
        }

        $content = new $target();
        $content->create($data);

        return $content->id;
    }

    /**
     * Imports all resources from the configured source.
     *
     * @return array The array of ids.
     */
    public function importAll()
    {
        $total      = $this->repository->countBy();
        $criteria   = [ 'source' => $this->config['id'] ];

        $resources = $this->repository->findBy($criteria, $total, 1);

        foreach ($resources as $resource) {
            $imported[] = $this->import(
                $resource,
                $this->config['category'],
                'Article',
                null,
                1
            );
        }

        return $imported;
    }

    /**
     * Checks if the related contents have to be imported.
     *
     * @return boolean True if the related contents have to be imported.
     *                 Otherwise, returns false.
     */
    public function importRelated()
    {
        if ($this->autoImport()) {
            return $this->config['import_related'];
        }

        return true;
    }

    /**
     * Checks if comments are allowed for imported contents.
     *
     * @return integer The value for with_comments flag for contents.
     */
    protected function getComments()
    {
        $config = $this->container->get('setting_repository')
            ->get('comments_config');

        if (!empty($config) && array_key_exists('with_comments', $config)) {
            return $config['with_comments'];
        }

        return 1;
    }

    /**
     * Returns the content data from the resource.
     *
     * @param Resource $resource The resource to import.
     * @param string   $path     The resource to import.
     * @param integer  $category The category id.
     * @param author   $author   The author id.
     * @param integer  $enabled  The enabled flag.
     * @param string   $target   The target content.
     *
     * @return array The array of data.
     */
    protected function getData($resource, $category, $author, $enabled, $target)
    {
        $data = [
            'category'            => $category,
            'content_status'      => $enabled,
            'description'         => $resource->summary,
            'frontpage'           => 0,
            'fk_author'           => (isset($authorId) ? $authorId : 0),
            'fk_publisher'        => 0,
            'fk_user_last_editor' => 0,
            'in_home'             => 0,
            'metadata'            => \Onm\StringUtils::getTags($resource->title),
            'title'               => $resource->title,
            'urn_source'          => $resource->urn,
            'with_comment'        => $this->getComments(),
        ];

        if ($target === 'photo') {
            $data['local_file'] = realpath($this->repository->syncPath. DS
                . $this->config['id'] .  DS . $resource->file_name);
            $data['original_filename'] = $resource->file_name;

            return $data;
        }

        if ($target === 'Article') {
            $data['title_int']     = $resource->title;
            $data['subtitle']      = $resource->pretitle;
            $data['agency']        = $this->config['agency_string'];
            $data['summary']       = $resource->summary;
            $data['body']          = $resource->body;
            $data['img1']          = 0;
            $data['img1_footer']   = '';
            $data['img2']          = 0;
            $data['img2_footer']   = '';
            $data['fk_video']      = 0;
            $data['footer_video']  = '';
            $data['fk_video2']     = 0;
            $data['footer_video2'] = '';

            if (!empty($resource->signature)) {
                $data['agency'] = $resource->signature;
            }
        }

        if ($target === 'Opinion') {
            $data['type_opinion'] = 0;

            if ($author == 1 || $author == 2) {
                $data['type_opinion'] = $author;
            }
        }

        if ($this->importRelated() && !empty($resource->related)) {
            $data = array_merge($data, $this->getRelatedData());
        }

        return $data;
    }

    /**
     * Returns the content related contents data from the resource.
     *
     * @param Resource $resource The resource to import.
     * @param string   $target   The target content.
     *
     * @return array The array of data.
     */
    protected function getRelatedData($resource, $target)
    {
        $data       = [];
        $related    = [];

        foreach ($resource->related as $id) {
            $related[] = $this->repository->find($this->config['id'], $id);
        }

        if (empty($related)) {
            return $data;
        }

        $urns = array_map(function ($a) {
            return $a->urn;
        }, $related);

        $criteria = [
            'urn_source' => [
                [ 'value' => $urns, 'operator' => 'in' ]
            ]
        ];

        $total = $this->container->get('entity_repository')
            ->countBy($criteria);
        $contents = $this->container->get('entity_repository')
            ->findBy($criteria, [], $total);

        if (empty($contents)) {
            return $data;
        }

        foreach ($contents as $content) {
            if ($content->content_type_name === 'photo') {
                if (!array_key_exists('img1', $data) || empty($data['img1'])) {
                    $data['img1']        = $content->pk_content;
                    $data['img1_footer'] = $content->description;
                }

                // Add as inner image if no image or if it is equals to img1
                if (!array_key_exists('img2', $data)
                    || empty($data['img2'])
                    || $data['img1'] == $data['img2']
                ) {
                    $data['img2']        = $content->pk_content;
                    $data['img2_footer'] = $content->description;
                }
            }

            if ($target === 'article'
                && $content->content_type_name === 'video'
            ) {
                if (!array_key_exists('fk_video', $data)) {
                    $data['fk_video']        = $content->pk_content;
                    $data['footer_video'] = $content->description;
                }

                // Add as inner image if no image or if it is equals to video1
                if (!array_key_exists('fk_video2', $data)
                    || $data['fk_video'] == $data['fk_video2']
                ) {
                    $data['fk_video2']     = $content->pk_content;
                    $data['footer_video2'] = $content->description;
                }
            }
        }

        return $data;
    }

    /**
     * Returns the category id of the most similar category in database given a
     * given a external category name.
     *
     * @param string The external category name.
     *
     * @return integer The category id of the most similar category.
     */
    protected function getSimilarCategory($original)
    {
        if (empty($original)) {
            return $original;
        }

        $ccm = \ContentCategoryManager::get_instance();
        $categories = $ccm->findAll();

        $prevPoint = 1000;
        $final     = 0;
        foreach ($categories as $category) {
            $lev = levenshtein($original, $category->name);

            if ($lev < 2  && $lev < $prevPoint) {
                $prevPoint = $lev;
                $final     = $category->id;
            }
        }

        return $final;
    }
}
