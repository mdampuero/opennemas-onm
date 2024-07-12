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
 * The Sendpulse helper provides methods to work with senpulse API.
 */
class SendpulseHelper
{
    /**
     * The service container.
     *
     * @var ServiceContainer
     */
    protected $container;

    /**
     * The Settings DataSet.
     *
     * @var DataSet
     */
    protected $ds;

    /**
     * The service name.
     *
     * @var String
     */
    protected $service = 'external.web_push.factory.sendpulse';

    /**
     * Avaliable image types.
     *
     * @var Array
     */
    protected $avaliableImageType = ['jpg', 'png', 'gif'];

    /**
     * Previous data map required for endpointns.
     *
     * @var Array
     */
    protected $endpointData = [
        'subscriber'   => [ 'id' => 'getWebsiteId'],
    ];

    /**
     * Initializes the SendPulseHelper.
     *
     * @param ServiceContainer $container The service container.
     */
    public function __construct($container)
    {
        $this->container = $container;
        $this->ds        = $this->container->get('orm.manager')->getDataSet('Settings', 'instance');
    }

    /**
     * Get prevoius requiered data for an endpoint.
     *
     * @param String $endpoint The endpoint name.
     *
     * @return Array The endpoint data.
     */
    public function prepareDataForEndpoint($endpoint = null)
    {
        if (!array_key_exists($endpoint, $this->endpointData)) {
            return [];
        }

        $data = [];
        foreach ($this->endpointData[$endpoint] as $param => $method) {
            if (method_exists($this, $method)) {
                $data[$param] = $this->$method();
            }
        }

        return $data;
    }

    /**
     * Get current Website ID from sendpulse API.
     *
     * @return Mixed The website ID.
     */
    public function getWebsiteId()
    {
        $websiteId = $this->ds->get('sendpulse_website_id');

        if (empty($websiteId)) {
            $sendPulse       = $this->container->get($this->service);
            $websiteEndpoint = $sendPulse->getEndpoint('website');
            $websiteList     = $websiteEndpoint->getList();
            $mainDomain      = $this->container->get('core.instance')->getMainDomain();

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

    /**
     * Get SendPulse required JS file
     *
     * @return BinaryFileResponse The file respinse.
     */
    public function getWebpushCollectionFile()
    {
        if (!$this->container->get('core.security')->hasExtension('es.openhost.module.webpush_notifications')
            || $this->ds->get('webpush_service') !== 'sendpulse'
        ) {
            throw new \Exception('Module not activated');
        }

        $response = new BinaryFileResponse('assets/js/sendpulse.js');
        $response->headers->set('X-Status-Code', 200);
        $response->headers->set('Content-Type', 'application/javascript');
        $response->headers->set('Cache-Control', 'public');
        $response->headers->set('max-age', 2628000);
        $response->headers->set('s-maxage', 2628000);
        return $response;
    }

    /**
     * Get SendPulse required script
     *
     * @return String The service script.
     */
    public function getWebpushCollectionScript()
    {
        if (!$this->container->get('core.security')->hasExtension('es.openhost.module.webpush_notifications')
            || $this->ds->get('webpush_service') !== 'sendpulse'
        ) {
            throw new \Exception('Module not activated');
        }

        $script = $this->ds->get('webpush_script');

        if (empty($script)) {
            try {
                $webpushService = $this->container->get($this->service);
                $endpoint       = $webpushService->getEndpoint('code_snippet');
                $response       = $endpoint->getCode([ 'id' => $this->getWebsiteId() ]);
                $script         = array_key_exists('script_code', $response) ? $response['script_code'] : '';
                $this->ds->set('webpush_script', $script);
            } catch (\Exception $e) {
                $script = '';
            }
        }

        return $script;
    }

    /**
     * Remove account data
     */
    public function removeAccountData()
    {
        $this->ds->set('webpush_script', '');
        $this->ds->set('webpush_apikey', '');
        $this->ds->set('webpush_token', '');
        $this->ds->set('webpush_publickey', '');
        $this->ds->set('sendpulse_website_id', '');
        $cache = $this->container->get('cache.connection.instance');
        $cache->remove('sendpulse-access-token');
        $cache->remove('sendpulse-refresh-token');
        $cache->remove('webpush_script');
        $cache->remove('webpush_apikey');
        $cache->remove('webpush_token');
        $cache->remove('webpush_publickey');
        $cache->remove('sendpulse_website_id');
    }

    /**
     * Get requiered data in order to send a push notification
     *
     * @param Mixed $article The article object.
     *
     * @return Array The notification data.
     */
    public function getNotificationData($article)
    {
        if (is_string($article) || is_int($article)) {
            $article = $this->container->get('api.service.article')->getItem($article);
        }

        if (empty($article)) {
            return [];
        }

        $imageHelper = $this->container->get('core.helper.image');
        $contentPath = $this->container->get('core.helper.url_generator')->getUrl($article, ['_absolute' => true]);
        $image       = $this->container->get('core.helper.featured_media')->getFeaturedMedia($article, 'inner');
        $favico      = $this->container->get('api.service.content')->getItem($this->ds->get('logo_favico'));

        $body = !empty($article->description)
            ? strip_tags($article->description)
            : mb_substr(strip_tags($article->body), 0, 157) . '...';

        $data = [
            'title'      => $article->title ?? '',
            'body'       => $body,
            'website_id' => '74433',
            'ttl'        => 86400, //Max ttl allowed
            'link'       => $contentPath,
        ];

        $imageContent = '';

        $basePath = $this->container->getParameter('core.paths.public')
            . $this->container->get('core.instance')->getMediaShortPath() . DS;

        $imagePath  = $basePath . $image->path;
        $favicoPath = $basePath . $favico->path;

        if ($imageHelper->exists($imagePath) && $image->size <= 200) {
            // Get image content and base64 encode
            $imageContent  = file_get_contents($imagePath);
            $data['image'] = [
                'name' => basename($imagePath),
                'data' => base64_encode($imageContent)
            ];
        }

        if ($imageHelper->exists($favicoPath)) {
            // Get favico content and base64 encode
            $favicoContent = file_get_contents($favicoPath);
            $data['icon']  = [
                'name' => basename($favicoPath),
                'data' => base64_encode($favicoContent)
            ];
        }

        return $data;
    }

    /**
     * Parse notification information in order to match with all services
     *
     * @param Mixed $data The article object.
     *
     * @return Array the parsed Data.
     */
    public function parseNotificationData($data)
    {
        return [
            'send_count'  => $data['send'],
            'impressions' => $data['delivered'],
            'clicks'      => $data['redirect'],
            'closed'      => $data['unsubscribed']
        ];
    }

    /**
     * Check if transaction ID is valid for current service
     *
     * @param Mixed $transactionId The transaction ID.
     *
     * @return Bool true if ti is not empty and is numeric, false otherwise.
     */
    public function isValidId($transactionId)
    {
        return !empty($transactionId) && is_numeric($transactionId);
    }
}
