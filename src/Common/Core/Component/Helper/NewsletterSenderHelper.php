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
        $errorLog,
        $appLog,
        $instance,
        $mailer,
        $ssb,
        $actOnFactory,
        $newsletterService,
        $noReplyAddress
    ) {
        $this->ormManager           = $settingsRepository;
        $this->appLog               = $appLog;
        $this->errorLog             = $errorLog;
        $this->noReplyAddress       = $noReplyAddress;
        $this->instanceInternalName = $instance->internal_name;
        $this->mailer               = $mailer;
        $this->ssb                  = $ssb;
        $this->actOnFactory         = $actOnFactory;
        $this->ns                   = $newsletterService;
        $this->newsletterConfigs    = $this->ormManager->getDataSet('Settings', 'instance')->get('newsletter_maillist');
        $this->siteName             = $this->ormManager->getDataSet('Settings', 'instance')->get('site_name');
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
            return [
                'total'      => 0,
                'create_new' => false,
                'report'     => [],
            ];
        }

        $sentEmails      = 0;
        $maxAllowed      = (int) $this->ormManager->getDataSet('Settings', 'instance')->get('max_mailing', 0);
        $lastInvoiceDate = $this->getLastInvoiceDate();
        $remaining       = $maxAllowed - $this->ns->getSentNewslettersSinceLastInvoice($lastInvoiceDate);

        // Fix encoding of the html
        $newsletter->html = htmlspecialchars_decode($newsletter->html, ENT_QUOTES);

        $recipients = json_decode(json_encode($recipients), false);
        foreach ($recipients as $mailbox) {
            if ($maxAllowed > 0 && abs($remaining) >= 0) {
                $sendResults[] = [$mailbox, false, _('Max sents reached')];

                continue;
            }

            try {
                if ($mailbox->type == 'acton') {
                    list($errors, $sentEmails) = $this->sendActon($newsletter, $mailbox);

                    $sendResults[] = [ $mailbox, $sentEmails > 0, '' ];
                } elseif ($mailbox->type == 'list') {
                    list($errors, $sentEmailsList) = $this->sendList($newsletter, $mailbox);

                    $sentEmails   += $sentEmailsList;
                    $sendResults[] = [ $mailbox, true, sprintf(_('%d subscribers'), $sentEmailsList) ];
                    if (count($errors) > 0) {
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

        return [
            'total'      => $sentEmails,
            'report'     => $sendResults,
            'create_new' => !empty($newsletter->sent),
        ];
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
                'iscustom'              => 'Y',
                'htmlbody'              => $newsletter->html,
                'textbody'              => $newsletter->html,
                'sendername'            => $settings['site_name'],
                'senderemail'           => $settings['newsletter_maillist']['sender'],
                'subject'               => $newsletter->title,
                'when'                  => time(),
                'sendtoids'             => $marketingList->id,
                'createcrmmsgsentnotes' => "N",
            ];

            if (!empty($settings['actOn.headerId'])) {
                $sendingParams['headerid'] = $settings['actOn.headerId'];
            }

            if (!empty($settings['actOn.footerId'])) {
                $sendingParams['footerid'] = $settings['actOn.footerId'];
            }

            $result = $endpoint->sendMessage($id, $sendingParams);

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
        $users      = $this->ssb->getList(
            '(user_group_id = "' . $list->id
            . '" and status != 0)'
        );

        if ($users['total'] == 0) {
            return [ [], $sentEmails ];
        }

        $errors = [];
        foreach ($users['items'] as $user) {
            try {
                $sentEmails += $this->sendEmail($newsletter, $user);
            } catch (\Swift_RfcComplianceException $e) {
                $errors[] = sprintf(_('Email not valid: %s'), $user->email);
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
    private function sendEmail($newsletter, $mailbox)
    {
        // Build the message
        $message = \Swift_Message::newInstance();
        $message
            ->setSubject($newsletter->title)
            ->setBody($newsletter->html, 'text/html')
            ->setFrom([$this->newsletterConfigs['sender'] => $this->siteName])
            ->setSender($this->noReplyAddress)
            ->setTo([ $mailbox->email => $mailbox->name ]);

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
     * Sends an email with the new subscription data
     *
     * @param Array $data Data for subscription
     *
     * @return Array Message and class to show the user
     * @throws Exception Thrown on any problem with the external service
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
            $this->instanceInternalName . ' - Newsletter subscription'
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
