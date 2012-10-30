<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
// Start up and setup the app
require_once '../bootstrap.php';

$contentId = $request->query->getDigits('content_id', 0);

if ($contentId <= 0) {
    header('Bad Request', true, 400);
    echo "Not content identifier provided.";
}
if ($request->isXmlHttpRequest() && $contentId > 0) {
    Content::setNumViews($contentId);
    echo "Ok";
} else {
    header('Bad Request', true, 400);
    echo "Not AJAX request";
}