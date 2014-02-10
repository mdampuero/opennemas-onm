<?php
/**
 * Handles the actions for comments
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
use Onm\Settings as s;
use Onm\Message as m;

/**
 * Handles the actions for comments
 *
 * @package Backend_Controllers
 **/
class CommentsController extends Controller
{
    /**
     * Common code for all the actions
     *
     * @return Response the response object
     **/
    public function init()
    {
        //Check if module is activated in this onm instance
        \Onm\Module\ModuleManager::checkActivatedOrForward('COMMENT_MANAGER');

        $this->statuses = array(
            \Comment::STATUS_ACCEPTED => _('Accepted'),
            \Comment::STATUS_REJECTED => _('Rejected'),
            \Comment::STATUS_PENDING  => _('Pending'),
        );

        $this->view->assign('statuses', $this->statuses);
    }

    /**
     * Description of the action
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("has_role('COMMENT_ADMIN')")
     **/
    public function defaultAction(Request $request)
    {
        // Select between comments system
        $commentSystem = s::get('comment_system');

        switch ($commentSystem) {
            case 'onm':
                return $this->redirect($this->generateUrl('admin_comments_list'));
                break;

            case 'disqus':
                return $this->redirect($this->generateUrl('admin_comments_disqus'));
                break;

            case 'facebook':
                return $this->redirect($this->generateUrl('admin_comments_facebook'));
                break;

            default:
                return $this->render('comment/select_module.tpl');
                break;
        }

        return $this->render('comment/select_module.tpl');
    }

    /**
     * Description of the action
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("has_role('COMMENT_ADMIN')")
     **/
    public function selectAction(Request $request)
    {
        $type = $request->query->filter('type', '', FILTER_SANITIZE_STRING);

        switch ($type) {
            case 'onm':
                $this->get('setting_repository')->set('comment_system', 'onm');
                m::add(_("Congratulations! You are now using Opennemas comment system."), m::SUCCESS);
                return $this->redirect($this->generateUrl('admin_comments_list'));
                break;

            case 'disqus':
                return $this->redirect($this->generateUrl('admin_comments_disqus_config'));
                break;

            case 'facebook':
                $this->get('setting_repository')->set('comment_system', 'facebook');
                m::add(_("Congratulations! You are now using Facebook comment system."), m::SUCCESS);
                return $this->redirect($this->generateUrl('admin_comments_facebook_config'));
                break;

            case 'reset':
                return $this->render('comment/select_module.tpl');
                break;

            default:
                m::add(_("Comment data sent not valid."), m::ERROR);
                return $this->redirect($this->generateUrl('admin_comments'));
                break;
        }
    }

    /**
     * Description of the action
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("has_role('COMMENT_ADMIN')")
     **/
    public function defaultDisqusAction(Request $request)
    {
        $disqusShortName = s::get('disqus_shortname');
        $disqusSecretKey = s::get('disqus_secret_key');

        // Check if module is configured, if not redirect to configuration form
        if (!$disqusShortName || !$disqusSecretKey) {
            m::add(_('Please provide your Disqus configuration to start to use your Disqus Comments module'));

            return $this->redirect($this->generateUrl('admin_comments_disqus_config'));
        }

        return $this->render(
            'comment/disqus/list.tpl',
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
     *
     * @Security("has_role('COMMENT_ADMIN')")
     **/
    public function configDisqusAction(Request $request)
    {
        if ($this->request->getMethod() != 'POST') {
            $disqusShortName = s::get('disqus_shortname');
            $disqusSecretKey = s::get('disqus_secret_key');

            return $this->render(
                'comment/disqus/config.tpl',
                array(
                    'shortname' => $disqusShortName,
                    'secretKey' => $disqusSecretKey,
                )
            );
        } else {
            $shortname = $this->request->request->filter('shortname', null, FILTER_SANITIZE_STRING);
            $secretKey = $this->request->request->filter('secret_key', null, FILTER_SANITIZE_STRING);

            if (s::set('disqus_shortname', $shortname) && s::set('disqus_secret_key', $secretKey)) {
                s::set('comment_system', 'disqus');
                return $this->redirect($this->generateUrl('admin_comments_disqus'));
            } else {
                m::add(_('There was an error while saving the Disqus module configuration'), m::ERROR);
            }

            return $this->redirect($this->generateUrl('admin_comments_disqus_config'));
        }
    }


    /**
     * Description of the action
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("has_role('COMMENT_ADMIN')")
     **/
    public function defaultFacebookAction(Request $request)
    {
        $fbSettings = s::get('facebook');

        return $this->render(
            'comment/facebook/list.tpl',
            array(
                'fb_app_id'  => $fbSettings['api_key'],
            )
        );
    }


    /**
     * Shows the disqus configuration form and stores its values
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("has_role('COMMENT_ADMIN')")
     **/
    public function configFacebookAction(Request $request)
    {
        $fbSettings = s::get('facebook');

        if ($this->request->getMethod() != 'POST') {

            $fbAppId = $fbSettings['api_key'];

            return $this->render(
                'comment/facebook/config.tpl',
                array(
                    'fb_app_id'  => $fbAppId,
                )
            );
        } else {
            $fbAppId    = $this->request->request->filter('facebook', null, FILTER_SANITIZE_STRING);
            $fbSettings = array_merge($fbSettings, $fbAppId);

            if (s::set('facebook', $fbSettings)) {
                m::add(_('Facebook configuration saved successfully'), m::SUCCESS);

                return $this->redirect($this->generateUrl('admin_comments_facebook'));
            } else {
                m::add(_('There was an error while saving the Facebook comments module configuration'), m::ERROR);
            }

            return $this->redirect($this->generateUrl('admin_comments_facebook_config'));
        }
    }

    /**
     * Lists comments
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("has_role('COMMENT_ADMIN')")
     **/
    public function listAction(Request $request)
    {
        $page         = $request->query->getDigits('page', 1);
        $itemsPerPage = s::get('items_per_page') ?: 20;
        $filterSearch = $request->query->filter('filter_search', '', FILTER_SANITIZE_STRING);
        $filterStatus = $request->query->filter('filter_status', \Comment::STATUS_PENDING, FILTER_SANITIZE_STRING);


        $searchCriteria =  "`status`='$filterStatus'";
        if (!empty($filterSearch)) {
            $searchCriteria .= " AND `body` LIKE '%$filterSearch%'";
        }
        $commentManager = $this->get('comment_repository');
        $commentsCount  = $commentManager->count($searchCriteria);
        $comments       = $commentManager->findBy($searchCriteria, 'date DESC', $itemsPerPage, $page);

        // Build the pager
        $pagination = \Onm\Pager\Slider::create(
            $commentsCount,
            $itemsPerPage,
            $this->generateUrl(
                'admin_comments_list',
                array('filter_status' => $filterStatus, 'filter_search' => $filterSearch,)
            )
        );

        if (!empty($comments)) {
            foreach ($comments as &$comment) {
                $comment->content = new \Content($comment->content_id);
                $comment->votes   = new \Vote($comment->id);
            }
        }

        return $this->render(
            'comment/list.tpl',
            array(
                'comments'      => $comments,
                'pagination'    => $pagination,
                'filter_status' => $filterStatus,
                'filter_search' => $filterSearch,
                'statuses'      => $this->statuses,
            )
        );
    }

    /**
     * Shows coments in the edit form
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("has_role('COMMENT_UPDATE')")
     **/
    public function showAction(Request $request)
    {
        $id = $request->query->getDigits('id');

        if (!is_null($id)) {
            $comment = new \Comment($id);
            $comment->content = new \Content($comment->content_id);

            return $this->render(
                'comment/read.tpl',
                array('comment' => $comment)
            );
        } else {
            m::add(sprintf(_('Comment with id "%d" doesn\'t exists.'), $id), m::ERROR);

            return $this->redirect($this->generateUrl('admin_comments_list'));
        }
    }

    /**
     * Updates a comment given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("has_role('COMMENT_UPDATE')")
     **/
    public function updateAction(Request $request)
    {
        $id      = $request->query->getDigits('id');
        $comment = new \Comment($id);

        // Check empty data
        if (count($request->request) < 1) {
            m::add(_("Comment data sent not valid."), m::ERROR);

            return $this->redirect($this->generateUrl('admin_comment_show', array('id' => $id)));
        }

        $data = array(
            'status' => $request->request->filter('status'),
            'body'   => $request->request->filter('body', '', FILTER_SANITIZE_STRING),
        );

        try {
            $comment->update($data);

            m::add(_('Comment saved successfully.'), m::SUCCESS);
        } catch (\Exception $e) {
            m::add($e->getMessage(), m::ERROR);
        }

        return $this->redirect($this->generateUrl('admin_comment_show', array('id' => $id)));
    }

    /**
     * Deletes a comment given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("has_role('COMMENT_DELETE')")
     **/
    public function deleteAction(Request $request)
    {
        $id = $request->query->getDigits('id');

        try {
            $comment = new \Comment();
            $comment->delete($id);

            m::add(_('Comment deleted successfully.'), m::SUCCESS);
        } catch (\Exception $e) {
            m::add($e->getMessage(), m::ERROR);
        }

        return $this->redirect($this->generateUrl('admin_comments_list'));
    }

    /**
     * Toggle status in comment given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("has_role('COMMENT_AVAILABLE')")
     **/
    public function toggleStatusAction(Request $request)
    {
        $id     = $request->query->getDigits('id');
        $status = $request->query->filter('status');

        try {
            $comment = new \Comment($id);
            $comment->setStatus($status);

            m::add(sprintf(_("Comment status changed to '%s'."), $this->statuses[$status]));
        } catch (\Exception $e) {
            m::add($e->getMessage(), m::ERROR);
        }

        $params = array(
            'page'   => $request->query->getDigits('page', 1),
            'filter_search' => $request->query->filter('search', '', FILTER_SANITIZE_STRING),
            'filter_status' => $request->query->filter('return_status', 'accepted', FILTER_SANITIZE_STRING)
        );

        return $this->redirect($this->generateUrl('admin_comments_list', $params));
    }

    /**
     * Change  status some comments given its ids
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("has_role('COMMENT_AVAILABLE')")
     **/
    public function batchStatusAction(Request $request)
    {
        // Get request data
        $selected = $request->query->get('selected_fld');
        $status   = $request->query->filter('status', 'accepted');

        if (count($selected) > 0) {

            // Iterate over each comment and update its status
            $success = 0;
            foreach ($selected as $id) {
                try {

                    $comment = new \Comment($id);
                    $comment->setStatus($status);
                    $success++;
                } catch (\Exception $e) {
                    m::add(
                        sprintf(_('Comment id %s: ').$e->getMessage(), $id),
                        m::ERROR
                    );
                }
            }

            if ($success > 0) {
                m::add(
                    sprintf(
                        _("Successfully changed the status to '%s' to %d comments."),
                        $this->statuses[$status],
                        $success
                    ),
                    m::SUCCESS
                );
            }
        }

        $params = array(
            'page'     => $request->query->getDigits('page', 1),
            'filter_status'   => $status,
        );

        return $this->redirect($this->generateUrl('admin_comments_list', $params));

    }

    /**
     * Deletes multiple comments given their ids
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("has_role('COMMENT_DELETE')")
     **/
    public function batchDeleteAction(Request $request)
    {
        $selected = $request->query->get('selected_fld');

        if (count($selected) > 0) {
            $success = 0;
            foreach ($selected as $id) {
                try {
                    $comment = new \Comment($id);
                    $comment->delete($id);
                    $success++;
                } catch (\Exception $e) {
                    m::add(
                        sprintf(_('Comment id %s: ').$e->getMessage(), $id),
                        m::ERROR
                    );
                }
            }

            if ($success > 0) {
                m::add(sprintf(_("%d comments deleted successfully."), $success), m::SUCCESS);
            }
        } else {
            m::add(_('You haven\'t selected any comment to delete.'), m::ERROR);
        }

        $params = array(
            'page'          => $request->query->getDigits('page', 1),
            'filter_status' => $request->query->filter('filter_status', \Comment::STATUS_PENDING),
            'filter_search' => $request->query->filter('filter_search', null, FILTER_SANITIZE_STRING),
        );

        return $this->redirect($this->generateUrl('admin_comments_list', $params));
    }

    /**
     * Shows the disqus configuration form and stores its values
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("has_role('COMMENT_ADMIN')")
     **/
    public function configAction(Request $request)
    {
        if ('POST' == $this->request->getMethod()) {
            $configs = $this->request->request->filter('configs', array(), FILTER_SANITIZE_STRING);

            $defaultConfigs = array(
                'moderation'      => false,
                'number_elements' => 10,
            );
            $configs = array_merge($defaultConfigs, $configs);

            if (s::set('comments_config', $configs)) {
                m::add(_('Settings saved.'), m::SUCCESS);
            } else {
                m::add(_('There was an error while saving the settings'), m::ERROR);
            }

            return $this->redirect($this->generateUrl('admin_comments_config'));
        } else {
            $configs = s::get('comments_config');

            return $this->render('comment/config.tpl', array('configs' => $configs,));
        }
    }
}
