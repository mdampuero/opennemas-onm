<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Onm;

/**
* Mailer
*/
class Mailer
{
    private $allowedServerEncriptions = array('ssl', 'tls');

    private $defaultParams = array(
        'transport'        => 'mail',
        'username'         => '',
        'password'         => '',
        'port'             => 25,
        'sendmail_command' => '/usr/sbin/sendmail -bs',
        'protocol'         => 'none',
    );

    public function __construct($mailerParameters)
    {
        $mailerParameters = array_merge($this->defaultParams, $mailerParameters);

        // Create the Transport
        if ($mailerParameters['transport'] == 'smtp') {
            if (in_array($mailerParameters['protocol'], $this->allowedServerEncriptions)) {
                // Use the smtp transport with encryption
                $transport = \Swift_SmtpTransport::newInstance(
                    $mailerParameters['host'],
                    $mailerParameters['port'],
                    $mailerParameters['protocol']
                );
            } else {
                $transport = \Swift_SmtpTransport::newInstance(
                    $mailerParameters['host'],
                    $mailerParameters['port']
                );
            }

            $transport
                ->setUsername($mailerParameters['username'])
                ->setPassword($mailerParameters['password']);

        } elseif ($mailerParameters['transport'] == 'sendmail') {
            // Use the Sendmail transport
            $transport = \Swift_SendmailTransport::newInstance($mailerParameters['sendmail_command']);
        } elseif ($mailerParameters['transport'] == 'mail') {
            // Use the php built-in mail function
            $transport = \Swift_MailTransport::newInstance();
        }

        // Create the Mailer using your created Transport
        $this->mailer = \Swift_Mailer::newInstance($transport);

        return $this;
    }

    /**
     * Redirects all the calls to the Swift_Mailer call
     *
     * @return void
     **/
    public function __call($method, $params)
    {
        return call_user_func_array(array($this->mailer, $method), $params);
    }
}
