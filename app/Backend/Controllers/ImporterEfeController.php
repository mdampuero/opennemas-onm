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

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Onm\Framework\Controller\Controller;
use Onm\Settings as s;
use Onm\Message as m;

/**
 * Handles the actions for the efe module
 *
 * @package Backend_Controllers
 **/
class ImporterEfeController extends Controller
{

    /**
     * Common code for all the actions
     *
     * @return void
     **/
    public function init()
    {
        // Check ACL
        $this->checkAclOrForward('IMPORT_EFE');

        $this->view = new \TemplateAdmin(TEMPLATE_ADMIN);

        ini_set('memory_limit', '128M');
        ini_set('set_time_limit', '0');

        // Check if module is configured, if not redirect to configuration form
        if (
            is_null(s::get('efe_server_auth'))
            && $action != 'config'
        ) {
            m::add(_('Please provide your EFE auth credentials to start to use your EFE Importer module'));
            $this->redirect($this->generateUrl('admin_importer_efe_config'));
        }
    }

    /**
     * Shows the list of downloaded newsfiles from Efe service
     *
     * @return Response the response object
     **/
    public function listAction(Request $request)
    {
        $page = $this->request->query->filter('page', 1, FILTER_VALIDATE_INT);

        $efe = \Onm\Import\Efe::getInstance();

        // Get the amount of minutes from last sync
        $minutesFromLastSync = $efe->minutesFromLastSync();

        $categories = \Onm\Import\DataSource\NewsMLG1::getOriginalCategories();

        $queryParams = $this->request->query;
        $filterCategory = $queryParams->filter('filter_category', '*', FILTER_SANITIZE_STRING);
        $filterTitle = $queryParams->filter('filter_title', '*', FILTER_SANITIZE_STRING);
        $page = $queryParams->filter('page', 0, FILTER_VALIDATE_INT);
        $itemsPage =  s::get('items_per_page') ?: 20;

        $findParams = array(
            'category'   => $filterCategory,
            'title'      => $filterTitle,
            'page'       => $page,
            'items_page' => $itemsPage,
        );

        list($countTotalElements, $elements) = $efe->findAll($findParams);

        $pagination = \Pager::factory(
            array(
                'mode'        => 'Sliding',
                'perPage'     => $itemsPage,
                'delta'       => 4,
                'clearIfVoid' => true,
                'urlVar'      => 'page',
                'append'      => false,
                'path'        => '',
                'totalItems'  => $countTotalElements,
                'fileName'        => $this->generateUrl(
                    'admin_importer_efe',
                    array(
                        'filter_category' => $filterCategory,
                        'filter_title'    => $filterTitle,
                    )
                ).'&page=%d',
            )
        );

        $urns = array();
        foreach ($elements as $element) {
            $urns []= $element->urn;
        }
        $alreadyImported = \Content::findByUrn($urns);

        $message = '';
        if ($minutesFromLastSync > 100) {
            $message = _('A long time ago from synchronization.');
        } elseif ($minutesFromLastSync > 10) {
            $message = sprintf(_('Last sync was %d minutes ago.'), $minutesFromLastSync);
        }
        if ($message) {
            m::add(
                $message
                . _(
                    'Try syncing the news list from server by clicking '
                    .'in "Sync with server" button above'
                ),
                m::NOTICE
            );
        }

        $_SESSION['_from'] = $this->generateUrl(
            'admin_importer_efe',
            array(
                'filter_category' => $filterCategory,
                'filter_title'    => $filterTitle,
                'page'            => $page
            )
        );

        return $this->render(
            'agency_importer/efe/list.tpl',
            array(
                'elements'         =>  $elements,
                'already_imported' =>  $alreadyImported,
                'categories'       =>  $categories,
                'minutes'          =>  $minutesFromLastSync,
                'pagination'       =>  $pagination,
            )
        );
    }

    /**
     * Shows the information for a given newfile filename
     *
     * @return Response the response object
     **/
    public function showAction(Request $request)
    {
        $id = $this->request->query->filter('id', null, FILTER_SANITIZE_STRING);

        try {
            $ep = new \Onm\Import\Efe();
            $element = $ep->findByFileName($id);

            $alreadyImported = \Content::findByUrn($element->urn);
        } catch (\Exception $e) {
            // Redirect the user to the list of articles and
            // show him/her an error message
            m::add(sprintf(_('Unable to find an element with the id "%d"'), $id), m::ERROR);

            $page = $this->request->query->filter('page', 0, FILTER_VALIDATE_INT);

            return $this->redirect(
                $this->generateUrl('admin_importer_efe', array('page' => $page))
            );
        }

        return $this->render(
            'agency_importer/efe/show.tpl',
            array(
                'element'   => $element,
                'imported'       => count($alreadyImported) > 0,
            )
        );

    }

    /**
     * Imports the article information given a newfile filename
     *
     * @return Response the response object
     **/
    public function importAction(Request $request)
    {
        $id       = $this->request->query->filter('id', null, FILTER_SANITIZE_STRING);
        $category = $this->request->request->filter('category', null, FILTER_SANITIZE_STRING);

        if (empty($id)) {
            m::add(_('Please specify the article to import.'), m::ERROR);

            return $this->redirect($this->generateUrl('admin_importer_efe'));
        }

        if (empty($category)) {
            m::add(_('Please assign the category where import this article'), m::ERROR);

            return $this->redirect($this->generateUrl('admin_importer_efe_pickcategory', array('id' => $id)));
        }

        $categoryInstance = new \ContentCategory($category);
        if (!is_object($categoryInstance)) {
            m::add(_('The category you have chosen doesn\'t exists.'), m::ERROR);

            return $this->redirect($this->generateUrl('admin_importer_efe_pickcategory', array('id' => $id)));
        }

        // Get EFE new from a filename
        try {
            $efe = new \Onm\Import\Efe();
            $element = $efe->findByFileName($id);
        } catch (\Exception $e) {
            m::add(_('Please specify the article to import.'), m::ERROR);

            return $this->redirect($this->generateUrl('admin_importer_efe'));
        }

        // If the new has photos import them
        if ($element->hasPhotos()) {
            $photos = $element->getPhotos();
            foreach ($photos as $photo) {

                $filePath = realpath($efe->_syncPath.DIRECTORY_SEPARATOR.$photo->file_path);


                // Check if the file exists
                if ($filePath) {
                    $data = array(
                        'title'         => $photo->file_path,
                        'description'   => $photo->title,
                        'local_file'    => realpath($efe->_syncPath.DIRECTORY_SEPARATOR.$photo->file_path),
                        'fk_category'   => $category,
                        'category_name' => $categoryInstance->name,
                        'metadata'      => \StringUtils::get_tags($photo->title),
                        'author_name'   => '&copy; EFE '.date('Y')
                    );

                    $photo = new \Photo();
                    $photoID = $photo->createFromLocalFile($data);

                    // If this article has more than one photo take the first one
                    if (!isset($innerPhoto)) {
                        $innerPhoto = new \Photo($photoID);
                    }
                }

            }
        }

        // If the new has videos import them
        if ($element->hasVideos()) {
            $videos = $element->getVideos();
            foreach ($videos as $video) {

                $filepath = realpath($efe->syncPath.DIRECTORY_SEPARATOR.$video->file_path);

                // Check if the file exists
                if ($filePath) {
                    $videoFileData = array(
                        'file_type'      => $video->file_type,
                        'file_path'      => $filepath,
                        'category'       => $category,
                        'available'      => 1,
                        'content_status' => 0,
                        'title'          => $video->title,
                        'metadata'       => \StringUtils::get_tags($video->title),
                        'description'    => '',
                        'author_name'    => 'internal',
                    );

                    $video = new \Video();
                    $videoID = $video->createFromLocalFile($videoFileData);

                    // If this article has more than one video take the first one
                    if (!isset($innerVideo)) {
                        $innerVideo = new \Video($videoID);
                    }
                }
            }
        }

        $values = array(
            'title'          => $element->texts[0]->title,
            'category'       => $category,
            'with_comment'   => 1,
            'content_status' => 0,
            'frontpage'      => 0,
            'in_home'        => 0,
            'title_int'      => $element->texts[0]->title,
            'metadata'       => \StringUtils::get_tags($element->texts[0]->title),
            'subtitle'       => $element->texts[0]->pretitle,
            'agency'         => s::get('efe_agency_string') ?: $element->agency_name,
            'summary'        => $element->texts[0]->summary,
            'body'           => $element->texts[0]->body,
            'posic'          => 0,
            'id'             => 0,
            'fk_publisher'   => $_SESSION['userid'],
            'img1'           => '',
            'img1_footer'    => '',
            'img2'           => (isset($innerPhoto) ? $innerPhoto->id : ''),
            'img2_footer'    => (isset($innerPhoto) ? $innerPhoto->title : ''),
            'fk_video'       => '',
            'fk_video2'      => (isset($innerVideo) ? $innerVideo->id : ''),
            'footer_video2'  => (isset($innerVideo) ? $innerVideo->title : ''),
            'ordenArti'      => '',
            'ordenArtiInt'   => '',
            'urn_source'     => $element->urn,
        );

        $article           = new \Article();
        $newArticleID      = $article->create($values);
        $_SESSION['desde'] = 'efe_press_import';

        // TODO: change this redirection when creating the ported article controller
        if (!empty($newArticleID)) {
            return $this->redirect($this->generateUrl('admin_article_show', array('id' => $newArticleID)));
        } else {
            m::add(sprintf('Unable to import the file "%s"', $id));

            return $this->redirect($this->generateUrl('admin_importer_efe'));
        }
    }

    /**
     * Shows the category form to pick a category under where to import the new
     *
     * @return Response the response object
     **/
    public function selectCategoryWhereToImportAction(Request $request)
    {
        $id       = $this->request->query->filter('id', null, FILTER_SANITIZE_STRING);
        $category = $this->request->query->filter('category', null, FILTER_SANITIZE_STRING);

        if (empty($id)) {
            m::add(_('The article you want to import doesn\'t exists.'), m::ERROR);
            $this->redirect($this->generateUrl('admin_importer_efe'));
        }

        $ccm = \ContentCategoryManager::get_instance();
        list($parentCategories, $subcat, $categoryData) = $ccm->getArraysMenu();

        $categories = array();
        foreach ($parentCategories as $category) {
            $categories [$category->pk_content_category]= $category->title;
        }

        $ep = new \Onm\Import\Efe();
        $element = $ep->findByFileName($id);

        return $this->render(
            'agency_importer/efe/import_select_category.tpl',
            array(
                'id' => $id,
                'article' => $element,
                'categories' => $categories,
            )
        );
    }

    /**
     * Returns the image file given a newsfile id and attached image id, if
     * not found return an 404 response error.
     *
     * @return Response the response object
     **/
    public function showAttachmentAction(Request $request)
    {
        $id           = $this->request->query->filter('id', null, FILTER_SANITIZE_STRING);
        $attachmentId = $this->request->query->filter('attachment_id', null, FILTER_SANITIZE_STRING);

        $ep = new \Onm\Import\Efe();
        $element = $ep->findById($id);

        if ($element->hasPhotos()) {
            $photos = $element->getPhotos();

            if (array_key_exists($attachmentId, $photos)) {
                $photo = $photos[$attachmentId];
                $content = file_get_contents(realpath($ep->_syncPath.DIRECTORY_SEPARATOR.$photo->file_path));
                $response = new Response($content, 200, array('content-type' => $photo->file_type));
            } else {
                $response = new Response('Image not found', 404);
            }

        } else {
            $response = new Response('Image not found', 404);
        }

        return $response;
    }

    /**
     * Shows and handles the configuration form for Efe module
     *
     * @return Response the response object
     **/
    public function configAction(Request $request)
    {
        if (count($_POST) <= 0) {
            if ($serverAuth = s::get('efe_server_auth')) {

                $message = $this->request->query->filter('message', null, FILTER_SANITIZE_STRING);

                $this->view->assign(
                    array(
                        'server'        => $serverAuth['server'],
                        'username'      => $serverAuth['username'],
                        'password'      => $serverAuth['password'],
                        'message'       => $message,
                        'agency_string' => s::get('efe_agency_string'),
                        'sync_from'     => array(
                            'no_limits'     => _('No limit'),
                            '21600'         => sprintf(_('%d hours'), '6'),
                            '43200'         => sprintf(_('%d hours'), '12'),
                            '86400'         => _('1 day'),
                            '172800'        => sprintf(_('%d days'), '2'),
                            '259200'        => sprintf(_('%d days'), '3'),
                            '345600'        => sprintf(_('%d days'), '4'),
                            '432000'        => sprintf(_('%d days'), '5'),
                            '518400'        => sprintf(_('%d days'), '6'),
                            '604800'        => sprintf(_('%d week'), '1'),
                            '1209600'       => sprintf(_('%d weeks'), '2'),
                        ),
                        'sync_from_setting'=> s::get('efe_sync_from_limit'),
                    )
                );

            }

            return $this->render('agency_importer/efe/config.tpl');
        } else {

            $requestParams = $this->request->request;
            $server       = $requestParams->filter('server', null, FILTER_SANITIZE_STRING);
            $username     = $requestParams->filter('username', null, FILTER_SANITIZE_STRING);
            $password     = $requestParams->filter('password', null, FILTER_SANITIZE_STRING);
            $syncFrom     = $requestParams->filter('sync_from', null, FILTER_SANITIZE_STRING);
            $agencyString = $requestParams->filter('agency_string', null, FILTER_SANITIZE_STRING);

            if (!isset($server) || !isset($username) || !isset($password)) {
                return $this->redirect($this->generateUrl('admin_importer_efe_config'));
            }

            $serverAuth =  array(
                'server'   => $server,
                'username' => $username,
                'password' => $password,
            );

            if (s::set('efe_server_auth', $serverAuth)
                && s::set('efe_sync_from_limit', $syncFrom)
                && s::set('efe_agency_string', $agencyString)
            ) {
                m::add(_('EFE module configuration saved successfully'), m::SUCCESS);
            } else {
                m::add(_('There was an error while saving the EFE module configuration'), m::ERROR);
            }

            return $this->redirect($this->generateUrl('admin_importer_efe'));
        }
    }

    /**
     * Cleans the unlock file for Efe module
     *
     * @return Response the response object
     **/
    public function unlockAction(Request $request)
    {
        $e = new \Onm\Import\Efe();
        $e->unlockSync();
        unset($_SESSION['error']);

        $page = $this->request->query->filter('page', null, FILTER_VALIDATE_INT);

        return $this->redirect(
            $this->generateUrl('admin_importer_efe', array('page' => $page))
        );
    }

    /**
     * Performs the files synchronization with the external server
     *
     * @return Response the response object
     **/
    public function syncAction(Request $request)
    {
        $page = $this->request->query->filter('page', 0, FILTER_VALIDATE_INT);
        try {

            $serverAuth = s::get('efe_server_auth');

            $ftpConfig = array(
                'server'                         => $serverAuth['server'],
                'user'                           => $serverAuth['username'],
                'password'                       => $serverAuth['password'],
                'allowed_file_extesions_pattern' => '.*',
                'max_age'                        => s::get('efe_sync_from_limit')
            );

            $efeSynchronizer = \Onm\Import\Efe::getInstance();
            $message         = $efeSynchronizer->sync($ftpConfig);
            $efeSynchronizer->updateSyncFile();

            m::add(
                sprintf(
                    _('Downloaded %d new articles and deleted %d old ones.'),
                    $message['downloaded'],
                    $message['deleted']
                )
            );

        } catch (\Onm\Import\SynchronizationException $e) {
            m::add($e->getMessage(), m::ERROR);
        } catch (\Onm\Import\Synchronizer\LockException $e) {
            $errorMessage = $e->getMessage()
                .sprintf(
                    _('If you are sure <a href="%s?action=unlock">try to unlock it</a>'),
                    $_SERVER['PHP_SELF']
                );
            m::add($errorMessage, m::ERROR);
        } catch (\Exception $e) {
            m::add($e->getMessage(), m::ERROR);
            $e = new \Onm\Import\Efe();
            $e->unlockSync();
        }

        return $this->redirect(
            $this->generateUrl('admin_importer_efe', array('page' => $page))
        );
    }
}

