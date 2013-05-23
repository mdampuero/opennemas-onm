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
        // Check if the module is activated, if not redirect to the config form
        $configuredRedirection = $this->checkModuleActivated();
        if ($configuredRedirection != false) {
            return $configuredRedirection;
        }
        $itemsPerPage = 20;
        $page = $request->query->getDigits('page', 1);
        $nm = new \NewsletterManager();
        list($nmCount, $newsletters) = $nm->find('1 = 1', 'created DESC', $page, $itemsPerPage);

        // Build the pager
        $pagination = \Pager::factory(
            array(
                'mode'        => 'Sliding',
                'perPage'     => $itemsPerPage,
                'append'      => false,
                'path'        => '',
                'delta'       => 4,
                'clearIfVoid' => true,
                'urlVar'      => 'page',
                'totalItems'  => $nmCount,
                'fileName'    => $this->generateUrl(
                    'admin_newsletters'
                ).'?page=%d',
            )
        );

        return $this->render(
            'newsletter/list.tpl',
            array(
                'newsletters'     => $newsletters,
                'pagination' => $pagination,
                'page'       => $page,
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
        $configurations = \Onm\Settings::get('newsletter_maillist');

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
        $newsletter = new \NewNewsletter($id);

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

        $nm = new \NewsletterManager();

        if ($id > 0) {
            $newsletter = new \NewNewsletter($id);
            $nm = new \NewsletterManager();

            $newValues = array(
                'title' => $title,
                'data'  => $contentsRAW,
                'html'    => $nm->render($contents),
            );

            if (is_null($newsletter->html)) {
                $newValues['html'] = $nm->render($contents);
            }

            $newsletter->update($newValues);
        } else {
            $newsletter = new \NewNewsletter();
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

        $newsletter = new \NewNewsletter($id);

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

        $newsletter = new \NewNewsletter($id);

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

        $newsletter = new \NewNewsletter($id);
        $newsletterOld = new \Newsletter(array('namespace' => 'PConecta_'));
        $account    = $newsletterOld->getAccountsProvider();
        $accounts   = $account->getAccounts();
        $receiver   = array();
        $mailList   = array();

        $configurations = \Onm\Settings::get('newsletter_maillist');
        if (!is_null($configurations)
            && array_key_exists('email', $configurations)
            && !empty($configurations['email'])
        ) {
            $mailList[] = new \Newsletter_Account(
                $configurations['email'],
                $configurations['name']
            );
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

        $newsletter = new \NewNewsletter($id);

        $_SESSION['data-recipients-'.$newsletter->id] = array();

        $nManager = new \NewsletterManager();
        $nManager->setConfigMailing();

        $htmlContent = htmlspecialchars_decode($newsletter->html, ENT_QUOTES);

        $configurations = \Onm\Settings::get('newsletter_maillist');
        if (array_key_exists('newsletter_sender', $configurations)
            && !empty($configurations['newsletter_sender'])
        ) {
            $mail_from = $configurations['newsletter_sender'];
        } elseif (array_key_exists('sender', $configurations)
            && !empty($configurations['sender'])
        ) {
            $mail_from = $configurations['sender'];
        } else {
            $mail_from = MAIL_FROM;
        }

        // TODO: Fetch this params from the container
        $params = array(
            'subject'        => $newsletter->title,
            'mail_host'      => MAIL_HOST,
            'mail_user'      => MAIL_USER,
            'mail_pass'      => MAIL_PASS,
            'mail_from'      => $mail_from,
            'mail_from_name' => s::get('site_name'),
        );

        $sentResult = array();
        $countMailing = 0;
        foreach ($recipients as $mailbox) {
            // Replace name destination
            $emailHtmlContent = str_replace('###DESTINATARIO###', $mailbox->name, $htmlContent);

            // Send the mail
            $properlySent = $nManager->sendToUser($mailbox, $emailHtmlContent, $params);
         //   if ($properlySent) {
                $countMailing++;
           // }
            // Register the
            $sentResult[]= array(
                $mailbox,
                $properlySent
            );
        }

        $newsletter->update(array('sent' => $countMailing));
        $this->updateMailing(array('counter' => $countMailing));

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
            $newsletter = new \NewNewsletter($id);
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
                    'newsletter_mailling_counter',
                    'max_mailing',
                )
            );

            // Check that user has configured reCaptcha keys if newsletter is enabled
            $missingRecaptcha = false;
            if (empty($configurations['recaptcha']['public_key'])
                || empty($configurations['recaptcha']['private_key'])
            ) {
                $missingRecaptcha = true;
            }

            $date = new \DateTime();

            $keyMonth ='counter_'.$date->format("m");
            if (array_key_exists($keyMonth, $configurations['newsletter_mailling_counter'])
                && !is_null($configurations['newsletter_mailling_counter'][$keyMonth])) {

                $counterValue = $configurations['newsletter_mailling_counter'][$keyMonth];
                if ($counterValue >= $configurations['max_mailing']) {
                    m::add(_('You have send max mailing allowed').$counterValue);
                }
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
     * Updates the counter by month with given an array of data
     *
     * @param array $newdata array with data for update
     *
     * @return NewNewsletter the object instance
     **/
    public function updateMailing($newdata)
    {
        $configurations = s::get('newsletter_mailling_counter');
        if (array_key_exists('counter', $newdata) && !is_null($newdata['counter'])) {
            $counter = $newdata['counter'];
            $date = new \DateTime();
            $keyMonth ='counter_'.$date->format("m");
            if (!empty($configurations)
                && array_key_exists($keyMonth, $configurations)
                && !is_null($configurations[$keyMonth])) {

                foreach ($configurations as $key => $value) {
                    if ($key == $keyMonth) {
                        $value = (int)$value + $counter;
                        $configurations[$keyMonth] = $value;
                        if ($value >= s::get('max_mailing')) {
                            m::add(_('You have send max mailing allowed'));
                        }
                    }
                }
            } else {
                $configurations[$keyMonth] = $counter;
            }

            s::set('newsletter_mailling_counter', $configurations);

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
            || !(s::get('newsletter_enable'))
        ) {
            m::add(
                _('Please provide your Newsletter configuration to start to use your Newsletter module')
            );

            return $this->redirect($this->generateUrl('admin_newsletter_config'));
        } else {
            $configurations = s::get('newsletter_maillist');
            foreach ($configurations as $key => $value) {
                if ($key != 'receiver' && empty($value)) {
                    m::add(
                        _(
                            'Your newsletter configuration is not complete. Please'.
                            ' go to settings and complete the form.'
                        ),
                        m::ERROR
                    );

                    return $this->redirect($this->generateUrl('admin_newsletter_config'));
                }
            }
        }

        return false;
    }
}
