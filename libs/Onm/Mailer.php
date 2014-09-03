<?php
/**
 * Defines the Onm\Mailer class
 *
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  Onm
 **/
namespace Onm;

/**
 * Initializes the SwiftMailer service for sending emails
 *
 * @package  Onm
 */
class Mailer
{
    /**
     * List of allowed encription protocols
     *
     * @var array
     **/
    private $allowedServerEncriptions = array('ssl', 'tls');

    /**
     * The default configuration for connection to the mail server
     *
     * @var array
     **/
    private $defaultParams = array(
        'transport'        => 'mail',
        'username'         => '',
        'password'         => '',
        'port'             => 25,
        'sendmail_command' => '/usr/sbin/sendmail -bs',
        'protocol'         => 'none',
    );

    /**
     * Initializes the mailer service given an array with the SMTP server connection params
     *
     * @param array $mailerParameters the list of parameters to initialize the service
     *
     * @return Onm\Mailer the mailer instance
     **/
    public function __construct($mailerParameters)
    {
        $mailerParameters = array_filter(
            $mailerParameters,
            function ($component) {
                return ($component != null);
            }
        );

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
     * @param string $method the method to call
     * @param array $params the list of parameters to pass to the method
     *
     * @return mixed the result of the method call
     **/
    public function __call($method, $params)
    {
        return call_user_func_array(array($this->mailer, $method), $params);
    }
}
