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
                ['removeVarnishCacheCurrentInstance', 5],
            ],
            'poll.updateItem' => [
                ['removeSmartyCacheForContent', 5],
                ['removeObjectCacheForContent', 10],
                ['removeVarnishCacheCurrentInstance', 5],
            ],
            'poll.deleteItem' => [
                ['removeSmartyCacheForContent', 5],
                ['removeObjectCacheForContent', 10],
                ['removeVarnishCacheCurrentInstance', 5],
            ],
            'poll.patchItem'     => [
                ['removeSmartyCacheForContent', 5],
                ['removeObjectCacheForContent', 10],
                ['removeVarnishCacheCurrentInstance', 5],
            ]
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

        $content = $event->getArgument('item');

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

    /**
     * Deletes a content from cache after it is updated.
     *
     * @param Event $event The event to handle.
     *
     * @return null
     */
    public function removeObjectCacheForContent(Event $event)
    {
        $content = $event->getArgument('item');

        if (!empty($content->content_type_name)) {
            $contentType = $content->content_type_name;
        } else {
            $contentType = \underscore(get_class($content));
        }

        $this->cache->deleteItem($content);
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

        $id = $event->getArgument('id');

        $instanceName = $this->container->get('core.instance')->internal_name;

        $this->container->get('task.service.queue')->push(
            new ServiceTask('core.varnish', 'ban', [
                sprintf('obj.http.x-tags ~ instance-%s.*poll,list.*', $instanceName)
            ])
        )->push(
            new ServiceTask('core.varnish', 'ban', [
                sprintf('obj.http.x-tags ~ instance-%s.*poll-%s.*', $instanceName, $id)
            ])
        )->push(
            new ServiceTask('core.varnish', 'ban', [
                sprintf('obj.http.x-tags ~ instance-%s.*frontpage-page.*', $instanceName)
            ])
        );
    }
}
