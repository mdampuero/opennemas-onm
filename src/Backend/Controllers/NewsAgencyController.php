<?php
/**
 * Handles the actions for the news agency module
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
 * Handles the actions for the news agency module
 *
 * @package Backend_Controllers
 **/
class NewsAgencyController extends Controller
{
    /**
     * Common code for all the actions
     *
     * @return void
     **/
    public function init()
    {
        // Check ACL
        $this->checkAclOrForward('IMPORT_ADMIN');

        $this->view = new \TemplateAdmin(TEMPLATE_ADMIN);

        $this->syncFrom = array(
            '3600'         => sprintf(_('%d hour'), '1'),
            '10800'         => sprintf(_('%d hours'), '3'),
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
            'no_limits'     => _('No limit'),
        );

        ini_set('memory_limit', '128M');
        ini_set('set_time_limit', '0');

        // Check if module is configured, if not redirect to configuration form
        if (is_null(s::get('news_agency_config'))
            && $action != 'config'
        ) {
            m::add(_('Please provide your EFE auth credentials to start to use your EFE Importer module'));
            $this->redirect($this->generateUrl('admin_importer_efe_config'));
        }
    }

    /**
     * Shows the list of downloaded newsfiles from Efe service
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function listAction(Request $request)
    {
        $page = $this->request->query->filter('page', 1, FILTER_VALIDATE_INT);

        $repository = new \Onm\Import\Repository\LocalRepository();

        $servers = s::get('news_agency_config');

        $sources = array_map(
            function ($server) {
                return $server['name'];
            },
            $servers
        );

        // Get the amount of minutes from last sync
        $synchronizer = new \Onm\Import\Synchronizer\Synchronizer();
        $minutesFromLastSync = $synchronizer->minutesFromLastSync();

        $queryParams  = $this->request->query;
        $filterSource = $queryParams->filter('filter_source', '*', FILTER_SANITIZE_STRING);
        $filterTitle  = $queryParams->filter('filter_title', '*', FILTER_SANITIZE_STRING);
        $page         = $queryParams->filter('page', 1, FILTER_VALIDATE_INT);
        $itemsPage    = s::get('items_per_page') ?: 20;

        $findParams = array(
            'source'     => $filterSource,
            'title'      => $filterTitle,
            'page'       => $page,
            'items_page' => $itemsPage,
        );

        list($countTotalElements, $elements) = $repository->findAll($findParams);

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
                    'admin_news_agency',
                    array(
                        'filter_source' => $filterSource,
                        'filter_title'  => $filterTitle,
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
            'admin_news_agency',
            array(
                'filter_source' => $filterSource,
                'filter_title'  => $filterTitle,
                'page'          => $page
            )
        );

        return $this->render(
            'news_agency/list.tpl',
            array(
                'source_names'     => $sources,
                'selectedSource'   => $filterSource,
                'servers'          => $servers,
                'elements'         => $elements,
                'already_imported' => $alreadyImported,
                'minutes'          => $minutesFromLastSync,
                'pagination'       => $pagination,
            )
        );
    }

    /**
     * Shows the information for a given newfile filename
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function showAction(Request $request)
    {
        $id = $this->request->query->filter('id', null, FILTER_SANITIZE_STRING);
        $sourceId = $this->request->query->getDigits('source_id');

        try {
            $repository = new \Onm\Import\Repository\LocalRepository();
            $element = $repository->findByFileName($sourceId, $id);
            $element->source_id = $sourceId;

            $alreadyImported = false;
            if (!is_null($element)) {
                $alreadyImported = (count(\Content::findByUrn($element->urn)) > 0);
            }
        } catch (\Exception $e) {
            // Redirect the user to the list of articles and show  an error message
            m::add(sprintf(_('Unable to find an element with the id "%d"'), $id), m::ERROR);

            $page = $this->request->query->filter('page', 1, FILTER_VALIDATE_INT);

            return $this->redirect(
                $this->generateUrl('admin_news_agency', array('page' => $page))
            );
        }

        return $this->render(
            'news_agency/show.tpl',
            array(
                'element'  => $element,
                'imported' => $alreadyImported,
            )
        );
    }

    /**
     * Imports the article information given a newfile filename
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function importAction(Request $request)
    {
        $id       = $this->request->query->filter('id', null, FILTER_SANITIZE_STRING);
        $sourceId = $this->request->query->getDigits('source_id');
        $category = $this->request->request->filter('category', null, FILTER_SANITIZE_STRING);

        if (empty($id) || empty($sourceId)) {
            m::add(_('Please specify the article to import.'), m::ERROR);

            return $this->redirect($this->generateUrl('admin_news_agency'));
        }

        if (empty($category)) {
            m::add(_('Please assign the category where import this article'), m::ERROR);

            return $this->redirect(
                $this->generateUrl(
                    'admin_news_agency_pickcategory',
                    array(
                        'id'        => $id,
                        'source_id' => $sourceId
                    )
                )
            );
        }

        $categoryInstance = new \ContentCategory($category);
        if (!is_object($categoryInstance)) {
            m::add(_('The category you have chosen doesn\'t exists.'), m::ERROR);

            return $this->redirect(
                $this->generateUrl(
                    'admin_news_agency_pickcategory',
                    array(
                        'id' => $id,
                        'source_id' => $sourceId
                    )
                )
            );
        }

        // Get EFE new from a filename
        try {
            $repository = new \Onm\Import\Repository\LocalRepository();
            $element = $repository->findByFileName($sourceId, $id);
        } catch (\Exception $e) {
            m::add(_('Please specify the article to import.'), m::ERROR);

            return $this->redirect($this->generateUrl('admin_news_agency'));
        }

        // If the new has photos import them
        if ($element->hasPhotos()) {
            $photos = $element->getPhotos();
            foreach ($photos as $photo) {
                // Get image from FTP
                $filePath = realpath(
                    $repository->syncPath.DS.$sourceId.DS.$photo->file_path
                );
                $fileName = $photo->file_path;

                // If no image from FTP check HTTP
                if (!$filePath) {
                    $filePath = $repository->syncPath.DS.
                        $sourceId.DS.$photo->name;
                    $fileName = $photo->name;
                }

                // Check if the file apc_exists(keys)
                if ($filePath) {
                    $data = array(
                        'title'         => $fileName,
                        'description'   => $photo->title,
                        'local_file'    => $filePath,
                        'fk_category'   => $category,
                        'category_name' => $categoryInstance->name,
                        'category'      => $categoryInstance->name,
                        'metadata'      => \StringUtils::get_tags($photo->title),
                        'author_name'   => '&copy; EFE '.date('Y'),
                        'original_filename' => $fileName,
                    );

                    $photo = new \Photo();
                    $photoObject = $photo->createFromLocalFileAjax($data);

                    // If this article has more than one photo take the first one
                    if (!isset($innerPhoto)) {
                        $innerPhoto = new \Photo($photoObject->id);
                    }
                }
            }
        }

        // If the new has videos import them
        if ($element->hasVideos()) {
            $videos = $element->getVideos();
            foreach ($videos as $video) {
                $filepath = realpath(
                    $repository->syncPath.DS.$sourceId.DS.$video->file_path
                );

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

        $servers = s::get('news_agency_config');
        $server = $servers[$sourceId];

        $values = array(
            'title'          => $element->title,
            'category'       => $category,
            'with_comment'   => 1,
            'content_status' => 0,
            'frontpage'      => 0,
            'in_home'        => 0,
            'title_int'      => $element->title,
            'metadata'       => \StringUtils::get_tags($element->title),
            'subtitle'       => $element->pretitle,
            'agency'         => $server['agency_string'],
            'summary'        => $element->summary,
            'body'           => $element->body,
            'posic'          => 0,
            'id'             => 0,
            'fk_publisher'   => $_SESSION['userid'],
            'img1'           => '',
            'img1_footer'    => '',
            'img2'           => (isset($innerPhoto) ? $innerPhoto->id : ''),
            'img2_footer'    => (isset($innerPhoto) ? $innerPhoto->description : ''),
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
            return $this->redirect(
                $this->generateUrl(
                    'admin_article_show',
                    array('id' => $newArticleID)
                )
            );
        } else {
            m::add(sprintf('Unable to import the file "%s"', $id));

            return $this->redirect($this->generateUrl('admin_news_agency'));
        }
    }

    /**
     * Shows the category form to pick a category under where to import the new
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function selectCategoryWhereToImportAction(Request $request)
    {
        $id       = $this->request->query->filter('id', null, FILTER_SANITIZE_STRING);
        $sourceId = $this->request->query->getDigits('source_id');
        $category = $this->request->query->filter('category', null, FILTER_SANITIZE_STRING);

        if (empty($id)) {
            m::add(_('The article you want to import doesn\'t exists.'), m::ERROR);
            $this->redirect($this->generateUrl('admin_news_agency'));
        }

        $ccm = \ContentCategoryManager::get_instance();
        list($parentCategories, $subcat, $categoryData) = $ccm->getArraysMenu();

        $categories = array();
        foreach ($parentCategories as $category) {
            $categories [$category->pk_content_category]= $category->title;
        }

        $repository = new \Onm\Import\Repository\LocalRepository();
        $element = $repository->findByFileName($sourceId, $id);

        return $this->render(
            'news_agency/import_select_category.tpl',
            array(
                'id'         => $id,
                'source_id'  => $sourceId,
                'article'    => $element,
                'categories' => $categories,
            )
        );
    }

    /**
     * Returns the image file given a newsfile id and attached image id, if
     * not found return an 404 response error.
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function showAttachmentAction(Request $request)
    {
        $id           = $this->request->query->filter('id', null, FILTER_SANITIZE_STRING);
        $sourceId = $this->request->query->getDigits('source_id');
        $attachmentId = $this->request->query->filter('attachment_id', null, FILTER_SANITIZE_STRING);

        $repository = new \Onm\Import\Repository\LocalRepository();
        $element = $repository->findById($sourceId, $id);

        if ($element->hasPhotos()) {
            $photos = $element->getPhotos();
            if (array_key_exists($attachmentId, $photos)) {
                $photo = $photos[$attachmentId];
                // Get image from FTP
                $filePath = realpath(
                    $repository->syncPath.DS.$sourceId.DS.$photo->file_path
                );
                // If no image from FTP check HTTP
                if (!$filePath) {
                    $filePath = $repository->syncPath.DS.
                        $sourceId.DS.$photo->name;
                }
                $content = @file_get_contents($filePath);

                $response = new Response(
                    $content,
                    200,
                    array('content-type' => $photo->file_type)
                );
            } else {
                $response = new Response('Image not found', 404);
            }

        } else {
            $response = new Response('Image not found', 404);
        }

        return $response;
    }

    /**
     * Cleans the unlock file for Efe module
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function unlockAction(Request $request)
    {
        $synchronizer = new \Onm\Import\Synchronizer\Synchronizer();
        $synchronizer->unlockSync();
        unset($_SESSION['error']);

        $page = $this->request->query->filter('page', null, FILTER_VALIDATE_INT);

        return $this->redirect(
            $this->generateUrl('admin_news_agency', array('page' => $page))
        );
    }

    /**
     * Performs the files synchronization with the external server
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function syncAction(Request $request)
    {
        $page = $this->request->query->filter('page', 1, FILTER_VALIDATE_INT);

        $servers = s::get('news_agency_config');

        $synchronizer = new \Onm\Import\Synchronizer\Synchronizer();

        foreach ($servers as $server) {
            try {
                if ($server['activated'] != '1') {
                    continue;
                }

                $server['allowed_file_extesions_pattern'] = '.*';

                $message      = $synchronizer->sync($server);

                m::add(
                    sprintf(
                        _('Downloaded %d new articles and deleted %d old ones from "%s".'),
                        $message['downloaded'],
                        $message['deleted'],
                        $server['name']
                    )
                );

            } catch (\Onm\Import\SynchronizationException $e) {
                m::add($e->getMessage(), m::ERROR);
            } catch (\Onm\Import\Synchronizer\LockException $e) {
                if (!isset($lockErrors)) {
                    $errorMessage = $e->getMessage()
                    .sprintf(
                        _('If you are sure <a href="%s">try to unlock it</a>'),
                        $this->generateUrl('admin_news_agency_unlock')
                    );
                    m::add($errorMessage, m::ERROR);

                    $lockErrors = true;
                }
            } catch (\Exception $e) {
                m::add($e->getMessage(), m::ERROR);

                $synchronizer->unlockSync();
            }
        }
        $synchronizer->updateSyncFile();


        return $this->redirect(
            $this->generateUrl('admin_news_agency', array('page' => $page))
        );
    }

    /**
     * Lists all the servers for the news agency
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function configListServersAction(Request $request)
    {
        $servers = s::get('news_agency_config');

        return $this->render(
            'news_agency/config/list.tpl',
            array(
                'servers'   => $servers,
                'sync_from' => $this->syncFrom
            )
        );
    }

    /**
     * Shows and handles the configuration form for Efe module
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function configUpdateServerAction(Request $request)
    {
        $this->checkAclOrForward('IMPORT_EFE_CONFIG');

        $id = $request->query->getDigits('id');

        $servers = s::get('news_agency_config');

        $server = array(
            'id'            => $id,
            'name'          => $request->request->filter('name', '', FILTER_SANITIZE_STRING),
            'url'           => $request->request->filter('url', '', FILTER_SANITIZE_STRING),
            'username'      => $request->request->filter('username', '', FILTER_SANITIZE_STRING),
            'password'      => $request->request->filter('password', '', FILTER_SANITIZE_STRING),
            'agency_string' => $request->request->filter('agency_string', '', FILTER_SANITIZE_STRING),
            'sync_from'     => $request->request->filter('sync_from', '', FILTER_SANITIZE_STRING),
            'activated'     => $request->request->getDigits('activated', 0),
        );

        $servers[$id] = $server;

        s::set('news_agency_config', $servers);

        m::add(_('News agency server updated.'), m::SUCCESS);

        return $this->redirect(
            $this->generateUrl(
                'admin_news_agency_server_show',
                array('id' => $id)
            )
        );
    }

    /**
     * Shows the news agency information
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function configShowServerAction(Request $request)
    {
        $servers = s::get('news_agency_config');

        $id = $request->query->getDigits('id');

        $server = $servers[$id];

        $this->view->assign(
            array(
                'server'        => $server,
                'sync_from'     => $this->syncFrom,
            )
        );

        return $this->render('news_agency/config/new.tpl');
    }

    /**
     * Shows and handles the configuration form for Efe module
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function configCreateServerAction(Request $request)
    {
        if ('POST' != $request->getMethod()) {
            $this->checkAclOrForward('IMPORT_EFE_CONFIG');

            $this->view->assign(
                array(
                    'server'        => array(),
                    'sync_from'     => $this->syncFrom,
                )
            );

            return $this->render('news_agency/config/new.tpl');
        } else {
            $servers = s::get('news_agency_config');

            if (!is_array($servers)) {
                $servers = array();
            }

            $latestServerId = max(array_keys($servers));

            $server = array(
                'id'            => $latestServerId + 1,
                'name'          => $request->request->filter('name', '', FILTER_SANITIZE_STRING),
                'url'           => $request->request->filter('url', '', FILTER_SANITIZE_STRING),
                'username'      => $request->request->filter('username', '', FILTER_SANITIZE_STRING),
                'password'      => $request->request->filter('password', '', FILTER_SANITIZE_STRING),
                'agency_string' => $request->request->filter('agency_string', '', FILTER_SANITIZE_STRING),
                'sync_from'     => $request->request->filter('sync_from', '', FILTER_SANITIZE_STRING),
                'activated'     => $request->request->getDigits('activated', 0),
            );

            $servers[$server['id']] = $server;

            s::set('news_agency_config', $servers);

            m::add(_('News agency server added.'), m::SUCCESS);

            return $this->redirect(
                $this->generateUrl(
                    'admin_news_agency_server_show',
                    array('id' => $server['id'])
                )
            );
        }
    }

    /**
     * Shows and handles the configuration form for Efe module
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function configDeleteServerAction(Request $request)
    {
        $servers = s::get('news_agency_config');

        $id = $request->query->getDigits('id');

        if (!array_key_exists($id, $servers)) {
            m::add(
                sprintf(
                    _('Source identifier "%d" not valid'),
                    $id
                ),
                m::ERROR
            );

            return $this->redirect(
                $this->generateUrl('admin_news_agency_config', array('page' => $page))
            );
        }

        try {
            $repository = new \Onm\Import\Repository\LocalRepository();
            $repository->deleteFilesForSource($id);

            unset($servers[$id]);

            s::set('news_agency_config', $servers);

            m::add(_('News agency server deleted.'), m::SUCCESS);
        } catch (\Exception $e) {
            m::add($e->getMessage(), m::ERROR);
        }

        return $this->redirect($this->generateUrl('admin_news_agency_servers'));
    }

    /**
     * Removes the synchronized files for a given source
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function removeServerFilesAction(Request $request)
    {
        $servers = s::get('news_agency_config');

        $id = $request->query->getDigits('id');

        if (!array_key_exists($id, $servers)) {
            m::add(
                sprintf(
                    _('Source identifier "%d" not valid'),
                    $id
                ),
                m::ERROR
            );

            return $this->redirect(
                $this->generateUrl('admin_news_agency_config', array('page' => $page))
            );
        }

        try {
            $repository = new \Onm\Import\Repository\LocalRepository();
            $repository->deleteFilesForSource($id);

            m::add(sprintf(_('Files for "%s" cleaned.'), $servers[$id]['name']), m::SUCCESS);
        } catch (\Exception $e) {
            m::add($e->getMessage(), m::ERROR);
        }

        return $this->redirect($this->generateUrl('admin_news_agency_servers'));
    }
}
