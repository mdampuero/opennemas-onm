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

use Common\Core\Component\Helper\TemplateCacheHelper;
use Common\Core\Component\Helper\VarnishHelper;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class NewsstandSubscriber implements EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'content.deleteItem' => [ [ 'onNewsstandDelete', 5 ] ],
            'content.patchItem'  => [ [ 'onNewsstandPatch',  5 ] ],
            'content.updateItem' => [ [ 'onNewsstandUpdate', 5 ] ]
        ];
    }

    /**
     * Initializes the NewsstandSubscriber.
     *
     * @param TemplateCacheHelper $th The TemplateCacheHelper service.
     * @param VarnishHelper       $vh The VarnishHelper service.
     */
    public function __construct(TemplateCacheHelper $th, VarnishHelper $vh)
    {
        $this->th = $th;
        $this->vh = $vh;
    }

    /**
     * Removes  smarty and varnish caches when a newsstand or a list of
     * newsstands are deleted.
     *
     * @param Event $event The dispatched event.
     */
    public function onNewsstandDelete(Event $event)
    {
        $this->onNewsstandUpdate($event);
    }

    /**
     * Removes  smarty and varnish caches when a newsstand or a list of
     * newsstands are patched.
     *
     * @param Event $event The dispatched event.
     */
    public function onNewsstandPatch(Event $event)
    {
        $this->onNewsstandUpdate($event);
    }

    /**
     * Removes  smarty and varnish caches when a newsstand or a list of
     * newsstands are updated.
     *
     * @param Event $event The dispatched event.
     */
    public function onNewsstandUpdate(Event $event)
    {
        $items = $event->hasArgument('item')
            ? [ $event->getArgument('item') ]
            : $event->getArgument('items');

        $items = array_filter($items, function ($a) {
            return $a->content_type_name = 'kiosko';
        });

        $this->th->deleteNewsstands($items);
        $this->vh->deleteFiles($items);
    }
}
