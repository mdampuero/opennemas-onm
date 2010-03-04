<?php
/* 
 * headlines group by categorys
 *  
 */

$titulares = $cm->find_headlines();

$i=1;
foreach($categories as $category_data) {
    $category_titulares = array();
    foreach ($titulares as $titul) {

        if($category_data['name'] == $titul['catName']){
            $category_titulares[] = $titul;
        }

    }
    $titulares_cat[$i] =array_slice($category_titulares, 0, 5, true);
    $i++;
}

$tpl->assign('categories_data',$categories);
$tpl->assign('titulares_cat',$titulares_cat);

