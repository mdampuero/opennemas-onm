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
 * Handles the actions for the system information
 *
 * @package Backend_Controllers
 * @author
 **/
class ImporterEuropapressController extends Controller
{

    /**
     * Common code for all the actions
     *
     * @return void
     **/
    public function init()
    {
        $this->view = new \TemplateAdmin(TEMPLATE_ADMIN);

        ini_set('memory_limit', '128M');
        ini_set('set_time_limit', '0');
    }

    /**
     * Shows the list of downloaded newsfiles from Europapress service
     *
     * @return Response the response object
     **/
    public function listAction()
    {

        if (is_null(s::get('europapress_server_auth'))) {
            m::add(_('Please provide your Europapress auth credentials '
                .'to start to use your Europapress Importer module'));

            return $this->redirect($this->generateUrl('admin_import_europapress'));
        }

        $europapress = \Onm\Import\Europapress::getInstance();

        // Get the amount of minutes from last sync
        $minutesFromLastSync = $europapress->minutesFromLastSync();

        $categories = \Onm\Import\DataSource\Europapress::getOriginalCategories();

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

        list($countTotalElements, $elements) = $europapress->findAll($findParams);

        // Pager
        $pagination = \Pager::factory(array(
            'mode'        => 'Sliding',
            'perPage'     => $itemsPage,
            'delta'       => 4,
            'clearIfVoid' => true,
            'urlVar'      => 'page',
            'append'      => false,
            'path'        => '',
            'totalItems'  => $countTotalElements,
            'fileName'        => $this->generateUrl(
                'admin_importer_europapress',
                array(
                    'filter_category' => $filterCategory,
                    'filter_title'    => $filterTitle,
                )
            ).'&page=%d',
        ));

        $message = '';
        if ($minutesFromLastSync > 100) {
            $message = _('A long time ago from synchronization.');
        } elseif ($minutesFromLastSync > 10) {
            $message = sprintf(_('Last sync was %d minutes ago.'), $minutesFromLastSync);
        }
        if ($message) {
            m::add(
                $message
                ._('Try syncing the news list from server by clicking '
                    .'in "Sync with server" button above'),
                m::NOTICE
            );
        }

        $_SESSION['_from'] = $this->generateUrl('admin_importer_europapress', array(
            'filter_category' => $filterCategory,
            'filter_title'    => $filterTitle,
            'page'            => $page
        ));

        return $this->render('agency_importer/europapress/list.tpl', array(
            'elements'      =>  $elements,
            'categories'    =>  $categories,
            'minutes'       =>  $minutesFromLastSync,
            'pagination'    =>  $pagination,
        ));
    }

    /**
     * Shows the information for a given newfile filename
     *
     * @return Response the response object
     **/
    public function showAction()
    {
        $id = $this->request->query->filter('id', null, FILTER_SANITIZE_STRING);

        try {
            $ep = new \Onm\Import\Europapress();
            $element = $ep->findByFileName($id);
        } catch (\Exception $e) {
            // Redirect the user to the list of articles and show an error message
            m::add(sprintf(_('Unable to find the nwe with id "%d".'), $id), m::ERROR);

            return $this->redirect($this->generateUrl('admin_importer_europapress'));
        }

        return $this->render('agency_importer/europapress/show.tpl', array(
            'element' => $element
        ));
    }

    /**
     * Imports the article information given a newfile filename
     *
     * @return Response the response object
     **/
    public function importAction()
    {
        $id = $this->request->query->filter('id', null, FILTER_SANITIZE_STRING);

        $ep      = new \Onm\Import\Europapress();
        $element = $ep->findByFileName($id);

        $values = array(
            'title'          => $element->title,
            'category'       => 20,
            'with_comment'   => 1,
            'content_status' => 0,
            'frontpage'      => 0,
            'in_home'        => 0,
            'title_int'      => $element->title,
            'metadata'       => \StringUtils::get_tags($element->title),
            'subtitle'       => $element->pretitle,
            'agency'         => s::get('europapress_agency_string') ?: $element->agencyName,
            'summary'        => $element->summary,
            'body'           => $element->body,
            'posic'          => 0,
            'id'             => 0,
            'fk_publisher'   => $_SESSION['userid'],
            'img1'           => '',
            'img1_footer'    => '',
            'img2'           => '',
            'img2_footer'    => '',
            'fk_video'       => '',
            'fk_video2'      => '',
            'footer_video2'  => '',
            'ordenArti'      => '',
            'ordenArtiInt'   => '',
        );

        $article           = new \Article();
        $newArticleID      = $article->create($values);
        $_SESSION['desde'] = 'europa_press_import';

        if (!empty($newArticleID)) {

            return $this->redirect($this->generateUrl('admin_article_show', array('id' => $newArticleID)));
        } else {
            m::add(sprintf('Unable to import the file "%s"', $id));

            return $this->redirect($this->generateUrl('admin_importer_europapress'));
        }
    }

    /**
     * Shows and handles the configuration form for Europapress module
     *
     * @return Response the response object
     **/
    public function configAction()
    {
        // If request is post save the information
        if ('POST' != $this->request->getMethod()) {
            if ($serverAuth = s::get('europapress_server_auth')) {

                $message    = $this->request->query->filter('message', null, FILTER_SANITIZE_STRING);

                $this->view->assign(array(
                    'server'        => $serverAuth['server'],
                    'username'      => $serverAuth['username'],
                    'password'      => $serverAuth['password'],
                    'message'       => $message,
                    'agency_string' => s::get('europapress_agency_string'),
                    'sync_from'     => array(
                        'no_limits' => _('No limit'),
                        '21600'     => sprintf(_('%d hours'), '6'),
                        '43200'     => sprintf(_('%d hours'), '12'),
                        '86400'     => _('1 day'),
                        '172800'    => sprintf(_('%d days'), '2'),
                        '259200'    => sprintf(_('%d days'), '3'),
                        '345600'    => sprintf(_('%d days'), '4'),
                        '432000'    => sprintf(_('%d days'), '5'),
                        '518400'    => sprintf(_('%d days'), '6'),
                        '604800'    => sprintf(_('%d week'), '1'),
                        '1209600'   => sprintf(_('%d weeks'), '2'),
                    ),
                    'sync_from_setting'=> s::get('europapress_sync_from_limit'),
                ));
            }

            return $this->render('agency_importer/europapress/config.tpl');

        } else {
            // If request was GET show the form
            $server       = $this->request->request->filter('server', null, FILTER_SANITIZE_STRING );
            $username     = $this->request->request->filter('username', null, FILTER_SANITIZE_STRING );
            $password     = $this->request->request->filter('password', null, FILTER_SANITIZE_STRING );
            $syncFrom     = $this->request->request->filter('sync_from', null, FILTER_SANITIZE_STRING );
            $agencyString = $this->request->request->filter('agency_string', null, FILTER_SANITIZE_STRING);

            if (!isset($server) || !isset($username) || !isset($password)) {
                return $this->redirect($this->generateUrl('admin_import_europapress_config'));
            }

            $serverAuth =  array(
                'server'    => $server,
                'username' => $username,
                'password' => $password,
            );

            if (s::set('europapress_server_auth', $serverAuth)
                && s::set('europapress_sync_from_limit', $syncFrom)
                && s::set('europapress_agency_string', $agencyString)
            ) {
                m::add(_('Europapress configuration saved successfully'), m::SUCCESS);
            } else {
                m::add(_('There was an error while saving Europapress configuration'), m::ERROR);
            }

            return $this->redirect($this->generateUrl('admin_importer_europapress'));
        }
    }

    /**
     * Cleans the unlock file for Europapress module
     *
     * @return Response the response object
     **/
    public function unlockAction()
    {
        $e = new \Onm\Import\Europapress();
        $e->unlockSync();
        unset($_SESSION['error']);

        return $this->redirect($this->generateUrl('admin_importer_europapress'));
    }

    /**
     * Performs the files synchronization with the external server
     *
     * @return Response the response object
     **/
    public function syncAction()
    {
        try {

            $serverAuth = s::get('europapress_server_auth');

            $ftpConfig = array(
                'server'    => $serverAuth['server'],
                'user'      => $serverAuth['username'],
                'password'  => $serverAuth['password'],
                'allowed_file_extesions_pattern' => '.*\.xml$',
                'max_age'                => s::get('europapress_sync_from_limit')
            );

            $epSynchronizer = \Onm\Import\Europapress::getInstance();
            $message        = $epSynchronizer->sync($ftpConfig);
            $epSynchronizer->updateSyncFile();

            m::add(
                sprintf( _('Downloaded %d new articles and deleted %d old ones.'),
                        $message['downloaded'],
                        $message['deleted'])
            );

        } catch (\Onm\Import\SynchronizationException $e) {
            m::add($e->getMessage(), m::ERROR);
        } catch (\Onm\Import\Synchronizer\LockException $e) {
            $errorMessage = $e->getMessage()
               .sprintf(_('If you are sure <a href="%s">try to unlock it</a>'),
                $this->generateUrl('admin_importer_europapress_unlock'));
            m::add( $errorMessage, m::ERROR );
        } catch (\Exception $e) {
            m::add($e->getMessage(), m::ERROR);
        }
        $e = new \Onm\Import\Europapress();
        $e->unlockSync();

        return $this->redirect($this->generateUrl('admin_importer_europapress'));
    }

} // END class ImporterEuropapressController
