<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Frontend\Controller;

use Common\Core\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class BooksController extends Controller
{
    /**
     * Renders the books frontpage.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     */
    public function frontpageAction(Request $request)
    {
        $page    = $request->query->getDigits('page', 1);
        $cacheID = $this->view->getCacheId('frontpage', 'book', $page);

        $this->view->setConfig('articles');

        if ($this->view->getCaching() === 0
            || !$this->view->isCached('books/books_frontpage.tpl', $cacheID)
        ) {
            $contentManager = new \ContentManager();
            $books          = [];
            $categories     = $this->get('api.service.category')->getList();

            foreach ($categories['items'] as $category) {
                $books[$category->pk_content_category] = $contentManager
                    ->find_by_category(
                        'Book',
                        $category->pk_content_category,
                        'content_status=1',
                        'ORDER BY pk_content DESC, starttime DESC LIMIT 5'
                    );

                foreach ($books[$category->pk_content_category] as $book) {
                    $book->cover_img = $this->get('entity_repository')
                        ->find('Photo', $book->cover_id);
                }
            }

            $this->view->assign([
                'books'      => $books,
                'categories' => $categories['items'],
            ]);
        }

        return $this->render('books/books_frontpage.tpl', [
            'cache_id' => $cacheID,
            'page'     => $page,
            'x-tags'   => 'books-frontpage',
        ]);
    }

    /**
     * Shows a book given its id.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     *
     * @throws ResourceNotFoundException if the book is not available.
     */
    public function showAction(Request $request)
    {
        $categoryName = $request->get('category_name', null);
        $dirtyID      = $request->get('id', null);
        $urlSlug      = $request->get('slug', null);

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

            $contentManager = new \ContentManager();
            $books          = $contentManager->find_by_category(
                'Book',
                $content->category,
                'content_status=1 and pk_content != ' . $content->pk_content,
                'ORDER BY pk_content DESC, starttime DESC LIMIT 5'
            );

            foreach ($books as &$book) {
                $book->cover_img = $this->get('entity_repository')
                    ->find('Photo', $book->cover_id);
            }

            $this->view->assign([
                'books'    => $books,
                'category' => $this->get('api.service.category')
                    ->getItem($content->pk_fk_content_category)
            ]);
        }

        return $this->render('books/book_viewer.tpl', [
            'book'        => $content,
            'content'     => $content,
            'contentId'   => $content->id,
            'category'    => $content->category,
            'cache_id'    => $cacheID,
            'o_content'   => $content,
            'x-tags'      => 'book,' . $content->id,
            'x-cache-for' => '+1 day',
        ]);
    }
}
