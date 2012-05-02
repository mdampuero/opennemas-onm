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
    Onm\Message as m;
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
     * @author
     **/
    public function init()
    {
        // Initializae the session manager
        require_once './session_bootstrap.php';

        $this->view = new \TemplateAdmin(TEMPLATE_ADMIN);

        $this->filterContentType = $this->request->query->get('mytype', 'article');
        $this->page              = $this->request->query->getDigits('page', 0);
    }
    /**
     * Description of the action
     *
     * @return void
     **/
    public function defaultAction()
    {
        $cm           = new \ContentManager();
        $contentTypes = $cm->get_types();

        // Get paginated elements that are marked as in litter
        list($trashedElements, $pager) = $cm->find_pages(
            $this->filterContentType, 'in_litter=1',
            'ORDER BY changed DESC ', $this->page, 20
        );


        // Complete elements information
        $content = new \Content();
        foreach ($trashedElements as &$item) {
            $item->category_name =  $content->loadCategoryName($item->id);
            $item->category_title = $content->loadCategoryTitle($item->id);
        }

        return $this->render('trash/trash.tpl', array(
            'mytype' => $this->filterContentType,
            'types_content' => $contentTypes,
            'paginacion'    => $pager,
            'litterelems'   => $trashedElements
        ));
    }

    /**
     * Deletes trashed element/s given their ids
     *
     * @return void
     * @author
     **/
    public function deleteAction()
    {
        $contentId = $this->request->query->getDigits('id');

        if ((int)$contentId) {
            $content = new \Content($contentId);
            if (!empty($content->id)) {
                $contentTypeId = $content->content_type;

                // TODO: Use parameter binding
                $name = $GLOBALS['application']->conn->GetOne(
                    'SELECT name FROM `content_types` WHERE pk_content_type = "'. $contentTypeId.'"'
                );

                $contentTypeName = ucwords($name);
                $content = new $contentTypeName($contentId);
                $content->remove($contentId);
            } else {
                m::add(sprintf(_('Unable to find content with id "%d".'), $contentId));
            }
        }

        $this->redirect(url(
            'admin_trash',
            array('mytype' => $this->filterContentType, 'page' => $this->page)
        ));

    }

    /**
     * undocumented function
     *
     * @return void
     * @author
     **/
    public function batchDeleteAction()
    {
        $contentIDs = $this->request->request->get('selected', array());

        if (count($contentIDs) > 0) {
            foreach ($contentIDs as $contentId) {
                $content = new \Content($contentId);

                if (!empty($content->id)) {
                    $name = $GLOBALS['application']->conn->GetOne(
                        'SELECT name FROM `content_types` WHERE pk_content_type = "'
                        .$content->content_type.'"'
                    );

                    $contentClassName = ucwords($name); //Nombre de la clase
                    $content = new $contentClassName($contentId); //Llamamos a la clase
                    $content->remove($contentId); // eliminamos
                } else {
                    m::add(sprintf(_('Unable to find content with id "%d".'), $contentId));
                }
            }
        }
        return $this->redirect(url(
            'admin_trash',
            array('mytype' => $this->filterContentType, 'page' => $this->page)
        ));
    }

    // TODO: not finished
    /**
     * Deletes all the trashed elements
     *
     * @return string the response string
     **/
    public function deleteAllAction()
    {
        if ($_REQUEST['id'] == 6){ //Eliminar todos

            $cm = new ContentManager();
            $contents = $cm->find($_REQUEST['mytype'], 'in_litter=1', 'ORDER BY created DESC ');
            foreach ($contents as $cont){
                $content = new Content($cont->id);
                $content->remove($cont->id);
            }
            return $this->redirect(url('admin_trash', array('mytype' => $this->filterContentType, 'page' => $this->page)));
        }
    }

} // END class TrashController