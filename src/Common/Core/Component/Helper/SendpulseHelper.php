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
class SendpulseHelper
{
    protected $service = 'external.web_push.factory.sendpulse';

    protected $endpointData = [
        'code_snippet' => [ 'id' => 'getWebsiteId'],
        'notification' => [ 'website_id' => 'getWebsiteId'],
    ];

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

    public function getWebsiteId()
    {
        $websiteId = $this->ds->get('sendpulse_website_id');

        if (empty($websiteId)) {
            $sendPulse       = $this->container->get($this->service);
            $websiteEndpoint = $sendPulse->getEndpoint('website');
            $websiteList     = $websiteEndpoint->getList();

            $mainDomain = $this->instance->getMainDomain();
            $mainDomain = 'verdadesymentiras.com';

            foreach ($websiteList as $website) {
                if (strpos($mainDomain, $website['url']) !== false) {
                    $websiteId = $website['id'];
                    break;
                }
            }
        }

        $this->ds->set('sendpulse_website_id', $websiteId);

        return $websiteId;
    }

    public function getJsFileResponse()
    {
        $webpushSettings = $this->ds->get(['webpush_service', 'webpush_apikey']);

        if ($this->container->get('core.security')->hasExtension('es.openhost.module.webpush_notifications')
            && $webpushSettings['webpush_service'] == 'sendpulse'
            && !empty($this->getWebsiteId())
            && !$this->container->get('core.instance')->hasMultilanguage()) {
            $response = new BinaryFileResponse('assets/js/sp-push-worker-fb.js');
            $response->headers->set('X-Status-Code', 200);
            $response->headers->set('Content-Type', 'application/javascript');
            $response->headers->set('Cache-Control', 'public');
            $response->headers->set('max-age', 2628000);
            $response->headers->set('s-maxage', 2628000);
            return $response;
        }
    }
}
