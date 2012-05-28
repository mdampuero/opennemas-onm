<?php
/*
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
use Onm\Settings as s,
    Onm\Message as m;
/**
 * Setup app
*/
require_once(dirname(__FILE__).'/../../../bootstrap.php');
require_once(SITE_ADMIN_PATH.'session_bootstrap.php');

//Check if module is activated in this onm instance
\Onm\Module\ModuleManager::checkActivatedOrForward('NEWSLETTER_MANAGER');

 // Check if the user can admin books
Acl::checkOrForward('NEWSLETTER_ADMIN');


$tpl = new \TemplateAdmin(TEMPLATE_ADMIN);

// Initialize request parameters
$action = filter_input( INPUT_POST, 'action' , FILTER_SANITIZE_STRING );
if (!isset($action)) {
    $action = filter_input( INPUT_GET, 'action' , FILTER_SANITIZE_STRING, array('options' => array('default' => 'addContents')) );
}

if($action != 'config' && $action != 'save_config') {
    if (is_null(s::get('newsletter_maillist')) || !(s::get('newsletter_subscriptionType') )
        || !(s::get('newsletter_enable'))) {
            m::add(_('Please provide your Newsletter configuration to start to use your Newsletter module'));
            $httpParams [] = array( 'action'=>'config' );
            Application::forward($_SERVER['SCRIPT_NAME'] . '?'.StringUtils::toHttpParams($httpParams));
    } else {
        $configurations = s::get('newsletter_maillist');
            foreach ($configurations as $key => $value) {
            if ($key != 'receiver' && empty($value)) {
                m::add(_('Your newsletter configuration is not complete. Please go to settings and complete the form.'), m::ERROR);
                $httpParams [] = array(
                    'action'=>'config',
                );
            Application::forward($_SERVER['SCRIPT_NAME'] . '?'.StringUtils::toHttpParams($httpParams));
            }
        }
    }
}

switch($action) {

    case 'config':

        $configurationsKeys = array(
                                    'newsletter_maillist',
                                    'newsletter_subscriptionType',
                                    'newsletter_enable',
                                    'recaptcha',
                                    );

        $configurations = s::get($configurationsKeys);

        //Check that user has configured reCaptcha keys if newsletter is enabled
        $missingRecaptcha = false;
        if (empty($configurations['recaptcha']['public_key'])
             || empty($configurations['recaptcha']['private_key']))
        {
            $missingRecaptcha = true;
        }

        $tpl->assign(
                     array(
                            'configs'   => $configurations,
                            'missing_recaptcha'   => $missingRecaptcha,
                          )
                    );

        $tpl->display('newsletter/config.tpl');

    break;

    case 'save_config':

        unset($_POST['action']);
        unset($_POST['submit']);
        foreach ($_POST as $key => $value ) {
            s::set($key, $value);
        }

        m::add(_('Settings saved.'), m::SUCCESS);

        $httpParams = array(
                            array('action'=>'addContents'),
                            );
        Application::forward($_SERVER['SCRIPT_NAME'] . '?'.StringUtils::toHttpParams($httpParams));

    break;

    /**
     * Step: list all contents
     */
    case 'addContents':
    case 'updateContents':

        //Get saved newsletters
        $newsletter = new NewNewsletter();
        $savedNewsletters = $newsletter->search('1=1 ORDER BY created DESC LIMIT 0,30');

        $newsletterContent = isset($_SESSION['data-newsletter']) ?
                     json_decode(json_decode($_SESSION['data-newsletter'])) : '';

        if (array_key_exists('newsletterHtml', $_SESSION)) {
            unset($_SESSION['newsletterHtml']);
        }

        $tpl->assign( array(
            'newsletterContent' => $newsletterContent,
            'savedNewsletters'  => $savedNewsletters )
        );

        $tpl->display('newsletter/steps/newsletterContents.tpl');
    break;

    case 'loadSavedNewsletter':

        $id = filter_input(INPUT_POST, 'saved_newsletters', FILTER_VALIDATE_INT);

        $newsletter = new NewNewsletter($id);

        $_SESSION['data-newsletter'] = $newsletter->data;

        $_SESSION['newsletterHtml'] = $newsletter->html;

        $htmlContent = html_entity_decode($newsletter->html, ENT_QUOTES );

        $tpl->assign( array(
                    'htmlContent' => $htmlContent,
                    ) );

        $tpl->display('newsletter/steps/newsletterPreview.tpl');

    break;

    case 'saveNewsletterContent';
        $html = filter_input(INPUT_POST,'html');
        $html = htmlentities($html, ENT_QUOTES);
        $_SESSION['newsletterHtml'] = $html;

        Application::ajax_out('ok');

   /**
     * Step: preview the message
     */
    case 'preview':

        $newsletter = new NewsletterManager();

        if( array_key_exists('newsletterHtml', $_SESSION) &&
            !empty($_SESSION['newsletterHtml'])) {

            $htmlContent = html_entity_decode($_SESSION['newsletterHtml'], ENT_QUOTES);
        } else {
            $_SESSION['data-newsletter'] =$_POST['newsletterContent'];
            $htmlContent = $newsletter->render();
        }

        $tpl->assign('htmlContent', $htmlContent);
        $tpl->display('newsletter/steps/newsletterPreview.tpl');

    break;

    /**
     * Step: list Accounts available for send this message
     * TODO: must be improved to allow custom destinations or edit existing.
     */
    case 'listRecipients':

        $newsletter = new Newsletter(array('namespace' => 'PConecta_'));

        $account = $newsletter->getAccountsProvider();
        $accounts = $account->getAccounts();

        $receiver = array();
        $mailList = array();

        $configurations = \Onm\Settings::get('newsletter_maillist');
        if (!is_null($configurations)
            && array_key_exists('email', $configurations)
            && !empty($configurations['email']))
        {
            $mailList[] = new Newsletter_Account(
                $configurations['email'],
                $configurations['name']
            );

        }
        $tpl->assign('mailList', $mailList);

        // Ajax request
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
           ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
            header('Content-type: application/json');
            echo json_encode($accounts);
            exit(0);
        }

        $tpl->assign('accounts', $accounts);

        if(isset($_COOKIE['data-recipients'])) {
            $recipients = json_decode(json_decode($_COOKIE['data-recipients']));
            $tpl->assign('recipients', $recipients);
        }

        $tpl->display('newsletter/steps/newsletterRecipients.tpl');

    break;

    /**
     * Step: send the message to destinations and see send report.
     */
    case 'send':
        $subject =  $_COOKIE['data-subject'];

        $nManager = new NewsletterManager();
        $nManager->setConfigMailing();

        if( array_key_exists('newsletterHtml', $_SESSION) &&
            !empty($_SESSION['newsletterHtml'])) {

            $htmlContent = html_entity_decode($_SESSION['newsletterHtml'], ENT_QUOTES);
        } else {
            $htmlContent = $nManager->render();
        }

        $newsletter = new NewNewsletter();

        // save newsletter
        $postmaster = $_SESSION['data-newsletter'];
        $newsletter->create( array('content' => $postmaster, 'html'=> $htmlContent));



        $params = array(
            'subject'   => $subject,
            'mail_host' => MAIL_HOST,
            'mail_user' => MAIL_USER,
            'mail_pass' => MAIL_PASS,
            'mail_from' => MAIL_FROM,
            'mail_from_name' => s::get('site_name'),
        );

        $data = json_decode($postmaster);

        $htmlFinal = "";

        // Mail user by user
        $recipients = json_decode(json_decode($_COOKIE['data-recipients']));
        foreach($recipients as $mailbox) {

            // Replace name destination
            $emailHtmlContent = str_replace('###DESTINATARIO###', $mailbox->name, $htmlContent);

            if($nManager->sendToUser($mailbox, $emailHtmlContent, $params)) {
                $htmlFinal .= '<tr><td width=50% align=right><strong class="ok">OK</strong>&nbsp;&nbsp;</td><td>'. $mailbox->name . ' &lt;' . $mailbox->email . '&gt;</td></tr>';
            } else {
                $htmlFinal .= '<tr><td width=50% ><strong class="failed">FAILED</strong>&nbsp;&nbsp;</td><td>'. $mailbox->name . ' &lt;' . $mailbox->email. '&gt;</td></tr>';
            }
        }

        $tpl->assign(array(
            'html_final' => $htmlFinal,
            'postmaster' => $postmaster,
        ));

        unset($_SESSION['newsletterHtml']);
        unset($SESSION['data-newsletter']);
        unset($_COOKIE['data-recipients']);
        unset($_COOKIE['data-subject']);

        $tpl->display('newsletter/steps/newsletterSend.tpl');

    break;


}

