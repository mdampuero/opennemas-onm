<?php
/**
 * Handles the actions for the system information
 *
 * @package Backend_Controllers
 **/
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Backend\Controllers;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Onm\Framework\Controller\Controller;
use Onm\Settings as s;
use Onm\Message as m;

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
        $this->checkAclOrForward('VIDEO_ADMIN');
        $this->view = new \TemplateAdmin(TEMPLATE_ADMIN);

        /******************* GESTION CATEGORIAS  *****************************/
        $this->contentType = \ContentManager::getContentTypeIdFromName('video');

        $request = $this->get('request');

        $this->category = $request->query->filter('category', 'all', FILTER_SANITIZE_STRING);

        $this->ccm = \ContentCategoryManager::get_instance();
        list($this->parentCategories, $this->subcat, $this->categoryData) =
            $this->ccm->getArraysMenu($this->category, $this->contentType);
        if (empty($this->category)) {
            $this->category ='widget';
        }

        $this->view->assign(
            array(
                'category'     => $this->category,
                'subcat'       => $this->subcat,
                'allcategorys' => $this->parentCategories,
                //TODO: ¿datoscat?¿
                'datos_cat'    => $this->categoryData
            )
        );

        /******************* GESTION CATEGORIAS  *****************************/
    }

    /**
     * List videos
     *
     * @param Request $request the request object
     *
     * @return void
     **/
    public function listAction(Request $request)
    {
        $page           = $request->query->getDigits('page', 1);
        $category       = $request->query->filter('category', 'all', FILTER_SANITIZE_STRING);
        $configurations = s::get('video_settings');
        $numFavorites   = $configurations['total_widget'];

        $cm = new \ContentManager();

        if ($category == 'all') {
            $categoryForLimit = null;
        } else {
            $categoryForLimit = $category;
        }
        $itemsPerPage = s::get('items_per_page', 20);

        list($videoCount, $videos) = $cm->getCountAndSlice(
            'video',
            $categoryForLimit,
            '',
            'ORDER BY created DESC',
            $page,
            $itemsPerPage
        );

        if (!empty($videos)) {
            foreach ($videos as &$video) {
                $video->information    = unserialize($video->information);
                $video->category_name  = $this->ccm->get_name($video->category);
                $video->category_title = $this->ccm->get_title($video->category_name);
            }
        }

        // Build the pager
        $pagination = \Pager::factory(
            array(
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
            )
        );

        return $this->render(
            'video/list.tpl',
            array(
                'pagination' => $pagination,
                'videos'     => $videos
            )
        );
    }

    /**
     * List videos available for widget
     *
     * @param Request $request the request object
     *
     * @return void
     **/
    public function widgetAction(Request $request)
    {
        $category = $request->query->filter('category', 'widget', FILTER_SANITIZE_STRING);
        $configurations = s::get('video_settings');
        $numFavorites   = $configurations['total_widget'];

        $cm = new \ContentManager();
        $videos = $cm->find_all('Video', 'in_home = 1 AND available =1', 'ORDER BY  position ASC ');

        if (count($videos) < $numFavorites) {
            m::add(
                sprintf(
                    _("You must put %d videos in the HOME widget"),
                    $numFavorites
                )
            );
        }

        if (!empty($videos)) {
            foreach ($videos as &$video) {
                $video->category_name  = $this->ccm->get_name($video->category);
                $video->category_title = $this->ccm->get_title($video->category_name);
            }
        }

        return $this->render(
            'video/list.tpl',
            array(
                'videos'     => $videos,
                'category'   => $category,
            )
        );
    }

    /**
     * Handles the form for create a new video
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function createAction(Request $request)
    {
        $this->checkAclOrForward('VIDEO_CREATE');

        if ('POST' == $request->getMethod()) {
            $request  = $request->request;

            $type     = $request->filter('type', null, FILTER_SANITIZE_STRING);
            $page     = $request->getDigits('page', 1);
            $category = $request->getDigits('category');

            if ($type === 'file') {
                // Check if the video file entry was completed
                if (!(isset($_FILES)
                    && array_key_exists('video_file', $_FILES)
                    && array_key_exists('name', $_FILES["video_file"])
                    && !empty($_FILES["video_file"]["name"]))
                ) {
                    m::add(
                        'There was a problem while uploading the file. '
                        .'Please check if you have completed all the form fields.',
                        m::ERROR
                    );

                    return $this->redirect(
                        $this->generateUrl('admin_videos_create', array('type' => $type))
                    );
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

            return $this->redirect(
                $this->generateUrl(
                    'admin_videos',
                    array(
                        'category' => $category,
                        'page' => $page
                    )
                )
            );

        } else {
            $type = $request->query->filter('type', null, FILTER_SANITIZE_STRING);
            if (empty($type)) {
                return $this->render('video/selecttype.tpl');
            } else {
                return $this->render(
                    'video/new.tpl',
                    array('type' => $type)
                );
            }
        }
    }

    /**
     * Handles the form for update a video given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function updateAction(Request $request)
    {
        $this->checkAclOrForward('VIDEO_UPDATE');

        $id = $request->query->getDigits('id');
        $continue = $request->request->filter('continue', false, FILTER_SANITIZE_STRING);
        $video = new \Video($id);

        if ($video->id != null) {
            $_POST['information'] = json_decode($_POST['information'], true);

            if (!\Acl::isAdmin()
                && !\Acl::check('CONTENT_OTHER_UPDATE')
                && $video->pk_user != $_SESSION['userid']
            ) {
                m::add(_("You can't modify this video because you don't have enought privileges."));
            } else {
                $video->update($_POST);
                m::add(_("Video updated successfully."), m::SUCCESS);
            }
            if ($continue) {
                return $this->redirect(
                    $this->generateUrl(
                        'admin_video_show',
                        array('id' => $video->id)
                    )
                );
            } else {
                $page = $request->request->getDigits('page', 1);

                return $this->redirect(
                    $this->generateUrl(
                        'admin_videos',
                        array(
                            'category' => $video->category,
                            'page'     => $page,
                        )
                    )
                );
            }
        }
    }

    /**
     * Deletes a video given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function deleteAction(Request $request)
    {
        $this->checkAclOrForward('VIDEO_DELETE');

        $id =  $request->query->getDigits('id');
        $page = $request->query->getDigits('page', 1);

        if (!empty($id)) {
            $video = new \Video($id);
            // Delete relations
            $rel= new \RelatedContent();
            $rel->deleteAll($id);

            $video->delete($id, $_SESSION['userid']);

            m::add(_("Video '{$video->title}' deleted successfully."), m::SUCCESS);
        } else {
            m::add(_('You must give an id for delete the video.'), m::ERROR);
        }

        if (!$request->isXmlHttpRequest()) {
            return $this->redirect(
                $this->generateUrl(
                    'admin_videos',
                    array(
                        'category' => $video->category,
                        'page' => $page
                    )
                )
            );
        } else {
            return new Response('ok');
        }
    }

    /**
     * Shows the form for a video given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function showAction(Request $request)
    {
        $this->checkAclOrForward('VIDEO_UPDATE');

        $id = $request->query->getDigits('id', null);

        $video = new \Video($id);

        if (is_null($video->id)) {
            m::add(sprintf(_('Unable to find the video with the id "%d"'), $id));

            return $this->redirect($this->generateUrl('admin_videos'));
        }

        return $this->render(
            'video/new.tpl',
            array(
                'information' => $video->information,
                'video'       => $video,
            )
        );

        return new Response($content);
    }

    /**
     * Returns the video information for a given url
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function videoInformationAction(Request $request)
    {
        $url = $request->query->get('url', null, FILTER_DEFAULT);
        $url = rawurldecode($url);

        if ($url) {
            try {
                $videoP = new \Panorama\Video($url);
                $information = $videoP->getVideoDetails();

                $output = $this->renderView(
                    'video/partials/_video_information.tpl',
                    array('information' => $information,)
                );

            } catch (\Exception $e) {
                $output = _("Can't get video information. Check the url");
            }
        } else {
            $output = _("Please check the video url, seems to be incorrect");
        }

        return new Response($output);
    }

    /**
     * Handles the form for configure the video module
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function configAction(Request $request)
    {
        $this->checkAclOrForward('VIDEO_SETTINGS');

        if ('POST' == $request->getMethod()) {
            unset($_POST['action']);
            unset($_POST['submit']);

            foreach ($_POST as $key => $value) {
                s::set($key, $value);
            }

            m::add(_('Settings saved.'), m::SUCCESS);

            return $this->redirect($this->generateUrl('admin_videos_config'));
        } else {

            $configurationsKeys = array(
                'video_settings',
            );

            $configurations = s::get($configurationsKeys);

            return $this->render(
                'video/config.tpl',
                array('configs' => $configurations,)
            );
        }
    }

    /**
     * Deletes multiple videos at once given its ids
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function batchDeleteAction(Request $request)
    {
        $this->checkAclOrForward('VIDEO_DELETE');

        $category = $request->query->filter('category', 'all', FILTER_SANITIZE_STRING);
        $page = $request->query->getDigits('page', 1);
        $selectedItems = $request->query->get('selected_fld');

        if (is_array($selectedItems)
            && count($selectedItems) > 0
        ) {
            foreach ($selectedItems as $element) {
                $video = new \Video($element);

                $relations = array();
                $relations = \RelatedContent::getContentRelations($element);

                $video->delete($element, $_SESSION['userid']);

                m::add(sprintf(_('Video "%s" deleted successfully.'), $video->title), m::SUCCESS);
            }
        }

        return $this->redirect(
            $this->generateUrl(
                'admin_videos',
                array(
                    'categoy' => $category,
                    'page'    => $page,
                )
            )
        );
    }

    /**
     * Change availability for one video given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function toggleAvailableAction(Request $request)
    {
        $this->checkAclOrForward('VIDEO_AVAILABLE');

        $id       = $request->query->getDigits('id', 0);
        $status   = $request->query->getDigits('status', 0);
        $page     = $request->query->getDigits('page', 1);
        $category = $request->query->get('category', 'all');

        $video = new \Video($id);
        if (is_null($video->id)) {
            m::add(sprintf(_('Unable to find video with id "%d"'), $id), m::ERROR);
        } else {
            $video->toggleAvailable($video->id);
            if ($status == 0) {
                $video->set_favorite($status);
            }
            m::add(sprintf(_('Successfully changed availability for video with id "%d"'), $id), m::SUCCESS);
        }

        return $this->redirect(
            $this->generateUrl(
                'admin_videos',
                array(
                    'category' => $category,
                    'page'     => $page
                )
            )
        );
    }

    /**
     * Change suggested flag for one video given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function toggleFavoriteAction(Request $request)
    {
        $this->checkAclOrForward('VIDEO_AVAILABLE');

        $id       = $request->query->getDigits('id', 0);
        $status   = $request->query->getDigits('status', 0);
        $page     = $request->query->getDigits('page', 1);
        $category = $request->query->get('category', 'all');

        $video = new \Video($id);
        if (is_null($video->id)) {
            m::add(sprintf(_('Unable to find video with id "%d"'), $id), m::ERROR);
        } else {

            $video->set_favorite($status);
            m::add(sprintf(_('Successfully changed suggested flag for video with id "%d"'), $id), m::SUCCESS);
        }

        return $this->redirect(
            $this->generateUrl(
                'admin_videos',
                array(
                    'category' => $category,
                    'page'     => $page
                )
            )
        );
    }

    /**
     * Change in_home flag for one video given its id
     * Used for putting this content widgets in home
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function toggleInHomeAction(Request $request)
    {
        $this->checkAclOrForward('VIDEO_AVAILABLE');

        $id       = $request->query->getDigits('id', 0);
        $status   = $request->query->getDigits('status', 0);
        $page     = $request->query->getDigits('page', 1);
        $category = $request->query->get('category', 'all');

        $video = new \Video($id);
        if (is_null($video->id)) {
            m::add(sprintf(_('Unable to find video with id "%d"'), $id), m::ERROR);
        } else {
            $video->set_inhome($status, $_SESSION['userid']);
            m::add(sprintf(_('Successfully changed suggested flag for video with id "%d"'), $id), m::SUCCESS);
        }

        if ($category == 'widget') {
            return $this->redirect(
                $this->generateUrl(
                    'admin_videos_widget'
                )
            );
        }
        return $this->redirect(
            $this->generateUrl(
                'admin_videos',
                array(
                    'category' => $category,
                    'page'     => $page
                )
            )
        );
    }

    /**
     * Returns the relations for a given video
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function relationsAction(Request $request)
    {
        $id = $request->query->filter('id', null, FILTER_DEFAULT);

        $video = new \Video($id);
        $relations = array();
        $msg ='';
        $relations = \RelatedContent::getContentRelations($id);

        if (!empty($relations)) {
            $msg = sprintf(_("<br>The video has some relations"));
            $cm  = new \ContentManager();
            $relat = $cm->getContents($relations);
            foreach ($relat as $contents) {
                $msg.=" <br>- ".strtoupper($contents->category_name).": ".$contents->title;
            }
            $msg.="<br> "._("Caution! Are you sure that you want to delete this video and its relations?");
        }

        return new Response($msg);
    }

    /**
     * Save positions for widget
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function savePositionsAction(Request $request)
    {
        $positions = $request->request->get('positions');
        $msg = '';
        if (isset($positions)
            && is_array($positions)
            && count($positions) > 0
        ) {
            $_positions = array();
            $pos = 1;

            foreach ($positions as $id) {
                $_positions[] = array($pos, '1', $id);
                $pos += 1;
            }

            $video = new \Video();
            $msg = $video->set_position($_positions, $_SESSION['userid']);

            // FIXME: buscar otra forma de hacerlo
            /* Eliminar caché portada cuando actualizan orden opiniones {{{ */
            $tplManager = new \TemplateCacheManager(TEMPLATE_USER_PATH);
            $tplManager->delete('home|0');
        }

        if ($msg) {
            $msg = "<div class='alert alert-success'>"
                ._("Positions saved successfully.")
                .'<button data-dismiss="alert" class="close">×</button></div>';
        } else {
            $msg = "<div class='alert alert-error'>"
                ._("Unable to save the new positions. Please contact with your system administrator.")
                .'<button data-dismiss="alert" class="close">×</button></div>';
        }

        return new Response($msg);
    }

    /**
     * Set the published flag for contents in batch
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function batchPublishAction(Request $request)
    {
        $this->checkAclOrForward('VIDEO_AVAILABLE');

        $status   = $request->query->getDigits('status', 0);
        $selected = $request->query->get('selected_fld', null);
        $category = $request->query->filter('category', 'all', FILTER_SANITIZE_STRING);
        $page     = $request->query->getDigits('page', 1);

        if (is_array($selected)
            && count($selected) > 0
        ) {
            foreach ($selected as $id) {
                $video = new \Video($id);
                $video->set_available($status, $_SESSION['userid']);
                if ($status == 0) {
                    $video->set_favorite($status, $_SESSION['userid']);
                }
            }
        }

        return $this->redirect(
            $this->generateUrl(
                'admin_videos',
                array(
                    'category' => $category,
                    'page'     => $page,
                )
            )
        );
    }

    /**
     * Render the content provider for videos
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function contentProviderAction(Request $request)
    {
        $category     = $request->query->filter('category', 'home', FILTER_SANITIZE_STRING);
        $page         = $request->query->getDigits('page', 1);
        $itemsPerPage = 8;

        if ($category == 'home') {
            $category = 0;
        }

        $cm = new  \ContentManager();

        // Get contents for this home
        $contentElementsInFrontpage  = $cm->getContentsIdsForHomepageOfCategory($category);

        // Fetching opinions
        $sqlExcludedOpinions = '';
        if (count($contentElementsInFrontpage) > 0) {
            $contentsExcluded    = implode(', ', $contentElementsInFrontpage);
            $sqlExcludedOpinions = ' AND `pk_video` NOT IN ('.$contentsExcluded.') ';
        }

        list($countVideos, $videos) = $cm->getCountAndSlice(
            'Video',
            null,
            'contents.available=1 '.$sqlExcludedOpinions,
            'ORDER BY created DESC ',
            $page,
            8
        );

        // Build the pager
        $pagination = \Pager::factory(
            array(
                'mode'        => 'Sliding',
                'perPage'     => $itemsPerPage,
                'append'      => false,
                'path'        => '',
                'delta'       => 4,
                'clearIfVoid' => true,
                'urlVar'      => 'page',
                'totalItems'  => $countVideos,
                'fileName'    => $this->generateUrl(
                    'admin_videos_content_provider',
                    array('category' => $category)
                ).'&page=%d',
            )
        );

        return $this->render(
            'video/content-provider.tpl',
            array(
                'videos' => $videos,
                'pager'  => $pagination,
            )
        );
    }

    /**
     * Lists all the videos withing a category for the related manager
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function contentProviderRelatedAction(Request $request)
    {
        $category = $request->query->getDigits('category', 0);
        $page     = $request->query->getDigits('page', 1);
        $itemsPerPage = s::get('items_per_page') ?: 20;

        if ($category == 0) {
            $categoryFilter = null;
        } else {
            $categoryFilter = $category;
        }
        $cm = new  \ContentManager();

        list($countVideos, $videos) = $cm->getCountAndSlice(
            'Video',
            $categoryFilter,
            'contents.content_status=1',
            ' ORDER BY created DESC ',
            $page,
            $itemsPerPage
        );

        $pagination = \Pager::factory(
            array(
                'mode'        => 'Sliding',
                'perPage'     => $itemsPerPage,
                'append'      => false,
                'path'        => '',
                'delta'       => 1,
                'clearIfVoid' => true,
                'urlVar'      => 'page',
                'totalItems'  => $countVideos,
                'fileName'    => $this->generateUrl(
                    'admin_videos_content_provider_related',
                    array('category' => $category,)
                ).'&page=%d',
            )
        );

        return $this->render(
            'common/content_provider/_container-content-list.tpl',
            array(
                'contentType'           => 'Video',
                'contents'              => $videos,
                'contentTypeCategories' => $this->parentCategories,
                'category'              => $this->category,
                'pagination'            => $pagination->links,
                'contentProviderUrl'    => $this->generateUrl('admin_videos_content_provider_related'),
            )
        );
    }

    /**
     * Shows a paginated list of images from a category
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function contentProviderGalleryAction(Request $request)
    {
        $metadata = $request->query->filter('metadatas', '', FILTER_SANITIZE_STRING);
        $category = $request->query->getDigits('category', 0);
        $page     = $request->query->getDigits('page', 1);

        $itemsPerPage = 16;
        $numItems = $itemsPerPage + 1;

        if ($page == 1) {
            $limit    = "LIMIT {$numItems}";
        } else {
            $limit    = "LIMIT ".($page-1) * $itemsPerPage .', '.$numItems;
        }

        $cm = new \ContentManager();

        $szWhere = '';
        if (!empty($metadata)) {
            $metadata = \Onm\StringUtils::get_tags($metadata);
            $metadata = explode(', ', $metadata);

            foreach ($metadata as &$meta) {
                $meta = "`metadata` LIKE '%".trim($meta)."%'";
            }
            $szWhere = "AND  (".implode(' OR ', $metadata).") ";
        }

        if ($category == 0) {
            $videos = $cm->find(
                'Video',
                'contents.fk_content_type = 9 AND contents.content_status=1 ' . $szWhere,
                'ORDER BY created DESC '.$limit
            );
        } else {
            $videos = $cm->find_by_category(
                'Video',
                $category,
                'fk_content_type = 9 AND contents.content_status=1 ' . $szWhere,
                'ORDER BY created DESC '.$limit
            );
        }

        if (empty($videos)) {
            return new Response(
                _("<div><p>Unable to find any video matching your search criteria.</p></div>")
            );
        }

        $total = count($videos);
        if ($total > $itemsPerPage) {
            array_pop($videos);
        }

        $pagination = \Onm\Pager\SimplePager::getPagerUrl(
            array(
                'page'  => $page,
                'items' => $itemsPerPage,
                'total' => $total,
                'url'   => $this->generateUrl(
                    'admin_videos_content_provider_gallery',
                    array(
                        'category'  => $category,
                        'metadatas' => $metadata,
                    )
                )
            )
        );

        return $this->render(
            'video/video_gallery.ajax.tpl',
            array(
                'pagination' => $pagination,
                'videos'     => $videos,
            )
        );
    }
}
