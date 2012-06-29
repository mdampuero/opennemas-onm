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
        $page           = $this->get('request')->query->getDigits('page', 1);
        $category       = $this->get('request')->query->filter('category', 'all', FILTER_SANITIZE_STRING);
        $configurations = s::get('video_settings');
        $numFavorites   = $configurations['total_widget'];

        $cm = new \ContentManager();

        if ($category == 'all') {
            $categoryForLimit = null;
        } else {
            $categoryForLimit = $category;
        }
        $itemsPerPage = s::get('items_per_page');

        list($videoCount, $videos) = $cm->getCountAndSlice(
            'video',
            $categoryForLimit,
            '',
            'ORDER BY created DESC',
            $page,
            $itemsPerPage
        );


        if (!empty($videos)){
            foreach ($videos as &$video) {
                $video->category_name  = $this->ccm->get_name($video->category);
                $video->category_title = $this->ccm->get_title($video->category_name);
            }
        }

        // Build the pager
        $pagination = \Pager::factory(array(
            'mode'        => 'Sliding',
            'perPage'     => $itemsPerPage,
            'append'      => false,
            'path'        => '',
            'delta'       => 4,
            'clearIfVoid' => true,
            'urlVar'      => 'page',
            'totalItems'  => $videoCount,
            'fileName'    => $this->generateUrl(
                'admin_videos',
                array('category' => $category)
            ).'&page=%d',
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
        \Acl::checkOrForward('VIDEO_CREATE');

        if ('POST' == $this->request->getMethod()) {
            $request  = $this->request->request;

            $type     = $request->filter('type', null, FILTER_SANITIZE_STRING);
            $page     = $request->getDigits('page', 0);
            $category = $request->getDigits('category');

            if ($type === 'file') {
                // Check if the video file entry was completed
                if (
                    !(
                        isset($_FILES)
                        && array_key_exists('video_file', $_FILES)
                        && array_key_exists('name', $_FILES["video_file"])
                        && !empty($_FILES["video_file"]["name"])
                    )
                ) {
                    m::add(
                        'There was a problem while uploading the file. '
                        .'Please check if you have completed all the form fields.',
                        m::ERROR
                    );
                    return $this->redirect($this->generateUrl(
                        'admin_videos_create',
                        array('type' => $type)
                    ));
                }

                $videoFileData = array(
                    'file_type'      => $_FILES["video_file"]["type"],
                    'file_path'      => $_FILES["video_file"]["tmp_name"],
                    'category'       => $category,
                    'available'      => $request->filter('available', null, FILTER_SANITIZE_STRING),
                    'content_status' => $request->filter('content_status', null, FILTER_SANITIZE_STRING),
                    'title'          => $request->filter('title', null, FILTER_SANITIZE_STRING),
                    'metadata'       => $request->filter('metadata', null, FILTER_SANITIZE_STRING),
                    'description'    => $request->filter('description', null, FILTER_SANITIZE_STRING),
                    'author_name'    => $request->filter('author_name', null, FILTER_SANITIZE_STRING),
                );

                try {
                    $video = new \Video();
                    $video->createFromLocalFile($videoFileData);
                } catch (\Exception $e) {
                    m::add($e->getMessage(), m::ERROR);
                    return $this->redirect($this->generateUrl('admin_videos_create', array('type' => $type)));
                }
            } elseif ($type == 'web-source') {

                if (!empty($_POST['information'])) {

                    $video = new \Video();
                    $_POST['information'] = json_decode($_POST['information'], true);
                    try {
                        $video->create($_POST);
                    } catch (\Exception $e) {
                        m::add($e->getMessage());
                        return $this->redirect($this->generateUrl('admin_videos_create', array('type' => $type)));
                    }

                } else {
                    m::add('There was an error while uploading the form, not all the required data was sent.');
                        return $this->redirect($this->generateUrl('admin_videos_create', array('type' => $type)));
                }
            } else {
                m::add('There was an error while uploading the form, the video type is not specified.');
                return $this->redirect($this->generateUrl('admin_videos_create', array('type' => $type)));
            }

            return $this->redirect($this->generateUrl(
                'admin_videos',
                array(
                    'category' => $category,
                    'page' => $page
                )
            ));

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
        \Acl::checkOrForward('VIDEO_UPDATE');

        $id = $this->request->query->getDigits('id');
        $continue = $this->request->request->filter('continue', false, FILTER_SANITIZE_STRING);
        $video = new \Video($id);

        if ($video->id != null) {
            $_POST['information'] = json_decode($_POST['information'], true);

            if (!\Acl::isAdmin()
                && !\Acl::check('CONTENT_OTHER_UPDATE')
                && $video->pk_user != $_SESSION['userid'])
            {
                m::add(_("You can't modify this video because you don't have enought privileges."));
            } else {
                $video->update($_POST);
                m::add(_("Video updated successfully."), m::SUCCESS);
            }
            if ($continue) {
                return $this->redirect($this->generateUrl(
                    'admin_videos_show',
                    array('id' => $video->id)
                ));
            } else {
                $page = $this->request->request->getDigits('page', 0);
                return $this->redirect($this->generateUrl(
                    'admin_videos',
                    array(
                        'category' => $video->category,
                        'page'     => $page,
                    )
                ));
            }
        }
    }

    /**
     * Deletes a video given its id
     *
     * @return Response the response object
     **/
    public function deleteAction()
    {
        \Acl::checkOrForward('VIDEO_DELETE');

        $request = $this->request;
        $id = $request->getDigits('id');
        $page = $request->getDigits('page', 0);

        if (!empty($id)) {
            $video = new \Video($id);
            // Delete relations
            $rel= new \RelatedContent();
            $rel->deleteAll($id);

            $video->delete($id ,$_SESSION['userid']);
            m::add(_("Video '{$video->title}' deleted successfully."), m::SUCCESS);
        } else {
            m::add(_('You must give an id for delete the video.'), m::ERROR);
        }

        return $this->redirect($this->generateUrl(
            'admin_videos',
            array('category' => $video->category)
        ));
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