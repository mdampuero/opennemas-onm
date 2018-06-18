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

class NewsletterHelper
{
    /**
     * The settings repository service.
     *
     * @var SettingsRepository
     */
    protected $sm;

    /**
     * Initializes the NewsletterHelper.
     *
     * @param SettingsRepository $settingsRepository The settings repository service.
     *
     * @return void
     */
    public function __construct($settingsRepository)
    {
        $this->sm = $settingsRepository;
    }

    /**
     * Returns the subscription type name configured in backend
     *
     * @return string the subscription type configured
     **/
    public function getSubscriptionType()
    {
        return $this->sm->get('newsletter_subscriptionType');
    }
}
