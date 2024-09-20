<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\NewsAgency\Component\Importer;

use Common\Model\Entity\Content;
use Common\NewsAgency\Component\Repository\LocalRepository;
use Common\NewsAgency\Component\Resource\ExternalResource;
use Common\Model\Entity\Instance;

class Importer
{
    /**
     * The default values for resources while importing.
     *
     * @var array
     */
    protected $defaults = [
        'content_status'    => 1,
        'content_type_name' => 'article',
        'with_comment'      => 1
    ];

    /**
     * The importer configuration.
     *
     * @var array
     */
    protected $config = [];

    /**
     * The service container.
     *
     * @var Container
     */
    protected $container;

    /**
     * The base path to import resources from.
     *
     * @var string
     */
    protected $path;

    /**
     * Wether to also import related contents.
     *
     * @var bool
     */
    protected $propagation = true;

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
        $this->setInstance($container->get('core.instance'));
    }

    /**
     * Imports all resources from the configured source.
     *
     * @return array The array of ids.
     */
    public function autoImport()
    {
        $ignored   = 0;
        $invalid   = 0;
        $imported  = 0;
        $resources = $this->getResources();

        $finalContents = [];

        foreach ($resources as $resource) {
            try {
                if ($this->isImported($resource)) {
                    $ignored++;
                    continue;
                }

                $finalContents [] = $this->import($resource);
                $imported++;
            } catch (\Exception $e) {
                $invalid++;
            }
        }

        $this->sendVarnishBan($finalContents);
        return [
            'ignored'  => $ignored,
            'imported' => $imported,
            'invalid'  => $invalid
        ];
    }

    /**
     * Configures the Importer.
     *
     * @param array $config The importer configuration.
     */
    public function configure(array $config) : Importer
    {
        $this->config = $config;

        $config = $this->container->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->init()
            ->get('comment_settings');

        if (!empty($config) && array_key_exists('with_comments', $config)) {
            $this->defaults['with_comment'] = (int) $config['with_comments'];
        }

        return $this;
    }

    /**
     * Imports a resource.
     *
     * @param string  $id   The resource id.
     * @param integer $data The content information.
     *
     * @return int The content id.
     *
     * @throws \Exception
     */
    public function import($resource, $data = [])
    {
        if ($this->isImported($resource)) {
            $imported = $this->getImported([ $resource->urn ]);

            return $imported[$resource->urn];
        }

        $data = $this->getData($resource, $data);

        if ($resource->type === 'photo') {
            $file = new \SplFileInfo($data['path']);

            // Avoid uploading images with content_status = 0
            $data['content_status'] = 1;

            unset($data['path']);
            unset($data['params']);

            return $this->container->get('api.service.photo')
                ->createItem($data, $file, true);
        }

        $webpushNotification = $this->container->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->init()
            ->get('webpush_automatic');

        if (!empty($webpushNotification)
            && array_key_exists('content_type_name', $data)
            && $data['content_type_name'] == 'article') {
            $data['webpush_notifications'] = $this->container->get('core.helper.webpush_notifications')
                ->createNotificationFromData($data);
        }
        return $this->container->get('api.service.content')
            ->createItem($data);
    }

    /**
     * Check if auto-import mode is enabled.
     *
     * @return boolean True if the auto-import mode is enabled. Otherwise,
     *                 returns false.
     */
    public function isAutoImportEnabled()
    {
        return array_key_exists('auto_import', $this->config)
            && $this->config['auto_import'];
    }

    /**
     * Checks if a resource or a list of resources is already imported.
     *
     * @param mixed $resources A resource or a list of resources
     *
     * @return bool True if the item or one of the items in the list is already
     *              imported. False otherwise.
     */
    public function isImported($resources) : bool
    {
        if (array_key_exists('id', $resources)) {
            $resources = [ $resources ];
        }

        $urns = array_map(function ($a) {
            return $a->urn;
        }, $resources);

        $criteria = [
            'urn_source' => [
                [ 'value' => $urns, 'operator' => 'IN' ]
            ]
        ];

        $imported = $this->container->get('entity_repository')
            ->countBy($criteria, []);

        return $imported > 0;
    }

    /**
     * Configures the Importer for the provided instance.
     *
     * @param Instance $instance The instance.
     *
     * @return Importer The current Importer.
     */
    public function setInstance(Instance $instance) : Importer
    {
        $this->path = sprintf(
            '%s/%s/importers',
            $this->container->getParameter('core.paths.cache'),
            $instance->internal_name
        );

        $this->repository->read($this->path);

        return $this;
    }

    /**
     * Changes the propagation flag.
     *
     * @param bool $propagation The propagation flag value.
     *
     * @return Importer The current importer.
     */
    public function setPropagation(bool $propagation) : Importer
    {
        $this->propagation = $propagation;

        return $this;
    }

    /**
     * Returns the author id based on the resource and the server configuration.
     *
     * @param ExternalResource $resource The resource.
     * @param array            $data     The information to use while importing.
     *
     * @return ?int The author id if author id is present in data or in default
     *              server configuration or null if author id is not present.
     */
    protected function getAuthor(ExternalResource $resource, array $data) : ?int
    {
        if (array_key_exists('fk_author', $data)
            && !empty($data['fk_author'])
        ) {
            return (int) $data['fk_author'];
        }

        if ($this->isAutoImportEnabled()) {
            if (array_key_exists('authors_map', $this->config)) {
                $authors = array_filter(
                    $this->config['authors_map'],
                    function ($a) use ($resource) {
                        return preg_match(
                            '/' . str_replace('/', '\/', $a['slug']) . '/i',
                            $resource->author
                        );
                    }
                );

                if (!empty($authors)) {
                    return (int) array_pop($authors)['id'];
                }
            }

            return (int) $this->config['author'];
        }

        return null;
    }

    /**
     * Returns the category id based on the resource and the server
     * configuration.
     *
     * @param ExternalResource $resource The resource.
     * @param array            $data     The information to use while importing.
     *
     * @return ?int The category id if category id is present in data or in
     *              default server configuration or null if category id is not
     *              present.
     */
    protected function getCategory(ExternalResource $resource, array $data) : ?int
    {
        if (array_key_exists('fk_content_category', $data)
            && !empty($data['fk_content_category'])
        ) {
            return (int) $data['fk_content_category'];
        }

        if ($this->isAutoImportEnabled()) {
            if (array_key_exists('categories_map', $this->config)) {
                $categories = array_filter(
                    $this->config['categories_map'],
                    function ($a) use ($resource) {
                        return preg_match(
                            '/' . str_replace('/', '\/', $a['slug']) . '/i',
                            $resource->category
                        );
                    }
                );

                if (!empty($categories)) {
                    return (int) array_pop($categories)['id'];
                }
            }

            return (int) $this->config['category'];
        }

        return null;
    }

    /**
     * Returns the content data from the resource.
     *
     * @param Resource $resource The resource to import.
     * @param array    $data     The information to use while importing.
     *
     * @return array The array of data.
     */
    protected function getData(ExternalResource $resource, array $data) : array
    {
        if (!array_key_exists('content_type_name', $data)
            && array_key_exists('target', $this->config)
        ) {
            $data['content_type_name'] = $this->config['target'];
        }

        $data = array_merge($this->defaults, $data, [
            'description'         => $resource->summary,
            'frontpage'           => 0,
            'fk_author'           => $this->getAuthor($resource, $data),
            'fk_publisher'        => $this->getAuthor($resource, $data),
            'fk_user_last_editor' => $this->getAuthor($resource, $data),
            'in_home'             => 0,
            'tags'                => $this->getTags($resource),
            'title'               => $resource->title,
            'urn_source'          => $resource->urn,
            'body'                => $resource->body,
            'href'                => $resource->href,
        ]);

        // Force some properties for photos
        if ($resource->type === 'photo') {
            $data['content_type_name'] = 'photo';
            $data['fk_content_type']   = 8;
            $data['description']       = $resource->body;
        }

        // Check if the source has an external link configured
        if (array_key_exists('external', $this->config)
            && $this->config['external'] === 'redirect'
            && array_key_exists('external_link', $this->config)
            && !empty($this->config['external_link'])
        ) {
            $data['params'] = [ 'bodyLink' => $this->config['external_link'] ];
        }

        if (array_key_exists('external', $this->config)
            && $this->config['external'] === 'original'
        ) {
            $data['params'] = [ 'bodyLink' => $data['href'] ];
        }

        $method = 'getDataFor' . ucfirst($data['content_type_name']);

        if (method_exists($this, $method)) {
            $data = $this->{$method}($resource, $data);
        }

        return $data;
    }

    /**
     * Returns the required information to import the resource as article.
     *
     * @param ExternalResource $resource The resource to import.
     * @param array            $data     The information extracted from the
     *                                   resource regardless of the target
     *                                   content type.
     *
     * @return array The information to import the resource as article.
     */
    protected function getDataForArticle(ExternalResource $resource, array $data) : array
    {
        $date = new \DateTime();

        $data = array_merge($data, [
            'created'         => $date->format('Y-m-d H:i:s'),
            'fk_content_type' => 1,
            'categories'      => [ $this->getCategory($resource, $data) ],
            'agency'          => !empty($resource->signature)
                ? $resource->signature
                : (array_key_exists('agency_string', $this->config)
                    ? $this->config['agency_string']
                    : null),
            'pretitle'        => $resource->pretitle,
            'description'     => $resource->summary,
            'title_int'       => $resource->title,
            'slug'            => $this->container->get('data.manager.filter')
                ->set($resource->title)
                ->filter('slug')
                ->get()
        ]);

        if (empty($resource->related)) {
            return $data;
        }

        $resources = $this->repository->find($resource->related);
        $urns      = array_map(function ($a) {
            return $a->urn;
        }, $resources);

        $contents = !$this->propagation
            ? $this->getImported($urns)
            : array_map(function ($a) use ($data) {
                return $this->setPropagation(false)->import($a, $data);
            }, $resources);

        $this->setPropagation(true);

        $data['related_contents'] = [];
        foreach ($contents as $content) {
            if ($content->content_type_name === 'photo') {
                $data['related_contents'] = $this->container->get('core.helper.featured_media')->getRelated(
                    $content,
                    [ 'featured_frontpage', 'featured_inner' ]
                );
            }

            if ($content->content_type_name === 'video') {
                $data['related_contents'] = $this->container->get('core.helper.featured_media')->getRelated(
                    $content,
                    [ 'featured_frontpage', 'featured_inner' ]
                );
            }

            if ($content->content_type_name === 'article') {
                $data['related_contents'] = $this->container->get('core.helper.featured_media')->getRelated(
                    $content,
                    [ 'related_frontpage', 'related_inner' ],
                    $data['related_contents']
                );
            }
        }

        return $data;
    }

    /**
     * Returns the required information to import the resource as opinion.
     *
     * @param ExternalResource $resource The resource to import.
     * @param array            $data     The information extracted from the
     *                                   resource regardless of the target
     *                                   content type.
     *
     * @return array The information to import the resource as opinion.
     */
    protected function getDataForOpinion(ExternalResource $resource, array $data) : array
    {
        $date = new \DateTime();
        $data = array_merge($data, [
            'created' => $date->format('Y-m-d H:i:s'),
            'fk_content_type' => 4,
            'slug' => $this->container->get('data.manager.filter')
                ->set($resource->title)
                ->filter('slug')
                ->get()
        ]);

        if (empty($resource->related)) {
            return $data;
        }

        $resources = $this->repository->find($resource->related);
        $urns      = array_map(function ($a) {
            return $a->urn;
        }, $resources);

        $contents = !$this->propagation
            ? $this->getImported($urns)
            : array_map(function ($a) use ($data) {
                return $this->setPropagation(false)->import($a, $data);
            }, $resources);

        $this->setPropagation(true);

        foreach ($contents as $content) {
            if ($content->content_type_name === 'photo') {
                $data['related_contents'] = $this->container->get('core.helper.featured_media')->getRelated(
                    $content,
                    [ 'featured_frontpage', 'featured_inner' ]
                );
                break;
            }
        }

        return $data;
    }

    /**
     * Returns the required information to import the resource as photo.
     *
     * @param ExternalResource $resource The resource to import.
     * @param array            $data     The information extracted from the
     *                                   resource regardless of the target
     *                                   content type.
     *
     * @return array The information to import the resource as photo.
     */
    protected function getDataForPhoto(ExternalResource $resource, array $data) : array
    {
        $data['path'] = sprintf(
            '%s/%s/%s',
            $this->path,
            $resource->source,
            $resource->file_name
        );

        return $data;
    }

    /**
     * Returns the list of contents already imported based on a resource urn or
     * a list of resource urns.
     *
     * @param mixed $urns A resource urn or a list of resource urns.
     *
     * @return array The list of imported contents.
     */
    protected function getImported(array $urns) : array
    {
        $criteria = [
            'urn_source' => [
                [ 'value' => $urns, 'operator' => 'IN' ]
            ]
        ];

        $imported = [];
        $contents = $this->container->get('entity_repository')
            ->findBy($criteria, []);

        foreach ($contents as $content) {
            $imported[$content->urn_source] = $content;
        }

        return $imported;
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
            return $this->repository->findBy($criteria, 'priority asc');
        }

        $resources = [];
        foreach ($this->config['filters'] as $filter) {
            $criteria = array_merge(
                $criteria,
                [ 'tags' => $filter, 'title' => $filter, 'body' => $filter ]
            );

            $items     = $this->repository->findBy($criteria, 'priority asc');
            $resources = array_merge($resources, $items);
        }

        return $resources;
    }

    /**
     * Returns a list of tags for the resource.
     *
     * @param ExternalResource $resource The resource.
     *
     * @return array A list of tag ids.
     */
    protected function getTags(ExternalResource $resource) : array
    {
        $tags = !empty($resource->tags) ? $resource->tags : $resource->title;
        $tags = $this->container->get('api.service.tag')
            ->getListByString($tags)['items'];

        return array_map(function ($tag) {
            return $tag->id;
        }, $tags);
    }

    /**
     * Sends a varnish ban
     *
     * @return Importer The current Importer.
     */
    protected function sendVarnishBan($contents)
    {
        $articlesBans = [
            '{{content_type_name}}-frontpage$',
            '{{content_type_name}}-frontpage',
            'content_type_name-widget-article',
            'author-{{fk_author}}(,|$)',
            'rss-article$',
            'sitemap',
            'category-{{categories}}',
            'content-author-{{fk_author}}-frontpage',
            '{{content_type_name}}-frontpage,category-{{content_type_name}}-{{categories}}',
            'content_type_name-widget-article' .
            '.*category-widget-({{categories}}|all)' .
            '.*tag-widget-({{tags}}|all)' .
            '.*author-widget-({{fk_author}}|all)',
            'last-suggested-{{categories}}',
            'rss-author-{{fk_author}}',
            'tag-{{tags}}',
        ];
        $opinionsBans = [
            'author-{{fk_author}}(,|$)',
            'sitemap',
            'opinion-author-{{fk_author}}-frontpage',
            'content-author-{{fk_author}}-frontpage',
            '{{content_type_name}}-frontpage$',
            'content_type_name-widget-{{content_type_name}}' .
            '.*tag-widget-({{tags}}|all)' .
            '.*author-widget-({{fk_author}}|all)',
            'rss-author-{{fk_author}}',
            'rss-{{content_type_name}}$',
            'sitemap',
            'tag-{{tags}}',
        ];

        $bans = [];
        foreach ($contents as $content) {
            if ($content->content_type_name == 'article') {
                $bans = array_merge(
                    $bans,
                    $this->replaceWildcards($content, $articlesBans)
                );
            }
            if ($content->content_type_name == 'opinion') {
                $bans = array_merge(
                    $bans,
                    $this->replaceWildcards($content, $opinionsBans)
                );
            }
        }

        if (empty($bans)) {
            return;
        }

        $bans = array_unique($bans);

        $banRegExpr = '';

        foreach ($bans as $key) {
            $banRegExpr .= '|(' . $key . ')';
        }

        $this->container->get('core.varnish')
            ->ban(
                sprintf(
                    'obj.http.x-tags ~ ^instance-%s.*%s',
                    $this->container->get('core.globals')->getInstance()->internal_name,
                    '(' . substr($banRegExpr, 1) . ')'
                )
            );

        return $this;
    }

    /**
     * Replace the keys with the property of the item.
     *
     * @param Content $item The item to get the properties from.
     * @param array   $keys The array of keys to loop over.
     *
     * @return String The key of the varnish cache.
     */
    protected function replaceWildcards($item, $keys)
    {
        foreach ($keys as &$key) {
            preg_match_all('@{{([A-Za-z0-9_-]+)}}@', $key, $matches);

            foreach ($matches[1] as $match) {
                $pattern = sprintf('@{{%s}}@', $match);

                // Don't add the key if the content doesn't have the property and is a single replacement
                if (empty($item->{$match}) && count($matches[1]) === 1) {
                    $key = null;
                    continue;
                }

                if (empty($item->{$match})) {
                    $key = preg_replace($pattern, '0', $key);
                    continue;
                }

                $method = sprintf('replace%s', ucfirst($match));

                $replacement = method_exists($this, $method)
                    ? $this->{$method}($item)
                    : $item->{$match};

                $key = preg_replace($pattern, $replacement, $key);
            }
        }

        return array_filter($keys);
    }

    /**
     * Custom replace function for starttime.
     *
     * @param Content $item The item to get the starttime from.
     *
     * @return String The key with the starttime replaced in it.
     */
    protected function replaceStarttime(Content $item)
    {
        return $item->starttime->format('Y-m-d');
    }

    /**
     * Custom replace function for tags.
     *
     * @param Content $item The item to get the tags from.
     *
     * @return String The key with the tags replaced in it.
     */
    protected function replaceTags(Content $item)
    {
        return sprintf('(%s)', implode('|', $item->tags));
    }

    /**
     * Custom replace function for categories.
     *
     * @param Content $item The item to get the tags from.
     *
     * @return String The key with the tags replaced in it.
     */
    protected function replaceCategories(Content $item)
    {
        return $item->categories[0];
    }
}
