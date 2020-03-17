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

use Common\Core\Component\Helper\VarnishHelper;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AttachmentSubscriber implements EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'attachment.deleteItem' => [ [ 'onAttachmentDelete', 5 ] ],
            'attachment.patchItem'  => [ [ 'onAttachmentUpdate', 5 ] ],
            'attachment.updateItem' => [ [ 'onAttachmentUpdate', 5 ] ]
        ];
    }

    /**
     * Initializes the AttachmentSubscriber.
     *
     * @param VarnishHelper $vh The VarnishHelper service.
     */
    public function __construct(VarnishHelper $vh)
    {
        $this->vh = $vh;
    }

    /**
     * Removes attachments from varnish when an attachment is deleted.
     *
     * @param Event $event The dispatched event.
     */
    public function onAttachmentDelete(Event $event)
    {
        $this->onAttachmentUpdate($event);
    }

    /**
     * Removes attachments from varnish when an attachment or a list of
     * attachments are updated.
     *
     * @param Event $event The dispatched event.
     */
    public function onAttachmentUpdate(Event $event)
    {
        $contents = $event->hasArgument('item')
            ? [ $event->getArgument('item') ]
            : $event->getArgument('items');

        $files = array_filter($contents, function ($a) {
            return $a->content_type_name === 'attachment';
        });

        $this->vh->deleteContents($files);
    }
}
