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

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Onm\Framework\Controller\Controller;
use Onm\Settings as s;
use Onm\Message as m;

/**
 * Handles the actions for the system information
 *
 * @package Backend_Controllers
 * @author
 **/
class TrashController extends Controller
{

    /**
     * Common code for all the actions
     *
     * @return void
     **/
    public function init()
    {
        $this->view = new \TemplateAdmin(TEMPLATE_ADMIN);

        $this->filterContentType = $this->request->query->get('mytype', 'article');
        $this->page              = $this->request->query->getDigits('page', 1);
    }
    /**
     * Description of the action
     *
     * @return Response the response object
     **/
    public function defaultAction(Request $request)
    {
        $cm           = new \ContentManager();
        $contentTypes = $cm->getContentTypes();
        $page         = $request->query->getDigits('page', 1);
        $itemsPerPage = s::get('items_per_page', 20);

        // Get paginated elements that are marked as in litter
        list($countElements, $elements) = $cm->getCountAndSlice(
            $this->filterContentType,
            null,
            'in_litter=1',
            'ORDER BY created DESC ',
            $page,
            $itemsPerPage
        );

        // Complete elements information
        foreach ($elements as &$item) {
            $item->category_name =  $item->loadCategoryName($item->id);
            $item->category_title = $item->loadCategoryTitle($item->id);
        }

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
                'totalItems'  => $countElements,
                'fileName'    => $this->generateUrl(
                    'admin_staticpages',
                    array(
                        'mytype' => $this->filterContentType,
                        'page' => $this->page
                    )
                ).'?page=%d',
            )
        );

        return $this->render(
            'trash/trash.tpl',
            array(
                'mytype'        => $this->filterContentType,
                'types_content' => $contentTypes,
                'pagination'    => $pagination,
                'contents'      => $elements
            )
        );
    }

    /**
     * Deletes trashed element/s given their ids
     *
     * @return Response the response object
     **/
    public function deleteAction(Request $request)
    {
        $contentId = $this->request->query->getDigits('id');

        if ((int) $contentId) {
            $content = new \Content($contentId);

            if (!empty($content->id)) {
                $contentTypeId = $content->content_type;

                // TODO: Use parameter binding
                $name = $GLOBALS['application']->conn->GetOne(
                    'SELECT name FROM `content_types` '
                    .'WHERE pk_content_type = "'. $contentTypeId.'"'
                );

                $contentTypeName = classify($name);
                $content = new $contentTypeName($contentId);
                $content->remove($contentId);
            } else {
                m::add(sprintf(_('Unable to find content with id "%d".'), $contentId), m::ERROR);
            }
        } else {
            m::add(sprintf(_('Unable to find content with id "%d".'), $contentId), m::ERROR);
        }

        return $this->redirect(
            $this->generateUrl(
                'admin_trash',
                array('mytype' => $this->filterContentType, 'page' => $this->page)
            )
        );

    }

    /**
     * Restores trashed element/s given their id
     *
     * @return Response the response object
     **/
    public function restoreAction(Request $request)
    {
        $contentId = $this->request->query->getDigits('id');

        if ((int) $contentId) {
            $content = new \Content($contentId);
            if (!empty($content->id)) {
                $contentTypeId = $content->content_type;

                // TODO: Use parameter binding
                $name = $GLOBALS['application']->conn->GetOne(
                    'SELECT name FROM `content_types` '
                    .'WHERE pk_content_type = "'. $contentTypeId.'"'
                );

                $contentTypeName = ucwords($name);
                $content = new $contentTypeName($contentId);
                $content->restoreFromTrash($contentId);
            } else {
                m::add(sprintf(_('Unable to find content with id "%d".'), $contentId), m::ERROR);
            }
        } else {
            m::add(sprintf(_('Unable to find content with id "%d".'), $contentId), m::ERROR);
        }

        return $this->redirect(
            $this->generateUrl(
                'admin_trash',
                array('mytype' => $this->filterContentType, 'page' => $this->page)
            )
        );

    }

    /**
     * Deletes multiple contents given their ids sent in POST request
     *
     * @return Response the response object
     **/
    public function batchDeleteAction(Request $request)
    {
        $contentIDs = $this->request->request->get('selected', array());

        if (count($contentIDs) > 0) {
            foreach ($contentIDs as $contentId) {
                $content = new \Content((int) $contentId);

                if (!empty($content->id)) {
                    $name = $GLOBALS['application']->conn->GetOne(
                        'SELECT name FROM `content_types` WHERE pk_content_type = "'
                        .$content->content_type.'"'
                    );

                    $contentClassName = ucwords($name);
                    $content = new $contentClassName($contentId);
                    $content->remove($contentId);
                } else {
                    m::add(sprintf(_('Unable to find content with id "%d".'), $contentId));
                }
            }
        } else {
            m::add(_('You must specify contents for delete.'));
        }

        return $this->redirect(
            $this->generateUrl(
                'admin_trash',
                array('mytype' => $this->filterContentType, 'page' => $this->page)
            )
        );
    }

    /**
     * Deletes multiple contents given their ids sent in POST request
     *
     * @return Response the response object
     **/
    public function batchRestoreAction(Request $request)
    {
        $contentIDs = $request->get('selected', array());

        if (count($contentIDs) > 0) {
            foreach ($contentIDs as $contentId) {
                $content = new \Content((int) $contentId);

                if (!empty($content->id)) {
                    $name = $GLOBALS['application']->conn->GetOne(
                        'SELECT name FROM `content_types` WHERE pk_content_type = "'
                        .$content->content_type.'"'
                    );

                    $contentClassName = classify($name);
                    $content = new $contentClassName($contentId);
                    $content->restoreFromTrash($contentId);
                } else {
                    m::add(sprintf(_('Unable to find content with id "%d".'), $contentId));
                }
            }
        } else {
            m::add(_('You must specify contents for restore.'));
        }

        return $this->redirect(
            $this->generateUrl(
                'admin_trash',
                array('mytype' => $this->filterContentType, 'page' => $this->page)
            )
        );
    }

    // TODO: not finished, but I think that is not neccesary
    /**
     * Deletes all the trashed elements
     *
     * @return string the response string
     **/
    public function deleteAllAction(Request $request)
    {
        $type = $request->query->filter('mytype', null, FILTER_SANITIZE_STRING);
        if ($_REQUEST['id'] == 6) {
            //Eliminar todos
            $cm = new ContentManager();
            $contents = $cm->find($type, 'in_litter=1', 'ORDER BY created DESC ');

            foreach ($contents as &$item) {
                $content = new Content($item->id);
                $content->remove($item->id);
            }

            return $this->redirect(
                $this->generateUrl(
                    'admin_trash',
                    array(
                        'mytype' => $this->filterContentType,
                        'page' => $this->page
                    )
                )
            );
        }
    }
}
