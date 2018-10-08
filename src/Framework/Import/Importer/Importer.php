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
     * Returns the list of resources to import basing on the configuration.
     *
     * @return array The list of resources to import.
     */
    protected function getResources()
    {
        $criteria = [ 'source' => $this->config['id'] ];

        if (!array_key_exists('filters', $this->config)
            || empty($this->config['filters'])
        ) {
            return $this->repository->findBy($criteria);
        }

        $resources = [];
        foreach ($this->config['filters'] as $filter) {
            $criteria = array_merge(
                $criteria,
                [ 'tags' => $filter, 'title' => $filter, 'body' => $filter ]
            );

            $items     = $this->repository->findBy($criteria);
            $resources = array_merge($resources, $items);
        }

        return $resources;
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
    public function import($resource, $category = null, $target = 'Article', $author = null, $enabled = 1)
    {
        $content = $this->container->get('entity_repository')
            ->findOneBy([ 'urn_source' => [ [ 'value' => $resource->urn ] ] ]);

        if (!empty($content)) {
            throw new \Exception(_('Content already imported'));
        }

        $category = $this->getCategory($category);
        $author   = $this->getAuthor($resource, $author);

        $data = $this->getData($resource, $category, $author, $enabled, $target);

        if ($resource->type === 'photo') {
            $photo = new \Photo();
            $id    = $photo->createFromLocalFile($data);

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
        $ignored   = 0;
        $imported  = [];
        $resources = $this->getResources();

        foreach ($resources as $resource) {
            try {
                $id = $this->import($resource);

                if (!empty($id)) {
                    $imported[] = $id;
                }
            } catch (\Exception $e) {
                $ignored++;
            }
        }

        return [ $imported, $ignored ];
    }

    /**
     * Downloads and create a photo form URL for the given author.
     *
     * @param string $url    The photo URL.
     * @param User   $author The author object.
     *
     * @return integer The photo id.
     */
    protected function createPhoto($url, $author)
    {
        $cm       = new \ContentManager();
        $photoRaw = $cm->getUrlContent($url);
        $name     = substr($url, strrpos($url, '/') + 1);

        if (!$photoRaw) {
            return $author->id;
        }

        $tmp  = tmpfile();
        $path = stream_get_meta_data($tmp)['uri'];

        file_put_contents($path, $photoRaw);

        $data = [
            'description'       => $author->name,
            'local_file'        => $path,
            'original_filename' => $name,
        ];

        $photo = new \Photo();

        return $photo->createFromLocalFile($data);
    }

    /**
     * Returns the author id for a resource.
     *
     * @param Resource $resource The resource.
     * @param integer  $id       The author id.
     * @param string   $target   The target content type.
     *
     * @return integer The author id for the resource.
     */
    protected function getAuthor($resource, $author)
    {
        // Author as parameter
        if (!empty($author)) {
            return $author;
        }

        // Resource has no author
        if (!property_exists($resource, 'author')
            || empty($resource->author)
            || !array_key_exists('author', $this->config)
            || $this->config['author'] !== '1'
        ) {
            if (array_key_exists('target_author', $this->config)
                && !empty($this->config['target_author'])
            ) {
                return $this->config['target_author'];
            }

            return 0;
        }

        if (!is_array($resource->author)) {
            return 0;
        }

        $as     = $this->container->get('api.service.author');
        $author = null;

        try {
            $response = $as->getList("name = '{$resource->author['name']}'");
            $author   = array_pop($response['items']);

            if (empty($author)) {
                $author = $as->createItem([
                    'name'        => $resource->author['name'],
                    'email'       => $resource->author['name'],
                    'user_groups' => [ [ 'user_group_id' => 3, 'status' => 1 ] ],
                    'type'        => 0
                ]);
            }
        } catch (\Exception $e) {
            $this->container->get('error.log')->error($e->getMessage());
            return 0;
        }

        // Do not import photo if missing or author already has a photo
        if (empty($resource->author['photo'])
            || !empty($author->avatar_img_id)
        ) {
            return $author->id;
        }

        try {
            $photoId = $this->createPhoto($resource->author['photo'], $author);

            if (!empty($photoId)) {
                $as->patchItem($author->id, [ 'avatar_img_id' => $photoId ]);
            }
        } catch (\Exception $e) {
            $this->container->get('error.log')->error($e->getMessage());
        }

        return $author->id;
    }

    /**
     * Checks if comments are allowed for imported contents.
     *
     * @return integer The value for with_comments flag for contents.
     */
    protected function getComments()
    {
        $config = $this->container->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('comments_config');

        if (!empty($config) && array_key_exists('with_comments', $config)) {
            return $config['with_comments'];
        }

        return 1;
    }

    /**
     * Returns the category basing on the configuration.
     *
     * @param integer $category The category for contents.
     *
     * @return integer The category for contents.
     */
    protected function getCategory($category = null)
    {
        if (!empty($category)) {
            return $category;
        }

        if ($this->autoImport()) {
            return $this->config['category'];
        }

        return 20;
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
        $fm   = getService('data.manager.filter');
        $data = [
            'category'            => $category,
            'content_status'      => $enabled,
            'description'         => $resource->summary,
            'frontpage'           => 0,
            'fk_author'           => $this->getAuthor($resource, $author),
            'fk_publisher'        => $this->getAuthor($resource, $author),
            'fk_user_last_editor' => $this->getAuthor($resource, $author),
            'in_home'             => 0,
            'tag_ids'             => getService('api.service.tag')
                ->getTagIdsFromStr($resource->title),
            'title'               => $resource->title,
            'urn_source'          => $resource->urn,
            'with_comment'        => $this->getComments(),
            'summary'             => $resource->summary,
            'body'                => $resource->body,
            'img1'                => 0,
            'img1_footer'         => '',
            'img2'                => 0,
            'img2_footer'         => '',
        ];

        // If the source has an external link configured assign it as
        // the external link in the content to import
        if (array_key_exists('external_link', $this->config)
            && !empty($this->config['external_link'])
        ) {
            $data['params'] = [ 'bodyLink' => $this->config['external_link'] ];
        }

        if ($resource->type === 'photo' || $target === 'photo') {
            $data['original_filename'] = $resource->file_name;
            $data['local_file']        = realpath($this->repository->syncPath
                . DS . $this->config['id'] . DS . $resource->file_name);

            return $data;
        }

        if ($target === 'Article') {
            $data['title_int']     = $resource->title;
            $data['subtitle']      = $resource->pretitle;
            $data['agency']        = $this->config['agency_string'];
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
            $data = array_merge($data, $this->getRelatedData($resource, $target));
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
        $data    = [];
        $related = [];

        foreach ($resource->related as $id) {
            $r = $this->repository->find($this->config['id'], $id);

            if (!empty($r)) {
                $element = $this->container->get('entity_repository')
                    ->findOneBy([ 'urn_source' => [[ 'value' => $r->urn ]] ]);

                if (empty($element) && $r->type == 'photo') {
                    $photoData = $this->getData($r, null, null, 1, 'photo');
                    $photo     = new \Photo();
                    $photo->createFromLocalFile($photoData);
                }

                $related[] = $r;
            }
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

        $total    = $this->container->get('entity_repository')
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
                    $data['fk_video']     = $content->pk_content;
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
     * Checks if the related contents have to be imported.
     *
     * @return boolean True if the related contents have to be imported.
     *                 Otherwise, returns false.
     */
    protected function importRelated()
    {
        if ($this->autoImport()) {
            return $this->config['import_related'];
        }

        return true;
    }
}
