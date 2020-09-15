<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Api\Service\V1;

use Api\Exception\ApiException;
use Api\Exception\CreateItemException;
use Api\Exception\DeleteItemException;
use Api\Exception\DeleteListException;
use Api\Exception\GetItemException;
use Api\Exception\GetListException;
use Api\Exception\PatchItemException;
use Api\Exception\PatchListException;
use Api\Exception\UpdateItemException;
use Api\Service\Service;
use Common\NewsAgency\Component\Repository\LocalRepository;
use Symfony\Component\Finder\Finder;

class NewsAgencyResourceService implements Service
{
    /**
     * The service container.
     *
     * @var Container
     */
    protected $container;

    /**
     * The Finder component.
     *
     * @var Finder
     */
    protected $finder;

    /**
     * The importer service.
     *
     * @var Importer
     */
    protected $importer;

    /**
     * The base path for importer.
     *
     * @var string
     */
    protected $path;

    /**
     * The repository to read items.
     *
     * @var LocalRepository
     */
    protected $repository;

    /**
     * The event dispatcher service.
     *
     * @var EventDispatcher
     */
    protected $dispatcher;

    /**
     * Initializes the NewsAgencyServerService.
     *
     * @param Container $container The service container.
     */
    public function __construct($container)
    {
        $this->container    = $container;
        $this->dispatcher   = $container->get('core.dispatcher');
        $this->finder       = new Finder();
        $this->importer     = $this->container->get('news_agency.service.importer');
        $this->repository   = new LocalRepository();
        $this->synchronizer = $this->container->get('news_agency.service.synchronizer');
        $this->path         = sprintf(
            '%s/%s/importers',
            $container->getParameter('core.paths.cache'),
            $container->get('core.instance')->internal_name
        );

        $this->repository->read($this->path);
    }

    /**
     * {@inheritdoc}
     */
    public function createItem($data)
    {
        throw new CreateItemException('Action not implemented', 400);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteItem($id)
    {
        throw new DeleteItemException('Action not implemented', 400);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteList($ids)
    {
        throw new DeleteListException('Action not implemented', 400);
    }

    /**
     * Returns the content of the file for the resource.
     *
     * @param string $id The resource id.
     *
     * @return string The content of the file.
     */
    public function getContent($id)
    {
        try {
            $item  = $this->getItem($id);
            $path  = sprintf('%s/%s', $this->path, $item->source);
            $files = $this->finder->in($path)->name($item->file_name)->files();

            foreach ($files as $file) {
                return $file;
            }
        } catch (\Exception $e) {
            throw new ApiException($e->getMessage(), $e->getCode());
        }

        throw new ApiException(_('Item not found'), 404);
    }

    /**
     * {@inheritdoc}
     */
    public function getItem($id)
    {
        $this->checkSynchronizer();

        try {
            if (empty($id)) {
                throw new \InvalidArgumentException();
            }

            $item = $this->repository->find($id);

            $this->dispatcher->dispatch($this->getEventName('getItem'), [
                'id'   => $id,
                'item' => $item
            ]);

            return $item;
        } catch (\Exception $e) {
            throw new GetItemException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getList($oql = '')
    {
        $this->checkSynchronizer();

        try {
            list($criteria, $order, $epp, $page) = $this->getCriteriaFromOql($oql);

            $total = $this->repository->countBy($criteria);
            $items = $this->repository->findBy($criteria, $order, $epp, $page);

            $this->dispatcher->dispatch($this->getEventName('getList'), [
                'items' => $items,
                'oql'   => $oql
            ]);

            return [ 'items' => $items, 'total' => $total ];
        } catch (\Exception $e) {
            throw new GetListException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getListByIds($ids)
    {
        $this->checkSynchronizer();

        if (!is_array($ids)) {
            throw new GetListException(_('Invalid argument'), 400);
        }

        if (empty($ids)) {
            return [ 'items' => [], 'total' => 0 ];
        }

        try {
            $items = [];

            foreach ($ids as $id) {
                try {
                    $items[] = $this->getItem($id);
                } catch (\Exception $e) {
                    continue;
                }
            }

            $this->dispatcher->dispatch($this->getEventName('getListByIds'), [
                'ids'   => $ids,
                'items' => $items
            ]);

            return [ 'items' => $items, 'total' => count($items) ];
        } catch (\Exception $e) {
            throw new GetListException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Imports an item as a content.
     *
     * @param string $id   The resource id.
     * @param array  $data The parameters to use while importing.
     *
     * @return int The imported content id.
     */
    public function importItem(string $id, array $data) : int
    {
        $this->checkSynchronizer();
        $this->checkParameters($data);

        try {
            $resource = $this->repository->find($id);

            if ($this->importer->isImported($resource)) {
                throw new ApiException(_('The item is already imported'), 409);
            }

            $server = $this->container->get('api.service.news_agency.server')
                ->getItem($resource->source);

            $imported = $this->importer
                ->configure($server)
                ->import($resource, $data);

            $this->dispatcher->dispatch($this->getEventName('importItem'), [
                'id'   => $id,
                'item' => $imported
            ]);

            return $imported->pk_content;
        } catch (\Exception $e) {
            throw new ApiException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Imports a list of items as contents.
     *
     * @param array $ids  The list of resource ids.
     * @param array $data The parameters to use while importing.
     *
     * @return int The number of imported resources.
     */
    public function importList(array $ids, array $data) : int
    {
        $this->checkSynchronizer();
        $this->checkParameters($data);

        try {
            $resources = $this->repository->find($ids);
        } catch (\Exception $e) {
            throw new ApiException($e->getMessage(), $e->getCode());
        }

        $imported = [];

        foreach ($resources as $resource) {
            try {
                if ($this->importer->isImported($resource)) {
                    continue;
                }

                $server = $this->container->get('api.service.news_agency.server')
                    ->getItem($resource->source);

                $imported[] = $this->importer
                    ->configure($server)
                    ->setPropagation(false)
                    ->import($resource, $data);
            } catch (\Exception $e) {
                throw new ApiException($e->getMessage(), $e->getCode());
            }
        }

        $this->dispatcher->dispatch($this->getEventName('importItem'), [
            'ids'   => $ids,
            'items' => $imported
        ]);

        return count($imported);
    }

    /**
     * {@inheritdoc}
     */
    public function patchItem($id, $data)
    {
        throw new PatchItemException('Action not implemented', 400);
    }

    /**
     * {@inheritdoc}
     */
    public function patchList($ids, $data)
    {
        throw new PatchListException('Action not implemented', 400);
    }

    /**
     * {@inheritdoc}
     */
    public function responsify($item)
    {
        return $item;
    }

    /**
     * {@inheritdoc}
     */
    public function updateItem($id, $data)
    {
        throw new UpdateItemException('Action not implemented', 400);
    }

    /**
     * Checks the list of parameters for import actions.
     *
     * @param array $params The list of parameters.
     *
     * @throws ApiException When one or more parameters are not valid.
     */
    protected function checkParameters($params)
    {
        if (!array_key_exists('content_type_name', $params)
            || ($params['content_type_name'] === 'article'
                && !array_key_exists('fk_content_category', $params))
            || ($params['content_type_name'] === 'opinion'
                && !array_key_exists('fk_author', $params))
        ) {
            throw new ApiException(_('Invalid argument'), 400);
        }
    }

    /**
     * Checks if there is a synchronization in progress.
     *
     * @throws ApiException If there is a synchronization in progress.
     */
    protected function checkSynchronizer()
    {
        if ($this->synchronizer->isLocked()) {
            throw new ApiException(
                _('The synchronization is already in progress. Try again later.'),
                409
            );
        }
    }

    /**
     * Returns a list of parameters extracted from OQL that can be recognized
     * by the repository.
     *
     * @param string $oql The OQL to extract parameters from.
     *
     * @return array The list of parameters.
     */
    protected function getCriteriaFromOql($oql = '')
    {
        list($criteria, $order, $epp, $page) = $this->container
            ->get('core.helper.oql')
            ->getFiltersFromOql($oql);

        $source = $title = '.*';
        $type   = 'text';

        if (preg_match_all('/title\s*LIKE\s*"([^"]+)"/', $criteria, $matches)) {
            $title = $matches[1][0];
        }

        if (preg_match_all('/source\s*=\s*"([^"]+)"/', $criteria, $matches)) {
            $source = $matches[1][0];
        }

        if (preg_match_all('/type\s*=\s*"([^"]+)"/', $criteria, $matches)) {
            $type = $matches[1][0];
        }

        $criteria = [
            'source'   => $source,
            'title'    => $title,
            'category' => $title,
            'type'     => $type
        ];

        return [ $criteria, $order, $epp, $page ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getEventName($action)
    {
        return 'news_agency.resource.' . $action;
    }
}
