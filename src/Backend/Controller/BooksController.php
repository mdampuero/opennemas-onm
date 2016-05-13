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
use Backend\Annotation\CheckModuleAccess;
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
        // Take out this crap from this PLEASE ---------------------------------
        $contentType = \ContentManager::getContentTypeIdFromName('book');

        $this->category = $this->get('request')->query->filter('category', 'all', FILTER_SANITIZE_STRING);

        $this->ccm = \ContentCategoryManager::get_instance();
        list($this->parentCategories, $this->subcat, $this->categoryData) =
            $this->ccm->getArraysMenu($this->category, $contentType);

        $this->bookCategories = array();
        foreach ($this->parentCategories as $bCat) {
            if ($bCat->internal_category == $contentType) {
                $this->bookCategories[] = $bCat;
            }
        }

        $timezones = \DateTimeZone::listIdentifiers();
        $timezone = new \DateTimeZone($timezones[s::get('time_zone', 'UTC')]);

        $this->view->assign(
            array(
                'category'     => $this->category,
                'subcat'       => $this->subcat,
                'allcategorys' => $this->bookCategories,
                'datos_cat'    => $this->categoryData,
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
     *
     * @CheckModuleAccess(module="BOOK_MANAGER")
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

        $categories = [ [ 'name' => _('All'), 'value' => -1 ] ];

        foreach ($this->parentCategories as $key => $category) {
            $categories[] = [
                'name' => $category->title,
                'value' => $category->name
            ];

            foreach ($this->subcat[$key] as $subcategory) {
                $categories[] = [
                    'name' => '&rarr; ' . $subcategory->title,
                    'value' => $subcategory->name
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
     * @Security("has_role('BOOK_ADMIN')")
     *
     * @CheckModuleAccess(module="BOOK_MANAGER")
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
     *
     * @CheckModuleAccess(module="BOOK_MANAGER")
     **/
    public function createAction(Request $request)
    {
        if ('POST' != $request->getMethod()) {
            $this->view->assign('category', $this->category);

            return $this->render('book/new.tpl');
        }

        $data = [
            'title'          => $request->request->filter('title', '', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES),
            'author'         => $request->request->filter('author', '', FILTER_SANITIZE_STRING),
            'cover_id'       => $request->request->getInt('book_cover_id'),
            'editorial'      => $request->request->filter('editorial', '', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES),
            'description'    => $request->request->filter('description', ''),
            'metadata'       => $request->request->filter('metadata', '', FILTER_SANITIZE_STRING),
            'starttime'      => $request->request->filter('starttime', '', FILTER_SANITIZE_STRING),
            'category'       => $request->request->getInt('category', 0),
            'position'       => $request->request->getInt('position', 1),
            'content_status' => $request->request->getInt('content_status', 0),
        ];

        $book = new \Book();
        $id   = $book->create($data);

        if (!empty($id)) {
            $book->setPosition($data['position']);
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

    /**
     * Shows the book information given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("has_role('BOOK_UPDATE')")
     *
     * @CheckModuleAccess(module="BOOK_MANAGER")
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
     *
     * @CheckModuleAccess(module="BOOK_MANAGER")
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
            'title'          => $request->request->filter('title', '', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES),
            'author'         => $request->request->filter('author', '', FILTER_SANITIZE_STRING),
            'editorial'      => $request->request->filter('editorial', '', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES),
            'cover_id'       => $request->request->getInt('book_cover_id'),
            'description'    => $request->request->filter('description', ''),
            'metadata'       => $request->request->filter('metadata', '', FILTER_SANITIZE_STRING),
            'starttime'      => $request->request->filter('starttime', '', FILTER_SANITIZE_STRING),
            'category'       => $request->request->getInt('category', 0),
            'position'       => $request->request->getInt('position', 1),
            'content_status' => $request->request->getInt('content_status', 0),
        ];

        if ($book->update($data)) {
            $book->setPosition($data['position']);
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
     *
     * @CheckModuleAccess(module="BOOK_MANAGER")
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
     *
     * @CheckModuleAccess(module="BOOK_MANAGER")
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
