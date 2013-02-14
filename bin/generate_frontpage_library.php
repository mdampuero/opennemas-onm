#!/usr/bin/php5
<?php
/**
 * Start up and setup the app
*/

//TODO: use includepath as import-tools.

$_SERVER['SERVER_NAME']   = 'www.cronicasdelaemigracion.com';
//$_SERVER['SERVER_NAME'] = 'onm-cronicas.local';
$_SERVER['REQUEST_URI']   = '/';
$_SERVER['REQUEST_PORT']  = '80';
$_SERVER['SERVER_PORT']   = '80';
$_SERVER['HTTP_HOST']     ='www.cronicasdelaemigracion.com';

require __DIR__.'/../app/bootstrap.php';

/**
 * Setup view
*/
$tpl = new Template(TEMPLATE_USER);
//$tpl->setConfig('newslibrary');

$urlBase = SITE_URL."seccion/";
//TODO: work with db
//$menuItems     = Menu::renderMenu('frontpage');
$date          =  new DateTime();
$directoryDate = $date->format("/Y/m/d/");
$basePath      = SITE_PATH."/media/cronicas/library".$directoryDate;
$curly         = array();

if ( !file_exists($basePath) ) {
    mkdir($basePath, 0777, true);
}

// multi handle
$mh = curl_multi_init();

$items = array('home', 'cronicas', 'galicia', 'castillaleon', 'asturias',
    'madrid', 'canarias', 'andalucia', 'cantabria', 'baleares', 'paisvasco');

//foreach ($menuItems->items as $id => $item) {
foreach ($items as $category_name) {
    // $category_name = $item->link;

    if ( !empty($category_name) ) {

        $curly[$category_name] = curl_init();

        $url = $urlBase. $category_name.'/';
        curl_setopt($curly[$category_name], CURLOPT_URL, $url);
        curl_setopt($curly[$category_name], CURLOPT_HEADER, 0);
        curl_setopt($curly[$category_name], CURLOPT_RETURNTRANSFER, 1);

        curl_multi_add_handle($mh, $curly[$category_name]);
    }
}

  // execute the handles
$running = null;
do {

    curl_multi_exec($mh, $running);

} while ($running > 0);


// change menu to stay in archive fronpages
$pattern     = array();
$replacement = array();

foreach ($items as $category) {
    $pattern[] = "@href=\"/seccion/{$category}\"@";
    //archive/digital/2013/02/02/home.html
    $replacement[] = "href=\"/archive/digital{$directoryDate}{$category}.html\"";
}
    array_push($pattern, "@href=\"/\"@");
    //archive/digital/2013/02/02/home.html
    array_push($replacement, "href=\"/archive/digital{$directoryDate}home.html\"");

  // get content and remove handles
foreach ($curly as $category_name => $c) {
    $htmlOut = curl_multi_getcontent($c);

    $htmlOut = preg_replace($pattern, $replacement, $htmlOut);

    $newFile = $basePath.$category_name.".html";
    $result  = file_put_contents($newFile, $htmlOut);

    curl_multi_remove_handle($mh, $c);
}
  // all done
curl_multi_close($mh);
echo "generate ok \n";
