<?php
/**
 * Handles the actions for books
 *
 * @package Frontend_Controllers
 **/
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Frontend\Controller;

use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Onm\Framework\Controller\Controller;
use Onm\Message as m;
use Onm\Settings as s;

/**
 * Handles the actions for books
 *
 * @package Frontend_Controllers
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
     * Renders the books frontpage
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function frontpageAction(Request $request)
    {
        $this->page = $request->query->getDigits('page', 1);

        $this->view->setConfig('gallery-frontpage');
        $cacheID = $this->view->generateCacheId($this->categoryName, null, $this->page);

        // Setup caching system
        $this->view->setConfig('book-frontpage');
        $cacheID = $this->view->generateCacheId($this->categoryName, null, $this->page);

        $contentType = \ContentManager::getContentTypeIdFromName('book');

        // Setting up available categories for menu.
        $this->cm  = new \ContentManager();
        $this->ccm = \ContentCategoryManager::get_instance();
        list($parentCategories, $subcat, $categoryData) =
            $this->ccm->getArraysMenu('', $contentType);

        $bookCategories = array();
        $i = 0;
        foreach ($parentCategories as $cat) {
            //only books categories
            if ($cat->internal_category == $contentType) {
                $bookCategories[$i] = new \stdClass();
                $bookCategories[$i]->id    = $cat->pk_content_category;
                $bookCategories[$i]->title = $cat->title;
                $bookCategories[$i]->books = $this->cm->find_by_category(
                    'Book',
                    $cat->pk_content_category,
                    'content_status=1',
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
     * Shows a book given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function showAction(Request $request)
    {

        $dirtyID = $request->query->filter('id', null, FILTER_SANITIZE_STRING);
        $id      = \Content::resolveID($dirtyID);

        if (empty($id)) {
            throw new \Symfony\Component\Routing\Exception\ResourceNotFoundException();
        }

        $book = new \Book($id);

        $this->view->setConfig('book-inner');
        $cacheID = $this->view->generateCacheId($this->categoryName, null, $book->id);

        if ($this->view->caching == 0
            || (!$this->view->isCached('books/book_viewer.tpl', $cacheID))
        ) {
            $book->category_title = $book->loadCategoryTitle($book->id);

            $swf = preg_replace('%\.pdf%', '.swf', $book->file_name);

            $this->cm  = new \ContentManager();
            $books = $this->cm->find_by_category(
                'Book',
                $book->category,
                'content_status=1',
                'ORDER BY position ASC, created DESC LIMIT 5'
            );

            $this->view->assign(
                array(
                    'book'        => $book,
                    'content'     => $book,
                    'libros'      => $books,
                    'contentId'   => $id,
                    'category'    => $book->category,
                    'archivo_swf' => $swf,
                    'cache_id'    => $cacheID,
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

    /**
     * Shows a paginated list of books for a category
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function ajaxPaginationListAction(Request $request)
    {
        $this->cm   = new \ContentManager();
        $category   = $request->query->filter('category', null, FILTER_SANITIZE_STRING);
        $this->page = $request->query->getDigits('page', 1);
        $last       = false;
        if ($this->page < 1) {
            $this->page = 1;
        }

        $limit = 'LIMIT '.(($this->page - 1) * 5).',  5';
        $books = $this->cm->find_by_category(
            'Book',
            $category,
            'content_status=1',
            'ORDER BY position ASC, created DESC '. $limit
        );

        if (count($books) == 0) {
            $this->page = $this->page - 1;
            $limit = 'LIMIT '.(($this->page - 1) * 5).',  5';
            $books  = $this->cm->find_by_category(
                'Book',
                $category,
                'content_status=1',
                'ORDER BY position ASC, created DESC '. $limit
            );
            $last = true;

        }

        $output = $this->renderView(
            'books/widget_books.tpl',
            array(
                'actualCat' => $category,
                'page'      => $this->page,
                'last'      => $last,
                'libros'    => $books,
            )
        );

        return new Response($output);
    }
}
