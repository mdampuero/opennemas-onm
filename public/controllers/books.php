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
require_once('../bootstrap.php');

/**
 * Setup view
 */
$tpl = new Template(TEMPLATE_USER);

/******************************  CATEGORIES & SUBCATEGORIES  *********************************/
/**
 * Setting up available categories for menu.
*/
$ccm = new ContentCategoryManager();

$contentType = Content::getIDContentType('book');
 
$categories = $ccm->find('internal_category='.$contentType, 'ORDER BY posmenu');
list($categories, $subcat, $categoryData) = $ccm->getArraysMenu('', $contentType);

$tpl->assign('categories', $categories);
 

//*****************************************************************************/
$tpl->assign('LIBROS_IMG_PATH',  INSTANCE_MEDIA_PATH.'/books/');
$tpl->assign('LIBROS_FILES_PATH',  INSTANCE_MEDIA_PATH.'/books/');
//*****************************************************************************/

$cm = new ContentManager();

$page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT,  array('options' => array('default' => '1'))  );
$tpl->assign('page', $page);


$action = filter_input( INPUT_POST, 'action' , FILTER_SANITIZE_STRING );
if (!isset($action)) {
    $action = filter_input( INPUT_GET, 'action' , FILTER_SANITIZE_STRING, array('options' => array('default' => 'list')) );
}
switch($action) {
    
    case 'list':
        $categoryBooks = array();
        $i=0;
        foreach ($categories as $cat) {
            $categoryBooks[$i] = new stdClass();
            $categoryBooks[$i]->id = $cat->pk_content_category;
            $categoryBooks[$i]->title = $cat->title;
            $categoryBooks[$i]->books = $cm->find_by_category('Libro', $cat->pk_content_category,
                    'content_status=1', 'ORDER BY position ASC, created DESC LIMIT 5');
            $i++;
        }
        $tpl->assign('categoryBooks', $categoryBooks);
        

        $tpl->display('books/frontpage.tpl');
        
        break;
    
    case 'view' :
        //Implementar vista de pdf con flex paper
        $id =  filter_input(INPUT_GET,'id',FILTER_SANITIZE_STRING);

        $book = new Book($id);
        $tpl->assign('book',$book);
        $swf = preg_replace('%\.pdf%', '.swf', $book->file);
        
        $tpl->assign('archivo_swf', $swf);
        $tpl->assign('FLEX_PATH', SITE_URL.'/media/files/libros/Flex/');

        $books = $cm->find_by_category('Book', $book->category, 'content_status=1',
                    'ORDER BY position ASC, created DESC LIMIT 5');

        $tpl->assign('libros', $books);

        $tpl->display('books/book_viewer.tpl');
        
        break;

     case 'more_books' :

         $category = filter_input(INPUT_GET, 'category', FILTER_VALIDATE_INT );
         if ($page<1) {
             $page = 1;
             $tpl->assign('page', $page);
         }
         $_limit = 'LIMIT '.(($page - 1) * 5).',  5';

         $books = $cm->find_by_category('Book', $category, 'content_status=1',
                    'ORDER BY position ASC, created DESC  '. $_limit);

         if (empty($books)) {
             $page = $page - 1;
             $tpl->assign('page', $page);
             $_limit = 'LIMIT '.(($page - 1) * 5).',  5';

             $books = $cm->find_by_category('Book', $category, 'content_status=1',
                    'ORDER BY position ASC, created DESC  '. $_limit);

         }
         
         $tpl->assign( array( 'actualCat'=> $category,
                    'libros' => $books) );
 

         $html = $tpl->fetch('books/partials/widget_books.tpl');

         echo $html;
         exit(0);
         
         break;
}

