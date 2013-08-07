<?php
/**
 * Handles the actions for the system information
 *
 * @package Manager_Controllers
 **/
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Manager\Controllers;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Onm\Module\ModuleManager as ModuleManager;
use Onm\Framework\Controller\Controller;
use Onm\Instance\InstanceManager as im;
use Onm\Message as m;
use Onm\Settings as s;

/**
 * Handles the actions for the system information
 *
 * @package Manager_Controllers
 **/
class InstancesController extends Controller
{
    /**
     * Common code for all the actions
     *
     * @return void
     **/
    public function init()
    {
        global $onmInstancesConnection;
        $this->instanceManager = new im($onmInstancesConnection);
        $this->view = new \TemplateManager(TEMPLATE_MANAGER);
    }

    /**
     * Shows a list of instances
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function listAction(Request $request)
    {
        $page = $request->query->getDigits('page', 1);
        $findParams = array(
            'name' => $request->query->filter('filter_name', '*', FILTER_SANITIZE_STRING),
            'per_page' => $request->query->filter('filter_per_page', 20, FILTER_SANITIZE_STRING),
        );

        $instances = $this->instanceManager->findAll($findParams);

        foreach ($instances as &$instance) {
            list($instance->totals, $instance->configs) =
                $this->instanceManager->getDBInformation($instance->settings);

            $instance->domains = preg_split("@, @", $instance->domains);
        }

        $itemsPerPage =  $findParams['per_page'];

        // Pager
        $pager_options = array(
            'mode'        => 'Sliding',
            'perPage'     => $itemsPerPage,
            'append'      => false,
            'path'        => '',
            'delta'       => 4,
            'clearIfVoid' => true,
            'urlVar'      => 'page',
            'totalItems'  => count($instances),
            'fileName'    => $this->generateUrl(
                'manager_instances'
            ).'?filter_name='.$findParams['name'].'&filter_per_page='.$itemsPerPage.'&page=%d',
        );
        $pager = \Pager::factory($pager_options);

        $instances = array_slice($instances, ($page-1) * $itemsPerPage, $itemsPerPage);

        return $this->render(
            'instances/list.tpl',
            array(
                'instances'     => $instances,
                'per_page'      => $itemsPerPage,
                'filter_name'   => $findParams['name'],
                'pagination'    => $pager,
            )
        );
    }

    /**
     * Returns a CSV file with all the instances information
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function listExportAction(Request $request)
    {
        $findParams = array(
            'name' => $request->query->filter('filter_name', '*', FILTER_SANITIZE_STRING),
        );

        $instances = $this->instanceManager->findAll($findParams);

        foreach ($instances as &$instance) {
            list($instance->totals, $instance->configs) =
                $this->instanceManager->getDBInformation($instance->settings);

            $instance->domains = preg_split("@, @", $instance->domains);
        }

        $response = $this->render(
            'instances/csv.tpl',
            array(
                'instances'   => $instances,
                'filter_name' => $findParams['name'],
            )
        );

        if ($findParams['name'] != '*') {
            $fileNameFilter = '-'.\Onm\StringUtils::get_title($findParams['name']);
        } else {
            $fileNameFilter = '-complete';
        }
        $fileName = 'opennemas-instances'.$fileNameFilter.'-'.date("Y_m_d_His").'.csv';

        $response->setStatusCode(200);
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Description', 'Submissions Export');
        $response->headers->set('Content-Disposition', 'attachment; filename='.$fileName);
        $response->headers->set('Content-Transfer-Encoding', 'binary');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');

        return $response;
    }

    /**
     * Shows the information form of a instance given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function showAction(Request $request)
    {
        $id = $request->query->getDigits('id');

        $templates = im::getAvailableTemplates();

        $instance = $this->instanceManager->read($id);

        if ($instance === false) {
            m::add(sprintf(_('Unable to find an instance with the id "%d"'), $id), m::ERROR);

            return $this->redirect($this->generateUrl('manager_instances'));
        }

        list($instance->totals, $instance->configs) =
            $this->instanceManager->getDBInformation($instance->settings);

        return $this->render(
            'instances/edit.tpl',
            array(
                'configs'           => $instance->configs,
                'available_modules' => ModuleManager::getAvailableModules(),
                'timezones'         => \DateTimeZone::listIdentifiers(),
                'languages'         => array(
                    'en_US' => _("English"),
                    'es_ES' => _("Spanish"),
                    'gl_ES' => _("Galician")
                ),
                'logLevels'         => array(
                    'normal'  => _('Normal'),
                    'verbose' => _('Verbose'),
                    'all'     => _('All (Paranoic mode)')
                ),
                'instance'          => $instance,
                'templates'         => $templates,
            )
        );
    }

    /**
     * Handles the form for create a new instance
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function createAction(Request $request)
    {
        if ('POST' == $request->getMethod()) {
            //Get internal_name from domains
            $internalName = "";
            if (isset($_POST['internal_name']) && !empty($_POST['internal_name'])) {
                $internalName = $_POST['internal_name'];
            } else {
                $internal = explode(".", filter_input(INPUT_POST, 'domains', FILTER_SANITIZE_STRING));
                $internalName = $internal[0];
            }
            //Force internal_name lowercase
            $internalName = strtolower($internalName);

            //If is creating a new instance, get DB params on the fly
            $internalNameShort = trim(substr($internalName, 0, 11));

            $password = \Onm\StringUtils::generatePassword(16);
            $settings = array(
                'TEMPLATE_USER' => "base",
                'MEDIA_URL'     => "",
                'BD_TYPE'       => "mysqli",
                'BD_HOST'       => "localhost",
                'BD_USER'       => $internalNameShort,
                'BD_PASS'       => $password,
                'BD_DATABASE'   => "c-".$internalNameShort,
            );

            //Get all the Post data
            $data = array(
                'contact_IP'    => $request->request->filter('contact_IP', '', FILTER_SANITIZE_STRING),
                'name'          => $request->request->filter('site_name', '', FILTER_SANITIZE_STRING),
                'user_name'     => $request->request->filter('contact_name', '', FILTER_SANITIZE_STRING),
                'user_mail'     => $request->request->filter('contact_mail', '', FILTER_SANITIZE_STRING),
                'user_pass'     => $request->request->filter('password', '', FILTER_SANITIZE_STRING),
                'internal_name' => $internalName,
                'domains'       => $request->request->filter('domains', '', FILTER_SANITIZE_STRING),
                'activated'     => $request->request->filter('activated', '', FILTER_SANITIZE_NUMBER_INT),
                'settings'      => $settings,
                'site_created'  => $request->request
                    ->filter('site_created', date("d-m-Y - H:m"), FILTER_SANITIZE_STRING)
            );

            // Also get timezone if comes from openhost form
            $timezone = $request->request->filter('timezone', '', FILTER_SANITIZE_STRING);
            if (!empty ($timezone)) {
                $allTimezones = \DateTimeZone::listIdentifiers();
                foreach ($allTimezones as $key => $value) {
                    if ($timezone == $value) {
                        $data['timezone'] = $key;
                    }
                }
            }

            $errors = array();
            // Check for reapeted internalnameshort and if so, add a number at the end
            $data = $this->instanceManager->checkInternalShortName($data);

            $errors = $this->instanceManager->create($data);

            if (is_array($errors) && count($errors) > 0) {
                m::add($errors, m::ERROR);
            } else {
                m::add('Instance saved successfully.', m::SUCCESS);
            }

            return $this->redirect($this->generateUrl('manager_instances'));
        } else {
            $templates = im::getAvailableTemplates();

            return $this->render(
                'instances/edit.tpl',
                array(
                    'configs' => array( 'activated_modules' => ModuleManager::getAvailableModules()),
                    'available_modules' => ModuleManager::getAvailableModules(),
                    'timezones' => \DateTimeZone::listIdentifiers(),
                    'languages' => array(
                        'en_US' => _("English"),
                        'es_ES' => _("Spanish"),
                        'gl_ES' => _("Galician")
                    ),
                    'logLevels' => array(
                        'normal'  => _('Normal'),
                        'verbose' => _('Verbose'),
                        'all'     => _('All (Paranoic mode)')
                    ),
                    'templates' => $templates,
                )
            );
        }
    }

    /**
     * Updates the instance information gives its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function updateAction(Request $request)
    {
        $id = $request->query->getDigits('id');

        //Get internal_name from domains
        $internalName = "";
        if (isset($_POST['internal_name'])
            && !empty($_POST['internal_name'])
        ) {
            $internalName = $_POST['internal_name'];
        } else {
            $internal = explode(".", filter_input(INPUT_POST, 'domains', FILTER_SANITIZE_STRING));
            $internalName = $internal[0];
        }
        //Force internal_name lowercase
        $internalName = strtolower($internalName);

        //If is creating a new instance, get DB params on the fly
        $internalNameShort = trim(substr($internalName, 0, 11));

        $settingsRAW = $request->request->get('settings');
        $settings = array(
            'TEMPLATE_USER' => filter_var($settingsRAW['TEMPLATE_USER'], FILTER_SANITIZE_STRING),
            'MEDIA_URL'     => filter_var($settingsRAW['MEDIA_URL'], FILTER_SANITIZE_STRING),
            'BD_TYPE'       => filter_var($settingsRAW['BD_TYPE'], FILTER_SANITIZE_STRING),
            'BD_HOST'       => filter_var($settingsRAW['BD_HOST'], FILTER_SANITIZE_STRING),
            'BD_DATABASE'   => filter_var($settingsRAW['BD_DATABASE'], FILTER_SANITIZE_STRING),
            'BD_USER'       => filter_var($settingsRAW['BD_USER'], FILTER_SANITIZE_STRING),
            'BD_PASS'       => filter_var($settingsRAW['BD_PASS'], FILTER_SANITIZE_STRING),
        );

        //Get all the Post data
        $data = array(
            'id'            => $request->query->getDigits('id'),
            'contact_IP'    => $request->request->filter('contact_IP', '', FILTER_SANITIZE_STRING),
            'name'          => $request->request->filter('site_name', '', FILTER_SANITIZE_STRING),
            'user_name'     => $request->request->filter('contact_name', '', FILTER_SANITIZE_STRING),
            'user_mail'     => $request->request->filter('contact_mail', '', FILTER_SANITIZE_STRING),
            'user_pass'     => $request->request->filter('password', '', FILTER_SANITIZE_STRING),
            'internal_name' => $internalName,
            'domains'       => $request->request->filter('domains', '', FILTER_SANITIZE_STRING),
            'activated'     => $request->request->filter('activated', '', FILTER_SANITIZE_NUMBER_INT),
            'settings'      => $settings,
            'site_created'  => $request->request->filter('site_created', date("d-m-Y - H:m"), FILTER_SANITIZE_STRING)
        );

        // Also get timezone if comes from openhost form
        $timezone = $request->request->filter('timezone', '', FILTER_SANITIZE_STRING);
        if (!empty ($timezone)) {
            $allTimezones = \DateTimeZone::listIdentifiers();
            foreach ($allTimezones as $key => $value) {
                if ($timezone == $value) {
                    $data['timezone'] = $key;
                }
            }
        }

        $errors = array();

        $configurationsKeys = array(
            'site_title', 'site_description','site_keywords',
            'site_agency','site_name','site_created',
            'contact_mail','contact_name','contact_IP',
            'time_zone','site_language',
            'newsletter_sender',  'max_mailing', 'mail_server',
            'mail_username','mail_password','google_maps_api_key',
            'google_custom_search_api_key','facebook',
            'google_analytics','piwik',
            'recaptcha',
            'items_per_page',
            'refresh_interval',
            'advertisements_enabled',
            'log_enabled', 'log_db_enabled', 'log_level',
            'activated_modules'
        );

        // Delete the 'activated_modules' from cache service for this instance
        s::invalidate('activated_modules', $data['internal_name']);
        // Delete the 'site_name' from cache service for this instance
        s::invalidate('site_name', $data['internal_name']);

        //TODO: PROVISIONAL WHILE DONT DELETE $GLOBALS['application']->conn // is used in settings set
        $GLOBALS['application']->conn = $this->instanceManager->getConnection($settings);

        foreach ($request->request->all() as $key => $value) {
            if (in_array($key, $configurationsKeys)) {
                s::set($key, $value);
            }
        }
        $errors = $this->instanceManager->update($data);

        if (is_array($errors) && count($errors) > 0) {
            m::add($errors, m::ERROR);
        } else {
            m::add('Instance saved successfully.', m::SUCCESS);
        }

        $domains = $request->request->filter('domains', '', FILTER_SANITIZE_STRING);
        $domains = explode(',', $domains);
        foreach ($domains as $domain) {
            $domain = trim($domain);
            $this->get('cache')->delete('instance_'.$domain);
        }

        return $this->redirect(
            $this->generateUrl('manager_instance_show', array('id' => $id))
        );
    }

    /**
     * Deletes an instance given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function deleteAction(Request $request)
    {
        $id = $request->query->getDigits('id');

        if (!empty($id)) {
            $delete = $this->instanceManager->delete($id);
            if (!$delete) {
                m::add(_("Unable to delete the instance."), m::ERROR);
                if (is_array($delete) && count($delete) > 0) {
                    m::add($delete, m::ERROR);
                }
            } else {
                m::add(_("Instance deleted successfully."), m::SUCCESS);
            }
        } else {
            m::add(_('You must provide an id for delete an instance.'), m::ERROR);
        }

        if (!$request->isXmlHttpRequest()) {
            return $this->redirect($this->generateUrl('manager_instances'));
        }
    }

    /**
     * Toggle the availability of an instance given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function toggleAvailableAction(Request $request)
    {
        $id = $request->query->getDigits('id');
        $instance = $this->instanceManager->read($id);

        if ($instance === false) {
            m::add(sprintf(_('Unable to find the instance with the id %d'), $id), m::ERROR);

            return $this->redirect($this->generateUrl('manager_instances'));
        }

        $this->instanceManager->changeActivated(
            $instance->id,
            ($instance->activated+1) % 2
        );

        return $this->redirect($this->generateUrl('manager_instances'));
    }
}
