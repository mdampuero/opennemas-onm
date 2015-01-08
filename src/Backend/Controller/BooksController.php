<?php
/**
 * Handles the actions for the system information
 *
 * @package Backend_Controllers
 **/
/*
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Backend\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Onm\Security\Acl;
use Onm\Framework\Controller\Controller;
use Onm\Settings as s;
use Onm\StringUtils;

/**
 * Handles the actions for the system information
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
        //Check if module is activated in this onm instance
        \Onm\Module\ModuleManager::checkActivatedOrForward('BOOK_MANAGER');

        // Take out this crap from this PLEASE ---------------------------------
        $contentType = \ContentManager::getContentTypeIdFromName('book');

        $this->category = $this->get('request')->query->filter('category', 'all', FILTER_SANITIZE_STRING);

        $this->ccm = \ContentCategoryManager::get_instance();
        list($parentCategories, $subcat, $categoryData) =
            $this->ccm->getArraysMenu($this->category, $contentType);

        $bookCategories = array();
        foreach ($parentCategories as $bCat) {
            if ($bCat->internal_category == $contentType) {
                $bookCategories[] = $bCat;
            }
        }

        $timezones = \DateTimeZone::listIdentifiers();
        $timezone = new \DateTimeZone($timezones[s::get('time_zone', 'UTC')]);

        $this->view->assign(
            array(
                'category'     => $this->category,
                'subcat'       => $subcat,
                'allcategorys' => $bookCategories,
                'datos_cat'    => $categoryData,
                'timezone'     => $timezone->getName()
            )
        );
    }

    /**
     * Lists all the
     *
     * @return Response the response object
     *
     * @Security("has_role('BOOK_ADMIN')")
     **/
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

        return $this->render('book/list.tpl');
    }

    /**
     * List books favorites for widget
     *
     * @return Response the response object
     *
     * @Security("has_role('BOOK_ADMIN')")
     **/
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

        return $this->render(
            'book/list.tpl',
            array(
                'category' => 'widget',
            )
        );
    }

    /**
     * Shows and handles the form for create new books
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("has_role('BOOK_CREATE')")
     **/
    public function createAction(Request $request)
    {
        if ('POST' != $request->getMethod()) {
            $this->view->assign('category', $this->category);

            return $this->render('book/new.tpl');

        } else {

            $data = array(
                'title'       => $request->request->filter('title', '', FILTER_SANITIZE_STRING),
                'author'      => $request->request->filter('author', '', FILTER_SANITIZE_STRING),
                'cover_id'    => $request->request->filter('cover_image', '', FILTER_SANITIZE_STRING),
                'editorial'   => $request->request->filter('editorial', '', FILTER_SANITIZE_STRING),
                'description' => $request->request->filter('description', '', FILTER_SANITIZE_STRING),
                'metadata'    => $request->request->filter('metadata', '', FILTER_SANITIZE_STRING),
                'starttime'   => $request->request->filter('starttime', '', FILTER_SANITIZE_STRING),
                'category'    => $request->request->getInt('category', 0),
                'content_status'   => $request->request->getInt('content_status', 0),
            );

            $book = new \Book();
            $id   = $book->create($data);

            if (!empty($id)) {

                $book = $book->read($id);

                return $this->render('book/new.tpl', array('book' => $book));

            } else {
                $this->get('session')->getFlashBag()->add(
                    'error',
                    _("Unable to create the new book.")
                );
            }

            return $this->render('book/new.tpl');
        }
    }

    /**
     * Shows the book information given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("has_role('BOOK_UPDATE')")
     **/
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

        return $this->render(
            'book/new.tpl',
            array(
                'book'     => $book,
                'category' => $book->category,
            )
        );
    }

    /**
     * Handles the form for update a book given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("has_role('BOOK_UPDATE')")
     **/
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

        if (!Acl::check('CONTENT_OTHER_UPDATE')
            && !$book->isOwner($_SESSION['userid'])
        ) {
            throw new AccessDeniedException();
        }

        // Check empty data
        if (count($request->request) < 1) {
            $this->get('session')->getFlashBag()->add(
                'error',
                _("Book data sent not valid.")
            );

            return $this->redirect($this->generateUrl('admin_book_show', array('id' => $id)));
        }

        $data = [
            'id'             => $id,
            'title'          => $request->request->filter('title', '', FILTER_SANITIZE_STRING),
            'author'         => $request->request->filter('author', '', FILTER_SANITIZE_STRING),
            'editorial'      => $request->request->filter('editorial', '', FILTER_SANITIZE_STRING),
            'cover_id'       => $request->request->filter('cover_image', '', FILTER_SANITIZE_STRING),
            'description'    => $request->request->filter('description', '', FILTER_SANITIZE_STRING),
            'metadata'       => $request->request->filter('metadata', '', FILTER_SANITIZE_STRING),
            'starttime'      => $request->request->filter('starttime', '', FILTER_SANITIZE_STRING),
            'category'       => $request->request->getInt('category'),
            'content_status' => $request->request->getInt('content_status'),
        ];

        if ($book->update($data)) {
            $this->get('session')->getFlashBag()->add(
                'success',
                _('Book updated succesfully.')
            );
        }

        return $this->redirect(
            $this->generateUrl(
                'admin_book_show',
                array(
                    'category' => $this->category,
                    'id'       => $book->id,
                )
            )
        );
    }

    /**
     * Deletes a book given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("has_role('BOOK_DELETE')")
     **/
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

        return $this->redirect(
            $this->generateUrl(
                'admin_books',
                array(
                    'category' => $book->category
                )
            )
        );
    }

    /**
     * Save positions for widget
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("has_role('BOOK_ADMIN')")
     **/
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
                $book = new \Book($id);
                $result = $result && $book->setPosition($pos);
                $pos += 1;
            }

        }

        if ($result) {
            $msg = "<div class='alert alert-success'>"
                ._("Positions saved successfully.")
                .'<button data-dismiss="alert" class="close">×</button></div>';
        } else {
            $msg = "<div class='alert alert-error'>"
                ._("Unable to save the new positions. Please contact with your system administrator.")
                .'<button data-dismiss="alert" class="close">×</button></div>';
        }

        return new Response($msg);
    }
}
