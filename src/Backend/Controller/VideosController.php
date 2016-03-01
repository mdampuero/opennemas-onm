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
use Backend\Annotation\CheckModuleAccess;
use Onm\Security\Acl;
use Onm\Framework\Controller\Controller;
use Onm\Settings as s;

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
        $this->contentType = \ContentManager::getContentTypeIdFromName('video');

        $this->category = $this->get('request')->query->filter('category', 'all', FILTER_SANITIZE_STRING);

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
                'datos_cat'    => $this->categoryData,
                'timezone'     => $timezone->getName()
            )
        );
    }

    /**
     * List videos.
     *
     * @return void
     *
     * @Security("has_role('VIDEO_ADMIN')")
     *
     * @CheckModuleAccess(module="VIDEO_MANAGER")
     */
    public function listAction()
    {
        $categories = [ [ 'name' => _('All'), 'value' => -1 ] ];

        foreach ($this->parentCategories as $key => $category) {
            $categories[] = [
                'name' => $category->title,
                'value' => $category->name
            ];

            foreach ($this->subcat[$key] as $subcategory) {
                $categories[] = [
                    'name' => '&rarr; ' . $subcategory->title,
                    'value' => $subcategory->name
                ];
            }
        }

        return $this->render(
            'video/list.tpl',
            [ 'categories' => $categories ]
        );
    }

    /**
     * List videos available for widget.
     *
     * @return void
     *
     * @Security("has_role('VIDEO_ADMIN')")
     *
     * @CheckModuleAccess(module="VIDEO_MANAGER")
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
     *
     * @CheckModuleAccess(module="VIDEO_MANAGER")
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
                    $this->get('session')->getFlashBag()->add(
                        'error',
                        _(
                            'There was a problem while uploading the file. '
                            .'Please check if you have completed all the form fields.'
                        )
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
                    'title'          => $requestPost->filter('title', null, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES),
                    'metadata'       => $requestPost->filter('metadata', null, FILTER_SANITIZE_STRING),
                    'description'    => $requestPost->get('description', ''),
                    'author_name'    => $requestPost->filter('author_name', null, FILTER_SANITIZE_STRING),
                    'fk_author'      => $requestPost->filter('fk_author', 0, FILTER_VALIDATE_INT),
                    'params'         => $request->request->get('params', []),
                );

                try {
                    $video = new \Video();
                    $videoId = $video->createFromLocalFile($videoFileData);
                } catch (\Exception $e) {
                    $this->get('session')->getFlashBag()->add('error', $e->getMessage());

                    return $this->redirect($this->generateUrl('admin_videos_create', array('type' => $type)));
                }
            } elseif ($type == 'external' || $type == 'script') {
                $information = $requestPost->get('infor');
                $information['thumbnail'] = $requestPost->filter('video_image', null, FILTER_SANITIZE_STRING);

                $video = new \Video();
                $videoData = array(
                    'category'       => $category,
                    'content_status' => $requestPost->filter('content_status', 0, FILTER_SANITIZE_STRING),
                    'title'          => $requestPost->filter('title', null, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES),
                    'metadata'       => $requestPost->filter('metadata', null, FILTER_SANITIZE_STRING),
                    'description'    => $requestPost->get('description', ''),
                    'author_name'    => $requestPost->filter('author_name', null, FILTER_SANITIZE_STRING),
                    'fk_author'      => $requestPost->filter('fk_author', 0, FILTER_VALIDATE_INT),
                    'information'    => $information,
                    'body'           => $requestPost->filter('body', ''),
                    'video_url'      => $requestPost->filter('video_url', ''),
                );

                try {
                    $videoId = $video->create($videoData);


                    // TODO: remove cache cleaning actions
                    $cacheManager = $this->get('template_cache_manager');
                    $cacheManager->setSmarty(new \Template(TEMPLATE_USER_PATH));
                    $cacheManager->delete(preg_replace('/[^a-zA-Z0-9\s]+/', '', $video->category_name).'|'.$video->id);
                    $cacheManager->delete('home|1');
                } catch (\Exception $e) {
                    $this->get('session')->getFlashBag()->add('notice', $e->getMessage());

                    return $this->redirect($this->generateUrl('admin_videos_create', array('type' => $type)));
                }

            } elseif ($type == 'web-source') {
                if (!empty($_POST['information'])) {
                    $video = new \Video();
                    $_POST['information'] = json_decode($_POST['information'], true);
                    try {
                        $videoId = $video->create($_POST);

                        // Clean cache album home and frontpage for category
                        // TODO: remove cache cleaning actions
                        $cacheManager = $this->get('template_cache_manager');
                        $cacheManager->setSmarty(new \Template(TEMPLATE_USER_PATH));
                        $cacheManager->delete(preg_replace('/[^a-zA-Z0-9\s]+/', '', $video->category_name).'|'.$video->id);
                        $cacheManager->delete('home|1');

                    } catch (\Exception $e) {
                        $this->get('session')->getFlashBag()->add('notice', $e->getMessage());

                        return $this->redirect($this->generateUrl('admin_videos_create', array('type' => $type)));
                    }

                } else {
                    $this->get('session')->getFlashBag()->add(
                        'notice',
                        _('There was an error while uploading the form, not all the required data was sent.')
                    );

                    return $this->redirect($this->generateUrl('admin_videos_create', array('type' => $type)));
                }
            } else {
                $this->get('session')->getFlashBag()->add(
                    'notice',
                    _('There was an error while uploading the form, the video type is not specified.')
                );

                return $this->redirect($this->generateUrl('admin_videos_create', array('type' => $type)));
            }

            return $this->redirect(
                $this->generateUrl(
                    'admin_video_show',
                    array(
                        'id' => $videoId
                    )
                )
            );

        } else {
            $type = $request->query->filter('type', null, FILTER_SANITIZE_STRING);
            if (empty($type)) {
                return $this->render('video/selecttype.tpl');
            } else {
                $authorsComplete = \User::getAllUsersAuthors();
                $authors = array('0' => _(' - Select one author - '));
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
     *
     * @CheckModuleAccess(module="VIDEO_MANAGER")
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
                $this->get('session')->getFlashBag()->add(
                    'notice',
                    _("You can't modify this video because you don't have enought privileges.")
                );
            } else {
                if ($video->author_name == 'external' || $video->author_name == 'script') {
                    $information = $_POST['infor'];
                    $information['thumbnail'] = $requestPost->filter('video_image', null, FILTER_SANITIZE_STRING);

                    $videoData = array(
                        'id'             => $id,
                        'category'       => $category,
                        'content_status' => $requestPost->filter('content_status', 0, FILTER_SANITIZE_STRING),
                        'title'          => $requestPost->filter('title', null, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES),
                        'metadata'       => $requestPost->filter('metadata', null, FILTER_SANITIZE_STRING),
                        'description'    => $requestPost->get('description', ''),
                        'author_name'    => $requestPost->filter('author_name', null, FILTER_SANITIZE_STRING),
                        'fk_author'      => $requestPost->filter('fk_author', 0, FILTER_VALIDATE_INT),
                        'information'    => $information,
                        'body'           => $requestPost->filter('body', ''),
                        'video_url'      => $requestPost->filter('video_url', ''),
                        'starttime'      => $video->starttime,
                        'params'         => $request->request->get('params', []),
                    );

                    $video->update($videoData);
                } else {
                    $_POST['starttime'] = $video->starttime;
                    $_POST['id']        = $id;
                    $_POST['params']    = $request->request->get('params');

                    $video->update($_POST);
                }

                $this->get('session')->getFlashBag()->add('success', _("Video updated successfully."));
            }

            // Clean cache home and frontpage for category
            // TODO: remove cache cleaning actions
            $cacheManager = $this->get('template_cache_manager');
            $cacheManager->setSmarty(new \Template(TEMPLATE_USER_PATH));
            $cacheManager->delete(preg_replace('/[^a-zA-Z0-9\s]+/', '', $video->category_name).'|'.$video->id);
            $cacheManager->delete('home|1');

            return $this->redirect(
                $this->generateUrl(
                    'admin_video_show',
                    array('id' => $video->id)
                )
            );
        }
    }

    /**
     * Deletes a video given its id.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     *
     * @Security("has_role('VIDEO_DELETE')")
     *
     * @CheckModuleAccess(module="VIDEO_MANAGER")
     */
    public function deleteAction(Request $request)
    {
        $id =  $request->query->getDigits('id');
        $page = $request->query->getDigits('page', 1);

        if (!empty($id)) {
            $video = new \Video($id);

            // Delete related and relations
            getService('related_contents')->deleteAll($id);

            $video->delete($id, $_SESSION['userid']);

            $this->get('session')->getFlashBag()->add(
                'success',
                _("Video '{$video->title}' deleted successfully.")
            );
        } else {
            $this->get('session')->getFlashBag()->add(
                'error',
                _('You must give an id to delete the video.')
            );
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
     *
     * @CheckModuleAccess(module="VIDEO_MANAGER")
     */
    public function showAction(Request $request)
    {
        $id = $request->query->getDigits('id', null);

        $video = $this->get('entity_repository')->find('Video', $id);

        if (is_null($video->id)) {
            $this->get('session')->getFlashBag()->add(
                'error',
                sprintf(_('Unable to find the video with the id "%d"'), $id)
            );

            return $this->redirect($this->generateUrl('admin_videos'));
        }

        if (($video->author_name == 'external' || $video->author_name == 'script')
            && is_array($video->information)
        ) {
            if (array_key_exists('thumbnail', $video->information) && !empty($video->information['thumbnail'])) {
                $video->thumb = $video->getThumb();
            }
        }
        $authorsComplete = \User::getAllUsersAuthors();
        $authors = array('0' => _(' - Select one author - '));
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
     *
     * @CheckModuleAccess(module="VIDEO_MANAGER")
     */
    public function videoInformationAction(Request $request)
    {
        $url = $request->query->get('url', null, FILTER_DEFAULT);
        $url = rawurldecode($url);
        $params = $this->container->getParameter('panorama');

        if ($url) {
            try {
                $videoP = new \Panorama\Video($url, $params);
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
     *
     * @CheckModuleAccess(module="VIDEO_MANAGER")
     */
    public function configAction(Request $request)
    {
        if ('POST' == $request->getMethod()) {
            unset($_POST['action']);
            unset($_POST['submit']);

            foreach ($_POST as $key => $value) {
                s::set($key, $value);
            }

            $this->get('session')->getFlashBag()->add('success', _('Settings saved.'));

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
     * Save positions for widget.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     *
     * @Security("has_role('VIDEO_ADMIN')")
     *
     * @CheckModuleAccess(module="VIDEO_MANAGER")
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

            /* Eliminar caché portada cuando actualizan orden opiniones {{{ */
            // TODO: remove cache cleaning actions
            $cacheManager = $this->get('template_cache_manager');
            $cacheManager->setSmarty(new \Template(TEMPLATE_USER_PATH));
            $cacheManager->delete('home|1');
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
     *
     * @CheckModuleAccess(module="VIDEO_MANAGER")
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

        // Build the pagination
        $pagination = $this->get('paginator')->get([
            'boundary'    => true,
            'directional' => true,
            'epp'         => $itemsPerPage,
            'page'        => $page,
            'total'       => $countVideos,
            'route'       => [
                'name'   => 'admin_videos_content_provider',
                'params' => [ 'category' => $categoryId ]
            ],
        ]);

        return $this->render(
            'video/content-provider.tpl',
            array(
                'videos'     => $videos,
                'pagination' => $pagination,
            )
        );
    }

    /**
     * Lists all the videos within a category for the related manager.
     *
     * @param  Request $request The request object.
     * @return Response         The response object.
     *
     * @CheckModuleAccess(module="VIDEO_MANAGER")
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

        $pagination = $this->get('paginator')->get([
            'epp'   => $itemsPerPage,
            'page'  => $page,
            'total' => $countVideos,
            'route' => [
                'name'   => 'admin_videos_content_provider_related',
                'params' => [ 'category' => $categoryId, ]
            ],
        ]);

        return $this->render(
            'common/content_provider/_container-content-list.tpl',
            array(
                'contentType'           => 'Video',
                'contents'              => $videos,
                'contentTypeCategories' => $this->parentCategories,
                'category'              => $this->category,
                'pagination'            => $pagination,
                'contentProviderUrl'    => $this->generateUrl('admin_videos_content_provider_related'),
            )
        );
    }
}
