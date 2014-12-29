<?php
/**
 * Handles the actions for the newsletter
 *
 * @package Backend_Controllers
 **/
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Backend\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Onm\Framework\Controller\Controller;
use Onm\Message as m;
use Onm\Settings as s;

/**
 * Handles the actions for the newsletter
 *
 * @package Backend_Controllers
 **/
class NewsletterController extends Controller
{
    /**
     * Common code for all the actions
     *
     * @return void
     **/
    public function init()
    {
        //Check if module is activated in this onm instance
        \Onm\Module\ModuleManager::checkActivatedOrForward('NEWSLETTER_MANAGER');
    }

    /**
     * Lists all the available newsletters
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("has_role('NEWSLETTER_ADMIN')")
     **/
    public function listAction(Request $request)
    {
        $maxAllowed     = s::get('max_mailing');
        $totalSendings  = $this->checkMailing();
        $date           = s::get('last_invoice');
        $lastInvoice    = new \DateTime($date);

        // Check if the module is configured, if not redirect to the config form
        $configuredRedirection = $this->checkModuleActivated();

        if ($configuredRedirection != false) {
            return $configuredRedirection;
        }

        $itemsPerPage = s::get('items_per_page');
        $page         = $request->query->getDigits('page', 1);

        $nm = $this->get('newsletter_manager');
        list($count, $newsletters) = $nm->find('1 = 1', 'created DESC', $page, $itemsPerPage);

        $pagination = \Onm\Pager\SimplePager::getPagerUrl(
            array(
                'page'  => $page,
                'items' => $itemsPerPage,
                'total' => $count,
                'url'   => $this->generateUrl(
                    'admin_newsletters',
                    array(
                        'page' => $page,
                    )
                )
            )
        );

        return $this->render(
            'newsletter/list.tpl',
            array(
                'newsletters'   => $newsletters,
                'count'         => $count,
                'pagination'    => $pagination,
                'totalSendings' => $totalSendings,
                'maxAllowed'    => $maxAllowed,
                'lastInvoice'   => $lastInvoice->format(_('Y-m-d')),
            )
        );
    }

    /**
     * List the form for create or load contents in a newsletter
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("has_role('NEWSLETTER_ADMIN')")
     **/
    public function createAction(Request $request)
    {
        $configurations = s::get('newsletter_maillist');

        $newsletterContent = array();
        $menu = new \Menu();

        $menu->getMenu('frontpage');
        $i = 1;
        foreach ($menu->items as $item) {
            if ($item->type == 'category' || $item->type == 'internal') {
                $container               = new \stdClass();
                $container->id           = $i;
                $container->title        = $item->title;
                $container->content_type =  'container';
                $container->position     = $item->position;
                $container->items        = array();
                $newsletterContent[]     = $container;
                $i++;
            }
        }

        $availableTimeZones = \DateTimeZone::listIdentifiers();
        $time = new \DateTime();
        $time->setTimezone(new \DateTimeZone($availableTimeZones[s::get('time_zone', 'UTC')]));
        $time = $time->format('d/m/Y');

        return $this->render(
            'newsletter/steps/1-pick-elements.tpl',
            array(
                'name'              => $configurations['name']." [".$time."]",
                'newsletterContent' => $newsletterContent,
                )
        );
    }

    /**
     * Shows the contents of a newsletter given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("has_role('NEWSLETTER_ADMIN')")
     **/
    public function showContentsAction(Request $request)
    {
        $id = $request->query->getDigits('id');
        $newsletter = new \Newsletter($id);

        return $this->render(
            'newsletter/steps/1-pick-elements.tpl',
            array(
                'with_html'         => true,
                'newsletter'        => $newsletter,
                'newsletterContent' => json_decode($newsletter->data),
            )
        );
    }

    /**
     * Saves the newsletter items
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("has_role('NEWSLETTER_ADMIN')")
     **/
    public function saveContentsAction(Request $request)
    {
        $id = (int) $request->request->getDigits('id');
        $contentsRAW = $request->request->get('content_ids');
        $contents = json_decode($contentsRAW);

        $availableTimeZones = \DateTimeZone::listIdentifiers();
        $time = new \DateTime();
        $time->setTimezone(new \DateTimeZone($availableTimeZones[s::get('time_zone', 'UTC')]));
        $time = $time->format('d/m/Y');

        $title = $request->request->filter(
            'title',
            s::get('site_name'). ' ['.$time.']',
            FILTER_SANITIZE_STRING
        );

        $nm = $this->get('newsletter_manager');

        if ($id > 0) {
            $newsletter = new \Newsletter($id);

            $newValues = array(
                'title' => $title,
                'data'  => $contentsRAW,
                'html'  => $nm->render($contents),
            );

            if (is_null($newsletter->html)) {
                $newValues['html'] = $nm->render($contents);
            }

            $newsletter->update($newValues);
        } else {
            $newsletter = new \Newsletter();
            $newsletter->create(
                array(
                    'title'   => $title,
                    'data'    => $contentsRAW,
                    'html'    => $nm->render($contents),
                )
            );
        }

        return $this->redirect(
            $this->generateUrl(
                'admin_newsletter_preview',
                array('id' => $newsletter->pk_newsletter)
            )
        );
    }

    /**
     * Previews the html contents for a newsletter given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("has_role('NEWSLETTER_ADMIN')")
     **/
    public function previewAction(Request $request)
    {
        $id = (int) $request->query->getDigits('id');

        $newsletter = new \Newsletter($id);
        $sessId = 'data-recipients-'.$newsletter->id;
        if (array_key_exists($sessId, $_SESSION)) {
            $_SESSION['data-recipients-'.$newsletter->id] = array();
        }

        return $this->render(
            'newsletter/steps/2-preview.tpl',
            array('newsletter' => $newsletter,)
        );
    }

    /**
     * Description of this action
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("has_role('NEWSLETTER_ADMIN')")
     **/
    public function saveHtmlContentAction(Request $request)
    {
        $id = (int) $request->query->getDigits('id');

        $newsletter = new \Newsletter($id);

        $values = array(
            'title' => $request->request->filter('title', FILTER_SANITIZE_STRING),
            'html'  => $request->request->filter('html', FILTER_SANITIZE_STRING),
        );

        $newsletter->update($values);

        return new Response('ok', 200);
    }

    /**
     * Lists all the available recipients and allows to select them before send
     * the newsletter
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("has_role('NEWSLETTER_ADMIN')")
     **/
    public function pickRecipientsAction(Request $request)
    {
        $id = $request->query->getDigits('id');

        $newsletter = new \Newsletter($id);
        $sbManager = new \Subscriptor();
        $accounts = $sbManager->getUsers('status > 0 AND subscription = 1', '', 'pk_pc_user ASC');

        $mailList   = array();

        $configurations = \Onm\Settings::get('newsletter_maillist');
        if (!is_null($configurations)
            && array_key_exists('email', $configurations)
            && !empty($configurations['email'])
        ) {
            $subscriptor = new \Subscriptor();

            $subscriptor->email = $configurations['email'];
            $subscriptor->name  = $configurations['name'];

            $mailList[] = $subscriptor;
        }

        // Ajax request
        if ($request->isXmlHttpRequest()) {
            return new Response(
                json_encode($accounts),
                200,
                array('Content-type' => 'application/json')
            );
        }

        $recipients = array();

        $sessId = 'data-recipients-'.$newsletter->id;
        if (array_key_exists($id, $_SESSION) && is_string($_SESSION[$sessId])) {
            $recipients = json_decode($_SESSION['data-recipients-'.$newsletter->id]);
        }

        return $this->render(
            'newsletter/steps/3-pick-recipients.tpl',
            array(
                'id'         => $id,
                'accounts'   => $accounts,
                'mailList'   => $mailList,
                'recipients' => $recipients,
                'subscriptionType' => \Onm\Settings::get('newsletter_subscriptionType'),
            )
        );
    }

    /**
     * Sends a newsletter
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("has_role('NEWSLETTER_ADMIN')")
     **/
    public function sendAction(Request $request)
    {
        $id = $request->query->getDigits('id');

        $recipients = $request->request->get('recipients');
        $recipients = json_decode($recipients);

        $sentResult = array();
        $newsletter = new \Newsletter($id);
        $sessId = 'data-recipients-'.$newsletter->id;
        if (array_key_exists($sessId, $_SESSION) && !empty($_SESSION[$sessId])) {
            $this->get('session')->getFlashBag()->add(
                'error',
                sprintf(_('Sorry, newsletter "%d" was been sent previously'), $newsletter->id)
            );

            return $this->redirect($this->generateUrl('admin_newsletters'));
        }

        $_SESSION['data-recipients-'.$newsletter->id] = $recipients;

        $htmlContent = htmlspecialchars_decode($newsletter->html, ENT_QUOTES);

        $newsletterSender = s::get('newsletter_sender');
        $configurations   = s::get('newsletter_maillist');

        if (empty($newsletterSender)) {
            $this->get('session')->getFlashBag()->add(
                'error',
                _('Your newsletter configuration is not complete. You must complete the sender email addres.')
            );

            return $this->redirect($this->generateUrl('admin_newsletters'));
        }

        $params = array(
            'subject'            => $newsletter->title,
            'newsletter_sender'  => $newsletterSender,
            'mail_from'          => $configurations['sender'],
            'mail_from_name'     => s::get('site_name'),
        );

        $maxAllowed = s::get('max_mailing');
        $remaining = $maxAllowed - $this->checkMailing();

        $subject = (!isset($params['subject']))? '[Boletin]': $params['subject'];

        $message = $htmlContent;

        $sent = 0;
        if (!empty($recipients)) {
            foreach ($recipients as $mailbox) {
                if (empty($maxAllowed) || (!empty($maxAllowed) && !empty($remaining))) {
                    try {
                         // Build the message
                        $message = \Swift_Message::newInstance();
                        $message
                            ->setSubject($subject)
                            ->setBody($htmlContent, 'text/html')
                            ->setFrom(array($params['mail_from'] => $params['mail_from_name']))
                            ->setSender($params['newsletter_sender'])
                            ->setTo(array($mailbox->email => $mailbox->name));

                        // Send it
                        $properlySent = $this->get('mailer')->send($message);

                        // $headers   = array();
                        // $headers[] = "MIME-Version: 1.0";
                        // $headers[] = "Content-type: text/html; charset=utf-8";
                        // $headers[] = "From: {$params['mail_from_name']} <{$params['mail_from']}>";
                        // $headers[] = "Sender: {$params['newsletter_sender']}";
                        // $headers[] = "Subject: {$subject}";
                        // $headers[] = "Message-ID: <".$_SERVER['REQUEST_TIME'].md5($_SERVER['REQUEST_TIME'])."@".$_SERVER['SERVER_NAME'].">";
                        // $headers[] = "X-Mailer: PHP/".phpversion();

                        // $properlySent = mail($mailbox->email, $subject, $message, implode("\r\n", $headers), '-f'.$params['newsletter_sender']);

                        $sentResult []= array($mailbox, (bool)$properlySent, _('Unable to deliver your email'));
                        $remaining--;
                        $sent++;
                    } catch (\Exception $e) {
                        $sentResult []= array($mailbox, false, $e->getMessage());
                    }
                } else {
                    $sentResult []= array($mailbox, false, _('Max sents reached.'));
                }
            }
        }


        if (empty($newsletter->sent)) {
            $newsletter->update(array('sent' => $sent));
        } else {
            //duplicated newsletter for count month mail send
            $newsletter->create(
                array(
                    'title'   => $newsletter->title,
                    'data'    => $newsletter->data,
                    'html'    => $newsletter->html,
                    'sent'    => $sent,
                )
            );
        }

        return $this->render(
            'newsletter/steps/4-send.tpl',
            array(
                'sent_result' => $sentResult,
                'newsletter'  => $newsletter,
            )
        );
    }

    /**
     * Deletes an newsletter given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("has_role('NEWSLETTER_ADMIN')")
     **/
    public function deleteAction(Request $request)
    {
        $id = $request->query->getDigits('id');

        if (!empty($id)) {
            $newsletter = new \Newsletter($id);
            $newsletter->delete();

            $this->get('session')->getFlashBag()->add(
                'success',
                _("Newsletter deleted successfully.")
            );
        } else {
            $this->get('session')->getFlashBag()->add(
                'error',
                _('You must give an id for delete a newsletter.')
            );
        }

        if (!$request->isXmlHttpRequest()) {
            return $this->redirect($this->generateUrl('admin_newsletters'));
        } else {
            return new Response('Ok', 200);
        }
    }

    /**
     * Configures the newsletter module
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("has_role('NEWSLETTER_ADMIN')")
     **/
    public function configAction(Request $request)
    {
        if ('POST' == $request->getMethod()) {
            $configurations = array(
                'newsletter_maillist'         => $request->request->get('newsletter_maillist'),
                'newsletter_subscriptionType' => $request->request->get('newsletter_subscriptionType'),
            );

            foreach ($configurations as $key => $value) {
                s::set($key, $value);
            }

            $this->get('session')->getFlashBag()->add(
                'success',
                _('Newsletter module settings saved successfully.')
            );

            return $this->redirect($this->generateUrl('admin_newsletter_config'));
        } else {
            $configurations = s::get(
                array(
                    'newsletter_maillist',
                    'newsletter_subscriptionType',
                    'newsletter_sender',
                    'recaptcha',
                    'max_mailing'
                )
            );

            // Check that user has configured reCaptcha keys if newsletter is enabled
            $missingRecaptcha = false;
            if (empty($configurations['recaptcha']['public_key'])
                || empty($configurations['recaptcha']['private_key'])
            ) {
                $missingRecaptcha = true;
            }

            return $this->render(
                'newsletter/config.tpl',
                array(
                    'configs'           => $configurations,
                    'missing_recaptcha' => $missingRecaptcha,
                )
            );
        }
    }

    /**
     * Checks if the module is activated, if not redirect to the configuration form
     *
     * @return boolean
     **/
    public function checkModuleActivated()
    {
        $sender   = s::get('newsletter_sender');
        $maillist = s::get('newsletter_maillist');
        $type     = s::get('newsletter_subscriptionType');
        $config   = s::get('newsletter_maillist');

        if (is_null($sender) || !$sender) {
            $this->get('session')->getFlashBag()->add(
                'notice',
                _('Please fill the sender email address in the module configuration.')
            );
        }

        if (is_null($maillist) || !$type) {
            $this->get('session')->getFlashBag()->add(
                'notice',
                _('Please fill the mail list email address in the module configuration.')
            );

            return $this->redirect($this->generateUrl('admin_newsletter_config'));
        } else {
            foreach ($config as $key => $value) {
                if ($type == 'submit' || ($key != 'subscription' && $key != 'email')) {
                    if (empty($value)) {
                        $this->get('session')->getFlashBag()->add(
                            'error',
                            _(
                                'Your newsletter configuration is not completed. Please'.
                                ' go to settings and complete the form.'
                            )

                        );

                        return $this->redirect($this->generateUrl('admin_newsletter_config'));
                    }
                }
            }
        }

        return false;
    }

    /**
     * Count sendings. Return total sendings in the month
     *
     * @return boolean
     **/

    public function checkMailing()
    {
        $maxAllowed = s::get('max_mailing');

         //change to last_invoice s::get('site_created')
        $initDate = $this->updateLastInvoice();

        if (empty($initDate)) {
            return false;
        }

        $availableTimeZones = \DateTimeZone::listIdentifiers();
        $today = new \DateTime();
        $today->setTimezone(new \DateTimeZone($availableTimeZones[s::get('time_zone', 'UTC')]));

        $nm = $this->get('newsletter_manager');
        $where =  " updated >= '".$initDate->format('Y-m-d H:i:s')."'
            AND updated <= '".$today->format('Y-m-d H:i:s')."' and sent > 0";
        list($nmCount, $newsletters) = $nm->find($where, 'created DESC');

        $total = 0;
        $result = 0;
        if ($nmCount > 0) {
            foreach ($newsletters as $newsletter) {
                $total += $newsletter->sent;
            }

            if ($maxAllowed > 0) {
                $result = $maxAllowed - $total;
                if ($result <= 0) {
                    $this->get('session')->getFlashBag()->add(
                        'error',
                        _('You have reached the maximum of emails allowed to send')
                    );

                    return $maxAllowed;
                }
            } else {
                return $total;
            }
        }

        return $total;
    }


    public function updateLastInvoice($date = null)
    {

        if ($date === null) {
            $date = s::get('last_invoice');
        }

        $availableTimeZones = \DateTimeZone::listIdentifiers();
        if (empty($date)) {
            $lastInvoice = new \DateTime();
            $lastInvoice->setTimezone(new \DateTimeZone($availableTimeZones[s::get('time_zone', 'UTC')]));
        } else {
            try {
                $lastInvoice = new \DateTime($date);
            } catch (\Exception $e) {
                $lastInvoice = new \DateTime();
                $lastInvoice->setTimezone(new \DateTimeZone($availableTimeZones[s::get('time_zone', 'UTC')]));
            }
        }

        if ($lastInvoice->format('d') > 28) {
            $lastInvoice = $lastInvoice->setDate($lastInvoice->format('Y'), $lastInvoice->format('m'), 28);
        }

        $today     = new \DateTime();
        $today->setTimezone(new \DateTimeZone($availableTimeZones[s::get('time_zone', 'UTC')]));
        $checkDate = new \DateTime($lastInvoice->format('Y-m-d H:i:s'));
        $checkDate->modify('+1 month');

        if ($today > $checkDate) {
            while ($today > $checkDate) {
                $checkDate->modify('+1 month');
            }

            $lastInvoice = $checkDate->modify('-1 month');
        }

        s::set('last_invoice', $lastInvoice->format('Y-m-d H:i:s'));

        return $lastInvoice;

    }
}
