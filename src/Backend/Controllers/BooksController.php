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
namespace Backend\Controllers;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Onm\Framework\Controller\Controller;
use Onm\Settings as s;
use Onm\Message as m;
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

        $this->checkAclOrForward('BOOK_ADMIN');

        // Take out this crap from this PLEASE ---------------------------------
        $contentType = \ContentManager::getContentTypeIdFromName('book');

        $this->category = $this->request->query->filter('category', 'all', FILTER_SANITIZE_STRING);

        $this->ccm = \ContentCategoryManager::get_instance();
        list($parentCategories, $subcat, $categoryData) =
            $this->ccm->getArraysMenu($this->category, $contentType);

        $bookCategories = array();
        foreach ($parentCategories as $bCat) {
            if ($bCat->internal_category == $contentType) {
                $bookCategories[] = $bCat;
            }
        }

        $this->view->assign(
            array(
                'category'     => $this->category,
                'subcat'       => $subcat,
                'allcategorys' => $bookCategories,
                'datos_cat'    => $categoryData,
            )
        );
        // ---------------------------------------------------------------------

        // Optimize  this crap  ---------------------------------------
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
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function listAction(Request $request)
    {
        $page           = $request->query->getDigits('page', 1);
        $status         = $request->query->getDigits('status');

        $itemsPerPage   = s::get('items_per_page');
        $configurations = s::get('book_settings');
        $numFavorites   =  1;

        if (isset($configurations['total_widget'])
            && !empty($configurations['total_widget'])) {
            $numFavorites =  $configurations['total_widget'];
        }

        $cm = new \ContentManager();

        if (empty($page)) {
            $limit = "LIMIT ".($itemsPerPage+1);
        } else {
            $limit = "LIMIT ".($page-1) * $itemsPerPage .', '.$itemsPerPage;
        }

        if ($this->category == 'all') {
            $categoryForLimit = null;
        } else {
            $categoryForLimit = $this->category;
        }

        $filter = ' contents.in_litter != 1 ';
        if (($status != '') && ($status != null)) {
            $filter .= ' AND contents.available = '. $status;
        }

        list($booksCount, $books) = $cm->getCountAndSlice(
            'book',
            $categoryForLimit,
            $filter,
            'ORDER BY position ASC, created DESC',
            $page,
            $itemsPerPage
        );

        if (!empty($books)) {
            foreach ($books as &$book) {
                $book->category_name  = $this->ccm->get_name($book->category);
                $book->category_title = $this->ccm->get_title($book->category_name);
            }
        }
        if (count($books) != $numFavorites) {
            m::add(sprintf(_("You must put %d books in the HOME widget"), $numFavorites));
        }

        // Build the pager
        $pagination = \Pager::factory(
            array(
                'mode'        => 'Sliding',
                'perPage'     => $itemsPerPage,
                'append'      => false,
                'path'        => '',
                'delta'       => 4,
                'clearIfVoid' => true,
                'urlVar'      => 'page',
                'totalItems'  => $booksCount,
                'fileName'    => $this->generateUrl(
                    'admin_books',
                    array('category' => $this->category)
                ).'&page=%d',
            )
        );

        return $this->render(
            'book/list.tpl',
            array(
                'pagination' => $pagination,
                'page'       => $page,
                'status'     => $status,
                'books'      => $books
            )
        );
    }

    /**
     * List books favorites for widget
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function widgetAction(Request $request)
    {
        $this->checkAclOrForward('BOOK_FAVORITE');

        $configurations = s::get('books_settings');
        $numFavorites   = $configurations['total_widget'];


        $cm = new \ContentManager();
        $books = $cm->find_all('book', 'in_home = 1 AND available =1', 'ORDER BY  position ASC ');

        if (!empty($books)) {
            foreach ($books as &$book) {
                $book->category_name  = $this->ccm->get_name($book->category);
                $book->category_title = $this->ccm->get_title($book->category_name);
            }
        }

        if (count($books) != $numFavorites) {
            m::add(sprintf(_("You must put %d books in the HOME widget"), $numFavorites));
        }

        return $this->render(
            'book/list.tpl',
            array(
                'books' => $books,
                'category' => $this->category,
            )
        );
    }

    /**
     * Shows and handles the form for create new books
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function createAction(Request $request)
    {
        $this->checkAclOrForward('BOOK_CREATE');

        $page    = $request->query->getDigits('page', 1);

        if ('POST' != $request->getMethod()) {
            $this->view->assign('category', $this->category);

            return $this->render('book/new.tpl');

        } else {
            $bookSavePath = INSTANCE_MEDIA_PATH.'/books/';
            $fileName     = StringUtils::cleanFileName($_FILES['file']['name']);
            $imageName    = StringUtils::cleanFileName($_FILES['file_img']['name']);

            // Move uploaded pdf
            $uploadStatusPdf    = @move_uploaded_file($_FILES['file']['tmp_name'], $bookSavePath.$fileName);
            $uploadStatusPdfImg = @move_uploaded_file($_FILES['file_img']['tmp_name'], $bookSavePath.$imageName);

            $data = array(
                'title'       => $request->request->filter('title', '', FILTER_SANITIZE_STRING),
                'author'      => $request->request->filter('author', '', FILTER_SANITIZE_STRING),
                'file_name'   => $fileName,
                'file_img'    => $imageName,
                'editorial'   => $request->request->filter('editorial', '', FILTER_SANITIZE_STRING),
                'description' => $request->request->filter('description', '', FILTER_SANITIZE_STRING),
                'metadata'    => $request->request->filter('metadata', '', FILTER_SANITIZE_STRING),
                'starttime'   => $request->request->filter('starttime', '', FILTER_SANITIZE_STRING),
                'category'    => $request->request->getInt('category'),
                'available'   => $request->request->getInt('available'),
            );

            $book = new \Book();
            $id   =$book->create($data);

            $sizeFile = ini_get('upload_max_filesize');
            if (($uploadStatusPdf !== false) && !empty($id)) {
                $continue = $request->request->filter('continue', false, FILTER_SANITIZE_STRING);
                if ($continue) {
                    $book = $book->read($id);

                    return $this->render('book/new.tpl', array( 'book' => $book, ));
                }

                return $this->redirect(
                    $this->generateUrl(
                        'admin_books',
                        array(
                            'category' => $this->category,
                            'page' => $page
                        )
                    )
                );

            } elseif ($_FILES['file']['size'] > $sizeFile) {
                 m::add(sprintf(_("Sorry, file can't upload. You must check file size.(< %sB)"), $sizeFile));

            } else {
                 m::add(sprintf(_("Sorry, file can't upload.")));
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
     **/
    public function showAction(Request $request)
    {
        $this->checkAclOrForward('BOOK_UPDATE');

        $id = $this->request->query->getInt('id');

        $book = new \Book($id);

        if (is_null($book->id)) {
            m::add(sprintf(_('Unable to find the book with the id "%d"'), $id));

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
     **/
    public function updateAction(Request $request)
    {
        $this->checkAclOrForward('BOOK_UPDATE');

        $id = $request->request->getInt('id');

        $book = new \Book($id);

        if (is_null($book->id)) {
            m::add(sprintf(_('Unable to find the book with the id "%d"'), $id));

            return $this->redirect($this->generateUrl('admin_books'));
        }
        if (!\Acl::isAdmin()
            && !\Acl::check('CONTENT_OTHER_UPDATE')
            && $book->fk_user != $_SESSION['userid']) {

            m::add(_("You can't modify this book data because you don't have enought privileges."));

        } else {
            // Check empty data
            if (count($request->request) < 1) {
                m::add(_("Book data sent not valid."), m::ERROR);

                return $this->redirect($this->generateUrl('admin_books_show', array('id' => $id)));
            }

            $bookSavePath = INSTANCE_MEDIA_PATH.'/books/';

            if (!empty($_FILES['file']['name'])) {
                $fileName  = StringUtils::cleanFileName($_FILES['file']['name']);

                $uploadStatusPdf = @move_uploaded_file($_FILES['file']['tmp_name'], $bookSavePath.$fileName);
            } else {
                $fileName = $book->file_name;
            }

            if (!empty($_FILES['file_img']['name'])) {
                $imageName = StringUtils::cleanFileName($_FILES['file_img']['name']);
                $uploadStatusPdfImg = @move_uploaded_file($_FILES['file_img']['tmp_name'], $bookSavePath.$imageName);
            } else {
                $imageName = $book->file_img;
            }


            $data = array(
                'id'          => $id,
                'title'       => $request->request->filter('title', '', FILTER_SANITIZE_STRING),
                'author'      => $request->request->filter('author', '', FILTER_SANITIZE_STRING),
                'file_name'   => $fileName,
                'file_img'    => $imageName,
                'editorial'   => $request->request->filter('editorial', '', FILTER_SANITIZE_STRING),
                'description' => $request->request->filter('description', '', FILTER_SANITIZE_STRING),
                'metadata'    => $request->request->filter('metadata', '', FILTER_SANITIZE_STRING),
                'starttime'   => $request->request->filter('starttime', '', FILTER_SANITIZE_STRING),
                'category'    => $request->request->getInt('category'),
                'available'   => $request->request->getInt('available'),
            );

            $book->update($data);

            $continue = $request->request->filter('continue', false, FILTER_SANITIZE_STRING);
            if ($continue) {
                return $this->redirect(
                    $this->generateUrl(
                        'admin_books_show',
                        array(
                            'category' => $this->category,
                            'id'       => $book->id,
                        )
                    )
                );
            }

        }

        return $this->redirect(
            $this->generateUrl('admin_books', array('category' => $data['category'],))
        );
    }

    /**
     * Deletes a book given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function deleteAction(Request $request)
    {
        $this->checkAclOrForward('BOOK_DELETE');

        $id = $request->query->getInt('id');

        $book = new \Book($id);
        if (is_null($book->id)) {
            m::add(sprintf(_('Unable to find the book with the id "%d"'), $id));

        } else {
            $book->delete($id);
            m::add(_("Book '{$book->title}' deleted successfully."), m::SUCCESS);
        }

        return $this->redirect(
            $this->generateUrl(
                'admin_books',
                array(
                    'category' => $book->category,
                    'page' => $page
                )
            )
        );
    }

    /**
     * Deletes multiple books at once given its ids
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function batchDeleteAction(Request $request)
    {
        $this->checkAclOrForward('BOOK_DELETE');

        $page = $request->query->getDigits('page', 1);
        $selectedItems = $request->query->get('selected_fld');

        if (is_array($selectedItems)
            && count($selectedItems) > 0
        ) {
            foreach ($selectedItems as $element) {
                $book = new \Book($element);

                $relations = array();
                $relations = \RelatedContent::getContentRelations($element);

                $book->delete($element, $_SESSION['userid']);

                m::add(sprintf(_('Book "%s" deleted successfully.'), $book->title), m::SUCCESS);
            }
        }

        return $this->redirect(
            $this->generateUrl(
                'admin_books',
                array(
                    'category' => $this->category,
                    'page'    => $page,
                )
            )
        );
    }

    /**
     * Change availability for one book given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function toggleAvailableAction(Request $request)
    {
        $this->checkAclOrForward('BOOK_AVAILABLE');

        $id       = $request->query->getDigits('id', 0);
        $status   = $request->query->getDigits('status', 0);
        $page     = $request->query->getDigits('page', 1);

        $book = new \Book($id);
        if (is_null($book->id)) {
            m::add(sprintf(_('Unable to find book with id "%d"'), $id), m::ERROR);
        } else {
            $book->toggleAvailable($book->id);
            if ($status == 0) {
                $book->set_favorite($status);
            }
            m::add(sprintf(_('Successfully changed availability for book with id "%d"'), $id), m::SUCCESS);
        }

        return $this->redirect(
            $this->generateUrl(
                'admin_books',
                array(
                    'category' => $this->category,
                    'page'     => $page
                )
            )
        );
    }


    /**
     * Change in_home flag for one book given its id
     * Used for putting this content widgets in home
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function toggleInHomeAction(Request $request)
    {
        $this->checkAclOrForward('BOOK_AVAILABLE');

        $id       = $request->query->getDigits('id', 0);
        $status   = $request->query->getDigits('status', 0);
        $page     = $request->query->getDigits('page', 1);

        $book = new \Book($id);
        if (is_null($book->id)) {
            m::add(sprintf(_('Unable to find book with id "%d"'), $id), m::ERROR);
        } else {
            $book->set_inhome($status, $_SESSION['userid']);
            m::add(sprintf(_('Successfully changed suggested flag for book with id "%d"'), $id), m::SUCCESS);
        }

        return $this->redirect(
            $this->generateUrl(
                'admin_books',
                array(
                    'category' => $this->category,
                    'page'     => $page
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
     **/
    public function savePositionsAction(Request $request)
    {
        $positions = $request->request->get('positions');

        $msg = '';
        if (isset($positions)
            && is_array($positions)
            && count($positions) > 0
        ) {
            $_positions = array();
            $pos = 1;

            foreach ($positions as $id) {
                $_positions[] = array($pos, '1', $id);
                $pos += 1;
            }

            $book= new \Book();
            $msg = $book->set_position($_positions, $_SESSION['userid']);

        }

        if ($msg) {
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

    /**
     * Set the published flag for contents in batch
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function batchPublishAction(Request $request)
    {
        $this->checkAclOrForward('BOOK_AVAILABLE');

        $status   = $request->query->getDigits('status', 0);
        $selected = $request->query->get('selected_fld', null);
        $page     = $request->query->getDigits('page', 1);

        if (is_array($selected)
            && count($selected) > 0
        ) {
            foreach ($selected as $id) {
                $book = new \Book($id);
                $book->set_available($status, $_SESSION['userid']);
                if ($status == 0) {
                    $book->set_favorite($status, $_SESSION['userid']);
                }
            }
        }

        return $this->redirect(
            $this->generateUrl(
                'admin_books',
                array(
                    'category' => $this->category,
                    'page'     => $page,
                )
            )
        );
    }
}
