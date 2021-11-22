<?php

namespace Api\EventSubscriber;

use Opennemas\Task\Component\Task\ServiceTask;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\Event;

class PollSubscriber implements EventSubscriberInterface
{

    /**
     * Initializes the SettingSubscriber
     *
     * @param Container       $container The service container.
     * @param ContentCacheHelper   $cache     The cache helper.
     * @param CacheManager    $template  The CacheManager services for template.
     */
    public function __construct($container, $cache, $template)
    {
        $this->cache     = $cache;
        $this->container = $container;
        $this->template  = $template;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'poll.createItem' => [
                ['logAction', 5],
                ['removeVarnishCacheCurrentInstance', 5],
            ],
            'poll.updateItem' => [
                ['logAction', 5],
                ['removeSmartyCacheForContent', 5],
                ['removeVarnishCacheCurrentInstance', 5],
            ],
            'poll.deleteItem' => [
                ['logAction', 5],
                ['removeSmartyCacheForContent', 5],
                ['removeVarnishCacheCurrentInstance', 5],
            ],
            'poll.patchItem' => [
                ['logAction', 5],
                ['removeSmartyCacheForContent', 5],
                ['removeVarnishCacheCurrentInstance', 5],
            ],
            'poll.patchList' => [
                ['logAction', 5],
                ['removeSmartyCacheForContent', 5],
                ['removeVarnishCacheCurrentInstance', 5],
            ],
            'poll.deleteList' => [
                ['logAction', 5],
                ['removeSmartyCacheForContent', 5],
                ['removeVarnishCacheCurrentInstance', 5],
            ],
        ];
    }

    /**
     * Deletes the Smarty cache when the updated content is an article.
     *
     * @param Event $event The event to handle.
     */
    public function removeSmartyCacheForContent(Event $event)
    {
        if (!$event->hasArgument('item')) {
            return;
        }

        $item  = $event->getArgument('item');
        $items = is_array($item) ? $item : [ $item ];

        foreach ($items as $content) {
            // Clean cache for the content
            $this->template
                ->delete('content', $content->pk_content)
                ->delete('archive', date('Ymd'))
                ->delete('rss', $content->content_type_name)
                ->delete('frontpage', $content->content_type_name)
                ->delete('category', 'list', $content->category_id)
                ->delete($content->content_type_name, 'frontpage')
                ->delete($content->content_type_name, 'list')
                ->delete('sitemap', 'contents');
        }
    }

    /**
     * Logs the action.
     *
     * @param Event $event The event object.
     */
    public function logAction(Event $event)
    {
        if (empty($event->hasArgument('action'))) {
            return;
        }

        $action = $event->getArgument('action');
        $item   = $event->getArgument('item');
        $items  = is_array($item) ? $item : [ $item ];

        if (!empty($items)) {
            foreach ($items as $content) {
                logContentEvent($action, $content);
            }

            return;
        }
    }

    /**
     * Queues a varnish ban request.
     *
     * @param Event $event The event to handle.
     */
    public function removeVarnishCacheCurrentInstance(Event $event)
    {
        if (!$this->container->hasParameter('varnish')) {
            return false;
        }

        $item  = $event->getArgument('item');
        $items = is_array($item) ? $item : [ $item ];

        $instanceName = $this->container->get('core.instance')->internal_name;

        $this->container->get('task.service.queue')->push(
            new ServiceTask('core.varnish', 'ban', [
                sprintf('obj.http.x-tags ~ instance-%s.*poll,list.*', $instanceName)
            ])
        )->push(
            new ServiceTask('core.varnish', 'ban', [
                sprintf('obj.http.x-tags ~ instance-%s.*frontpage-page.*', $instanceName)
            ])
        );

        foreach ($items as $item) {
            $this->container->get('task.service.queue')->push(
                new ServiceTask(
                    'core.varnish',
                    'ban',
                    [
                        sprintf('obj.http.x-tags ~ instance-%s.*poll-%s.*', $instanceName, $item->pk_content)
                    ]
                )
            );
        }
    }
}
