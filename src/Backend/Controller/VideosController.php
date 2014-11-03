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
namespace Backend\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Onm\Security\Acl;
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
     * Common code for all the actions.
     */
    public function init()
    {
        //Check if module is activated in this onm instance
        \Onm\Module\ModuleManager::checkActivatedOrForward('VIDEO_MANAGER');

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

        $timezones = \DateTimeZone::listIdentifiers();
        $timezone  = new \DateTimeZone($timezones[s::get('time_zone', 'UTC')]);

        $this->view->assign(
            array(
                'category'     => $this->category,
                'subcat'       => $this->subcat,
                'allcategorys' => $this->parentCategories,
                //TODO: ¿datoscat?¿
                'datos_cat'    => $this->categoryData,
                'timezone'     => $timezone->getName()
            )
        );

        /******************* GESTION CATEGORIAS  *****************************/
    }

    /**
     * List videos.
     *
     * @return void
     *
     * @Security("has_role('VIDEO_ADMIN')")
     */
    public function listAction()
    {
        return $this->render('video/list.tpl');
    }

    /**
     * List videos available for widget.
     *
     * @return void
     *
     * @Security("has_role('VIDEO_ADMIN')")
     */
    public function widgetAction()
    {
        $configurations = s::get('video_settings');
        $numFavorites   = $configurations['total_widget'];

        return $this->render(
            'video/list.tpl',
            array(
                'total_elements_widget' => $numFavorites,
                'category'              => 'widget',
            )
        );
    }

    /**
     * Handles the form for create a new video.
     *
     * @param  Request $request The request object.
     * @return Response         The response object.
     *
     * @Security("has_role('VIDEO_CREATE')")
     */
    public function createAction(Request $request)
    {
        if ('POST' == $request->getMethod()) {
            $requestPost  = $request->request;

            $type     = $requestPost->filter('type', null, FILTER_SANITIZE_STRING);
            $page     = $requestPost->getDigits('page', 1);
            $category = $requestPost->getDigits('category');

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
                    'content_status' => $requestPost->filter('content_status', 0, FILTER_SANITIZE_STRING),
                    'title'          => $requestPost->filter('title', null, FILTER_SANITIZE_STRING),
                    'metadata'       => $requestPost->filter('metadata', null, FILTER_SANITIZE_STRING),
                    'description'    => $requestPost->filter('description', null, FILTER_SANITIZE_STRING),
                    'author_name'    => $requestPost->filter('author_name', null, FILTER_SANITIZE_STRING),
                    'fk_author'      => $requestPost->filter('fk_author', 0, FILTER_VALIDATE_INT),
                );

                try {
                    $video = new \Video();
                    $video->createFromLocalFile($videoFileData);
                } catch (\Exception $e) {
                    m::add($e->getMessage(), m::ERROR);

                    return $this->redirect($this->generateUrl('admin_videos_create', array('type' => $type)));
                }
            } elseif ($type == 'external' || $type == 'script') {

                $information = $_POST['infor'];

                $information['thumbnail'] = $requestPost->filter('video_image', null, FILTER_SANITIZE_STRING);

                $video = new \Video();
                $videoData = array(
                    'category'       => $category,
                    'content_status' => $requestPost->filter('content_status', 0, FILTER_SANITIZE_STRING),
                    'title'          => $requestPost->filter('title', null, FILTER_SANITIZE_STRING),
                    'metadata'       => $requestPost->filter('metadata', null, FILTER_SANITIZE_STRING),
                    'description'    => $requestPost->filter('description', null, FILTER_SANITIZE_STRING),
                    'author_name'    => $requestPost->filter('author_name', null, FILTER_SANITIZE_STRING),
                    'fk_author'      => $requestPost->filter('fk_author', 0, FILTER_VALIDATE_INT),
                    'information'    => $information,
                    'body'           => $requestPost->filter('body', 0, FILTER_VALIDATE_INT),
                    'video_url'      => $requestPost->filter('video_url', 0, FILTER_VALIDATE_INT),
                );

                try {
                    $video->create($videoData);
                    $tplManager = new \TemplateCacheManager(TEMPLATE_USER_PATH);
                    $tplManager->delete(preg_replace('/[^a-zA-Z0-9\s]+/', '', $video->category_name).'|'.$video->id);
                    $tplManager->delete('home|1');
                } catch (\Exception $e) {
                    m::add($e->getMessage());

                    return $this->redirect($this->generateUrl('admin_videos_create', array('type' => $type)));
                }

            } elseif ($type == 'web-source') {

                if (!empty($_POST['information'])) {

                    $video = new \Video();
                    $_POST['information'] = json_decode($_POST['information'], true);
                    try {
                        $video->create($_POST);
                        $tplManager = new \TemplateCacheManager(TEMPLATE_USER_PATH);
                        $tplManager->delete(preg_replace('/[^a-zA-Z0-9\s]+/', '', $video->category_name).'|'.$video->id);
                        $tplManager->delete('home|1');
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
                $authorsComplete = \User::getAllUsersAuthors();
                $authors = array( '0' => _(' - Select one author - '));
                foreach ($authorsComplete as $author) {
                    $authors[$author->id] = $author->name;
                }

                return $this->render(
                    'video/new.tpl',
                    array(
                        'type'           => $type,
                        'authors'        => $authors,
                        'commentsConfig' => s::get('comments_config'),
                    )
                );
            }
        }
    }

    /**
     * Handles the form for update a video given its id.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     *
     * @Security("has_role('VIDEO_UPDATE')")
     */
    public function updateAction(Request $request)
    {
        $id = $request->query->getDigits('id');
        $requestPost  = $request->request;
        $continue = $requestPost->filter('continue', false, FILTER_SANITIZE_STRING);
        $category = $requestPost->getDigits('category');
        $video = new \Video($id);

        if ($video->id != null) {
            $_POST['information'] = json_decode($_POST['information'], true);

            if (!Acl::isAdmin()
                && !Acl::check('CONTENT_OTHER_UPDATE')
                && !$video->isOwner($_SESSION['userid'])
            ) {
                m::add(_("You can't modify this video because you don't have enought privileges."));
            } else {

                if ($video->author_name == 'external' || $video->author_name == 'script') {
                    $information = $_POST['infor'];
                    $information['thumbnail'] = $requestPost->filter('video_image', null, FILTER_SANITIZE_STRING);

                    $videoData = array(
                        'id'             => $id,
                        'category'       => $category,
                        'content_status' => $requestPost->filter('content_status', 0, FILTER_SANITIZE_STRING),
                        'title'          => $requestPost->filter('title', null, FILTER_SANITIZE_STRING),
                        'metadata'       => $requestPost->filter('metadata', null, FILTER_SANITIZE_STRING),
                        'description'    => $requestPost->filter('description', null, FILTER_SANITIZE_STRING),
                        'author_name'    => $requestPost->filter('author_name', null, FILTER_SANITIZE_STRING),
                        'fk_author'      => $requestPost->filter('fk_author', 0, FILTER_VALIDATE_INT),
                        'information'    => $information,
                        'body'           => $requestPost->filter('body', 0, FILTER_VALIDATE_INT),
                        'video_url'      => $requestPost->filter('video_url', 0, FILTER_VALIDATE_INT),
                        'starttime'      => $video->starttime,
                    );

                    $video->update($videoData);
                } else {
                    $_POST['starttime'] = $video->starttime;
                    $video->update($_POST);
                }
                m::add(_("Video updated successfully."), m::SUCCESS);
            }
            $tplManager = new \TemplateCacheManager(TEMPLATE_USER_PATH);
            $tplManager->delete(preg_replace('/[^a-zA-Z0-9\s]+/', '', $video->category_name).'|'.$video->id);
            $tplManager->delete('home|1');

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
     * Deletes a video given its id.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     *
     * @Security("has_role('VIDEO_DELETE')")
     */
    public function deleteAction(Request $request)
    {
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
            m::add(_('You must give an id to delete the video.'), m::ERROR);
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
     * Shows the form for a video given its id.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     *
     * @Security("has_role('VIDEO_UPDATE')")
     */
    public function showAction(Request $request)
    {
        $id = $request->query->getDigits('id', null);

        $video = new \Video($id);

        if (is_null($video->id)) {
            m::add(sprintf(_('Unable to find the video with the id "%d"'), $id));

            return $this->redirect($this->generateUrl('admin_videos'));
        }

        if (($video->author_name == 'external' || $video->author_name == 'script')
            && is_array($video->information)) {
            $video->thumbnail = '';
            if (array_key_exists('thumbnail', $video->information) && !empty($video->information['thumbnail'])) {
                $video->thumb_image = new \Photo($video->information['thumbnail']);
                $video->thumbnail   = $video->thumb_image->path_file.$video->thumb_image->name;
            }
        }
        $authorsComplete = \User::getAllUsersAuthors();
        $authors = array( '0' => _(' - Select one author - '));
        foreach ($authorsComplete as $author) {
            $authors[$author->id] = $author->name;
        }

        return $this->render(
            'video/new.tpl',
            array(
                'information'    => $video->information,
                'video'          => $video,
                'authors'        => $authors,
                'commentsConfig' => s::get('comments_config'),
            )
        );
    }

    /**
     * Returns the video information for a given url.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     *
     * @Security("has_role('VIDEO_ADMIN')")
     */
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
     * Handles the form for configure the video module.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     *
     * @Security("has_role('VIDEO_SETTINGS')")
     */
    public function configAction(Request $request)
    {
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
     * Returns the relations for a given video.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     *
     * @Security("has_role('VIDEO_ADMIN')")
     */
    public function relationsAction(Request $request)
    {
        $id = $request->query->filter('id', null, FILTER_DEFAULT);

        $relations = array();
        $msg ='';
        $relations = \RelatedContent::getContentRelations($id);

        if (!empty($relations)) {
            $msg = sprintf(_("The video has some relations"));
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
     * Save positions for widget.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     *
     * @Security("has_role('VIDEO_ADMIN')")
     */
    public function savePositionsAction(Request $request)
    {
        $positions = $request->request->get('positions');
        $result = true;
        if (isset($positions)
            && is_array($positions)
            && count($positions) > 0
        ) {
            $pos = 1;

            foreach ($positions as $id) {
                $video = new \Video($id);
                $result = $result && $video->setPosition($pos);
                $pos++;
            }

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
     * Render the content provider for videos
     *
     * @param  Request $request The request object.
     * @return Response         The response object.
     */
    public function contentProviderAction(Request $request)
    {
        $categoryId   = $request->query->getDigits('category', 0);
        $page         = $request->query->getDigits('page', 1);
        $itemsPerPage = 8;

        $em  = $this->get('entity_repository');
        $ids = $this->get('frontpage_repository')->getContentIdsForHomepageOfCategory();

        $filters = array(
            'content_type_name' => array(array('value' => 'video')),
            'content_status'    => array(array('value' => 1)),
            'in_litter'         => array(array('value' => 1, 'operator' => '!=')),
            'pk_content'        => array(array('value' => $ids, 'operator' => 'NOT IN'))
        );

        $videos      = $em->findBy($filters, array('created' => 'desc'), $itemsPerPage, $page);
        $countVideos = $em->countBy($filters);

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
                    array('category' => $categoryId)
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
     * Lists all the videos within a category for the related manager.
     *
     * @param  Request $request The request object.
     * @return Response         The response object.
     */
    public function contentProviderRelatedAction(Request $request)
    {
        $categoryId   = $request->query->getDigits('category', 0);
        $page         = $request->query->getDigits('page', 1);
        $itemsPerPage = s::get('items_per_page') ?: 20;

        $em       = $this->get('entity_repository');
        $category = $this->get('category_repository')->find($categoryId);

        $filters = array(
            'content_type_name' => array(array('value' => 'video')),
            'in_litter'         => array(array('value' => 1, 'operator' => '!='))
        );

        if ($categoryId != 0) {
            $filters['category_name'] = array(array('value' => $category->name));
        }

        $videos      = $em->findBy($filters, array('created' => 'desc'), $itemsPerPage, $page);
        $countVideos = $em->countBy($filters);

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
                    array('category' => $categoryId,)
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
     * Shows a paginated list of images from a category.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     */
    public function contentProviderGalleryAction(Request $request)
    {
        $metadata   = $request->query->filter('metadatas', '', FILTER_SANITIZE_STRING);
        $categoryId = $request->query->getDigits('category', 0);
        $page       = $request->query->getDigits('page', 1);

        $itemsPerPage = 16;

        $em       = $this->get('entity_repository');
        $category = $this->get('category_repository')->find($categoryId);

        $filters = array(
            'content_type_name' => array(array('value' => 'video')),
            'content_status'    => array(array('value' => 1)),
            'in_litter'         => array(array('value' => 1, 'operator' => '!='))
        );

        if ($categoryId != 0) {
            $filters['category_name'] = array(array('value' => $category->name));
        }

        if (!empty($metadata)) {
            $tokens = \Onm\StringUtils::getTags($metadata);
            $tokens = explode(', ', $tokens);

            $filters['metadata'] = array(array('value' => $tokens, 'operator' => 'LIKE'));
            $filters['metadata']['union'] = 'OR';
        }

        $videos      = $em->findBy($filters, array('created' => 'desc'), $itemsPerPage, $page);
        $countVideos = $em->countBy($filters);

        if (empty($videos)) {
            return new Response(
                _("<div><p>Unable to find any video matching your search criteria.</p></div>")
            );
        }

        $pagination = \Onm\Pager\SimplePager::getPagerUrl(
            array(
                'page'  => $page,
                'items' => $itemsPerPage,
                'total' => $countVideos,
                'url'   => $this->generateUrl(
                    'admin_videos_content_provider_gallery',
                    array(
                        'category'  => $categoryId,
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
