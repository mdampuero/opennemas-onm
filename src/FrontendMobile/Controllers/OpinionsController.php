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
    }

    /**
     * Description of the action
     *
     * @return Response the response object
     **/
    public function frontpageAction(Request $request)
    {
        // Necesaria esta asignación para que funcione en index_sections.php e o menú
        $category_name = $_GET['category_name'] = 'opinion';
        $subcategory_name = null;
        $section = $category_name;

        $ccm = \ContentCategoryManager::get_instance();
        list($category_name, $subcategory_name) = $ccm->normalize($category_name, $subcategory_name);
        // $this->view->assign('ccm', $ccm);

        //Get rid of this as soon as posible
        // require_once 'sections.php';

        $section = (!empty($subcategory_name))? $subcategory_name: $category_name;
        $section = (is_null($section))? 'home': $section;

        //Fetch opinions
        $cm = new \ContentManager();
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

        $this->view->assign('editorial', $editorial);
        if (isset ($director[0])) {
            $director[0]->name = 'Director';
            $this->view->assign('director', $director[0]);
        }

        //Obtener los slug's de los autores
        foreach ($opinions as $i => $op) {
            $opinions[$i]['author_name_slug'] = \StringUtils::get_title($op['name']);
        }

        return $this->render(
            'mobile/opinion-index.tpl',
            array(
                'opinions' => $opinions,
                'section'  => $section
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
        // Fetch vars
        $category_name = $_GET['category_name'] = 'opinion';
        $dirtyID = $request->query->getDigits('opinion_id');

        $opinionID = \Content::resolveID($dirtyID);

        $opinion = new \Opinion($opinionID);

        $aut = new \Author($opinion->fk_author);

        $photo = $aut->get_photo($opinion->fk_author_img);

        // Show in Frontpage
        return $this->render(
            'mobile/opinion-inner.tpl',
            array(
                'opinion'     => $opinion,
                'author_name' => $opinion->name,
                'condition'   => $opinion->condition,
                'section'     => 'opinion',
                'photo'       => $photo
            )
        );
    }
}
