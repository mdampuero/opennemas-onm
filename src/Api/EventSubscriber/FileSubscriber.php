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

class FileSubscriber implements EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'attachment.updateItem' => [ [ 'onFileUpdate', 5 ] ],
            'attachment.deleteItem' => [ [ 'onFileDelete', 5 ] ],
            'attachment.deleteList' => [ [ 'onFileDelete', 5 ] ]
        ];
    }

    /**
     * Initializes the CategorySubscriber.
     *
     * @param TemplateCacheManager $tcm The TemplateCacheManager service.
     */
    public function __construct(VarnishHelper $vh)
    {
        $this->varnish = $vh;
    }

    /**
     * Removes caches for dynamic CSS and category list actions and varnish
     * caches for the instance when a category or a list of categories are
     * deleted.
     *
     * @param Event $event The dispatched event.
     */
    public function onFileDelete(Event $event)
    {
        $this->onFileUpdate($event);
    }

    /**
     * Removes caches for dynamic CSS, category list actions and varnish caches
     * for the instance when a category or a list of categories are updated.
     *
     * @param Event $event The dispatched event.
     */
    public function onFileUpdate(Event $event)
    {
        $files = $event->hasArgument('item')
            ? [ $event->getArgument('item') ]
            : $event->getArgument('items');

        $this->varnish->deleteFiles($files);
    }
}
