<?php
/**
 * Start up and setup the app
*/
require_once __DIR__.'/../../../bootstrap.php';

use \Symfony\Component\HttpFoundation\Response;

/**
 * Setup view
*/
$tpl = new \TemplateManager(TEMPLATE_ADMIN);

if (ENVIRONMENT === 'development') {

    // ob_start();
    echo '<html><body><pre>';
    $status = require SITE_PATH. '../bin/check-dependencies.php';
    echo '</pre></body></html>';
    // $status = ob_get_contents();

    // ob_end_clean();

    // $tpl->assign('status', $status);
    // $tpl->display('framework/status.tpl');

} else {
    $response = new Response('Content', 404, array('content-type' => 'text/html'));
}