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

use Api\Exception\GetItemException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * The Webpushr helper provides methods to work with webpushr API.
 */
class WebpushrHelper
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
    protected $service = 'external.web_push.factory.webpushr';

    /**
     * Avaliable image types.
     *
     * @var Array
     */
    protected $endpointData = [];

    /**
     * Initializes the UrlGeneratorHelper.
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
            if (function_exists($method)) {
                $data[$param] = $this->$method();
            }
        }

        return $data;
    }

    /**
     * Get SendPulse required JS file
     *
     * @return BinaryFileResponse The file respinse.
     */
    public function getWebpushCollectionFile()
    {
        if (!$this->container->get('core.security')->hasExtension('es.openhost.module.webpush_notifications')
            || empty($this->ds->get('webpush_apikey'))
            || $this->ds->get('webpush_service') !== 'webpushr'
            || $this->container->get('core.instance')->hasMultilanguage()
            || !$this->container->get('core.security')->hasExtension('es.openhost.module.frontendSsl')) {
            throw new \Exception();
        }

        $response = new BinaryFileResponse('assets/js/webpushr.js');
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
            || empty($this->ds->get('webpush_apikey'))
            || empty($this->ds->get('webpush_publickey'))
            || $this->ds->get('webpush_service') !== 'webpushr'
            || $this->container->get('core.instance')->hasMultilanguage()
            || !$this->container->get('core.security')->hasExtension('es.openhost.module.frontendSsl')) {
            throw new \Exception();
        }

        $script = "<script id='webpushr-script'>(function(w,d, s, id) {if(typeof(w.webpushr)!=='undefined') "
            . "return;w.webpushr=w.webpushr||function(){(w.webpushr.q=w.webpushr.q||[]).push(arguments)};var js, "
            . "fjs = d.getElementsByTagName(s)[0];js = d.createElement(s); js.id = id;js.async=1;js.src = "
            . "\"https://cdn.webpushr.com/app.min.js\";"
            . "fjs.parentNode.appendChild(js);}(window,document, 'script', 'webpushr-jssdk'));"
            . "webpushr('setup',{'key':'" . $this->ds->get('webpush_publickey') . "' });"
            . "</script>";

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
        try {
            if (is_string($article) || is_int($article)) {
                $article = $this->container->get('api.service.article')->getItem($article);
            }
        } catch (GetItemException $e) {
            $article = '';
        }

        if (empty($article)) {
            return [];
        }

        $contentService = $this->container->get('api.service.content');
        $photoHelper    = $this->container->get('core.helper.photo');

        $favico = $photoHelper->getPhotoPath(
            $contentService->getItem($this->ds->get('logo_favico')),
            null,
            [ 192, 192 ],
            true
        );

        $contentPath = $this->container->get('core.helper.url_generator')->getUrl($article, ['_absolute' => true]);
        $image       = $this->container->get('core.helper.featured_media')->getFeaturedMedia($article, 'inner');
        $imagePath   = $photoHelper->getPhotoPath($image, null, [], true);

        $body = !empty($article->description)
            ? strip_tags($article->description)
            : mb_substr(strip_tags($article->body), 0, 157) . '...';

        $data = [
            'title'      => $article->title ?? '',
            'message'    => $body,
            'target_url' => $contentPath,
            'image'      => $imagePath,
            'icon'       => strpos($favico, '.png') ? $favico : '',
        ];

        //Fix to avoid images with no extension
        if (array_key_exists('image', $data)) {
            $parts = explode('.', $data['image']);
            if (empty(end($parts))) {
                unset($data['image']);
            }
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
            'send_count'  => $data['count']['successfully_sent'],
            'impressions' => $data['count']['delivered'],
            'clicks'      => $data['count']['clicked'],
            'closed'      => $data['count']['closed']
        ];
    }

    /**
     * Check if transaction ID is valid for current service
     *
     * @param Mixed $transactionId The transaction ID.
     *
     * @return Bool true if ti is not empty, false otherwise.
     */
    public function isValidId($transactionId)
    {
        return !empty($transactionId);
    }
}
