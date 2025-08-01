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

class SubscriberController extends BackendController
{
    /**
     * {@inheritdoc}
     */
    protected $extension = 'CONTENT_SUBSCRIPTIONS';

    /**
     * {@inheritdoc}
     */
    protected $permissions = [
        'create'   => 'SUBSCRIBER_CREATE',
        'settings' => 'SUBSCRIBER_SETTINGS',
        'list'     => 'SUBSCRIBER_ADMIN',
        'show'     => 'SUBSCRIBER_UPDATE'
    ];

    /**
     * {@inheritdoc}
     */
    protected $resource = 'subscriber';

    /**
     * Displays the list of settings for subscribers.
     *
     * @return Response The response object.
     */
    public function settingsAction()
    {
        $this->checkSecurity($this->extension, $this->getActionPermission('settings'));

        return $this->render('subscriber/settings.tpl');
    }
}
