<?php

/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Common\Core\Component\Helper;

use Api\Exception\GetItemException;
use Symfony\Component\DependencyInjection\Container;

/**
* Perform searches in Database related with one content
*/
class WebPushNotificationsHelper
{
    /**
     * The services container.
     *
     * @var Container
     */
    protected $container;

    /**
     * The content service.
     *
     * @var ContentService
     */
    protected $service;

    /**
     * The frontend template.
     *
     * @var Template
     */
    protected $template;

    /**
     * The entity repository.
     *
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * The cache service.
     *
     * @var Cache
     */
    protected $cache;

    /**
     * The tags service.
     *
     * @var TagService
     */
    protected $tagService;

    /**
     * The subscriptions helper.
     *
     * @var SubscriptionHelper
     */
    protected $subscriptionHelper;

    /**
     * Initializes the ContentHelper.
     *
     * @param Container $container The service container.
     */
    public function __construct($container)
    {
        $this->container = $container;
        $this->service   = $this->container->get('api.service.webpush_notifications');
    }

    /**
     * Returns the first send date from the notifications history.
     *
     * @return string The content body.
     */
    public function getFirstItemDate()
    {
        $oql = sprintf(
            'send_date >="2000-01-01"'
            . ' order by send_date asc limit 1',
        );

        try {
            $item = $this->service->getItemBy($oql);

            return $item->send_date;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function createNotificationFromData($data)
    {
        $notification = [
            'status'         => $data['status'] ?? 0,
            'body'           => $data['body'] ?? $data['description'] ?? '',
            'title'          => $data['title'] ?? '',
            'send_date'      => $data['send_date'] ?? gmdate('Y-m-d H:i:s'),
            'image'          => $data['image'] ?? null,
            'transaction_id' => $data['transaction_id'] ?? '',
            'impressions'    => $data['impressions'] ?? 0,
            'clicks'         => $data['clicks'] ?? 0,
            'closed'         => $data['closed'] ?? 0,
        ];

        return $notification;
    }
}
