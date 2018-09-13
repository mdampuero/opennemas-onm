<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Backend\Controller;

use Common\Core\Annotation\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Common\Core\Controller\Controller;
use Onm\Settings as s;

/**
 * Handles the actions for the system information
 *
 * @package Backend_Controllers
 */
class BooksController extends Controller
{
    /**
     * Common code for all the actions
     *
     * @return void
     */
    public function init()
    {
        // Take out this crap from this PLEASE ---------------------------------
        $contentType = \ContentManager::getContentTypeIdFromName('book');

        $this->category = $this->get('request_stack')->getCurrentRequest()
            ->query->filter('category', 'all', FILTER_SANITIZE_STRING);

        $this->ccm = \ContentCategoryManager::get_instance();

        list($this->parentCategories, $this->subcat, $this->categoryData) =
            $this->ccm->getArraysMenu($this->category, $contentType);

        $this->bookCategories = [];
        foreach ($this->parentCategories as $bCat) {
            if ($bCat->internal_category == $contentType) {
                $this->bookCategories[] = $bCat;
            }
        }

        $this->view->assign([
            'category'     => $this->category,
            'subcat'       => $this->subcat,
            'allcategorys' => $this->bookCategories,
            'datos_cat'    => $this->categoryData,
        ]);
    }

    /**
     * Lists all the
     *
     * @return Response the response object
     *
     * @Security("hasExtension('BOOK_MANAGER')
     *     and hasPermission('BOOK_ADMIN')")
     */
    public function listAction()
    {
        $configurations = s::get('book_settings');
        if (isset($configurations['total_widget'])
            && !empty($configurations['total_widget'])
        ) {
            $this->get('session')->getFlashBag()->add(
                'notice',
                sprintf(_("You must put %d books in the HOME widget"), $configurations['total_widget'])
            );
        }

        $categories = [ [ 'name' => _('All'), 'value' => null ] ];

        foreach ($this->parentCategories as $key => $category) {
            $categories[] = [
                'name' => $category->title,
                'value' => $category->pk_content_category
            ];

            foreach ($this->subcat[$key] as $subcategory) {
                $categories[] = [
                    'name' => '&rarr; ' . $subcategory->title,
                    'value' => $subcategory->pk_content_category
                ];
            }
        }

        return $this->render(
            'book/list.tpl',
            [ 'categories' => $categories ]
        );
    }

    /**
     * List books favorites for widget
     *
     * @return Response the response object
     *
     * @Security("hasExtension('BOOK_MANAGER')
     *     and hasPermission('BOOK_ADMIN')")
     */
    public function widgetAction()
    {
        $configurations = s::get('book_settings');
        if (isset($configurations['total_widget'])
            && !empty($configurations['total_widget'])
        ) {
            $this->get('session')->getFlashBag()->add(
                'notice',
                sprintf(_("You must put %d books in the HOME widget"), $configurations['total_widget'])
            );
        }

        return $this->render('book/list.tpl', [ 'category' => 'widget' ]);
    }

    /**
     * Shows and handles the form for create new books
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("hasExtension('BOOK_MANAGER')
     *     and hasPermission('BOOK_CREATE')")
     */
    public function createAction(Request $request)
    {
        if ('POST' != $request->getMethod()) {
            $this->view->assign('category', $this->category);

            $ls = $this->get('core.locale');
            return $this->render(
                'book/new.tpl',
                [
                    'locale' => $ls->getLocale('frontend'),
                    'tags'   => []
                ]
            );
        }

        $data = [
            'title'          => $request->request
                ->filter('title', '', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES),
            'author'         => $request->request->filter('author', '', FILTER_SANITIZE_STRING),
            'cover_id'       => $request->request->getInt('book_cover_id'),
            'editorial'      => $request->request
                ->filter('editorial', '', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES),
            'description'    => $request->request->filter('description', ''),
            'starttime'      => $request->request->filter('starttime', '', FILTER_SANITIZE_STRING),
            'category'       => $request->request->getInt('category', 0),
            'position'       => $request->request->getInt('position', 1),
            'content_status' => $request->request->getInt('content_status', 0),
            'tag_ids'        => json_decode($request->request->get('tag_ids', ''), true)
        ];

        $book = new \Book();
        $id   = $book->create($data);

        if (!empty($id)) {
            $book->setPosition($data['position']);
            $book = $book->read($id);

            $request->getSession()->getFlashBag()->add(
                'success',
                _('Book created successfully.')
            );

            return $this->redirect(
                $this->generateUrl('admin_book_show', [ 'id' => $id ])
            );
        } else {
            $this->get('session')->getFlashBag()->add(
                'error',
                _("Unable to create the new book.")
            );
        }

        return $this->render(
            'book/new.tpl',
            [
                'locale' => $this->get('core.locale')->getLocale('frontend'),
                'tags'   => []
            ]
        );
    }

    /**
     * Shows the book information given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("hasExtension('BOOK_MANAGER')
     *     and hasPermission('BOOK_UPDATE')")
     */
    public function showAction(Request $request)
    {
        $id = $request->query->getInt('id');

        $book = new \Book($id);

        if (is_null($book->id)) {
            $this->get('session')->getFlashBag()->add(
                'error',
                sprintf(_('Unable to find the book with the id "%d"'), $id)
            );

            return $this->redirect($this->generateUrl('admin_books'));
        }

        $auxTagIds     = $book->getContentTags($book->id);
        $book->tag_ids = array_key_exists($book->id, $auxTagIds) ?
            $auxTagIds[$book->id] :
            [];

        if (!$this->get('core.security')->hasPermission('CONTENT_OTHER_UPDATE')
            && !$book->isOwner($this->getUser()->id)
        ) {
            $this->get('session')->getFlashBag()->add(
                'error',
                _("You can't modify this book because you don't have enough privileges.")
            );

            return $this->redirect($this->generateUrl('admin_books'));
        }

        $ls = $this->get('core.locale');
        return $this->render('book/new.tpl', [
            'book'     => $book,
            'category' => $book->category,
            'locale'         => $ls->getRequestLocale('frontend'),
            'tags'           => $this->get('api.service.tag')
                ->getListByIdsKeyMapped($book->tag_ids)['items']
        ]);
    }

    /**
     * Handles the form for update a book given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("hasExtension('BOOK_MANAGER')
     *     and hasPermission('BOOK_UPDATE')")
     */
    public function updateAction(Request $request)
    {
        $id = $request->request->getInt('id');

        $book = new \Book($id);

        if (is_null($book->id)) {
            $this->get('session')->getFlashBag()->add(
                'error',
                sprintf(_('Unable to find the book with the id "%d"'), $id)
            );

            return $this->redirect($this->generateUrl('admin_books'));
        }

        if (!$this->get('core.security')->hasPermission('CONTENT_OTHER_UPDATE')
            && !$book->isOwner($this->getUser()->id)
        ) {
            $this->get('session')->getFlashBag()->add(
                'error',
                _("You can't modify this book because you don't have enough privileges.")
            );

            return $this->redirect($this->generateUrl('admin_books'));
        }

        // Check empty data
        if (count($request->request) < 1) {
            $this->get('session')->getFlashBag()->add(
                'error',
                _("Book data sent not valid.")
            );

            return $this->redirect($this->generateUrl('admin_book_show', [ 'id' => $id ]));
        }

        $data = [
            'id'             => $id,
            'title'          => $request->request
                ->filter('title', '', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES),
            'author'         => $request->request->filter('author', '', FILTER_SANITIZE_STRING),
            'editorial'      => $request->request
                ->filter('editorial', '', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES),
            'cover_id'       => $request->request->getInt('book_cover_id'),
            'description'    => $request->request->filter('description', ''),
            'starttime'      => $request->request->filter('starttime', '', FILTER_SANITIZE_STRING),
            'category'       => $request->request->getInt('category', 0),
            'position'       => $request->request->getInt('position', 1),
            'content_status' => $request->request->getInt('content_status', 0),
            'tag_ids'        => json_decode($request->request->get('tag_ids', ''), true)
        ];

        if ($book->update($data)) {
            $book->setPosition($data['position']);
            $this->get('session')->getFlashBag()->add(
                'success',
                _('Book updated succesfully.')
            );
        }

        return $this->redirect($this->generateUrl('admin_book_show', [
            'category' => $this->category,
            'id'       => $book->id,
        ]));
    }

    /**
     * Deletes a book given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("hasExtension('BOOK_MANAGER')
     *     and hasPermission('BOOK_DELETE')")
     */
    public function deleteAction(Request $request)
    {
        $id = $request->query->getInt('id');

        $book = new \Book($id);
        if (is_null($book->id)) {
            $this->get('session')->getFlashBag()->add(
                'error',
                sprintf(_('Unable to find the book with the id "%d"'), $id)
            );
        }

        $book->delete($id);

        $this->get('session')->getFlashBag()->add(
            'success',
            sprintf(_("Book '%s' deleted successfully."), $book->title)
        );

        return $this->redirect($this->generateUrl('admin_books', [
            'category' => $book->category
        ]));
    }

    /**
     * Save positions for widget
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("hasExtension('BOOK_MANAGER')
     *     and hasPermission('BOOK_ADMIN')")
     */
    public function savePositionsAction(Request $request)
    {
        $positions = $request->request->get('positions');

        $result = true;
        if (isset($positions)
            && is_array($positions)
            && count($positions) > 0
        ) {
            $pos = 1;
            foreach ($positions as $id) {
                $book   = new \Book($id);
                $result = $result && $book->setPosition($pos);
                $pos++;
            }
        }

        if ($result) {
            $msg = "<div class='alert alert-success'>"
                . _("Positions saved successfully.")
                . '<button data-dismiss="alert" class="close">×</button></div>';
        } else {
            $msg = "<div class='alert alert-error'>"
                . _("Unable to save the new positions. Please contact with your system administrator.")
                . '<button data-dismiss="alert" class="close">×</button></div>';
        }

        return new Response($msg);
    }
}
