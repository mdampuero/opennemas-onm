<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Frontend\Controllers;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Onm\Framework\Controller\Controller;
use Onm\Settings as s;

/**
 * Handles the actions for the system information
 *
 * @package Backend_Controllers
 * @author
 **/
class ErrorController extends Controller
{
    /**
     * Common code for all the actions
     *
     * @return void
     * @author
     **/
    public function init()
    {
        $this->view = new \Template(TEMPLATE_USER);
    }

    /**
     * Description of the action
     *
     * @return void
     **/
    public function defaultAction(Request $request)
    {
        $errorCode     = $request->query->filter('errordoc', 404, FILTER_SANITIZE_STRING);
        $category_name = $request->query->filter('category_name', '', FILTER_SANITIZE_STRING);
        $cache_page    = $request->query->filter('page', 0, FILTER_VALIDATE_INT);

        // Look how to get advertisements without this require.
        require_once 'controllers/statics_advertisement.php';
        if ($errorCode =='404' || empty($errorCode)) {
            $code = 404;
            $content = $this->view->fetch('static_pages/404.tpl');
        } else {
            $code = 500;
            $page = new \stdClass();

            // Dummy content while testing this feature
            $page->title   = 'No hemos podido encontrar la página que buscas.';
            $page->content = 'Whoups!';

            $content = $this->renderView('static_pages/statics.tpl', array(
                'category_real_name' => $page->title,
                'page'               => $page,
            ));
        }

        return new Response($content, $code);
    }
}

