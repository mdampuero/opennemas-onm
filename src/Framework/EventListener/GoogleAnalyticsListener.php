<?php

namespace Framework\EventListener;

use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\Event;

class GoogleAnalyticsListener
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
            $this->addBackendcode($event);
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
     * Adds Google Analytics code to backend pages.
     *
     * @param FilterResponseEvent $event The event object.
     */
    private function addBackendCode(FilterResponseEvent &$event)
    {
        $code = '<script>'
            . '(function(i,s,o,g,r,a,m){i[\'GoogleAnalyticsObject\']=r;i[r]=i[r]||function(){'
            . '(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),'
            . 'm=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)'
            . '})(window,document,\'script\',\'//www.google-analytics.com/analytics.js\',\'ga\');'
            . 'ga(\'create\', \'UA-40838799-4\', \'auto\');'
            . 'ga(\'send\', \'pageview\');'
            . '</script>';

        $content = $event->getResponse()->getContent();

        $content = str_replace('</body>', $code . '</body>', $content);

        $event->getResponse()->setContent($content);
    }

    /**
     * Adds google analytics code to frontend pages.
     *
     * @param FilterResponseEvent $event The event object.
     */
    private function addFrontendCode(FilterResponseEvent &$event)
    {
        $content = $event->getResponse()->getContent();

        $config = $this->container->get('setting_repository')
            ->get('google_analytics');

        if (!is_array($config)
            || !array_key_exists('api_key', $config)
            || empty(trim($config['api_key']))
        ) {
            return;
        }

        $apiKey = trim($config['api_key']);

        $code = "<script type=\"text/javascript\">
            var _gaq = _gaq || [];
            _gaq.push(['_setAccount', '" . $apiKey . "']);";

        // If base domain for ganalytics is set append it to the final output.
        if (array_key_exists('base_domain', $config)
            && !empty($config['base_domain'])
        ) {
            $code .= " _gaq.push(['_setDomainName', '". $config['base_domain'] ."']); ";
        }

        $code .= "_gaq.push(['_trackPageview']);"
            ."(function() {\n"
            ."var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;\n"
            ."ga.src = ('https:' == document.location.protocol ? 'https://' : 'http://')"
            ." + 'stats.g.doubleclick.net/dc.js';\n"
            ."var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);\n"
            ."})();\n"
            ."</script>\n";

        $content = $event->getResponse()->getContent();

        $content = str_replace('</body>', $code . '</body>', $content);

        $event->getResponse()->setContent($content);
    }
}
