<?php

namespace Framework\EventListener;

use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\Event;

class OJDAnalyticsListener
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
        $uri     = $event->getRequest()->getUri();
        $referer = $event->getRequest()->headers->get('referer');

        if ($this->container->getParameter('backend_analytics.enabled')
            && preg_match('/\/admin/', $uri)
        ) {
            return;
        }

        if (!preg_match('/\/admin\/frontpages/', $referer)
            && !preg_match('/\/manager/', $uri)
            && !preg_match('/\/managerws/', $uri)
            && !preg_match('/\/share-by-email/', $uri)
            && !preg_match('/\/sharrre/', $uri)
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

        $code = '<!-- START Nielsen//NetRatings SiteCensus V5.3 -->'
            . '<!-- COPYRIGHT 2007 Nielsen//NetRatings -->'
            . '<script type="text/javascript">'
            . 'var _rsCI="'. $config['page_id'] .'";'
            . 'var _rsCG="0";'
            . 'var _rsDN="//secure-uk.imrworldwide.com/";'
            . 'var _rsCC=0;'
            . '</script>'
            . '<script type="text/javascript" src="//secure-uk.imrworldwide.com/v53.js"></script>'
            . '<noscript>'
            . '<div><img src="//secure-uk.imrworldwide.com/cgi-bin/m?ci='
            . $config['page_id'] .'&amp;cg=0" alt=""/></div>'
            . '</noscript>'
            . '<!-- END Nielsen//NetRatings SiteCensus V5.3 -->';

        $content = $event->getResponse()->getContent();

        $content = str_replace('</body>', $code . '</body>', $content);

        $event->getResponse()->setContent($content);
    }
}
