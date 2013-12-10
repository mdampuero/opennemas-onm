<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace FrontendMobile\Controllers;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Onm\Framework\Controller\Controller;
use Onm\Message as m;
use Onm\Settings as s;

/**
 * Handles the actions for the mobile opinion section
 *
 * @package FrontendMobile_Controllers
 **/
class OpinionsController extends Controller
{
    /**
     * Common code for all the actions
     *
     * @return void
     **/
    public function init()
    {
        $this->view = new \Template(TEMPLATE_USER);
        define('BASE_PATH', 'mobile');
    }

    /**
     * Description of the action
     *
     * @return Response the response object
     **/
    public function frontpageAction(Request $request)
    {
        $categoryName = 'opinion';

        $cm  = new \ContentManager();
        $ccm = \ContentCategoryManager::get_instance();

        // Get rid of this as soon as posible
        // require_once 'sections.php';
        // TODO: Get rid of this when posible
         require __DIR__.'/../sections.php';

        //Fetch opinions
        $director  = $cm->find(
            'Opinion',
            'type_opinion=2 AND in_home=1 AND available=1 AND content_status=1',
            'ORDER BY created DESC  LIMIT 0,1'
        );
        $editorial = $cm->find(
            'Opinion',
            'type_opinion=1 AND in_home=1 AND available=1 AND content_status=1',
            'ORDER BY position ASC, created DESC LIMIT 0,2'
        );
        $opinions = $cm->getOpinionArticlesWithAuthorInfo(
            'available=1 AND type_opinion=0 AND content_status=1',
            'ORDER BY in_home DESC, position ASC, created DESC LIMIT 0,10'
        );
        if (isset ($director[0])) {
            $director[0]->name = 'Director';
            $this->view->assign('director', $director[0]);
        }

        foreach ($opinions as &$opinion) {
            $opinion['author_name_slug'] = \StringUtils::get_title($opinion['name']);
        }

        return $this->render(
            'mobile/opinion-index.tpl',
            array(
                'editorial' => $editorial,
                'opinions'  => $opinions,
                'section'   => 'opinion'
            )
        );
    }

    /**
     * Displays the mobile version of an opinion
     *
     * @return Respone the response object
     **/
    public function showAction(Request $request)
    {
        // Fetch vars from http
        $dirtyID = $request->query->getDigits('opinion_id');
        // Clean dirty id
        $opinionID = \Content::resolveID($dirtyID);

        // Fetch opinion
        $opinion = new \Opinion($opinionID);
        // Get author photo
        $photo = new \Photo($opinion->fk_author_img);

        // Show in Frontpage
        return $this->render(
            'mobile/opinion-inner.tpl',
            array(
                'opinion'     => $opinion,
                'author_name' => $opinion->name,
                'condition'   => $opinion->bio,
                'section'     => 'opinion',
                'photo'       => $photo
            )
        );
    }
}
