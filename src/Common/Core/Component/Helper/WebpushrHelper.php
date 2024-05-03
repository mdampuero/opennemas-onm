<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Core\Component\Helper;

use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * The UrlHelper service provides methods to parse and generate URLs.
 */
class WebpushrHelper
{
    protected $service = 'external.web_push.factory.webpushr';

    protected $endpointData = [];

    /**
     * Initializes the UrlGeneratorHelper.
     *
     * @param ServiceContainer $container The service container.
     */
    public function __construct($container)
    {
        $this->container = $container;
        $this->instance  = $this->container->get('core.instance');
        $this->cache     = $this->container->get('cache.connection.instance');
        $this->ds        = $this->container->get('orm.manager')->getDataSet('Settings', 'instance');
    }

    public function prepareDataForEndpoint($endpoint = null)
    {
        if (!array_key_exists($endpoint, $this->endpointData)) {
            return [];
        }

        $data = [];
        foreach ($this->endpointData[$endpoint] as $param => $method) {
            if (function_exists($method)) {
                $data[$param] = $this->$method();
            }
        }

        return $data;
    }

    public function getJsFileResponse()
    {
        $webpushSettings = $this->ds->get(['webpush_service', 'webpush_apikey']);

        if ($this->container->get('core.security')->hasExtension('es.openhost.module.webpush_notifications')
            && $webpushSettings['webpush_service'] == 'webpushr'
            && $webpushSettings['webpush_apikey']
            && !$this->container->get('core.instance')->hasMultilanguage()
            && $this->container->get('core.security')->hasExtension('es.openhost.module.frontendSsl')) {
            $response = new BinaryFileResponse('assets/js/webpush.js');
            $response->headers->set('X-Status-Code', 200);
            $response->headers->set('Content-Type', 'application/javascript');
            $response->headers->set('Cache-Control', 'public');
            $response->headers->set('max-age', 2628000);
            $response->headers->set('s-maxage', 2628000);
            return $response;
        }
    }
}
