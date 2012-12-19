<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
require_once '../bootstrap.php';

// Fetch HTTP variables
$contentType = $request->query->filter('content_type', 'null', FILTER_SANITIZE_STRING);
$contentId   = $request->query->filter('content_id', null, FILTER_SANITIZE_STRING);

$url = SITE_URL;

// All the info is available so lets create url to redirect to
if (!is_null($contentId)) {
    //Instantiate objects we will use
    $cm = new ContentManager();
    $ccm = ContentCategoryManager::get_instance();

    switch ($contentType) {
        case 'article':

            list($type,$newContentID) =
                getOriginalIdAndContentTypeFromID($contentId);

            if ($type == 'article') {
                $article = new Article($newContentID);
                $article->category_name = $article->catName;
                $url .=  $article->uri;
            } elseif ($type == 'opinion') {
                $opinion = new Opinion($newContentID);
                $url .=  $opinion->uri;
            }elseif ($type == 'Fauna' || $type == 'TopSecret') {
                $article = new Article($newContentID);
                $article->category_name = $article->catName;
                $url .=  $article->uri;
            }
            break;
        case 'category':

            $newContentID = getOriginalIDForContentTypeAndID($contentType, $contentId);

            $cc = new ContentCategory($newContentID);

            $url .= Uri::generate('section', array('id' => $cc->name));

            break;
        default:

            break;
    }
}

if (isset($_REQUEST['stop_redirect'])) {
    echo $url;
    die();
} else {
    header("HTTP/1.1 301 Moved Permanently");
    header("Location: $url");
}

