<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Backend\Controllers;

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

        $nm = new \NewsletterManager();
        $newsletters = $nm->find();

        return $this->render('newsletter/list.tpl', array(
            'newsletters'     => $newsletters
        ));
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

        return $this->render('newsletter/steps/1-pick-elements.tpl');
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
        $contentsRAW = $request->request->get('contentids');
        $contents = json_decode(json_decode($contentsRAW));

        $newsletter = new \NewNewsletter();
        $nm         = new \NewsletterManager();

        if ($id > 0) {
            $newsletter->update(array(
                'data' => $contentsRAW,
            ));
        } else {
            $newsletter->create(array(
                'subject' => s::get('site_name') + ' ['.date('%d/%m/%Y').']',
                'data'    => $contentsRAW,
                'html'    => $nm->render($contents),
            ));
        }

        return $this->redirect($this->generateUrl('admin_newsletter_preview', array('id' => $newsletter->id)));
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

        return $this->render('newsletter/steps/2-preview.tpl', array(
            'newsletter' => $newsletter,
        ));
    }

    /**
     * Description of this action
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function saveHTMLContentAction(Request $request)
    {
        $id = (int) $request->request->getDigits('id');

        $newsletter = new \NewNewsletter($id);

        $newsletter->update(array(
            'subject' => $request->request->filter('subject', FILTER_SANITIZE_STRING),
            'html'    => $request->request->filter('subject', FILTER_SANITIZE_STRING),
        ));

        return $this->redirect($this->generateUrl(
            'admin_newsletter_pick_recipients',
            array('id' => $newsletter->id))
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

            return $this->redirect($this->generateUrl('admin_newsletters'));
        } else {
            $configurations = s::get(array(
                'newsletter_maillist',
                'newsletter_subscriptionType',
                'newsletter_enable',
                'recaptcha',
            ));

            // Check that user has configured reCaptcha keys if newsletter is enabled
            $missingRecaptcha = false;
            if (empty($configurations['recaptcha']['public_key'])
                 || empty($configurations['recaptcha']['private_key'])
            ) {
                $missingRecaptcha = true;
            }

            return $this->render('newsletter/config.tpl', array(
                'configs'           => $configurations,
                'missing_recaptcha' => $missingRecaptcha,
            ));
        }
    }

    /**
     * Checks if the module is activated, if not redirect to the configuration form
     *
     * @return void
     **/
    public function checkModuleActivated()
    {
        if (is_null(s::get('newsletter_maillist'))
          || !(s::get('newsletter_subscriptionType') )
          || !(s::get('newsletter_enable'))
        ) {
            m::add(_('Please provide your Newsletter configuration to start to use'.
                ' your Newsletter module'));

            return $this->redirect($this->generateUrl('admin_newsletter_config'));
        } else {
            $configurations = s::get('newsletter_maillist');
            foreach ($configurations as $key => $value) {
                if ($key != 'receiver' && empty($value)) {
                    m::add(_('Your newsletter configuration is not complete. Please'.
                        ' go to settings and complete the form.'), m::ERROR);
                    return $this->redirect($this->generateUrl('admin_newsletter_config'));
                }
            }
        }

        return false;
    }

} // END class NewsletterController