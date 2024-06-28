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

class NewsletterSenderHelper
{
    /**
     * The settings repository service.
     *
     * @var SettingsRepository
     */
    protected $sm;

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
     * The service container.
     *
     * @var ServiceContainer
     */
    protected $container;

    /**
     * The mailer service.
     *
     * @var mailer
     */
    protected $mailer;

    /**
     * The subscription service.
     *
     * @var SubscriptionService
     */
    protected $ss;

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
     */
    public function __construct(
        $settingsRepository,
        $errorLog,
        $appLog,
        $container,
        $mailer,
        $ss,
        $actOnFactory,
        $newsletterService,
        $noReplyAddress
    ) {
        $this->ormManager     = $settingsRepository;
        $this->appLog         = $appLog;
        $this->errorLog       = $errorLog;
        $this->globals        = $container->get('core.globals');
        $this->noReplyAddress = $noReplyAddress;
        $this->mailer         = $mailer;
        $this->ss             = $ss;
        $this->actOnFactory   = $actOnFactory;
        $this->ns             = $newsletterService;
        $this->container      = $container;
    }

    /**
     * Sends a newsletter to a bunch of recipients
     *
     * @param Newsletter $newsletter The newsletter
     * @param mixed $recipients      The list of recipients to send
     *
     * @return int $sentEmails       Total newsletters sents
     */
    public function send($newsletter, $recipients)
    {
        // if no recipients we can exit directly
        if (empty($recipients)) {
            return 0;
        }

        //Set current newsletter pending status while sending emails
        $prevSent = $newsletter->getStored()['sent_items'];
        $this->container->get('api.service.newsletter')->patchItem($newsletter->id, [
            'sent_items' => -1,
            'updated'    => new \Datetime(),
        ]);

        $sentEmails      = 0;
        $maxAllowed      = (int) $this->ormManager->getDataSet('Settings', 'instance')->get('max_mailing', 0);
        $lastInvoiceDate = $this->getLastInvoiceDate();
        $remaining       = $maxAllowed - $this->ns->getSentNewslettersSinceLastInvoice($lastInvoiceDate);

        // Fix encoding of the html
        $newsletter->html = htmlspecialchars_decode($newsletter->html, ENT_QUOTES);

        $recipients = is_string($recipients) ? json_decode($recipients, true) : $recipients;
        foreach ($recipients as $mailbox) {
            if ($maxAllowed > 0 && abs($remaining) >= 0) {
                $sendResults[] = [$mailbox, false, _('Max sents reached')];

                continue;
            }

            try {
                if ($mailbox['type'] == 'acton') {
                    list($errors, $sentEmails) = $this->sendActon($newsletter, $mailbox);

                    $sendResults[] = [ $mailbox, $sentEmails > 0, '' ];
                } elseif ($mailbox['type'] == 'list') {
                    list($errors, $sentEmailsList) = $this->sendList($newsletter, $mailbox);

                    $sentEmails   += $sentEmailsList;
                    $sendResults[] = [ $mailbox, true, sprintf(_('%d subscribers'), $sentEmailsList) ];
                    if (!empty($errors)) {
                        foreach ($errors as $error) {
                            $sendResults[] = [ $mailbox, false, $error ];
                        }
                    }
                } else {
                    $sentEmails += $this->sendEmail($newsletter, $mailbox);

                    $sendResults[] = [ $mailbox, $sentEmails > 0, '' ];
                }
                $remaining -= $sentEmails;
            } catch (\Exception $e) {
                $sendResults[] = [ $mailbox, false,  _('Unable to deliver your email') ];

                $this->errorLog->error('Error sending newsletter ' . $e->getMessage());
            }
        }

        $newsletter->sent_items = $prevSent;

        // Duplicate newsletter if it was sent before.
        if ($newsletter->sent_items > 0) {
            $this->container->get('api.service.newsletter')->patchItem($newsletter->id, [
                'sent_items' => $prevSent,
                'updated'    => new \Datetime(),
            ]);
            $data = array_merge($newsletter->getStored(), [
                'recipients' => $recipients,
                'sent'       => new \Datetime(),
                'sent_items' => $sentEmails,
                'updated'    => new \Datetime(),
            ]);

            unset($data['id']);

            $newsletter = $this->container->get('api.service.newsletter')->createItem($data);
        } else {
            $this->container->get('api.service.newsletter')->patchItem($newsletter->id, [
                'recipients' => $recipients,
                'sent'       => new \Datetime(),
                'sent_items' => $sentEmails,
                'updated'    => new \Datetime(),
            ]);
        }

        return $sentEmails;
    }

    /**
     * Sends the newsletter to a subscription list
     *
     * @param Newsletter $newsletter the newsletter
     * @param array      $recipients the subscription group to send the newsletter
     *
     * @return array the number of emails sent
     */
    public function sendActon($newsletter, $marketingList)
    {
        $sentEmails = 0;

        $errors = [];
        try {
            $endpoint = $this->actOnFactory->getEndpoint('email_campaign');

            // Save settings
            $settings = $this->ormManager
                ->getDataSet('Settings', 'instance')
                ->get([
                    'site_name',
                    'newsletter_maillist',
                    'actOn.headerId',
                    'actOn.footerId',
                ]);

            $messageParams = [
                'subject'  => $newsletter->title,
                'title'    => $newsletter->title,
                'htmlbody' => $newsletter->html,
            ];

            // Load general header and footer acton params
            if (!empty($settings['actOn.headerId'])) {
                $messageParams['headerid'] = $settings['actOn.headerId'];
            }

            if (!empty($settings['actOn.footerId'])) {
                $messageParams['footerid'] = $settings['actOn.footerId'];
            }

            // Overwrite header and footer params if the newsletter has custom ones.
            if (!empty($newsletter->params['acton_headerid'])) {
                $messageParams['headerid'] = $newsletter->params['acton_headerid'];
            }

            if (!empty($newsletter->params['acton_headerid'])) {
                $messageParams['footerid'] = $newsletter->params['acton_footerid'];
            }

            $id = $endpoint->createMessage($messageParams);

            $sendingParams = [
                'createcrmmsgsentnotes' => 'N',
                'footerid'              => $messageParams['footerid'],
                'headerid'              => $messageParams['headerid'],
                'htmlbody'              => $newsletter->html,
                'iscustom'              => 'Y',
                'senderemail'           => $settings['newsletter_maillist']['sender'],
                'sendername'            => $settings['site_name'],
                'sendtoids'             => $marketingList['id'],
                'subject'               => $newsletter->title,
                'textbody'              => $newsletter->html,
                'when'                  => time()
            ];

            $endpoint->sendMessage($id, $sendingParams);

            $sentEmails += 1;
        } catch (\Exception $e) {
            $this->errorLog->error('Error sending to ActOn: ' . $e->getMessage());

            $errors[] = _('Unable to deliver your email');
        }

        return [ $errors, $sentEmails ];
    }

    /**
     * Sends the newsletter to a subscription list
     *
     * @param Newsletter $newsletter the newsletter
     * @param array      $recipients the subscription group to send the newsletter
     *
     * @return array the number of emails sent
     */
    public function sendList($newsletter, $list)
    {
        $sentEmails = 0;
        $list       = $this->ss->getItem($list['id']);
        $users      = $this->ss->getEmails($list);

        if (empty($users)) {
            return [ [], $sentEmails ];
        }

        $errors = [];
        foreach ($users as $user) {
            try {
                $replacement = empty($list)
                ? base64_encode($user['email'])
                : [base64_encode($user['email']), $list->pk_user_group];

                $newsletterCopy = clone $newsletter;

                $sentEmails += $this->sendEmail($newsletterCopy, $user, $replacement);
            } catch (\Swift_RfcComplianceException $e) {
                $errors[] = sprintf(_('Email not valid: %s %s'), $user['email'], $e->getMessage());
            } catch (\Exception $e) {
                $errors[] = _('Unable to deliver your email');
            }
        }

        return [ $errors, $sentEmails ];
    }

    /**
     * Sends a newsletter to an specific mailbox
     *
     * @param Newsletter $newsletter the newsletter
     * @param array      $recipients the email to send the newsletter to
     *
     * @return int the number of emails sent
     */
    private function sendEmail($newsletter, $mailbox, $replacement)
    {
        $this->newsletterConfigs = $this->ormManager->getDataSet('Settings', 'instance')->get('newsletter_maillist');
        $this->siteName          = $this->ormManager->getDataSet('Settings', 'instance')->get('site_name');

        // Build the message
        try {
            $newsletter->html = str_replace(
                ['%NEWSLETTER_EMAIL%', '%NEWSLETTER_ID%'],
                $replacement,
                $newsletter->html
            );

            $message = \Swift_Message::newInstance();
            $message
                ->setSubject($newsletter->title)
                ->setBody($newsletter->html, 'text/html')
                ->setFrom([$this->newsletterConfigs['sender'] => $this->siteName])
                ->setSender($this->noReplyAddress)
                ->setTo([ $mailbox['email'] => $mailbox['name']]);

            $headers = $message->getHeaders();

            $headers->addParameterizedHeader(
                'ACUMBAMAIL-SMTPAPI',
                $this->globals->getInstance()->internal_name . ' - Newsletter'
            );

            $this->appLog->notice(
                "Email sent. Backend newsletter sent (to: " . $mailbox['email'] . ")"
            );
        } catch (\Exception $e) {
            $this->appLog->notice('Unable to deliver your email: ' . $e->getMessage());

            return 0;
        }

        // Send it
        return ($this->mailer->send($message)) ? 1 : 0;
    }

    /**
     * Sends an email with the new subscription data
     *
     * @param array $data Data for subscription
     *
     * @return array Message and class to show the user
     *
     * @throws \Exception
     */
    public function sendSubscriptionMail($data)
    {
        $settings = $this->ormManager->getDataSet('Settings', 'instance')->get([
            'site_name',
            'newsletter_maillist'
        ]);

        // Checking the type of action to do (alta/baja)
        if ($data['subscription'] == 'alta') {
            $subject = "Solicitud de ALTA - Boletín ";
            $text    = [ "Solicitud de Alta en el boletín de:" ];

            $message = _("You have been subscribed to the newsletter.");
        } else {
            $subject = "Solicitud de BAJA - Boletín ";
            $text    = [ "Solicitud de Baja en el boletín de:" ];

            $message = _("You have been unsubscribed from the newsletter.");
        }
        $subject .= $settings['site_name'];

        // Build mail body
        $text[] = "Nombre: {$data['name']}";
        $text[] = "Email: {$data['email']}";

        if (!empty($data['subscritorEntity'])) {
            $text[] = "Entidad: {$data['subscritorEntity']}";
        }

        if (!empty($data['subscritorCountry'])) {
            $text[] = "País: {$data['subscritorCountry']}";
        }

        if (!empty($data['subscritorCommunity'])) {
            $text[] = "Provincia de Origen: {$data['subscritorCommunity']}";
        }

        $body = implode("\r\n", $text);

        //  Build the message
        $email = \Swift_Message::newInstance();
        $email
            ->setSubject($subject)
            ->setBody($body, 'text/html')
            ->setBody(strip_tags($body), 'text/plain')
            ->setTo([ $settings['newsletter_maillist']['subscription'] => _('Subscription form') ])
            ->setFrom([ $data['email'] => $data['name'] ])
            ->setSender([ 'no-reply@postman.opennemas.com' => $settings['site_name'] ]);

        $headers = $email->getHeaders();
        $headers->addParameterizedHeader(
            'ACUMBAMAIL-SMTPAPI',
            $this->globals->getInstance()->internal_name . ' - Newsletter subscription'
        );

        try {
            return $this->mailer->send($email);
        } catch (\Swift_SwiftException $e) {
            throw new \Exception(_(
                "Sorry, we were unable to complete your request.\n"
                . "Check the form and try again"
            ));
        }
    }

    /**
     * Updates last invoice date
     *
     * @param string $date Date of last invoice
     *
     * @return DateTime Last invoice
     */
    public function getLastInvoiceDate()
    {
        // Generate last invoice DateTime
        $lastInvoice = new \DateTime($this->ormManager->getDataSet('Settings', 'instance')->get('last_invoice'));

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

        $this->ormManager->getDataSet('Settings', 'instance')
            ->set('last_invoice', $lastInvoice->format('Y-m-d H:i:s'));

        return $lastInvoice;
    }
}
