<?php

/**
 * Start up and setup the app
*/
require_once('../bootstrap.php');

/**
 * Setup view
*/
$tpl = new Template(TEMPLATE_USER);
$tpl->setConfig('frontpages');
$cache_id = $tpl->generateCacheId($category_name, $subcategory_name, $cache_page);

if(($tpl->caching == 0)
   || !$tpl->isCached('frontpage/frontpage.tpl', $cache_id))
{
//generate cache

    $tplManager = new TemplateCacheManager(TEMPLATE_USER_PATH);
    $tplManager->fetch(SITE_URL . 'seccion/' . $this->category_name);

} else{
//save in a file
}