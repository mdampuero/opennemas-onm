<?php
/*
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Backend\Controllers;

use Onm\Framework\Controller\Controller,
    Onm\Settings as s,
    Onm\Message as m;

/**
 * Handles the actions for the system information
 *
 * @package Backend_Controllers
 * @author
 **/
class BooksController extends Controller
{
    /**
     * Common code for all the actions
     *
     * @return void
     * @author
     **/
    public function init()
    {
        $this->view = new \TemplateAdmin(TEMPLATE_ADMIN);

        // Take out this crap from this PLEASE ---------------------------------
        $contentType = \Content::getIDContentType('book');

        $category = $this->request->query->filter('category', 'favorite', FILTER_SANITIZE_STRING);

        $ccm = \ContentCategoryManager::get_instance();
        list($parentCategories, $subcat, $categoryData) = $ccm->getArraysMenu($category, $contentType);

        $bookCategories = array();
        foreach ($parentCategories as $bCat){
            if ($bCat->internal_category == $contentType){
                $bookCategories[] = $bCat;
            }
        }

        $this->view->assign(array(
            'category'     => $category,
            'subcat'       => $subcat,
            'allcategorys' => $bookCategories,
            'datos_cat'    => $categoryData,
        ));
        // ---------------------------------------------------------------------

        // Optimize  this crap from this ---------------------------------------
        $bookSavePath = INSTANCE_MEDIA_PATH.'/books/';

        // Create folder if it doesn't exist
        if (!file_exists($bookSavePath)) {
            \FilesManager::createDirectory($bookSavePath);
        }
        // ---------------------------------------------------------------------
    }

    /**
     * Lists all the
     *
     * @return void
     **/
    public function defaultAction()
    {
        \Acl::checkOrForward('BOOK_ADMIN');

        $page = $this->request->query->getInt('page');
        $category = $this->request->query->filter('category', 'favorite', FILTER_SANITIZE_STRING);

        $configurations = s::get('book_settings');
        $numFavorites = (isset($configurations['total_widget']) && !empty($configurations['total_widget']))? $configurations['total_widget']: 1;

        $cm = new \ContentManager();

        if (empty($page)) {
            $limit = "LIMIT ".(ITEMS_PAGE+1);
        } else {
            $limit = "LIMIT ".($page-1) * ITEMS_PAGE .', '.ITEMS_PAGE;
        }

        if ($category == 'favorite') {
            $books = $cm->find_all(
                'Book',
                'favorite=1 AND available =1',
                'ORDER BY position, created DESC '.$limit
            );

            if(!empty($books)) {
                foreach ($books as &$book) {
                    $book->category_name  = $ccm->get_name($book->category);
                    $book->category_title = $ccm->get_title($book->category_name);
                }
            }
            if (count($books) != $numFavorites ) {
                m::add( sprintf(_("You must put %d books in the HOME widget"), $numFavorites));
            }

        } else {
            $books = $cm->find_by_category(
                'Book',
                $category,
                '1=1',
               'ORDER BY position ASC, created DESC '.$limit
            );
        }

        $pagination = \Onm\Pager\SimplePager::getPagerUrl(array(
            'page'  => $page,
            'items' => ITEMS_PAGE,
            'total' => count($books),
            'url'   => $this->generateUrl(
                'admin_books',
                array('category' => $category)
            ),
        ));

        return $this->render('book/list.tpl', array(
            'pagination' => $pagination,
            'books'      => $books
        ));
    }

    /**
     * Shows and handles the form for create new books
     *
     * @return Response the response objcet
     **/
    public function createAction()
    {
        \Acl::checkOrForward('BOOK_CREATE');
        return $this->render('book/new.tpl');
    }

    /**
     * Shows the book information given its id
     *
     * @return Response the response object
     **/
    public function showAction()
    {
        \Acl::checkOrForward('BOOK_UPDATE');

        $id = $this->request->query->getInt('id');

        // TODO check if ID is available
        $book = new Book($id);

        return $this->render('book/new.tpl', array(
            'book'     => $book,
            'category' => $book->category,
        ));
    }

} // END class BooksController