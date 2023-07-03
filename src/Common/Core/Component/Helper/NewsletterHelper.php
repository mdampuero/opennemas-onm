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

use Api\Service\Service;
use Opennemas\Orm\Core\EntityManager;

class NewsletterHelper
{
    /**
     * The entity manager.
     *
     * @var EntityManager
     */
    protected $em;

    /**
     * The API service to get internal subscriptions.
     *
     * @var Service
     */
    protected $service;

    /**
     * Initializes the NewsletterHelper.
     *
     * @param EntityManager $em The entity manager
     */
    public function __construct(EntityManager $em, Service $service)
    {
        $this->em      = $em;
        $this->service = $service;
    }

    /**
     * Returns the list of content types available to use in newsletters.
     *
     * @return array The list of content types.
     */
    public function getContentTypes()
    {
        $types        = [];
        $contentTypes = \ContentManager::getContentTypesFiltered();
        $ignored      = [
            'advertisement', 'book', 'comment', 'event', 'photo',
            'widget'
        ];

        foreach ($contentTypes as $key => $value) {
            if (!in_array($key, $ignored)) {
                $types[] = [ 'title' => _($value), 'value' => $key ];
            }
        }

        return $types;
    }

    /**
     * Returns the list of available recipients.
     *
     * @return array The list of available recipients.
     */
    public function getRecipients()
    {
        return array_merge(
            $this->getInternalSubscriptions(),
            $this->getActOnSubscriptions(),
            $this->getExternalSubscriptions()
        );
    }

    /**
     * Returns the subscription type name configured in backend
     *
     * @return string the subscription type configured
     */
    public function getSubscriptionType()
    {
        return $this->em->getDataSet('Settings', 'instance')
            ->get('newsletter_subscriptionType');
    }

    /**
     * Returns the list of configured Act-On lists.
     *
     * @return array The list of configured Act-On lists.
     */
    protected function getActOnSubscriptions()
    {
        $lists = $this->em->getDataSet('Settings', 'instance')
            ->get('actOn.marketingLists');

        if (empty($lists)) {
            return [];
        }

        return array_map(function ($a) {
            return [
                'id'   => (string) $a['id'],
                'name' => $a['name'],
                'type' => 'acton',
            ];
        }, $lists);
    }

    /**
     * Returns the list of external mail lists.
     *
     * @return array The list of external mail lists.
     */
    protected function getExternalSubscriptions()
    {
        $mailList = $this->em->getDataSet('Settings', 'instance')
            ->get('newsletter_maillist');

        if (empty($mailList) || empty($mailList['email'])) {
            return [];
        }

        return [ [
            'email' => $mailList['email'],
            'name'  => $mailList['email'],
            'type'  => 'external'
        ] ];
    }

    /**
     * Returns the list of subscriptions with the newsletter privilege enabled.
     *
     * @return array The list of subscriptions.
     */
    protected function getInternalSubscriptions()
    {
        $response = $this->service->getList();

        $subscriptions = array_values(
            array_filter($response['items'], function ($a) {
                return in_array(224, $a->privileges);
            })
        );

        return array_map(function ($a) {
            return [
                'id'   => (string) $a->pk_user_group,
                'name' => $a->name,
                'type' => 'list',
            ];
        }, $subscriptions);
    }
}
