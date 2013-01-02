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

use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Onm\Framework\Controller\Controller;
use Onm\Message as m;
use Onm\Settings as s;

/**
 * Handles the actions for advertisements
 *
 * @package Backend_Controllers
 **/
class StaticPagesController extends Controller
{
    /**
     * Common code for all the actions
     *
     * @return void
     **/
    public function init()
    {
        $this->view = new \Template(TEMPLATE_USER);
    }

    /**
     * Displays the static page given its id
     *
     * @param string slug the slug that identifies the page
     *
     * @return Response the response object
     **/
    public function showAction(Request $request)
    {
        $slug = $request->query->filter('slug', null, FILTER_SANITIZE_STRING);

        $page = \StaticPage::getPageBySlug($slug);

        // if static page doesn't exist redirect to 404 error page.
        if (is_null($page) || (!$page->available)) {
            throw new ResourceNotFoundException();
        }

        // TODO: review this advertisement
        // require_once 'statics_advertisement.php';

        return $this->render(
            'static_pages/statics.tpl',
            array(
                'page'               => $page,
                'content'            => $page,
                'category_real_name' => $page->title,
                'content_id'         => $content->id
            )
        );
    }
}
