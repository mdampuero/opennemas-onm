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
        $page         = $request->query->getDigits('page', 1);
        $categoryName = $this->request->query->filter('category_name', 'all', FILTER_SANITIZE_STRING);

        // Setup templating cache layer
        $this->view->setConfig('articles');
        $cacheID = $this->view->getCacheId('frontpage', 'book', $categoryName, $page);

        if ($this->view->getCaching() === 0
            || !$this->view->isCached('books/books_frontpage.tpl', $cacheID)
        ) {
            $contentType = \ContentManager::getContentTypeIdFromName('book');

            // Setting up available categories for menu.
            $contentManager   = new \ContentManager();
            $this->ccm        = \ContentCategoryManager::get_instance();
            $parentCategories = $this->ccm->getArraysMenu('', $contentType);

            $categories = array_filter($parentCategories[0], function($item) use ($contentType) {
                return $item->internal_category == $contentType;
            });

            foreach ($categories as &$cat) {
                $cat->id    = $cat->pk_content_category;
                $cat->books = $contentManager->find_by_category(
                    'Book',
                    $cat->pk_content_category,
                    'content_status=1',
                    'ORDER BY position ASC, created DESC LIMIT 5'
                );

                // Get books cover image
                foreach ($cat->books as &$book) {
                    $book->cover_img = $this->get('entity_repository')->find('Photo', $book->cover_id);
                }
            }

            $this->view->assign([
                'categoryBooks' => $categories,
            ]);
        }

        return $this->render('books/books_frontpage.tpl', [
            'cache_id'      => $cacheID,
            'page'          => $page,
            'x-tags'        => 'books-frontpage',
        ]);
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
        $categoryName = $this->request->query->get('category_name', null);
        $dirtyID      = $request->query->get('id', null);
        $urlSlug      = $request->query->get('slug', null);

        $content = $this->get('content_url_matcher')
            ->matchContentUrl('book', $dirtyID, $urlSlug, $categoryName);

        if (empty($content)) {
            throw new ResourceNotFoundException();
        }

        // Setup templating cache layer
        $this->view->setConfig('articles');
        $cacheID = $this->view->getCacheId('content', $content->id);

        if ($this->view->getCaching() === 0
            || (!$this->view->isCached('books/book_viewer.tpl', $cacheID))
        ) {
            $content->cover_img = $this->get('entity_repository')->find('Photo', $content->cover_id);

            $content->category_title = $content->loadCategoryTitle($content->id);

            $contentManager  = new \ContentManager();
            $books = $contentManager->find_by_category(
                'Book',
                $content->category,
                'content_status=1',
                'ORDER BY position ASC, created DESC LIMIT 5'
            );

            // Get books cover image
            foreach ($books as &$book) {
                $book->cover_img = $this->get('entity_repository')->find('Photo', $book->cover_id);
            }

            $this->view->assign(['libros' => $books]);
        }

        return $this->render('books/book_viewer.tpl', [
            'book'        => $content,
            'content'     => $content,
            'contentId'   => $content->id,
            'category'    => $content->category,
            'cache_id'    => $cacheID,
            'x-tags'      => 'book,'.$content->id,
            'x-cache-for' => '+1 day',
        ]);
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

        return $this->render('books/widget_books.tpl', [
            'actualCat' => $category,
            'page'      => $this->page,
            'last'      => $last,
            'libros'    => $books,
        ]);
    }
}
