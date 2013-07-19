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
namespace Backend\Controllers;

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

        $this->checkAclOrForward('NEWSLETTER_ADMIN');

        $this->view = new \TemplateAdmin(TEMPLATE_ADMIN);
    }

    /**
     * Lists all the available newsletters
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function listAction(Request $request)
    {
        $newsletterManager = $this->get('newsletter_manager');

        // Check if the module is configured, if not redirect to the config form
        $configuredRedirection = $this->checkModuleActivated();

        if ($configuredRedirection != false) {
            return $configuredRedirection;
        }

        $nm = $this->get('newsletter_manager');
        list($count, $newsletters) = $nm->find();

        return $this->render(
            'newsletter/list.tpl',
            array(
                'newsletters' => $newsletters,
                'count'       => $count,
            )
        );
    }

    /**
     * List the form for create or load contents in a newsletter
     *
     * @param Request $request the request object
     *
     * @return Response the response object
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

        return $this->render(
            'newsletter/steps/1-pick-elements.tpl',
            array(
                'name'              => $configurations['name']." [".date('d/m/Y')."]",
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
     **/
    public function saveContentsAction(Request $request)
    {
        $id = (int) $request->request->getDigits('id');
        $contentsRAW = $request->request->get('content_ids');
        $contents = json_decode($contentsRAW);
        $title = $request->request->filter(
            'title',
            s::get('site_name'). ' ['.date('d/m/Y').']',
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
     **/
    public function previewAction(Request $request)
    {
        $id = (int) $request->query->getDigits('id');

        $newsletter = new \Newsletter($id);

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
     * Lists all the available recipients and allos to select them before send
     * the newsletter
     *
     * @param Request $request the request object
     *
     * @return Response the response object
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
            header('Content-type: application/json');
            echo json_encode($accounts);
            exit(0);
        }

        $recipients = array();

        $sessId = 'data-recipients-'.$newsletter->id;
        if (array_key_exists($id, $_SESSION) && is_string($_SESSION[$sessID])) {
            $recipients = json_decode($_SESSION['data-recipients-'.$newsletter->id]);
        }

        return $this->render(
            'newsletter/steps/3-pick-recipients.tpl',
            array(
                'id'         => $id,
                'accounts'   => $accounts,
                'mailList'   => $mailList,
                'recipients' => $recipients,
            )
        );
    }

    /**
     * Sends a newsletter
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function sendAction(Request $request)
    {
        $id = $request->query->getDigits('id');

        $recipients = $request->request->get('recipients');
        $recipients = json_decode($recipients);

        $newsletter = new \Newsletter($id);

        $_SESSION['data-recipients-'.$newsletter->id] = array();

        $nManager = $this->get('newsletter_manager');

        $htmlContent = htmlspecialchars_decode($newsletter->html, ENT_QUOTES);

        $configurations = \Onm\Settings::get('newsletter_maillist');
        if (array_key_exists('sender', $configurations)
            && !empty($configurations['sender'])
        ) {
            $mailFrom = $configurations['sender'];
        } else {
            $mailFrom = MAIL_FROM;
        }

        $params = array(
            'subject'        => $newsletter->title,
            'mail_from'      => $mailFrom,
            'mail_from_name' => s::get('site_name'),
        );

        $sentResult = array();
        foreach ($recipients as $mailbox) {
            // Replace name destination
            $emailHtmlContent = str_replace('###DESTINATARIO###', $mailbox->name, $htmlContent);

            try {
                // Send the mail
                $properlySent = $nManager->sendToUser($mailbox, $emailHtmlContent, $params);
                $sentResult []= array($mailbox, $properlySent);
            } catch (\Exception $e) {
                $sentResult []= array($mailbox, false);
            }

        }

        $newsletter->update(array('sent' => 1));

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
     **/
    public function deleteAction(Request $request)
    {
        $id = $request->query->getDigits('id');

        if (!empty($id)) {
            $newsletter = new \Newsletter($id);
            $newsletter->delete();

            m::add(_("Newsletter deleted successfully."), m::SUCCESS);
        } else {
            m::add(_('You must give an id for delete a newsletter.'), m::ERROR);
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
     **/
    public function configAction(Request $request)
    {
        if ('POST' == $request->getMethod()) {
            $configurations = array(
                'newsletter_maillist'         => $request->request->get('newsletter_maillist'),
                'newsletter_subscriptionType' => $request->request->get('newsletter_subscriptionType'),
                'newsletter_enable'           => $request->request->get('newsletter_enable'),
                'newsletter_subscription'     => 1,
            );

            foreach ($configurations as $key => $value) {
                s::set($key, $value);
            }

            m::add(_('Newsletter module settings saved successfully.'), m::SUCCESS);

            return $this->redirect($this->generateUrl('admin_newsletter_config'));
        } else {
            $configurations = s::get(
                array(
                    'newsletter_maillist',
                    'newsletter_subscriptionType',
                    'newsletter_enable',
                    'recaptcha',
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
        if (is_null(s::get('newsletter_maillist'))
            || !(s::get('newsletter_subscriptionType') )
        ) {
            m::add(
                _('Please provide your Newsletter configuration to start to use your Newsletter module')
            );

            return $this->redirect($this->generateUrl('admin_newsletter_config'));
        } else {
            $configurations = s::get('newsletter_maillist');

        //     foreach ($configurations as $key => $value) {
        //         if ($key != 'receiver' && empty($value)) {
        //             m::add(
        //                 _(
        //                     'Your newsletter configuration is not complete. Please'.
        //                     ' go to settings and complete the form.'
        //                 ),
        //                 m::ERROR
        //             );

        //             return $this->redirect($this->generateUrl('admin_newsletter_config'));
        //         }
        //     }
        }

        return false;
    }
}
