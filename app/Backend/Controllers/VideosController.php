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

use Onm\Framework\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Onm\Message as m;
use Onm\Settings as s;
/**
 * Handles the actions for the system information
 *
 * @package Backend_Controllers
 **/
class VideosController extends Controller
{
    /**
     * Common code for all the actions
     *
     * @return void
     **/
    public function init()
    {
        //Check if module is activated in this onm instance
        \Onm\Module\ModuleManager::checkActivatedOrForward('VIDEO_MANAGER');

        // Check if the user can admin video
        \Acl::checkOrForward('VIDEO_ADMIN');
        $this->view = new \TemplateAdmin(TEMPLATE_ADMIN);

        /******************* GESTION CATEGORIAS  *****************************/
        $this->contentType = \Content::getIDContentType('video');

        $this->category = $this->request->query->filter('category', 'all', FILTER_SANITIZE_STRING);

        $this->ccm = \ContentCategoryManager::get_instance();
        list($this->parentCategories, $this->subcat, $this->categoryData) =
            $this->ccm->getArraysMenu($this->category, $this->contentType);
        if (empty($this->category)) {
            $this->category ='widget';
        }

        $this->view->assign(array(
            'category'     => $this->category,
            'subcat'       => $this->subcat,
            'allcategorys' => $this->parentCategories,
            //TODO: ¿datoscat?¿
            'datos_cat'    => $this->categoryData
        ));

        /******************* GESTION CATEGORIAS  *****************************/
    }

    /**
     * List videos
     *
     * @return void
     **/
    public function listAction()
    {
        $page = $this->request->query->getDigits('page', 1);
        $cm = new \ContentManager();

        $configurations = s::get('video_settings');
        $numFavorites = $configurations['total_widget'];


        if (empty($page)) {
            $limit = "LIMIT ".(ITEMS_PAGE+1);
        } else {
            $limit = "LIMIT ".($page-1) * ITEMS_PAGE .', '.(ITEMS_PAGE+1);
        }

        if ($this->category == 'all') {
            $videos = $cm->find_all(
                'Video',
                'available =1',
                'ORDER BY created DESC '. $limit
            );
        } else {
            $videos = $cm->find_by_category(
                'Video',
                $this->category,
                'fk_content_type = 9 ', 'ORDER BY created DESC '.$limit
            );
        }

        if(!empty($videos)){
            foreach ($videos as &$video) {
                $video->category_name = $this->ccm->get_name($video->category);
                $video->category_title = $this->ccm->get_title($video->category_name);
            }
        }

        $pagination = \Onm\Pager\SimplePager::getPagerUrl(array(
            'page'  => $page,
            'items' => ITEMS_PAGE-10,
            'total' => count($videos),
            'url'   => $this->generateUrl('admin_videos', array('category' => $this->category)),
        ));

        return $this->render('video/list.tpl', array(
            'pagination' => $pagination,
            'videos'     => $videos
        ));
    }

     /**
     * List videos available for widget
     *
     * @return void
     **/
    public function widgetAction()
    {
        $this->category = $this->request->query->filter('category', 'widget', FILTER_SANITIZE_STRING);

        $page = $this->request->query->getDigits('page', 0);
        $cm = new \ContentManager();

        $configurations = s::get('video_settings');
        $numFavorites = $configurations['total_widget'];


        if (empty($page)) {
            $limit = "LIMIT ".(ITEMS_PAGE+1);
        } else {
            $limit = "LIMIT ".($page-1) * ITEMS_PAGE .', '.(ITEMS_PAGE+1);
        }

        $videos = $cm->find_all(
            'Video',
            'in_home = 1 AND available =1',
            'ORDER BY  created DESC '. $limit
        );

        if (count($videos) < $numFavorites ) {
            m::add(sprintf(
                _("You must put %d videos in the HOME widget"),
                $numFavorites
            ));
        }

        if(!empty($videos)){
            foreach ($videos as &$video) {
                $video->category_name = $this->ccm->get_name($video->category);
                $video->category_title = $this->ccm->get_title($video->category_name);
            }
        }

        $pagination = \Onm\Pager\SimplePager::getPagerUrl(array(
            'page'  => $page,
            'items' =>ITEMS_PAGE,
            'total' => count($videos),
            'url'   =>$_SERVER['SCRIPT_NAME'].'?action=list&category='.$this->category,
        ));

        return $this->render('video/list.tpl', array(
            'pagination' => $pagination,
            'videos'     => $videos,
            'category'   => $this->category,
        ));
    }

    /**
     * Handles the form for create a new video
     *
     * @return Response the response object
     **/
    public function createAction()
    {
        if ('POST' == $this->request->getMethod()) {
            var_dump('post');die();

        } else {
            $type = $this->request->query->filter('type', null, FILTER_SANITIZE_STRING);
            if (empty($type)) {

                return $this->render('video/selecttype.tpl');
            } else {

                return $this->render('video/new.tpl', array(
                    'type' => $type
                ));
            }
        }
    }

    /**
     * Handles the form for update a video given its id
     *
     * @return Response the response object
     **/
    public function updateAction()
    {
        $content = 'default content';

        return new Response($content);
    }

    /**
     * Deletes a video given its id
     *
     * @return Response the response object
     **/
    public function deleteAction()
    {
        $content = 'default content';

        return new Response($content);
    }

    /**
     * Shows the form for a video given its id
     *
     * @return Response the response object
     **/
    public function showAction()
    {
        \Acl::checkOrForward('VIDEO_UPDATE');

        $id = $this->request->query->getDigits('id');
        if (is_null($id)) {
            m::add(sprintf(_('Unable to find the video with the id "%d"'), $id));
            $this->redirect($this->generateUrl('admin_videos'));
        }
        $video = new \Video($id);

        return $this->render('video/new.tpl', array(
            'information' => $video->information,
            'video'       => $video,
        ));

        return new Response($content);
    }

    /**
     * Returns the video information for a given url
     *
     * @return Response the response object
     **/
    public function videoInformationAction()
    {
        $url = $this->request->query->get('url', null, FILTER_DEFAULT);
        $url = rawurldecode($url);

        if ($url) {
            try {
                $videoP = new \Panorama\Video($url);
                $information = $videoP->getVideoDetails();

                $output = $this->renderView('video/partials/_video_information.tpl', array(
                    'information' => $information,
                ));

            } catch (\Exception $e) {
                $output = _( "Can't get video information. Check the url");
            }
        }  else {
            $output = _("Please check the video url, seems to be incorrect");
        }
        return new Response($output);
    }

    /**
     * Handles the form for configure the video module
     *
     * @return Response the response object
     **/
    public function configAction()
    {
        \Acl::checkOrForward('VIDEO_SETTINGS');

        if ('POST' == $this->request->getMethod()) {
            unset($_POST['action']);
            unset($_POST['submit']);

            foreach ($_POST as $key => $value ) {
                s::set($key, $value);
            }

            m::add(_('Settings saved.'), m::SUCCESS);

            return $this->redirect($this->generateUrl('admin_videos'));
        } else {

            $configurationsKeys = array(
                'video_settings',
            );

            $configurations = s::get($configurationsKeys);

            return $this->render('video/config.tpl', array(
                'configs'   => $configurations,
            ));
        }
    }

} // END class VideosController