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
namespace Backend\Controllers;

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

        // Check if the user can admin video
        $this->checkAclOrForward('COMMENT_ADMIN');

        $this->statuses = array(
            \Comment::STATUS_ACCEPTED => _('Accepted'),
            \Comment::STATUS_REJECTED => _('Rejected'),
            \Comment::STATUS_PENDING  => _('Pending'),
        );

        $this->view->assign('statuses', $this->statuses);
    }

    /**
     * Lists comments
     *
     * @param Request $request the request object
     *
     * @return Response the response object
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
                'admin_comments',
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
                'comments'   => $comments,
                'pagination' => $pagination,
                'filter_status'     => $filterStatus,
                'filter_search'     => $filterSearch,
                'statuses'   => $this->statuses,
            )
        );
    }

    /**
     * Shows coments in the edit form
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function showAction(Request $request)
    {
        $this->checkAclOrForward('COMMENT_UPDATE');

        $id      = $request->query->getDigits('id');

        if (!is_null($id)) {
            $comment = new \Comment($id);
            $comment->content = new \Content($comment->content_id);

            return $this->render(
                'comment/read.tpl',
                array('comment' => $comment)
            );
        } else {
            m::add(sprintf(_('Comment with id "%d" doesn\'t exists.'), $id), m::ERROR);

            return $this->redirect($this->generateUrl('admin_comments'));
        }
    }

    /**
     * Updates a comment given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function updateAction(Request $request)
    {
        $this->checkAclOrForward('COMMENT_UPDATE');

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
     **/
    public function deleteAction(Request $request)
    {
        $this->checkAclOrForward('COMMENT_DELETE');

        $id = $request->query->getDigits('id');

        try {
            $comment = new \Comment();
            $comment->delete($id);

            m::add(_('Comment deleted successfully.'), m::SUCCESS);
        } catch (\Exception $e) {
            m::add($e->getMessage(), m::ERROR);
        }

        return $this->redirect($this->generateUrl('admin_comments'));
    }

    /**
     * Toggle status in comment given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function toggleStatusAction(Request $request)
    {
        $this->checkAclOrForward('COMMENT_AVAILABLE');

        $id           = $request->query->getDigits('id');
        $status       = $request->query->filter('status');

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

        return $this->redirect($this->generateUrl('admin_comments', $params));
    }

    /**
     * Change  status some comments given its ids
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function batchStatusAction(Request $request)
    {
        $this->checkAclOrForward('COMMENT_AVAILABLE');

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

        return $this->redirect($this->generateUrl('admin_comments', $params));

    }

    /**
     * Deletes multiple comments given their ids
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function batchDeleteAction(Request $request)
    {
        $this->checkAclOrForward('COMMENT_DELETE');

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

        return $this->redirect($this->generateUrl('admin_comments', $params));
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
