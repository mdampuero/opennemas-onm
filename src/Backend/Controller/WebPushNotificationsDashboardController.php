<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Backend\Controller;

use Api\Exception\GetListException;
use Common\Core\Annotation\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

/**
 * Handles the actions for managing notifications
 */
class WebPushNotificationsDashboardController extends BackendController
{
    /**
     * The extension name required by this controller.
     *
     * @var string
     */
    protected $extension = 'es.openhost.module.webpush_notifications';

    /**
     * The list of permissions for every action.
     *
     * @var type
     */
    protected $permissions = [
        'list'   => 'WEBPUSH_ADMIN',
    ];

    /**
     * The resource name.
     */
    protected $resource = 'webpush_notifications';

    /**
     * Configures the Web Push notifications module
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("hasExtension('es.openhost.module.webpush_notifications')
     *     and hasPermission('WEBPUSH_ADMIN')")
     */
    public function configAction()
    {
        $webPushNotificationsConfig = $this->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get(['webpush_service',
                'webpush_apikey',
                'webpush_token',
                'webpush_publickey',
                'webpush_automatic',
                'webpush_delay',
                'webpush_restricted_hours']);

        $webPushNotificationsService = [
            'service' => $webPushNotificationsConfig['webpush_service'],
            'apikey'  => $webPushNotificationsConfig['webpush_apikey'],
            'token'   => $webPushNotificationsConfig['webpush_token']
        ];

        return $this->render('webpush_notifications/dashboard.tpl', [
            'webpush_service'          => $webPushNotificationsService,
            'webpush_automatic'        => $webPushNotificationsConfig['webpush_automatic'],
            'webpush_delay'            => $webPushNotificationsConfig['webpush_delay'],
            'webpush_restricted_hours' => $webPushNotificationsConfig['webpush_restricted_hours']
        ]);
    }
}
