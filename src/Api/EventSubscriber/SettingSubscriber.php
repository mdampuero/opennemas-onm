<?php

namespace Api\EventSubscriber;

use Api\Helper\Cache\CacheHelper;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SettingSubscriber implements EventSubscriberInterface
{
    /**
     * The helper service.
     *
     * @var CacheHelper
     */
    protected $helper;

    /**
     * Initializes the SettingSubscriber
     *
     * @param CacheHelper $helper The helper to remove caches.
     */
    public function __construct(CacheHelper $helper)
    {
        $this->helper = $helper;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'setting.update' => [ [ 'onSettingUpdate' ] ]
        ];
    }

    /**
     * Removes smarty, varnish and dynamic css caches when settings are updated.
     */
    public function onSettingUpdate()
    {
        $this->helper->deleteInstance()->deleteDynamicCss();
    }
}
