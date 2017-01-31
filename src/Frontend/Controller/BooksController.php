<?php
/**
 * Defines the frontend controller for the books content type
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
use Common\Core\Controller\Controller;
use Onm\Settings as s;

/**
 * Handles the actions for books
 *
 * @package Frontend_Controllers
 **/
class BooksController extends Controller
{
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
        $categoryName = $this->request->query->filter('category_name', 'all', FILTER_SANITIZE_STRING);

        // Setup caching system
        $this->view->setConfig('article-inner');
        $cacheID = $this->view->generateCacheId($categoryName, null, $this->page);

        $contentType = \ContentManager::getContentTypeIdFromName('book');

        // Setting up available categories for menu.
        $contentManager  = new \ContentManager();
        $this->ccm = \ContentCategoryManager::get_instance();
        $parentCategories = $this->ccm->getArraysMenu('', $contentType);

        $bookCategories = array();
        $i = 0;
        foreach ($parentCategories[0] as $cat) {
            // get only books categories
            if ($cat->internal_category == $contentType) {
                $bookCategories[$i] = new \stdClass();
                $bookCategories[$i]->id    = $cat->pk_content_category;
                $bookCategories[$i]->title = $cat->title;
                $bookCategories[$i]->books = $contentManager->find_by_category(
                    'Book',
                    $cat->pk_content_category,
                    'content_status=1',
                    'ORDER BY position ASC, created DESC LIMIT 5'
                );

                // Get books cover image
                foreach ($bookCategories[$i]->books as &$book) {
                    $book->cover_img = new \Photo($book->cover_id);
                }

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
     * @throws ResourceNotFoundException if the book is not available
     **/
    public function showAction(Request $request)
    {
        $categoryName = $this->request->query->filter('category_name', null, FILTER_SANITIZE_STRING);
        $dirtyID      = $request->query->filter('id', null, FILTER_SANITIZE_STRING);
        $urlSlug      = $request->query->filter('slug', null, FILTER_SANITIZE_STRING);

        $book = $this->get('content_url_matcher')
            ->matchContentUrl('book', $dirtyID, $urlSlug, $categoryName);

        if (empty($book)) {
            throw new ResourceNotFoundException();
        }

        $this->view->setConfig('article-inner');

        $cacheID = $this->view->generateCacheId($categoryName, null, $book->id);
        if ($this->view->getCaching() === 0
            || (!$this->view->isCached('books/book_viewer.tpl', $cacheID))
        ) {
            $book->category_title = $book->loadCategoryTitle($book->id);

            $contentManager  = new \ContentManager();
            $books = $contentManager->find_by_category(
                'Book',
                $book->category,
                'content_status=1',
                'ORDER BY position ASC, created DESC LIMIT 5'
            );

            // Get books cover image
            foreach ($books as $key => $value) {
                $books[$key]->cover_img =
                    $this->get('entity_repository')->find('Photo', $value->cover_id);
            }

            $this->view->assign(['libros' => $books]);
        }

        return $this->render(
            'books/book_viewer.tpl',
            [
                'book'      => $book,
                'content'   => $book,
                'contentId' => $book->id,
                'category'  => $book->category,
                'cache_id'  => $cacheID,
            ]
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
        $contentManager   = new \ContentManager();
        $category   = $request->query->filter('category', null, FILTER_SANITIZE_STRING);
        $this->page = $request->query->getDigits('page', 1);
        $last       = false;
        if ($this->page < 1) {
            $this->page = 1;
        }

        $limit = 'LIMIT '.(($this->page - 1) * 5).',  5';
        $books = $contentManager->find_by_category(
            'Book',
            $category,
            'content_status=1',
            'ORDER BY position ASC, created DESC '. $limit
        );

        if (count($books) == 0) {
            $this->page = $this->page - 1;
            $limit = 'LIMIT '.(($this->page - 1) * 5).',  5';
            $books  = $contentManager->find_by_category(
                'Book',
                $category,
                'content_status=1',
                'ORDER BY position ASC, created DESC '. $limit
            );
            $last = true;
        }

        // Get books cover image
        foreach ($books as &$book) {
            $book->cover_img = new \Photo($book->cover_id);
        }

        return $this->render(
            'books/widget_books.tpl',
            array(
                'actualCat' => $category,
                'page'      => $this->page,
                'last'      => $last,
                'libros'    => $books,
            )
        );
    }
}
