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
        // Get smarty instance

        // Get underlying mailer service
        require SITE_VENDOR_PATH."/phpmailer/class.phpmailer.php";
        $this->mailer = new \PHPMailer();
        $this->mailer->SetLanguage('es');
        $this->mailer->IsSMTP();
        $this->mailer->Host = $mailerParameters['host'];
        if (!empty($mailerParameters['username'])
            && !empty($mailerParameters['password'])
        ) {
            $this->mailer->SMTPAuth = true;
        } else {
            $this->mailer->SMTPAuth = false;
        }

        $this->mailer->CharSet = 'utf-8';

        $this->mailer->Username = $mailerParameters['username'];
        $this->mailer->Password = $mailerParameters['password'];

        return $this;
    }

    /**
     * Generates and sends a mail to a set of addressses given a set of parameters
     *
     * @param array $mailerParameters the parameters for sending the email
     *
     * @return Mailer the mailer instance
     * @throws Exception If any problem raises while sending the email.
     **/
    public function send($mailerParameters = array())
    {
        if (!(count($mailerParameters) > 0)) {
            throw new \Exception('Please provide the necessary parameters for send the mail.');
        }

        // Inject values by $mailerParameters array
        $this->mailer->From     = $mailerParameters['mail_from'];
        $this->mailer->FromName = $mailerParameters['mail_from_name'];
        $this->mailer->IsHTML(true);
        $this->HTML = $htmlcontent;

        $this->mailer->AddAddress($mailbox->email, $mailbox->name);

        $subject = (!isset($mailerParameters['subject']))? '[Xornal]': $mailerParameters['subject'];
        $this->mailer->Subject  = $subject;

        // TODO: crear un filtro
        $this->HTML = preg_replace('/(>[^<"]*)["]+([^<"]*<)/', "$1&#34;$2", $this->HTML);
        $this->HTML = preg_replace("/(>[^<']*)[']+([^<']*<)/", "$1&#39;$2", $this->HTML);
        $this->HTML = str_replace('“', '&#8220;', $this->HTML);
        $this->HTML = str_replace('”', '&#8221;', $this->HTML);
        $this->HTML = str_replace('‘', '&#8216;', $this->HTML);
        $this->HTML = str_replace('’', '&#8217;', $this->HTML);

        $this->mailer->Body = $this->HTML;

        if (!$this->mailer->Send()) {
            throw new \Exception("Error en el envío del mensaje ".$this->mailer->ErrorInfo);
        }
        return $this;
    }
}