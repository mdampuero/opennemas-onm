<?php
/*
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
/**
 * Newsletter
 **/

class newNewsletter {

     /**
     * Send mail to all users
     *
     */
    function send($mailboxes, $htmlcontent, $params)
    {
        foreach($mailboxes as $mailbox) {
            $this->sendToUser($mailbox, $htmlcontent, $params);
        }
    }

    function sendToUser($mailbox, $htmlcontent, $params)
    {
        require_once(SITE_LIBS_PATH.'phpmailer/class.phpmailer.php');

        $mail = new PHPMailer();
        $mail->SetLanguage('es');
        $mail->IsSMTP();
        $mail->Host = $params['mail_host'];
        if (!empty($params['mail_user'])
            && !empty($paramsp['mail_password']))
        {
            $mail->SMTPAuth = true;
        } else {
            $mail->SMTPAuth = false;
        }

        $mail->CharSet = 'utf-8';

        $mail->Username = $params['mail_user'];
        $mail->Password = $params['mail_pass'];

        // Inject values by $params array
        $mail->From     = $params['mail_from'];
        $mail->FromName = $params['mail_from_name'];
        $mail->IsHTML(true);
        $this->HTML = $htmlcontent;

        $mail->AddAddress($mailbox->email, $mailbox->name);

        // embed image logo
        $mail->AddEmbeddedImage(SITE_PATH . 'themes/xornal/images/xornal-boletin.jpg', 'logo-cid', 'Logotipo');

        $subject = (!isset($params['subject']))? '[Xornal]': $params['subject'];
        $mail->Subject  = $subject;

        // TODO: crear un filtro
        $this->HTML = preg_replace('/(>[^<"]*)["]+([^<"]*<)/', "$1&#34;$2", $this->HTML);
        $this->HTML = preg_replace("/(>[^<']*)[']+([^<']*<)/", "$1&#39;$2", $this->HTML);
        $this->HTML = str_replace('“', '&#8220;', $this->HTML);
        $this->HTML = str_replace('”', '&#8221;', $this->HTML);
        $this->HTML = str_replace('‘', '&#8216;', $this->HTML);
        $this->HTML = str_replace('’', '&#8217;', $this->HTML);

        $mail->Body = $this->HTML;

        if(!$mail->Send()) {
            $this->errors[] = "Error en el envío del mensaje " . $mail->ErrorInfo;

            return false;
        }

        return true;
    }


}