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
 * Handles the actions for managing opinions
 *
 * @package Backend_Controllers
 **/
class OpinionAuthorsController extends Controller
{
    /**
     * Common code for all the actions
     *
     * @return void
     **/
    public function init()
    {
        $this->view = new \TemplateAdmin(TEMPLATE_ADMIN);

        $this->checkAclOrForward('AUTHOR_ADMIN');
    }

    /**
     * Description of the action
     *
     * @return Response the response object
     **/
    public function listAction(Request $request)
    {
        $this->checkAclOrForward('AUTHOR_ADMIN');

        $page = $request->query->getDigits('page', 1);

        $itemsPerPage = s::get('items_per_page') ?: 20;

        $cm         = new \ContentManager();
        $author     = new \Author();
        $authors    = $author->list_authors(
            NULL,
            'ORDER BY name ASC'
        );
        $authorsPage = array_slice($authors, ($page-1)*$itemsPerPage, $itemsPerPage);


        // Build the pager
        $pagination = \Pager::factory(array(
            'mode'        => 'Sliding',
            'perPage'     => $itemsPerPage,
            'append'      => false,
            'path'        => '',
            'delta'       => 4,
            'clearIfVoid' => true,
            'urlVar'      => 'page',
            'totalItems'  => count($authors),
            'fileName'    => $this->generateUrl('admin_opinion_authors').'?page=%d',
        ));

        return $this->render('opinion/authors/list.tpl', array(
            'authors'       => $authorsPage,
            'pagination'    => $pagination,
        ));
    }

    /**
     * Shows the information for an opinion author given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function showAction(Request $request)
    {

        $this->checkAclOrForward('AUTHOR_UPDATE');

        $id = $request->query->getDigits('id', null);
        $page = $request->query->getDigits('page', 1);

        $author = new \Author($id);
        if (is_null($author->id)) {
            m::add(sprintf(_('Unable to find the author with the id "%d"'), $id));

            return $this->redirect($this->generateUrl('admin_opinion_authors', array('page' => $page)));
        }

        $photos = $author->get_author_photos($id);

        return $this->render('opinion/authors/new.tpl', array(
            'author'  => $author,
            'photos' => $photos,
        ));
    }

} // END class OpinionsController