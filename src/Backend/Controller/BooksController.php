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

use Api\Exception\GetItemException;
use Common\Core\Annotation\Security;
use Common\Core\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BooksController extends Controller
{
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
        return $this->render('book/list.tpl');
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
            return $this->render('book/new.tpl', [
                'locale' => $this->get('core.locale')->getLocale('frontend'),
            ]);
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
            'category_id'    => $request->request->getInt('category', 0),
            'content_status' => $request->request->getInt('content_status', 0),
            'in_home'        => $request->request->filter('in_home', 0, FILTER_SANITIZE_STRING),
            'tags'           => json_decode($request->request->get('tags', ''), true)
        ];

        $book = new \Book();
        $id   = $book->create($data);

        if (!empty($id)) {
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

        return $this->render('book/new.tpl', [
            'locale' => $this->get('core.locale')->getLocale('frontend'),
        ]);
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
        $id   = $request->query->getInt('id');
        $book = $this->get('entity_repository')->find('Book', $id);

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
        $service = $this->get('api.service.photo');
        try {
            $photo           = $service->getItem($book->cover_id);
            $book->cover_img = $service->responsify($photo);
        } catch (GetItemException $e) {
        }

        return $this->render('book/new.tpl', [
            'book'   => $book,
            'locale' => $this->get('core.locale')->getRequestLocale('frontend'),
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
        if (empty($request->request)) {
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
            'category_id'    => $request->request->getInt('category', 0),
            'content_status' => $request->request->getInt('content_status', 0),
            'in_home'        => $request->request->filter('in_home', 0, FILTER_SANITIZE_STRING),
            'tags'           => json_decode($request->request->get('tags', ''), true)
        ];

        if ($book->update($data)) {
            $this->get('session')->getFlashBag()->add(
                'success',
                _('Book updated succesfully.')
            );
        }

        return $this->redirect($this->generateUrl('admin_book_show', [
            'id' => $book->id,
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

        return $this->redirect($this->generateUrl('admin_books'));
    }
}
