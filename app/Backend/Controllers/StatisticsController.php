<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Backend\Controllers;

use Onm\Framework\Controller\Controller,
    Onm\Message as m;
/**
 * Handles the actions for the system information
 *
 * @package Backend_Controllers
 * @author
 **/
class StatisticsController extends Controller
{

    /**
     * Common code for all the actions
     *
     * @return void
     * @author
     **/
    public function init()
    {
        // Initializae the session manager
        require_once './session_bootstrap.php';

        $this->view = new \TemplateAdmin(TEMPLATE_ADMIN);

        require_once SITE_LIBS_PATH.'ofc1/open-flash-chart.php';
        require_once SITE_LIBS_PATH.'ofc1/open_flash_chart_object.php';

        // Assign a content types for don't reinvent the wheel into template
        $this->view->assign('content_types', array(
            1 => 'Noticia' ,
            7 => 'Galeria',
            9 => 'Video',
            4 => 'Opinion',
            3 => 'Fichero'
        ));

        // Fetch vars
        $this->category = $this->request->query->filter('category', 0, FILTER_SANITIZE_STRING);
        if (!isset($_SESSION['desde'])) {
            $_SESSION['desde'] = 'index';
        }

        // Get all data category
        $this->ccm = \ContentCategoryManager::get_instance();
        list($this->parentCategories, $this->subcategories, $this->categoryData) = $this->ccm->getArraysMenu();

        // Assign vars to tpl
        $this->view->assign(array(
            'category'     => $this->category,
            'subcat'       => $this->subcategories,
            'allcategorys' => $this->parentCategories,
            'datos_cat'    => $this->categoryData,
        ));

    }
    /**
     * Description of the action
     *
     * @return void
     **/
    public function defaultAction()
    {
        return $this->render('statistics/statistics.tpl');
    }


    /**
     *
     *
     * @return string the string response
     **/
    public function getWidgetAction()
    {
        $days = $this->request->query->filter('days', null, FILTER_VALIDATE_INT);
        $type = $this->request->query->filter('type', null, FILTER_SANITIZE_STRING);
        $page = $this->request->query->filter('page',    1, FILTER_VALIDATE_INT);
        $category = $this->request->query->filter('category', null, FILTER_VALIDATE_INT);

        $tiempo = "";
        if ($days<=3) {
            $tiempo = ($days*24)." hours";
        } elseif ($days==7) {
            $tiempo = _("1 week");
        } elseif ($days==14) {
            $tiempo = _("2 weeks");
        } elseif ($days==30) {
            $tiempo = _("1 month");
        }

        if ($type=='viewed') {
            $title       = "<h2>".sprintf(_("More seen in %s"), $tiempo)."</h2>";
            $items       = \Dashboard::getMostViewed('Article',$category,$days);
            \StringUtils :: disabled_magic_quotes($items);
            $output = \Dashboard::viewedTable($items, $title);
        } elseif ($type=='comented') {
            $title       = "<h2>".sprintf(_("Most commented %s"), $tiempo)."</h2>";
            $items       = \Dashboard::getMostComented('Article',$category,$days);
            \StringUtils :: disabled_magic_quotes($items);
            $output = \Dashboard::comentedTable($items, $title);
        } elseif ($type=='voted') {
            $title       = "<h2>".sprintf(_("Most voted %s"), $tiempo)."</h2>";
            $items       = \Dashboard::getMostVoted('Article',$category,$days);
            \StringUtils :: disabled_magic_quotes($items);
            $output = \Dashboard::votedTable($items, $title);
        }
        return $output;
    }

} // END class StatisticsController