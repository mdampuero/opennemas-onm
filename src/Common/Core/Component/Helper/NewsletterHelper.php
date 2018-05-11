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
     * The application log service.
     *
     * @var Monolog
     */
    protected $appLog;

    /**
     * The error log service.
     *
     * @var Monolog
     */
    protected $errorLog;

    /**
     * The instance service.
     *
     * @var Instance
     */
    protected $instance;

    /**
     * The mailer service.
     *
     * @var mailer
     */
    protected $mailer;

    /**
     * The subscriptors service.
     *
     * @var SubscriptorService
     */
    protected $ssb;

    /**
     * The no-reply address parameter.
     *
     * @var string
     */
    protected $noReplyAddress;

    /**
     * Initializes the NewsletterHelper.
     *
     * @param SettingsRepository $settingsRepository The settings repository service.
     * @param NewsletterManager  $newsletterManager The newsletter manager service.
     * @param string             $noReplyAddress The no-reply address parameter.
     *
     * @return void
     */
    public function __construct(
        $settingsRepository,
        $newsletterManager,
        $errorLog,
        $appLog,
        $instance,
        $mailer,
        $ssb,
        $noReplyAddress
    ) {
        $this->sm                   = $settingsRepository;
        $this->nm                   = $newsletterManager;
        $this->appLog               = $appLog;
        $this->errorLog             = $errorLog;
        $this->noReplyAddress       = $noReplyAddress;
        $this->instanceInternalName = $instance->internal_name;
        $this->mailer               = $mailer;
        $this->ssb                  = $ssb;
        $this->newsletterConfigs    = $this->sm->get('newsletter_maillist');
        $this->siteName             = $this->sm->get('site_name');
    }

    /**
     * Sends a newsletter to a bunch of recipients
     *
     * @param Newsletter $newsletter the newsletter
     * @param array $recipients the list of recipients to send
     *
     * @return array the array with the report of each sent
     */
    public function send($newsletter, $recipients)
    {
        // if no recipients we can exit directly
        if (empty($recipients)) {
            return [];
        }

        $sentEmails = 0;
        $maxAllowed = (int) $this->sm->get('max_mailing', 0);
        $remaining  = $maxAllowed - $this->getTotalNumberOfNewslettersSend();
        $remaining  = 0;

        foreach ($recipients as $mailbox) {
            if ($maxAllowed > 0 && abs($remaining) > 0) {
                $sendResults[] = [$mailbox, false, _('Max sents reached.')];

                continue;
            }

            try {
                if ($mailbox->type == 'list') {
                    $sentEmails += $this->sendList($newsletter, $mailbox);
                } else {
                    $sentEmails += $this->sendEmail($newsletter, $mailbox);
                }

                $properlySent = $sentEmails !== false;


                $remaining -= $sentEmails;

                $sendResults[] = [ $mailbox, (bool) $properlySent,_('Email queued') ];
            } catch (\Exception $e) {
                $sendResults[] = [ $mailbox, false,  $e->getMessage() . _('Unable to deliver your email') ];

                $this->errorLog->error('Error sending newsletter ' . $e->getMessage());
            }
        }

        // If the newsletter was already sent in the past, then duplicate it
        if (empty($newsletter->sent)) {
            $newsletter->update([
                'sent' => $sentEmails
            ]);
        } else {
            $newsletter->create([
                'title' => $newsletter->title,
                'data'  => $newsletter->data,
                'html'  => $newsletter->html,
                'sent'  => $sentEmails,
            ]);
        }

        return [
            'total' => $sentEmails,
            'report' => $sendResults,
        ];
    }

    /**
     * Sends the newsletter to a subscription list
     *
     * @param Newsletter $newsletter the newsletter
     * @param array      $recipients the subscription group to send the newsletter
     *
     * @return int the number of emails sent
     */
    public function sendList($newsletter, $list)
    {
        $emailsSent = 0;
        $users      = $this->ssb->getList(
            '(user_group_id ~ "' . $list->id
            . '" and status != 0)'
        );

        if ($users['total'] == 0) {
            return $emailsSent;
        }

        foreach ($users['items'] as $user) {
            $emailsSent += $this->sendEmail($newsletter, $user);
        }

        return $emailsSent;
    }

    /**
     * Sends a newsletter to an specific mailbox
     *
     * @param Newsletter $newsletter the newsletter
     * @param array      $recipients the email to send the newsletter to
     *
     * @return int the number of emails sent
     */
    private function sendEmail($newsletter, $mailbox)
    {
        // Build the message
        $message = \Swift_Message::newInstance();
        $message
            ->setSubject($newsletter->title)
            ->setBody($newsletter->html, 'text/html')
            ->setFrom([$this->newsletterConfigs['sender'] => $this->siteName])
            ->setSender($this->noReplyAddress)
            ->setTo([$mailbox->email => $mailbox->name]);

        $headers = $message->getHeaders();
        $headers->addParameterizedHeader(
            'ACUMBAMAIL-SMTPAPI',
            $this->instanceInternalName . ' - Newsletter'
        );

        $this->appLog->notice(
            "Email sent. Backend newsletter sent (to: " . $mailbox->email . ")"
        );

        // Send it
        return ($this->mailer->send($message)) ? 1 : 0;
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
                $this->get('session')->getFlashBag()->add(
                    'error',
                    _('You have reached the maximum of emails allowed to send')
                );

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
