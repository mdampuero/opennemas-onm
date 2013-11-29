<?php
/**
 * Handles the actions for the Disqus comments
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
use Onm\Settings as s;
use Onm\Message as m;

/**
 * Handles the actions for the Disqus comments
 *
 * @package Backend_Controllers
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
        // Check MODULE
        \Onm\Module\ModuleManager::checkActivatedOrForward('COMMENT_DISQUS_MANAGER');
        // Check ACL
        $this->checkAclOrForward('COMMENT_ADMIN');
    }

    /**
     * Description of the action
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function defaultAction(Request $request)
    {
        $disqusShortName = s::get('disqus_shortname');
        $disqusSecretKey = s::get('disqus_secret_key');

        // Check if module is configured, if not redirect to configuration form
        if (!$disqusShortName || !$disqusSecretKey) {
            m::add(_('Please provide your Disqus configuration to start to use your Disqus Comments module'));

            return $this->redirect($this->generateUrl('admin_comments_disqus_config'));
        }

        return $this->render(
            'disqus/list.tpl',
            array(
                'disqus_shortname'  => $disqusShortName,
                'disqus_secret_key' => $disqusSecretKey,
            )
        );
    }

    /**
     * Shows the disqus configuration form and stores its values
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function configAction(Request $request)
    {
        if ($this->request->getMethod() != 'POST') {
            $disqusShortName = s::get('disqus_shortname');
            $disqusSecretKey = s::get('disqus_secret_key');

            return $this->render(
                'disqus/config.tpl',
                array(
                    'shortname' => $disqusShortName,
                    'secretKey' => $disqusSecretKey,
                )
            );
        } else {
            $shortname = $this->request->request->filter('shortname', null, FILTER_SANITIZE_STRING);
            $secretKey = $this->request->request->filter('secret_key', null, FILTER_SANITIZE_STRING);

            if (s::set('disqus_shortname', $shortname) && s::set('disqus_secret_key', $secretKey)) {
                m::add(_('Disqus configuration saved successfully'), m::SUCCESS);
            } else {
                m::add(_('There was an error while saving the Disqus module configuration'), m::ERROR);
            }

            return $this->redirect($this->generateUrl('admin_comments_disqus_config'));
        }
    }

    /**
     * Synchronize disqus comments to local database
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function syncAction(Request $request)
    {
        // Update disqus last sync cache with time and uuid
        $this->container->get('cache')->save(
            CACHE_PREFIX.'disqus_last_sync',
            array('time' => time(), 'uuid' => uniqid()),
            300
        );

        // Save disqus comments to database
        \Onm\DisqusSync::saveDisqusCommentsToDatabase();

        return $this->redirect($this->generateUrl('admin_comments_disqus'));
    }
}
