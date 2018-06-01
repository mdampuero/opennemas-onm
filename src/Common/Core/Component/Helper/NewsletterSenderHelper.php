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
        $newsletterHelper,
        $noReplyAddress
    ) {
        $this->sm                   = $settingsRepository;
        $this->appLog               = $appLog;
        $this->errorLog             = $errorLog;
        $this->noReplyAddress       = $noReplyAddress;
        $this->instanceInternalName = $instance->internal_name;
        $this->mailer               = $mailer;
        $this->ssb                  = $ssb;
        $this->nh                   = $newsletterHelper;
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
        $remaining  = $maxAllowed - $this->nh->getTotalNumberOfNewslettersSend();

        foreach ($recipients as $mailbox) {
            if ($maxAllowed > 0 && abs($remaining) >= 0) {
                $sendResults[] = [$mailbox, false, _('Max sents reached')];

                continue;
            }

            try {
                if ($mailbox->type == 'list') {
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
        $settings = $this->sm->get([
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
}
