<?php
/*
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
use Onm\Settings as s;

/**
 * Start up and setup the app
*/
require_once '../bootstrap.php';

/**
 * Setup view
 */
$tpl = new Template(TEMPLATE_USER);

/**************  CATEGORIES & SUBCATEGORIES  *********************************/
/**
 * Setting up available categories for menu.
*/
$ccm = new ContentCategoryManager();

$contentType = Content::getIDContentType('book');

$categories = $ccm->find('internal_category='.$contentType, 'ORDER BY posmenu');
list($categories, $subcat, $categoryData) = $ccm->getArraysMenu('', $contentType);

$tpl->assign('categories', $categories);

//*****************************************************************************/
$tpl->assign('LIBROS_IMG_PATH', INSTANCE_MEDIA_PATH.'/books/');
$tpl->assign('LIBROS_FILES_PATH', INSTANCE_MEDIA_PATH.'/books/');
//*****************************************************************************/

$page = $request->query->filter('page', 1, FILTER_VALIDATE_INT);
$tpl->assign('page', $page);

$action = $request->query->filter('action', 'list', FILTER_SANITIZE_STRING);

$cm = new ContentManager();
switch ($action) {
    case 'list':
        $categoryBooks = array();
        $i=0;
        foreach ($categories as $cat) {
            //only books categories
            if ($cat->internal_category == $contentType) {
                $categoryBooks[$i] = new stdClass();
                $categoryBooks[$i]->id    = $cat->pk_content_category;
                $categoryBooks[$i]->title = $cat->title;
                $categoryBooks[$i]->books = $cm->find_by_category(
                    'Book',
                    $cat->pk_content_category,
                    'available=1',
                    'ORDER BY position ASC, created DESC LIMIT 5'
                );
                $i++;
            }
        }
        $tpl->assign('categoryBooks', $categoryBooks);

        $tpl->display('books/books_frontpage.tpl');

        break;
    case 'view':

        $dirtyID = $request->query->filter('id', null, FILTER_SANITIZE_STRING);

        $id = Content::resolveID($dirtyID);

        $book = new Book($id);
        Content::setNumViews($id);
        $book->category_title = $book->loadCategoryTitle($book->id);
        $tpl->assign('book', $book);
        $swf = preg_replace('%\.pdf%', '.swf', $book->file_name);
        $tpl->assign('archivo_swf', $swf);

        $books = $cm->find_by_category(
            'Book',
            $book->category,
            'available=1',
            'ORDER BY position ASC, created DESC LIMIT 5'
        );

        $tpl->assign('libros', $books);

        $tpl->display('books/book_viewer.tpl');

        break;
    case 'more_books':

        $category = $request->query->filter('category', null, FILTER_SANITIZE_STRING);
        if ($page<1) {
            $page = 1;
            $tpl->assign('page', $page);
        }
        $_limit = 'LIMIT '.(($page - 1) * 5).',  5';

        $books = $cm->find_by_category(
            'Book',
            $category,
            'available=1',
            'ORDER BY position ASC, created DESC  '. $_limit
        );

        if (empty($books)) {
            $page = $page - 1;
            $tpl->assign('page', $page);
            $_limit = 'LIMIT '.(($page - 1) * 5).',  5';

            $books = $cm->find_by_category(
                'Book',
                $category,
                'content_status=1',
                'ORDER BY position ASC, created DESC  '. $_limit
            );

        }

        $tpl->assign(
            array(
                'actualCat'=> $category,
                'libros' => $books
            )
        );

        $html = $tpl->fetch('books/widget_books.tpl');

        echo $html;
        break;
}

