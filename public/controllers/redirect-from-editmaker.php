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
$contentType = $request->query->filter(
    'content_type', null, FILTER_SANITIZE_STRING
);
$contentId   = $request->query->filter(
    'content_id', null, FILTER_SANITIZE_STRING
);

$url = SITE_URL;

// All the info is available so lets create url to redirect to
if (!is_null($contentId)) {

    switch ($contentType) {
        case 'article':
            list($type,$newContentID)
                = getOriginalIdAndContentTypeFromID($contentId);

            $finalId = Content::resolveID($newContentID);

            if ($type == 'article') {
                $article = new Article($finalId);
                $article->category_name = $article->catName;

                $url .=  Uri::generate(
                    'article',
                    array(
                        'id'       => $article->id,
                        'date'     => date('YmdHis', strtotime($article->created)),
                        'category' => $article->category_name,
                        'slug'     => $article->slug,
                    )
                );
            } elseif ($type == 'opinion') {

                $opinion = new Opinion($finalId);
                $url .=  Uri::generate(
                    'opinion',
                    array(
                        'id'       => $opinion->id,
                        'date'     => date('YmdHis', strtotime($opinion->created)),
                        'category' => StringUtils::get_title($opinion->name),
                        'slug'     => $opinion->slug,
                    )
                );
            }
            break;

        case 'category':
            $newContentID
                = getOriginalIDForContentTypeAndID($contentType, $contentId);

            $cc = new ContentCategory($newContentID);

            $url .= Uri::generate('section', array('id' => $cc->name));
            break;

        default:
            break;
    }
}

if (isset($_REQUEST['stop_redirect'])) {
    echo $url;
} else {
    header("HTTP/1.1 301 Moved Permanently");
    header("Location: $url");
}
