<?php
/*
 * headlines group by categorys
*/

$titulares = $cm->findHeadlines();
$i = 1;

foreach ($categories as $this_category) {

    $category_titulares = array();
    foreach ($titulares as $titul) {
        if ($this_category['name'] == $titul['catName']) {
            $category_titulares[] = $titul;
        }
    }
    $c = $this_category['posmenu'];
    $titulares_cat[$c] = array_slice($category_titulares, 0, 5, true);
    $i++;

}

$tpl->assign('categories_data', $categories);

if (isset($titulares_cat)) {
    $tpl->assign('titulares_cat', $titulares_cat);
}
