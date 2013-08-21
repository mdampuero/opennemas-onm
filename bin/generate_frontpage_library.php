#!/usr/bin/php5
<?php
/**
 * Start up and setup the app
*/
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Console\Application;

require __DIR__.'/../app/autoload.php';


$_SERVER['SERVER_NAME']   = 'www.cronicasdelaemigracion.com';
//$_SERVER['SERVER_NAME'] = 'onm-cronicas.local:8080';
$_SERVER['REQUEST_URI']   = '/';
$_SERVER['REQUEST_PORT']  = '8080';
$_SERVER['SERVER_PORT']   = '8080';
$_SERVER['HTTP_HOST']     ='www.cronicasdelaemigracion.com';

// Load the available route collection
$routes = new \Symfony\Component\Routing\RouteCollection();

// Create the request object
$request = Request::createFromGlobals();
$request->setTrustedProxies(array('127.0.0.1'));

require __DIR__.'/../app/container.php';


require __DIR__.'/../app/bootstrap.php';

/**
 * Setup view
*/
$tpl = new Template(TEMPLATE_USER);

$urlBase = SITE_URL."seccion/";

$date          =  new DateTime();
$directoryDate = $date->format("/Y/m/d/");
$basePath      = SITE_PATH."/media/cronicas/library".$directoryDate;
$curly         = array();

if ( !file_exists($basePath) ) {
    mkdir($basePath, 0777, true);
}

// multi handle
$mh = curl_multi_init();

$menu = new \Menu();
$menu->getMenu('archive');

if (count(($menu->items)) <= 0) {
    echo "There are no frontpages. You must define archive menu. \n";
    die();
}

foreach ($menu->items as $item) {

    $category_name = $item->link;

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

foreach ($menu->items as $item) {
    $category = $item->link;
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
