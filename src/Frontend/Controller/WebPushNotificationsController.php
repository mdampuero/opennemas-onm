<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Frontend\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

/**
 * Defines the frontend controller for the articles.
 */
class WebPushNotificationsController extends FrontendController
{
    /**
     * {@inheritdoc}
     */
    public function scriptAction()
    {
        $webpushSettings = $this->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get(['webpush_service', 'webpush_apikey', 'webpush_token', 'webpush_publickey']);

        if ($this->get('core.security')->hasExtension('es.openhost.module.webpush_notifications')
            && $webpushSettings['webpush_service'] == 'webpushr'
            && $webpushSettings['webpush_apikey']
            && !$this->get('core.instance')->hasMultilanguage()
            && $this->get('core.security')->hasExtension('es.openhost.module.frontendSsl')) {
                $response = new Response("importScripts('https://cdn.webpushr.com/sw-server.min.js');", 200);
                $response->headers->set('Content-Type', 'application/javascript');
                $response->headers->set('Cache-Control', 'no-cache');
                $response->headers->set('Content-Length', 59);
                return $response;
        }

        throw new ResourceNotFoundException();
    }
}
