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

        $this->view = new \TemplateAdmin(TEMPLATE_ADMIN);

        $this->category = $this->get('request')
            ->query->filter('category', 'all', FILTER_SANITIZE_STRING);

        $this->ccm = \ContentCategoryManager::get_instance();
        list($this->parentCategories, $this->subcat, $this->categoryData) =
            $this->ccm->getArraysMenu($this->category);
        if (empty($this->category)) {
            $this->category ='widget';
        }

        $content_types = array(
            1  => _('Article'),
            4  => _('Opinion'),
            7  => _('Album'),
            9  => _('Video'),
            11 => _('Poll')
        );

        $this->view->assign(array(
            'category'      => $this->category,
            'subcat'        => $this->subcat,
            'allcategorys'  => $this->parentCategories,
            'datos_cat'     => $this->categoryData,
            'content_types' => $content_types,
        ));
    }

    /**
     *  Lists comments
     *
     * @return Symfony\Component\HttpFoundation\Response the response object
     **/
    public function listAction()
    {

        $request = $this->get('request');

        $page          = $request->query->getDigits('page', 1);
        $itemsPerPage  = s::get('items_per_page') ?: 20;
        $requestFilter = $request->query->get('filter', null);
        $status          = $request->query->getDigits('status', 0);

        if (is_array($requestFilter)
            && array_key_exists('status', $requestFilter)
        ) {
            $status = $requestFilter['status'];
        }
        $filter = ' contents.in_litter != 1 AND contents.content_status = '
                    . $status;

        $module = 0;
        if (is_array($requestFilter)
            && array_key_exists('module', $requestFilter)
        ) {
            $module = $requestFilter['module'];
        }

        if ($this->category == 'all') {
            $categoryForLimit = null;
        } else {
            $categoryForLimit = $this->category;
        }

        $cm           = new \ContentManager();
        $itemsPerPage = s::get('items_per_page');

        if ($module != 0) {
            $allComments   = $cm->find_all('Comment', $filter,
                ' ORDER BY  created DESC');
            $comments      = array();
            $commentsCount = 0;
            foreach ($allComments as $comm) {
                $comm->content_type =
                    \ContentType::getContentTypeByContentId($comm->fk_content);

                if ($comm->content_type == $module) {
                    $commentsCount++;
                    $comments[] = $comm;
                }
            }
        } else {
            list($commentsCount, $comments) = $cm->getCountAndSlice(
                'comment',
                $categoryForLimit,
                $filter,
                'ORDER BY created DESC',
                $page,
                $itemsPerPage
            );
        }

        // Build the pager
        $pagination = \Pager::factory(array(
            'mode'        => 'Sliding',
            'perPage'     => $itemsPerPage,
            'append'      => false,
            'path'        => '',
            'delta'       => 4,
            'clearIfVoid' => true,
            'urlVar'      => 'page',
            'totalItems'  => $commentsCount,
            'fileName'    => $this->generateUrl(
                'admin_comments',
                array('category' => $this->category)
            ).'&page=%d',
        ));

        $contents = array();
        $votes = array();
        if (!empty($comments)) {
            // Get titles
            $i = 0;

            foreach ($comments as $comment) {
                $contents[$i] = new \Content( $comment->fk_content );

                $contents[$i]->category_name  =
                    $contents[$i]->loadCategoryName($comment->fk_content);
                $contents[$i]->category_title =
                    $this->ccm->get_title($comment->category_name);

                $votes[$i] = new \Vote( $comment->pk_comment );
                $i++;
            }
        }

        return $this->render('comment/list.tpl', array(
            'comments'   => $comments,
            'pagination' => $pagination,
            'contents'   => $contents,
            'votes'      => $votes,
            'module'     => $module,
            'status'     => $status,
        ));
    }

    /**
     * Shows coments in the edit form
     *
     * @return Symfony\Component\HttpFoundation\Response the response object
     **/
    public function showAction()
    {
        $this->checkAclOrForward('COMMENT_UPDATE');

        $request = $this->get('request');
        $id      = $request->query->getDigits('id');

        if (!is_null($id)) {
            $comment = new \Comment();
            $comment->read($id);
            $content = new \Content( $comment->fk_content );

            return $this->render('comment/read.tpl', array(
                'id'      => $id,
                'comment' => $comment,
                'content' => $content,
            ));
        } else {
            m::add(sprintf(_('Comment with id "%d" doesn\'t exists.'), $id),
                m::ERROR);

            return $this->redirect($this->generateUrl('admin_comments'));
        }
    }

    /**
     * Updates a comment given its id
     *
     * @return Response the response object
     **/
    public function updateAction()
    {
        $this->checkAclOrForward('COMMENT_UPDATE');

        $request = $this->get('request');
        $id      = $request->query->getDigits('id');
        $comment = new \Comment($id);

        if (!is_null($comment->pk_comment)) {
            $comment->update( $_POST );
            m::add(_('Comment saved successfully.'), m::SUCCESS);
        }

        $params = array(
            'page'     => $request->query->getDigits('page', 1),
            'category' => $this->category,
            'status'   => $comment->content_status
        );
        return $this->redirect($this->generateUrl('admin_comments', $params));

    }

    /**
     * Deletes a comment given its id
     *
     * @return Symfony\Component\HttpFoundation\Response the response object
     **/
    public function deleteAction()
    {
        $this->checkAclOrForward('COMMENT_DELETE');

        $request = $this->get('request');
        $id      = $request->query->getDigits('id');

        $comment = new \Comment();
        $comment->delete($id, $_SESSION['userid']);
        $params = array(
            'page'     => $request->query->getDigits('page', 1),
            'category' => $comment->category,
            'status'   => $comment->content_status
        );
        m::add(_('Comment deleted successfully.'), m::SUCCESS);

        return $this->redirect($this->generateUrl('admin_comments'));
    }

    /**
     * Toggle status in comment given its id
     *
     * @return Symfony\Component\HttpFoundation\Response the response object
     **/
    public function toggleStatusAction()
    {
        $this->checkAclOrForward('COMMENT_AVAILABLE');

        $request = $this->get('request');
        $status  = $request->query->getDigits('status');
        $id      = $request->query->getDigits('id');

        $comment = new \Comment($id);
        if ($status == 2) {
            $comment->set_status($status, $_SESSION['userid']);
            m::add(_('Comment was rejected successfully.'), m::SUCCESS);
        } else {
            $comment->set_available($status, $_SESSION['userid']);
            m::add(_('Comment was published successfully.'), m::SUCCESS);
        }

        $params = array(
            'page'     => $request->query->getDigits('page', 1),
            'category' => $request->query->filter('category'),
            'status'   => $status
        );

        return $this->redirect($this->generateUrl('admin_comments', $params));

    }

    /**
     * Change  status some comments given its ids
     *
     * @return Symfony\Component\HttpFoundation\Response the response object
     **/
    public function batchStatusAction()
    {
        $this->checkAclOrForward('COMMENT_AVAILABLE');

        $request  = $this->request;
        $selected = $request->query->get('selected_fld');
        $status   = $request->query->getDigits('status');

        if (count($selected) > 0) {
            foreach ($selected as $id) {
                $comment = new \Comment($id);

                if (!is_null($comment->pk_comment)) {
                    $oldstatus = $comment->content_status;
                    $comment->set_available($status, $_SESSION['userid']);
                }
            }
        }
        if ($status == 1) {
            m::add(_('Comment was published successfully.'), m::SUCCESS);

        } else {
            m::add(_('Comment was unpublished successfully.'), m::SUCCESS);
        }

        $params = array(
                'page'     => $request->query->getDigits('page', 1),
                'category' => $this->category,
                'status'   => $oldstatus,
            );

        return $this->redirect($this->generateUrl('admin_comments', $params));

    }

    /**
     *  Delete multiple comments given its ids
     *
     * @return Symfony\Component\HttpFoundation\Response the response object
     **/
    public function batchDeleteAction()
    {
        $this->checkAclOrForward('COMMENT_DELETE');

        $request  = $this->request;
        $selected = $request->query->get('selected_fld');

        if (count($selected) > 0) {
            foreach ($selected as $id) {
                $comment = new \Comment($id);
                if (!is_null($comment->pk_comment)) {
                    $comment->delete($id, $_SESSION['userid']);
                }
            }
            m::add(_('Comments deleted successfully.'), m::SUCCESS);
        } else {
            m::add(_('You haven\'t selected any comment to delete.'), m::ERROR);
        }

        $params = array(
                'page'     => $request->query->getDigits('page', 1),
                'category' => $this->category,
                'status'   => $request->query->getDigits('status', 0),
            );

        return $this->redirect($this->generateUrl('admin_comments', $params));

    }

}