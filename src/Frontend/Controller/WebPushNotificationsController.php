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
    protected function scriptAction($request)
    {
        if ($smarty->getContainer()->get('core.security')->hasExtension('es.openhost.module.webpush_notifications')
            && $webpushService == 'webpushr'
            && $webpushKeys
            && !$smarty->getContainer()->get('core.instance')->hasMultilanguage()
            && $smarty->getContainer()->get('core.security')->hasExtension('es.openhost.module.frontendSsl')) {
            return new Response(
                "importScripts('https://cdn.webpushr.com/sw-server.min.js');",
                200,
                ['Content-Type:' => 'text/javascript']
            );
        }

        throw new ResourceNotFoundException();
    }
}
