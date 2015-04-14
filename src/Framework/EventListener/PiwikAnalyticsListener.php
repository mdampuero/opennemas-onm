<?php

namespace Framework\EventListener;

use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\Event;

class PiwikAnalyticsListener
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
            $this->addBackendcode($event);
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
     * Adds Google Analytics code to backend pages.
     *
     * @param FilterResponseEvent $event The event object.
     */
    private function addBackendCode(FilterResponseEvent &$event)
    {
        $code = '<!-- Piwik -->'
            . '<script type="text/javascript">'
            . 'var _paq = _paq || [];'
            . '_paq.push(["setDocumentTitle", document.domain + "/" + document.title]);'
            . '_paq.push(["setCookieDomain", "*.opennemas.com"]);'
            . '_paq.push([\'trackPageView\']);'
            . '_paq.push([\'enableLinkTracking\']);'
            . '(function() {'
            . 'var u="//piwik.openhost.es/";'
            . '_paq.push([\'setTrackerUrl\', u+\'piwik.php\']);'
            . '_paq.push([\'setSiteId\', 139]);'
            . 'var d=document, g=d.createElement(\'script\'), s=d.getElementsByTagName(\'script\')[0];'
            . 'g.type=\'text/javascript\'; g.async=true; g.defer=true; g.src=u+\'piwik.js\'; s.parentNode.insertBefore(g,s);'
            . '})();'
            . '</script>'
            . '<noscript><p><img src="//piwik.openhost.es/piwik.php?idsite=139" style="border:0;" alt="" /></p></noscript>'
            . '<!-- End Piwik Code -->';

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
            ->get('piwik');

        if (!is_array($config)
            || !array_key_exists('page_id', $config)
            || !array_key_exists('server_url', $config)
            || empty(trim($config['page_id']))
        ) {
            return;
        }

        $httpsHost = preg_replace("/http:/", "https:", $config['server_url']);

        $code = '<!-- Piwik -->
            <script type="text/javascript">
            var _paq = _paq || [];
            _paq.push([\'trackPageView\']);
            _paq.push([\'enableLinkTracking\']);
            (function() {
                var u = (("https:" == document.location.protocol) ? "'.
                $httpsHost . '" : "' . $config['server_url'] .'");
                _paq.push([\'setTrackerUrl\', u+\'piwik.php\']);
                _paq.push([\'setSiteId\', ' . $config['page_id'].']);
                var d=document, g=d.createElement(\'script\'), s=d.getElementsByTagName(\'script\')[0];
                g.type=\'text/javascript\';
                g.async=true; g.defer=true;
                g.src=u+\'piwik.js\'; s.parentNode.insertBefore(g,s);
            })();
            </script>
            <noscript>
                <p><img src="'. $config['server_url'] .'piwik.php?idsite='.
                $config['page_id'] .'" style="border:0" alt="" /></p>
            </noscript>
            <!-- End Piwik Tracking Code -->';

        $content = $event->getResponse()->getContent();

        $content = str_replace('</body>', $code . '</body>', $content);

        $event->getResponse()->setContent($content);
    }
}
