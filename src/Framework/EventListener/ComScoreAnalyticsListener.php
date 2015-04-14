<?php

namespace Framework\EventListener;

use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\Event;

class ComScoreAnalyticsListener
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
        if (!$this->container->getParameter('analytics.enabled')) {
            return;
        }

        $uri     = $event->getRequest()->getUri();
        $referer = $event->getRequest()->headers->get('referer');

        if (preg_match('/\/admin/', $uri)) {
            return;
        }

        if (!preg_match('/\/admin\/frontpages/', $referer)
            && !preg_match('/\/manager/', $uri)
            && !preg_match('/\/managerws/', $uri)
        ) {
            $this->addFrontendCode($event);
            return;
        }
    }

    /**
     * Adds google analytics code to frontend pages.
     *
     * @param FilterResponseEvent $event The event object.
     */
    private function addFrontendCode(FilterResponseEvent &$event)
    {
        $content = $event->getResponse()->getContent();

        $config = $this->container->get('setting_repository')->get('ojd');

        if (!is_array($config)
            || !array_key_exists('page_id', $config)
            || empty(trim($config['page_id']))
        ) {
            return;
        }

        $code = '<!-- BegincomScore  Tag -->'
            . '<script>'
            . 'var _comscore = _comscore || [];'
            . '_comscore.push({ c1: "2", c2: "'. $config['page_id'] .'" });'
            . '(function() {'
            . 'var s = document.createElement("script"), el = document.getElementsByTagName("script")[0]; s.async = true;'
            . 's.src = (document.location.protocol == "https:" ? "https://sb" :"http://b") + ".scorecardresearch.com/beacon.js";'
            . 'el.parentNode.insertBefore(s, el);'
            . '})();'
            . '</script>'
            . '<noscript>'
            . '<img src="http://b.scorecardresearch.com/p?c1=2&c2='. $config['page_id'] .'&cv=2.0&cj=1" />'
            . '</noscript>'
            . '<!-- EndcomScore  Tag -->';

        $content = $event->getResponse()->getContent();

        $content = str_replace('</body>', $code . '</body>', $content);

        $event->getResponse()->setContent($content);
    }
}
