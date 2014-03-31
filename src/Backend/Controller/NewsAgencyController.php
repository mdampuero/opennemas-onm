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
namespace Backend\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
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
        //Check if module is activated in this onm instance
        \Onm\Module\ModuleManager::checkActivatedOrForward('NEWS_AGENCY_IMPORTER');

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
     *
     * @Security("has_role('IMPORT_ADMIN')")
     **/
    public function listAction(Request $request)
    {
        $filterSource = $request->query->filter('filter_source', '*', FILTER_SANITIZE_STRING);
        $filterTitle  = $request->query->filter('filter_title', '*', FILTER_SANITIZE_STRING);

        // Get the amount of minutes from last sync
        $syncParams = array('cache_path' => CACHE_PATH);
        $synchronizer = new \Onm\Import\Synchronizer\Synchronizer($syncParams);
        $minutesFromLastSync = $synchronizer->minutesFromLastSync();

        // Fetch all servers and activated sources
        $servers = s::get('news_agency_config');
        if (!is_array($servers)) {
            $servers = array();
        }
        $sources = array_map(
            function ($server) {
                return $server['name'];
            },
            $servers
        );

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
                'page'          => 1
            )
        );

        return $this->render(
            'news_agency/list.tpl',
            array(
                'source_names'     => $sources,
                'servers'          => $servers,
                'minutes'          => $minutesFromLastSync,
            )
        );
    }

    /**
     *
     *
     * @return void
     * @author
     **/
    public function webServiceAction(Request $request)
    {
        $elementsPerPage = $request->request->getDigits('elements_per_page', 10);
        $page            = $request->request->getDigits('page', 1);
        $search          = $request->request->get('search');

        $filterSource = $filterTitle = '*';

        if (is_array($search)) {
            if (array_key_exists('source', $search)) {
                $filterSource          = $search['source'][0]['value'];
            }
            if (array_key_exists('title', $search)) {
                $filterTitle     = $search['title'][0]['value'];
            }
        }

        // Get LocalRepository instance
        $repository = new \Onm\Import\Repository\LocalRepository();

        // Fetch all servers and activated sources
        $servers = s::get('news_agency_config');
        if (!is_array($servers)) {
            $servers = array();
        }
        $sources = array_map(
            function ($server) {
                return $server['name'];
            },
            $servers
        );

        // Fetch filter params
        $findParams = array(
            'source'     => $filterSource,
            'title'      => $filterTitle,
            'page'       => $page,
            'items_page' => $elementsPerPage,
        );

        list($countTotalElements, $elements) = $repository->findAll($findParams);

        $urns = array();
        foreach ($elements as $element) {
            $urns []= $element->urn;
        }
        $alreadyImported = \Content::findByUrn($urns);

        foreach ($elements as &$element) {
            $element->load();
            $element->source_name = $servers[$element->source_id]['name'];
            $element->source_color = $servers[$element->source_id]['color'];
            $element->import_url = $this->generateUrl(
                'admin_news_agency_pickcategory',
                array(
                    'source_id' => $element->source_id,
                    'id'        => \urlencode($element->xmlFile)
                )
            );
            $element->id = $element->source_id . ',' . $element->id;

            $element->already_imported = in_array($element->urn, $alreadyImported);
        }

        return new JsonResponse(
            array(
                'elements_per_page' => $elementsPerPage,
                'page'              => $page,
                'results'           => $elements,
                'total'             => $countTotalElements
            )
        );
    }

    /**
     * Shows the information for a given newfile filename
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("has_role('IMPORT_ADMIN')")
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
     *
     * @Security("has_role('IMPORT_ADMIN')")
     **/
    public function importAction(Request $request)
    {
        $id       = $request->query->filter('id', null, FILTER_SANITIZE_STRING);
        $sourceId = $request->query->getDigits('source_id');
        $category = $request->query->filter('category', null, FILTER_SANITIZE_STRING);
        if (empty($category)) {
            $category = $request->request->filter('category', null, FILTER_SANITIZE_STRING);
        }

        // Import and create element
        $article = $this->importElements($id, $sourceId, $category);

        // If something went wrong, redirect
        if ($article == 'redirect_list') {
            return $this->redirect($this->generateUrl('admin_news_agency'));
        } elseif ($article == 'redirect_category') {
            return $this->redirect($this->generateUrl('admin_news_agency_pickcategory', array(
                'id'        => $id,
                'source_id' => $sourceId
            )));
        }

        // TODO: change this redirection when creating the ported article controller
        if (!empty($article)) {
            return $this->redirect(
                $this->generateUrl(
                    'admin_article_show',
                    array('id' => $article)
                )
            );
        } else {
            m::add(sprintf('Unable to import the file "%s"', $id));

            return $this->redirect($this->generateUrl('admin_news_agency'));
        }
    }

    /**
     * Imports a list of articles given a list Ids
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function batchImportAction(Request $request)
    {
        $selected = $this->request->request->get('selected', null);

        if (is_array($selected) && count($selected) > 0) {

            foreach ($selected as $value) {
                // First is sorce_id and second is xml filename
                $item = explode(',', $value);

                // Import and create element - category unknown
                $this->importElements($item[1], $item[0], 'GUESS');

            }
        }

        return $this->redirect($this->generateUrl('admin_news_agency'));
    }

    /**
     * Shows the category form to pick a category under where to import the new
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("has_role('IMPORT_ADMIN')")
     **/
    public function selectCategoryWhereToImportAction(Request $request)
    {
        $id       = $this->request->query->filter('id', null, FILTER_SANITIZE_STRING);
        $category = $this->request->query->filter('category', null, FILTER_SANITIZE_STRING);
        $sourceId = $this->request->query->getDigits('source_id');

        if (empty($id)) {
            m::add(_('The article you want to import doesn\'t exists.'), m::ERROR);
            $this->redirect($this->generateUrl('admin_news_agency'));
        }

        $repository       = new \Onm\Import\Repository\LocalRepository();
        $element          = $repository->findByFileName($sourceId, $id);

        $ccm = \ContentCategoryManager::get_instance();
        list($parentCategories, $subcat, $categoryData) = $ccm->getArraysMenu();
        $categories = array();
        foreach ($parentCategories as $category) {
            $categories [$category->pk_content_category] = $category->title;
        }

        // If the element has a original category that matches an existing category
        // in the newspaper redirect it to the import action with that category
        $targetCategory = $this->getSimilarCategoryIdForElement($element);
        if (!empty($targetCategory)) {
            return $this->redirect($this->generateUrl(
                'admin_news_agency_import',
                array(
                    'source_id' => $sourceId,
                    'id'        => $id,
                    'category'  => $targetCategory,
                )
            ));
        }

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
     * undocumented function
     *
     * @return void
     * @author
     **/
    public function getSimilarCategoryIdForElement($element)
    {
        $originalCategory     = utf8_decode($element->getOpennemasData('category'));
        $originalCategoryTemp = strtolower($originalCategory);

        $ccm        = \ContentCategoryManager::get_instance();
        $categories = $ccm->findAll();

        $prevPoint = 1000;
        $finalCategory = null;
        foreach ($categories as $category) {
            $categoryName = strtolower(utf8_decode($category->title));
            $lev          = levenshtein($originalCategoryTemp, $categoryName);

            if ($lev < 2  && $lev < $prevPoint) {
                $prevPoint     = $lev;
                $finalCategory = $category->id;
            }
        }

        return $finalCategory;
    }

    /**
     * Returns the image file given a newsfile id and attached image id, if
     * not found return an 404 response error.
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("has_role('IMPORT_ADMIN')")
     **/
    public function showAttachmentAction(Request $request)
    {
        $id           = $this->request->query->filter('id', null, FILTER_SANITIZE_STRING);
        $index        = $this->request->query->getDigits('index');
        $sourceId     = $this->request->query->getDigits('source_id');
        $attachmentId = $this->request->query->filter('attachment_id', null, FILTER_SANITIZE_STRING);

        $repository = new \Onm\Import\Repository\LocalRepository();
        $element    = $repository->findById($sourceId, $id);
        $content = null;
        if ($element->hasPhotos()) {
            $photos = $element->getPhotos();
            foreach ($photos as $photo) {

                if ($photo->id == $attachmentId) {

                    // Get image from FTP
                    $filePath = realpath(
                        $repository->syncPath.DS.$sourceId.DS.$photo->file_path
                    );

                    // If no image from FTP check HTTP
                    if (!$filePath) {
                        $filePath = $repository->syncPath.DS.
                            $sourceId.DS.$photo->name[$index];
                    }
                    $content = @file_get_contents($filePath);

                    $response = new Response(
                        $content,
                        200,
                        array('content-type' => $photo->file_type)
                    );
                }
            }
            if (empty($content)) {
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
     *
     * @Security("has_role('IMPORT_ADMIN')")
     **/
    public function unlockAction(Request $request)
    {
        $syncParams = array('cache_path' => CACHE_PATH);
        $synchronizer = new \Onm\Import\Synchronizer\Synchronizer($syncParams);
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
     *
     * @Security("has_role('IMPORT_ADMIN')")
     **/
    public function syncAction(Request $request)
    {
        $page = $this->request->query->filter('page', 1, FILTER_VALIDATE_INT);

        $servers = s::get('news_agency_config');

        $syncParams = array('cache_path' => CACHE_PATH);
        $synchronizer = new \Onm\Import\Synchronizer\Synchronizer($syncParams);

        try {
            $message = $synchronizer->syncMultiple($servers);
            m::add($message, m::SUCCESS);
        } catch (\Onm\Import\Synchronizer\LockException $e) {
            $errorMessage = $e->getMessage()
                .sprintf(
                    _('If you are sure <a href="%s">try to unlock it</a>'),
                    $this->generateUrl('admin_news_agency_unlock')
                );
            m::add($errorMessage, m::ERROR);
        } catch (\Exception $e) {
            m::add($e->getMessage(), m::ERROR);
        }


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
     *
     * @Security("has_role('IMPORT_ADMIN')")
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
     *
     * @Security("has_role('IMPORT_NEWS_AGENCY_CONFIG')")
     **/
    public function configUpdateServerAction(Request $request)
    {
        $id = $request->query->getDigits('id');

        $servers = s::get('news_agency_config');

        $server = array(
            'id'            => $id,
            'name'          => $request->request->filter('name', '', FILTER_SANITIZE_STRING),
            'url'           => $request->request->filter('url', '', FILTER_SANITIZE_STRING),
            'username'      => $request->request->filter('username', '', FILTER_SANITIZE_STRING),
            'password'      => $request->request->filter('password', '', FILTER_SANITIZE_STRING),
            'agency_string' => $request->request->filter('agency_string', '', FILTER_SANITIZE_STRING),
            'color'         => $request->request->filter('color', '#424E51', FILTER_SANITIZE_STRING),
            'sync_from'     => $request->request->filter('sync_from', '', FILTER_SANITIZE_STRING),
            'activated'     => $request->request->getDigits('activated', 0),
            'author'        => $request->request->getDigits('author', 0),
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
     *
     * @Security("has_role('IMPORT_NEWS_AGENCY_CONFIG')")
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
     * Toogle an server state to enabled/disabled
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function toogleEnabledAction(Request $request)
    {
        $serverId = $request->query->getDigits('id');

        $servers = s::get('news_agency_config');

        if ($servers[$serverId]['activated'] == '1') {
            $servers[$serverId]['activated'] = '0';
            $status = 'disabled';
        } else {
            $servers[$serverId]['activated'] = '1';
            $status = 'enabled';
        }

        s::set('news_agency_config', $servers);

        m::add(
            sprintf(
                'Server "%s" has been %s',
                $servers[$serverId]['name'],
                $status
            ),
            m::SUCCESS
        );

        return $this->redirect($this->generateUrl('admin_news_agency_servers'));
    }

    /**
     * Shows and handles the configuration form for Efe module
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("has_role('IMPORT_NEWS_AGENCY_CONFIG')")
     **/
    public function configCreateServerAction(Request $request)
    {
        if ('POST' != $request->getMethod()) {
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
                'color'         => $request->request->filter('color', '#424E51', FILTER_SANITIZE_STRING),
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
     *
     * @Security("has_role('IMPORT_NEWS_AGENCY_CONFIG')")
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
                $this->generateUrl('admin_news_agency_config')
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
     *
     * @Security("has_role('IMPORT_NEWS_AGENCY_CONFIG')")
     **/
    public function removeServerFilesAction(Request $request)
    {
        $id = $request->query->getDigits('id');

        $servers = s::get('news_agency_config');
        if (!array_key_exists($id, $servers)) {
            m::add(
                sprintf(
                    _('Source identifier "%d" not valid'),
                    $id
                ),
                m::ERROR
            );

            return $this->redirect(
                $this->generateUrl('admin_news_agency_config')
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

    /**
     * Basic logic to import an element
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    private function importElements($id = '', $sourceId = '', $category = null)
    {
        if (empty($id) || empty($sourceId)) {
            m::add(_('Please specify the article to import.'), m::ERROR);

            return 'redirect_list';
        }

        $categoryInstance = new \ContentCategory($category);
        if (!is_object($categoryInstance)) {
            m::add(_('The category you have chosen doesn\'t exists.'), m::ERROR);

            return 'redirect_category';
        }

        // Get EFE new from a filename
        try {
            $repository = new \Onm\Import\Repository\LocalRepository();
            $element = $repository->findByFileName($sourceId, $id);
        } catch (\Exception $e) {
            m::add(_('Please specify the article to import.'), m::ERROR);

            return 'redirect_list';
        }


        if ($category == 'GUESS') {
            // If the element has a original category that matches an existing category
            // in the newspaper redirect it to the import action with that category
            $category = $this->getSimilarCategoryIdForElement($element);
            if (empty($category)) {
                $category = '20';
            }
        } elseif (empty($category)) {
            m::add(_('Please assign the category where import this article'), m::ERROR);

            return 'redirect_category';
        }

        // Get server config
        $servers = s::get('news_agency_config');
        $server = $servers[$sourceId];

        // If the new has photos import them
        if ($element->hasPhotos()) {
            $photos = $element->getPhotos();
            $i = 0;
            foreach ($photos as $photo) {
                // Get image from FTP
                $filePath = realpath(
                    $repository->syncPath.DS.$sourceId.DS.$photo->file_path
                );
                $fileName = $photo->file_path;

                // If no image from FTP check HTTP
                if (!$filePath) {
                    $filePath = $repository->syncPath.DS.
                        $sourceId.DS.$photo->name[$i];
                    $fileName = $photo->name[$i];
                }

                // Check if the file cache exists(keys)
                if (file_exists($filePath)) {
                    $data = array(
                        'title'         => $fileName,
                        'description'   => $photo->title,
                        'local_file'    => $filePath,
                        'fk_category'   => $category,
                        'category_name' => $categoryInstance->name,
                        'category'      => $categoryInstance->name,
                        'metadata'      => \Onm\StringUtils::get_tags($photo->title),
                        'author_name'   => '&copy; EFE '.date('Y'),
                        'original_filename' => $fileName,
                    );

                    $photo = new \Photo();
                    $photoObject = $photo->createFromLocalFileAjax($data);

                    // Check if sync is from Opennemas instances
                    if ($element->getServicePartyName() == 'Opennemas') {
                        // If this article has more than one photo take the first one to front
                        if (!isset($frontPhoto)) {
                            $frontPhoto = new \Photo($photoObject->id);
                        } elseif (!isset($innerPhoto)) {
                            $innerPhoto = new \Photo($photoObject->id);
                        }
                    } elseif (!isset($innerPhoto)) {
                        $innerPhoto = new \Photo($photoObject->id);
                    }

                }
                $i++;
            }
        }

        // Check if sync is from Opennemas instances for importing author
        if ($element->getServicePartyName() == 'Opennemas') {
            // Check if allow to import authors
            if (isset($server['author']) && $server['author'] == '1') {

                // Get author object,decode it and create new author
                $authorObj = json_decode($element->getRightsOwner());
                $authorArray = get_object_vars($authorObj);
                $user = new \User();

                // Create author
                if (!is_null($authorArray['id']) && !$user->checkIfUserExists($authorArray)) {
                    // Create new user
                    $user->create($authorArray);

                    // Set user meta if exists
                    if ($authorObj->meta) {
                        $userMeta = get_object_vars($authorObj->meta);
                        $user->setMeta($userMeta);
                    }

                    // Fetch and save author image if exists
                    $authorImgUrl = $element->getRightsOwnerPhoto();
                    $cm = new \ContentManager();
                    $authorPhotoRaw = $cm->getUrlContent($authorImgUrl);
                    if ($authorPhotoRaw) {
                        $localImageDir  = MEDIA_IMG_PATH.$authorObj->photo->path_file;
                        $localImagePath = MEDIA_IMG_PATH.$authorObj->photo->path_img;
                        if (!is_dir($localImageDir)) {
                            \FilesManager::createDirectory($localImageDir);
                        }
                        if (file_exists($localImagePath)) {
                            unlink($localImagePath);
                        }
                        file_put_contents($localImagePath, $authorPhotoRaw);

                        // Get all necessary data for the photo
                        $infor = new \MediaItem($localImagePath);
                        $data = array(
                            'title'       => $authorObj->photo->name,
                            'name'        => $authorObj->photo->name,
                            'user_name'   => $authorObj->photo->name,
                            'path_file'   => $authorObj->photo->path_file,
                            'nameCat'     => $authorObj->username,
                            'category'    => '',
                            'created'     => $infor->atime,
                            'changed'     => $infor->mtime,
                            'date'        => $infor->mtime,
                            'size'        => round($infor->size/1024, 2),
                            'width'       => $infor->width,
                            'height'      => $infor->height,
                            'type'        => $infor->type,
                            'type_img'    => substr($authorObj->photo->name, -3),
                            'media_type'  => 'image',
                            'author_name' => $authorObj->username,
                        );

                        $photo = new \Photo();
                        $photoId = $photo->create($data);

                        // Get new author id and update avatar_img_id
                        $newAuthor = get_object_vars($user->findByEmail($authorObj->email));
                        $authorId = $newAuthor['id'];
                        $newAuthor['avatar_img_id'] = $photoId;
                        unset($newAuthor['password']);
                        $user->update($newAuthor);
                    }
                } else {
                    // Fetch the user if exists and is not null
                    if (!is_null($authorObj->email)) {
                        $author = $user->findByEmail($authorObj->email);
                        $authorId = $author->id;
                    }
                }
            }
        }

        // If the new has videos import them
        if ($element->hasVideos()) {
            $videos = $element->getVideos();
            foreach ($videos as $video) {
                $filePath = realpath(
                    $repository->syncPath.DS.$sourceId.DS.$video->file_path
                );

                // If no video from FTP check HTTP
                if (!$filePath) {
                    $filePath = $repository->syncPath.DS.
                        $sourceId.DS.$video->name[$i];
                    $fileName = $video->name[$i];
                }


                // Check if the file exists
                if ($filePath) {
                    $videoFileData = array(
                        'file_type'      => $video->file_type,
                        'file_path'      => $filePath,
                        'category'       => $category,
                        'available'      => 1,
                        'content_status' => 0,
                        'title'          => $video->title,
                        'metadata'       => \Onm\StringUtils::get_tags($video->title),
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
                $i++;
            }
        }

        $values = array(
            'title'          => $element->title,
            'category'       => $category,
            'with_comment'   => 1,
            'available'      => 0,
            'content_status' => 0,
            'frontpage'      => 0,
            'in_home'        => 0,
            'title_int'      => $element->title,
            'metadata'       => \Onm\StringUtils::get_tags($element->title),
            'subtitle'       => $element->pretitle,
            'agency'         => $server['agency_string'],
            'fk_author'      => (isset($authorId) ? $authorId : 0),
            'summary'        => $element->summary,
            'body'           => $element->body,
            'posic'          => 0,
            'id'             => 0,
            'fk_publisher'   => $_SESSION['userid'],
            'img1'           => (isset($frontPhoto) ? $frontPhoto->id : ''),
            'img1_footer'    => (isset($frontPhoto) ? $frontPhoto->description : ''),
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

        return $newArticleID;
    }
}
