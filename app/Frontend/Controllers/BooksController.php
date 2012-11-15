<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Frontend\Controllers;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Onm\Framework\Controller\Controller;
use Onm\Message as m;
use Onm\Settings as s;

/**
 * Handles the actions for advertisements
 *
 * @package Backend_Controllers
 **/
class BooksController extends Controller
{
    /**
     * Common code for all the actions
     *
     * @return void
     **/
    public function init()
    {
        $this->view = new \Template(TEMPLATE_USER);
        $this->categoryName = $this->request->query->filter('category_name', 'all', FILTER_SANITIZE_STRING);
        $this->view->assign(
            array(
                'LIBROS_IMG_PATH'   => INSTANCE_MEDIA_PATH.'/books/',
                'LIBROS_FILES_PATH' => INSTANCE_MEDIA_PATH.'/books/',
            )
        );
    }

    /**
     * Render books frontpage
     *
     * @return Response the response object
     **/
    public function frontpageAction(Request $request)
    {
        // Setup caching system
        $this->page = $request->query->getDigits('page', 1);

        $this->view->setConfig('gallery-frontpage');
        $cacheID = $this->view->generateCacheId($this->categoryName, '', $this->page);

        /**************  CATEGORIES & SUBCATEGORIES  *********************************/
        /**
         * Setting up available categories for menu.
        */// Setup caching system
        $this->view->setConfig('book-frontpage');
        $cacheID = $this->view->generateCacheId($this->categoryName, '', $this->page);

        $contentType = \Content::getIDContentType('book');

        $this->cm  = new \ContentManager();
        $this->ccm = \ContentCategoryManager::get_instance();
        list($parentCategories, $subcat, $categoryData) =
            $this->ccm->getArraysMenu('', $contentType);

        $bookCategories = array();
        $i=0;
        foreach ($parentCategories as $cat) {
            //only books categories
            if ($cat->internal_category == $contentType) {
                $bookCategories[$i] = new \stdClass();
                $bookCategories[$i]->id    = $cat->pk_content_category;
                $bookCategories[$i]->title = $cat->title;
                $bookCategories[$i]->books = $this->cm->find_by_category(
                    'Book',
                    $cat->pk_content_category,
                    'available=1',
                    'ORDER BY position ASC, created DESC LIMIT 5'
                );
                $i++;
            }
        }

        return $this->render(
            'books/books_frontpage.tpl',
            array(
                'categoryBooks' => $bookCategories,
                'cache_id'      => $cacheID,
                'page'          => $this->page
            )
        );
    }

    /**
     * Show book
     *
     * @return Response the response object
     **/
    public function showAction(Request $request)
    {


        $dirtyID = $request->query->filter('id', null, FILTER_SANITIZE_STRING);
        $id      = \Content::resolveID($dirtyID);

        $book = new \Book($id);
        if (!empty($book->id)) {
            $this->view->setConfig('book-inner');
            $cacheID = $this->view->generateCacheId($this->categoryName, null, $book->id);

            if (($this->view->caching == 0)
            || (!$this->view->isCached('books/book_viewer.tpl', $cacheID))
            ) {
                $book->category_title = $book->loadCategoryTitle($book->id);

                $swf = preg_replace('%\.pdf%', '.swf', $book->file_name);

                $this->cm  = new \ContentManager();
                $books = $this->cm->find_by_category(
                    'Book',
                    $book->category,
                    'available=1',
                    'ORDER BY position ASC, created DESC LIMIT 5'
                );

                return $this->render(
                    'books/book_viewer.tpl',
                    array(
                        'book'      => $book,
                        'libros'    => $books,
                        'contentId' => $id,
                        'category'  => $book->category,
                        'archivo_swf'=> $swf,
                        'cache_id'  => $cacheID,
                    )
                );
            }
            return $this->render(
                'books/book_viewer.tpl',
                array(
                    'cache_id' => $cacheID,
                )
            );
        }

    }

    /**
     * Description of the action
     *
     * @return Response the response object
     **/
    public function ajaxPaginationListAction(Request $request)
    {

        $this->cm  = new \ContentManager();
        $category = $request->query->filter('category', null, FILTER_SANITIZE_STRING);
        $this->page = $request->query->getDigits('page', 1);
        $last = false;
        if ($this->page<1) {
            $this->page = 1;
        }

        $_limit = 'LIMIT '.(($this->page - 1) * 5).',  5';
        $books = $this->cm->find_by_category(
            'Book',
            $category,
            'available=1',
            'ORDER BY position ASC, created DESC  '. $_limit
        );

        if (count($books) == 0) {
            $this->page = $this->page - 1;
            $_limit = 'LIMIT '.(($this->page - 1) * 5).',  5';
            $books = $this->cm->find_by_category(
                'Book',
                $category,
                'available=1',
                'ORDER BY position ASC, created DESC  '. $_limit
            );
            $last = true;

        }

        $output = $this->renderView(
            'books/widget_books.tpl',
            array(
                'actualCat'=> $category,
                'page' => $this->page,
                'last' =>$last,
                'libros' => $books,
            )
        );
        return new Response($output);

    }
}
// END class BooksController

