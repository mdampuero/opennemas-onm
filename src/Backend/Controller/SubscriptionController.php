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

class SubscriptionController extends BackendController
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
        'create' => 'SUBSCRIPTION_CREATE',
        'list'   => 'SUBSCRIPTION_ADMIN',
        'show'   => 'SUBSCRIPTION_UPDATE'
    ];

    /**
     * The resource name.
     *
     * @var string
     */
    protected $resource = 'subscription';
}
