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

use Common\Core\Annotation\Security;

class SubscriberController extends BackendController
{
    /**
     * The extension name required by this controller.
     *
     * @var string
     */
    protected $extension = 'CONTENT_SUBSCRIPTIONS';

    /**
     * The list of permissions for every action.
     *
     * @var type
     */
    protected $permissions = [
        'create' => 'SUBSCRIBER_CREATE',
        'list'   => 'SUBSCRIBER_ADMIN',
        'show'   => 'SUBSCRIBER_UPDATE'
    ];

    /**
     * The resource name.
     *
     * @var string
     */
    protected $resource = 'subscriber';

    /**
     * Displays the list of settings for subscribers.
     *
     * @return Response The response object.
     *
     * @Security("hasExtension('CONTENT_SUBSCRIPTIONS')
     *     and hasPermission('SUBSCRIBER_SETTINGS')")
     */
    public function settingsAction()
    {
        return $this->render('subscriber/settings.tpl');
    }
}
