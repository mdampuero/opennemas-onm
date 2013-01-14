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

    public function __construct($mailerParameters)
    {
        $this->defaultParams = array(
            'transport'        => 'mail',
            'username'         => '',
            'password'         => '',
            'port'             => 25,
            'sendmail_command' => '/usr/sbin/sendmail -bs',
        );
        $mailerParameters = array_merge($this->defaultParams, $mailerParameters);

        // Create the Transport
        if ($mailerParameters['transport'] == 'smtp') {
            $transport = \Swift_SmtpTransport::newInstance($mailerParameters['host'], $mailerParameters['port'])
                ->setUsername($mailerParameters['username'])
                ->setPassword($mailerParameters['password']);
        } elseif ($mailerParameters['transport'] == 'sendmail') {
            $transport = \Swift_SendmailTransport::newInstance($mailerParameters['sendmail_command']);
        } elseif ($mailerParameters['transport'] == 'mail') {
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
