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
 **/
class StaticPagesController extends Controller
{

    /**
     * Common code for all the actions
     *
     * @return Response the response object
     **/
    public function init()
    {
        $this->checkAclOrForward('STATIC_ADMIN');

        $this->view = new \TemplateAdmin(TEMPLATE_ADMIN);
    }

    /**
     * Description of the action
     *
     * @return Symfony\Component\HttpFoundation\Response the response object
     **/
    public function listAction()
    {
        $filter = null;
        $requestFilter = $this->request->query->get('filter');
        if (is_array($requestFilter)
            && array_key_exists('title', $requestFilter)
        ) {
            $filter = '`title` LIKE "%' . $requestFilter['title'] . '%"';
        }

        $page       = $this->request->query->filter('page', 0, FILTER_VALIDATE_INT);
        $itemsPerPage = s::get('items_per_page') ?: 20;

        $cm = new \ContentManager();
        list($pages, $pager) = $cm->find_pages(
            'StaticPage', $filter, 'ORDER BY created DESC ', $page, $itemsPerPage
        );

        return $this->render('static_pages/list.tpl', array(
            'pages' => $pages,
            'pager' => $pager,
        ));
    }

    /**
     * Shows the edit form
     *
     * @return Symfony\Component\HttpFoundation\Response the response object
     **/
    public function showAction()
    {
        $id = $this->request->query->filter('id', null, FILTER_SANITIZE_STRING);

        if (!is_null($id)) {
            $page = new \StaticPage();
            $page->read($id);

            return $this->render('static_pages/read.tpl', array(
                'id' => $id,
                'page' => $page,
            ));
        } else {
            m::add(sprintf(_('Static page with id "%d" doesn\'t exists.'), $id), m::ERROR);

            return $this->redirect($this->generateUrl('admin_staticpages'));
        }
    }

    /**
     * Handles the creation of a new static page
     *
     * @return Symfony\Component\HttpFoundation\Response the response object
     **/
    public function createAction()
    {
        if ('POST' != $this->request->getMethod()) {
            return $this->render('static_pages/read.tpl');
        } else {
            $page = new \StaticPage();

            $data = $_POST;
            $data = array_merge(
                $_POST,
                array(
                    'slug' => $page->buildSlug($data['slug'], $data['id'], $data['title']),
                    'metadata' => \StringUtils::normalize_metadata($data['metadata']),
                )
            );
            var_dump($data);die();

            $page->save($data);

            return $this->redirect($this->generateUrl('admin_staticpages'));
        }
    }

    /**
     * Updates a static page given its id
     *
     * @return Response the response object
     **/
    public function updateAction()
    {
    }

    /**
     * Deletes an static page given its id
     *
     * @return Symfony\Component\HttpFoundation\Response the response object
     **/
    public function deleteAction()
    {
    }

} // END class StaticPagesController
