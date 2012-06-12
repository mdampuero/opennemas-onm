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

use Onm\Framework\Controller\Controller,
    Onm\Message as m,
    Onm\Settings as s;
/**
 * Handles the actions for the system information
 *
 * @package Backend_Controllers
 * @author
 **/
class CommentsDisqusController extends Controller
{

    /**
     * Common code for all the actions
     *
     * @return void
     **/
    public function init()
    {
        $this->view = new \TemplateAdmin(TEMPLATE_ADMIN);

        // Check MODULE
        \Onm\Module\ModuleManager::checkActivatedOrForward('COMMENT_DISQUS_MANAGER');
        // Check ACL
        \Acl::checkOrForward('COMMENT_ADMIN');


        /**
         * Check if module is configured, if not redirect to configuration form
        */
        if (is_null(s::get('disqus_shortname')) && $action != 'config'
        ) {
            m::add(_('Please provide your Disqus configuration to start to use your Disqus Comments module'));

            return $this->redirect(url('admin_comments_disqus_config'));
        }
    }

    /**
     * Description of the action
     *
     * @return void
     **/
    public function defaultAction()
    {
        $disqusConfig = s::get('disqus_shortname');

        return $this->render('disqus/list.tpl', array(
            'disqus_shortname' => $disqusConfig,
        ));
    }

    /**
     * Shows the disqus configuration form and stores its values
     *
     * @return string the response string
     **/
    public function configAction()
    {
        if ($this->request->getMethod() != 'POST') {
            if ($disqusConfig = s::get('disqus_shortname')) {
                $message = $this->request->query->filter('message', null, FILTER_SANITIZE_STRING);
            }

            return $this->render('disqus/config.tpl', array(
                'shortname'    => $disqusConfig,
            ));
        } else {
            $shortname = $this->request->request->filter('shortname', null, FILTER_SANITIZE_STRING);
            if (!isset($shortname)) {
                return $this->redirect(url('admin_comments_disqus_config'));
            }

            if (s::set('disqus_shortname', $shortname)) {
                m::add(_('Disqus configuration saved successfully'), m::SUCCESS);
            } else {
                m::add(_('There was an error while saving the Disqus module configuration'), m::ERROR);
            }

            return $this->redirect(url('admin_comments_disqus_config'));
        }
    }
} // END class PruebaController
