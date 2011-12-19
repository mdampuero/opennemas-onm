<?php
   
/**
 * Start up and setup the app
*/
require_once('../bootstrap.php');

/**
 * Fetch HTTP variables
*/
$tpl = new Template(TEMPLATE_USER);

$contentType = Content::getIDContentType('kiosko');
 
/******************************  CATEGORIES & SUBCATEGORIES  *********************************/


$category_name = filter_input(INPUT_GET,'category_name',FILTER_SANITIZE_STRING);
if(empty($category_name)) {
    $category_name = filter_input(INPUT_POST,'category_name',FILTER_SANITIZE_STRING);
}
 
$ccm = ContentCategoryManager::get_instance();
if(!empty($category_name)) {
    $category = $ccm->get_id($category_name);
}else{
    $category = 0;
}

list($allcategorys, $subcat, $categoryData) = $ccm->getArraysMenu($category, $contentType);
 //list($parentCategories, $subcat, $categoryData) = $ccm->getArraysMenu($category, $contentType);

  
require_once("index_sections.php");
 

/******************************  CATEGORIES & SUBCATEGORIES  *********************************/

$cache_page = filter_input(INPUT_GET,'page',FILTER_VALIDATE_INT);
$cache_page = (is_null($cache_page))? 0 : $cache_page;
/**
 * Setup view
*/

$tpl->setConfig('kiosko');
  
$action = filter_input(INPUT_GET,'action',FILTER_SANITIZE_STRING, array('options' => array('default' => 'list' )) );
$month = filter_input(INPUT_GET,'month',FILTER_VALIDATE_INT, array('options' => array('default' => date('n') )) );
$year = filter_input(INPUT_GET,'year',FILTER_VALIDATE_INT, array('options' => array('default' => date('Y') )) );

if(!defined('KIOSKO_DIR'))
        define('KIOSKO_DIR', "kiosko".SS);

switch($action) {
    case 'list':  
        /**
         * Avoid to run the entire app logic if is available a cache for this page
        */
        $cache_id = $tpl->generateCacheId('newsstand', $category_name,  $cache_page);

        if((1==1) ||($tpl->caching == 0)
           || !$tpl->isCached('newsstand/newsstand.tpl', $cache_id) )
        {  
            foreach ($allcategorys as $theCategory) {
                $cm = new ContentManager();

                $portadas = $cm->find_by_category('Kiosko', $theCategory->pk_content_category,
                                              ' `contents`.`available`=1   '.
                                              'AND MONTH(`kioskos`.date)='.$month.' AND'.
                                              ' YEAR(`kioskos`.date)='.$year.'',
                                              'ORDER BY `kioskos`.date DESC ');
                if(!empty($portadas)) {
                    $kiosko[] = array ('category' => $theCategory->title,
                                   'portadas' => $portadas);
                }
            }

          
            $tpl->assign( array('KIOSKO_IMG_URL' => INSTANCE_MEDIA.KIOSKO_DIR,       
                 'date' => '1-'.$month.'-'.$year,
                 'MONTH' =>$month,
                 'YEAR' => $year
            ) );

            $tpl->assign('kiosko', $kiosko);
            
        }
    
    break;
    
    case 'read':  
  
        $dirtyID = filter_input(INPUT_GET,'id',FILTER_SANITIZE_STRING);
        if(empty($dirtyID)) {
            $dirtyID = filter_input(INPUT_POST,'id',FILTER_SANITIZE_STRING);
        } 
        $epaperId = Content::resolveID($dirtyID);

        /**
         * Redirect to album frontpage if id_album wasn't provided
         */
        if (is_null($epaperId)) { Application::forward301('/portadas_papel/'); }

        
        $cache_id = $tpl->generateCacheId('newsstand', $epaperId,  $cache_page);

        $epaper = new Kiosko($epaperId);
 
        if (!empty($epaper)) {
            $tpl->assign('epaper', $epaper);
 
            $format_date = strtotime($epaper->date);                 
            $month = date('m', $format_date);
            $year = date('Y',$format_date);            
            $cm = new ContentManager();
 
            $portadas = $cm->find_by_category('Kiosko', $epaper->category,
                                          ' `contents`.`available`=1   '.
                                          'AND MONTH(`kioskos`.date)='.$month.' AND'.
                                          ' YEAR(`kioskos`.date)='.$year.' ',
                                          'ORDER BY `kioskos`.date DESC ');
            $kiosko =array();
            if(!empty($portadas)) {
                $kiosko[] = array ('category' => '',
                               'portadas' => $portadas);
            }
            $tpl->assign( array('KIOSKO_IMG_URL' => INSTANCE_MEDIA.KIOSKO_DIR,    
                 'date' => '1-'.$month.'-'.$year,
                 'MONTH' =>$month,
                 'YEAR' => $year
            ) );

            $tpl->assign('kiosko', $kiosko);
            
        } else {
            Application::forward301('/portadas_papel/'); 
        }
      
        
    break;      
}


//for widget_newsstand_dates 
//TODO: intelligent wigget
$ki = new Kiosko();            
$months_kiosko = $ki->get_months_by_years();
$tpl->assign('months_kiosko', $months_kiosko);


// advertisement NOCACHE
require_once('index_advertisement.php');

        
// Show in Frontpage
$tpl->display('newsstand/newsstand.tpl', $cache_id);
