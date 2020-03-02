<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Api\EventSubscriber;

use Common\Cache\Core\CacheManager;
use Common\Core\Component\Helper\TemplateCacheHelper;
use Common\Core\Component\Helper\VarnishHelper;
use Common\Orm\Entity\Instance;
use Onm\Cache\AbstractCache;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CategorySubscriber implements EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'category.createItem' => [ [ 'onCategoryCreate', 5 ], ],
            'category.deleteItem' => [ [ 'onCategoryDelete', 5 ], ],
            'category.deleteList' => [ [ 'onCategoryDelete', 5 ], ],
            'category.moveItem'   => [ [ 'onCategoryMove',   5 ], ],
            'category.moveItems'  => [ [ 'onCategoryMove',   5 ], ],
            'category.patchItem'  => [ [ 'onCategoryUpdate', 5 ], ],
            'category.patchList'  => [ [ 'onCategoryUpdate', 5 ], ],
            'category.updateItem' => [ [ 'onCategoryUpdate', 5 ], ]
        ];
    }

    /**
     * Initializes the CategorySubscriber.
     *
     * @param Instance            $instance The current instance.
     * @param TemplateCacheHelper $th       The TemplateCacheHelper service.
     * @param VarnishHelper       $vh       The VarnishHelper service.
     * @param AbstractCache       $cache    The old cache connection.
     * @param CacheManager        $cm       The CacheManager service.
     * @param ServiceContainer    $container The service container.
     */
    public function __construct(
        ?Instance           $instance,
        TemplateCacheHelper $th,
        VarnishHelper       $vh,
        AbstractCache       $cache,
        CacheManager        $cm,
        $container
    ) {
        $this->instance  = $instance;
        $this->template  = $th;
        $this->varnish   = $vh;
        $this->oldCache  = $cache;
        $this->cache     = $cm->getConnection('instance');
        $this->container = $container;
    }

    /**
     * Removes cache for dynamic CSS when a category is created.
     */
    public function onCategoryCreate()
    {
        $this->template->deleteDynamicCss();
    }

    /**
     * Removes caches for dynamic CSS and category list actions and varnish
     * caches for the instance when a category or a list of categories are
     * deleted.
     *
     * @param Event $event The dispatched event.
     */
    public function onCategoryDelete(Event $event)
    {
        $this->onCategoryUpdate($event);
    }

    /**
     * Removes contents from cache, category list actions and varnish caches for
     * the instance after moving contents from a category to another.
     *
     * @param Event $event The dispatched event.
     */
    public function onCategoryMove(Event $event)
    {
        if (!$event->hasArgument('contents')) {
            return;
        }

        $contents = $event->getArgument('contents');
        $cacheIds = [];

        foreach ($contents as $content) {
            $cacheIds[] = 'content-' . $content['id'];
            $cacheIds[] = $content['type'] . '-' . $content['id'];
        }

        if (!empty($cacheIds)) {
            $this->oldCache->delete($cacheIds);
            $this->cache->remove($cacheIds);
        }

        $source = $event->hasArgument('item')
            ? [ $event->getArgument('item') ]
            : $event->getArgument('items');

        $categories = array_merge($source, [ $event->getArgument('target') ]);

        $this->template->deleteCategories($categories);
        $this->varnish->deleteInstance($this->instance);
    }

    /**
     * Removes caches for dynamic CSS, category list actions and varnish caches
     * for the instance when a category or a list of categories are updated.
     *
     * @param Event $event The dispatched event.
     */
    public function onCategoryUpdate(Event $event)
    {
        $categories = $event->hasArgument('item')
            ? [ $event->getArgument('item') ]
            : $event->getArgument('items');

        $this->container->get('core.service.assetic.dynamic_css')->deleteTimestamp('%global%');
        foreach ($categories as $category) {
            $this->container->get('core.service.assetic.dynamic_css')->deleteTimestamp($category->name);
        }

        $this->template->deleteDynamicCss();
        $this->template->deleteCategories($categories);
        $this->varnish->deleteInstance($this->instance);
    }
}
