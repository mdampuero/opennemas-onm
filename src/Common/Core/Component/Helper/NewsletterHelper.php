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
     * The newsletter manager service.
     *
     * @var NewsletterManager
     */
    protected $nm;

    /**
     * Initializes the NewsletterHelper.
     *
     * @param SettingsRepository $settingsRepository The settings repository service.
     *
     * @return void
     */
    public function __construct(
        $settingsRepository,
        $newsletterManager
    ) {
        $this->sm = $settingsRepository;
        $this->nm = $newsletterManager;
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

    /**
     * Count total mailing sends in current month
     *
     * @return int Total number of mail sent in current mount
     */
    public function getTotalNumberOfNewslettersSend()
    {
        // Get maximum number of allowed sending mails
        $maxAllowed = $this->sm->get('max_mailing');

        // Get last invoice DateTime
        $lastInvoiceDate = $this->updateLastInvoice();

        // Get today DateTime
        $today = new \DateTime();

        // Get all newsletters updated between today and last invoice
        $where = " updated >= '" . $lastInvoiceDate->format('Y-m-d H:i:s')
            . "' AND updated <= '" . $today->format('Y-m-d H:i:s') . "' and sent > 0";

        list($nmCount, $newsletters) = $this->nm->find($where, 'created DESC');

        // Check if user has reached the limit of sent newsletters
        $totalSent = 0;
        if ($nmCount > 0) {
            foreach ($newsletters as $newsletter) {
                $totalSent += $newsletter->sent;
            }

            if ($maxAllowed > 0 && ($maxAllowed - $totalSent <= 0)) {
                return $maxAllowed;
            }
        }

        return $totalSent;
    }

    /**
     * Updates last invoice date
     *
     * @param string $date Date of last invoice
     *
     * @return DateTime Last invoice
     */
    private function updateLastInvoice()
    {
        // Generate last invoice DateTime
        $lastInvoice = new \DateTime($this->sm->get('last_invoice'));

        // Set day to 28 if it's more than that
        if ($lastInvoice->format('d') > 28) {
            $lastInvoice->setDate(
                $lastInvoice->format('Y'),
                $lastInvoice->format('m'),
                28
            );
        }

        // Get today DateTime
        $today = new \DateTime();

        // Get next invoice DateTime
        $nextInvoiceDate = new \DateTime($lastInvoice->format('Y-m-d H:i:s'));
        $nextInvoiceDate->modify('+1 month');

        // Update next invoice DateTime
        while ($today > $nextInvoiceDate) {
            $nextInvoiceDate->modify('+1 month');
        }

        // Update last invoice DateTime
        $lastInvoice = $nextInvoiceDate->modify('-1 month');

        $this->sm->set('last_invoice', $lastInvoice->format('Y-m-d H:i:s'));

        return $lastInvoice;
    }
}
