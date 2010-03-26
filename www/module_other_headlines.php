<?php
/* 
 * headlines group by categorys
 *  
 */

$titulares = $cm->find_headlines();

$i=0;
foreach($categories as $this_category) {
    $category_titulares = array();
    foreach ($titulares as $titul) {

        if($this_category['name'] == $titul['catName']){
            $category_titulares[] = $titul;
        }

    }
    $titulares_cat[$i] =array_slice($category_titulares, 0, 5, true);
    $i++;
}
 var_dump($titulares_cat);
$tpl->assign('categories_data',$categories);
$tpl->assign('titulares_cat',$titulares_cat);

