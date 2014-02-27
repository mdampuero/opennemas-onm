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
namespace Manager\Controller;

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
     * Shows a list of instances
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     * @Security("has_role('INSTANCE_SYNC_ADMIN')")
     **/
    public function listAction(Request $request)
    {
        $findParams = array(
            'name'  => $request->query->filter('filter_name', '', FILTER_SANITIZE_STRING),
            'email' => $request->query->filter('filter_email', '', FILTER_SANITIZE_STRING),
        );

        $instanceManager = getService('instance_manager');
        $instances = $instanceManager->findAll($findParams);

        foreach ($instances as &$instance) {
            list($instance->totals, $instance->configs) = $instanceManager->getDBInformation($instance->settings);

            // Get real time for last login
            if (isset($instance->configs['last_login'])) {
                $instance->configs['last_login'] = \DateTime::createFromFormat(
                    'Y-m-d H:i:s',
                    $instance->configs['last_login'],
                    new \DateTimeZone('UTC')
                );
            }

            $instance->domains = preg_split("@, @", $instance->domains);
        }

        $availableTimeZones = \DateTimeZone::listIdentifiers();

        return $this->render(
            'instances/list.tpl',
            array(
                'instances'     => $instances,
                'filter_name'   => $findParams['name'],
                'filter_email'  => $findParams['email'],
                'timeZones'     => $availableTimeZones,
            )
        );
    }

    /**
     * Returns a CSV file with all the instances information
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("has_role('INSTANCE_SYNC_ADMIN')")
     **/
    public function listExportAction(Request $request)
    {
        $findParams = array(
            'name' => $request->query->filter('filter_name', '*', FILTER_SANITIZE_STRING),
        );

        $instanceManager = getService('instance_manager');

        $instances = $instanceManager->findAll($findParams);

        foreach ($instances as &$instance) {
            list($instance->totals, $instance->configs) = $instanceManager->getDBInformation($instance->settings);

            // Get real time for last login
            if (isset($instance->configs['last_login'])) {
                $instance->configs['last_login'] = \DateTime::createFromFormat(
                    'Y-m-d H:i:s',
                    $instance->configs['last_login'],
                    new \DateTimeZone('UTC')
                );
            }

            $instance->domains = preg_split("@, @", $instance->domains);
        }

        $this->view = new \TemplateManager(TEMPLATE_MANAGER);

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
     *
     * @Security("has_role('INSTANCE_SYNC_ADMIN')")
     **/
    public function showAction(Request $request)
    {
        $id = $request->query->getDigits('id');

        $templates = im::getAvailableTemplates();

        $instanceManager = $this->get('instance_manager');

        $instance = $instanceManager->read($id);

        if ($instance === false) {
            m::add(sprintf(_('Unable to find an instance with the id "%d"'), $id), m::ERROR);

            return $this->redirect($this->generateUrl('manager_instances'));
        }

        list($instance->totals, $instance->configs) = $instanceManager->getDBInformation($instance->settings);

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
     *
     * @Security("has_role('INSTANCE_SYNC_ADMIN')")
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
                'domain_expire' => $request->request->filter('domain_expire', '', FILTER_SANITIZE_STRING),
                'activated'     => $request->request->filter('activated', '', FILTER_SANITIZE_NUMBER_INT),
                'settings'      => $settings,
                'site_created'  => $request->request
                    ->filter('site_created', date("Y-m-d - H:m:s"), FILTER_SANITIZE_STRING)
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

            $instanceManager = getService('instance_manager');

            // Check for reapeted internalnameshort and if so, add a number at the end
            $data   = $instanceManager->checkInternalShortName($data);
            $errors = $instanceManager->create($data);

            if (is_array($errors) && count($errors) > 0) {
                m::add($errors, m::ERROR);
            } else {
                m::add('Instance saved successfully.', m::SUCCESS);
            }

            return $this->redirect($this->generateUrl('manager_instances'));
        } else {
            $templates = im::getAvailableTemplates();

            $this->view = new \TemplateManager(TEMPLATE_MANAGER);

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
     *
     * @Security("has_role('INSTANCE_SYNC_ADMIN')")
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

        if (count($request->request) < 1) {
            m::add(_("Instance data sent not valid."), m::ERROR);

            return $this->redirect(
                $this->generateUrl('manager_instance_show', array('id' => $id))
            );
        }

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
            'domain_expire' => $request->request->filter('domain_expire', '', FILTER_SANITIZE_STRING),
            'activated'     => $request->request->filter('activated', '', FILTER_SANITIZE_NUMBER_INT),
            'settings'      => $settings,
            'site_created'  => $request->request->filter('site_created', date("Y-m-d - H:m:s"), FILTER_SANITIZE_STRING)
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
            'contact_mail','contact_name','contact_IP','domain_expire',
            'time_zone','site_language', 'pass_level',
            'newsletter_sender',  'max_mailing', 'mail_server', 'last_invoice',
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
        s::invalidate('last_invoice', $data['internal_name']);

        $instanceManager = getService('instance_manager');

        //TODO: PROVISIONAL WHILE DONT DELETE $GLOBALS['application']->conn // is used in settings set
        $GLOBALS['application']->conn = $instanceManager->getConnection($settings);

        foreach ($request->request->all() as $key => $value) {
            if (in_array($key, $configurationsKeys)) {
                s::set($key, $value);
            }
        }
        $errors = $instanceManager->update($data);

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
     *
     * @Security("has_role('INSTANCE_SYNC_ADMIN')")
     **/
    public function deleteAction(Request $request)
    {
        $id = $request->query->getDigits('id');

        if (!empty($id)) {
            $instanceManager = getService('instance_manager');

            $delete = $instanceManager->delete($id);
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
        } else {
            return new Response('ok');
        }
    }

    /**
     * Batch Delete instances given its ids
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("has_role('INSTANCE_SYNC_ADMIN')")
     **/
    public function batchDeleteAction(Request $request)
    {
        $filter_name  = $request->query->filter('filter_name', '', FILTER_SANITIZE_STRING);
        $filter_email = $request->query->filter('filter_email', '', FILTER_SANITIZE_STRING);

        $selected = $request->query->get('selected', null);

        if (is_array($selected) && count($selected) > 0) {
            $instanceManager = getService('instance_manager');

            foreach ($selected as $id) {
                $delete = $instanceManager->delete($id);
                if (!$delete) {
                    m::add(sprintf(_("Unable to delete instance %d."), $id), m::ERROR);
                    if (is_array($delete) && count($delete) > 0) {
                        m::add($delete, m::ERROR);
                    }
                } else {
                    m::add(sprintf(_("Instance %d deleted successfully."), $id), m::SUCCESS);
                }
            }
        }

        return $this->redirect(
            $this->generateUrl(
                'manager_instances',
                array('filter_name' => $filter_name, 'filter_email' => $filter_email)
            )
        );
    }

    /**
     * Toggle the availability of an instance given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("has_role('INSTANCE_SYNC_ADMIN')")
     **/
    public function toggleAvailableAction(Request $request)
    {
        $id = $request->query->getDigits('id');

        $instanceManager = getService('instance_manager');
        $instance = $instanceManager->read($id);

        if ($instance === false) {
            m::add(sprintf(_('Unable to find the instance with the id %d'), $id), m::ERROR);

            return $this->redirect($this->generateUrl('manager_instances'));
        }

        $instanceManager->changeActivated(
            $instance->id,
            ($instance->activated+1) % 2
        );

        return $this->redirect($this->generateUrl('manager_instances'));
    }

    /**
     * Set the activated flag for instances in batch
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("has_role('INSTANCE_SYNC_ADMIN')")
     **/
    public function batchAvailableAction(Request $request)
    {
        $selected = $request->query->get('selected', null);
        $status   = $request->query->getDigits('status', 0);

        if (is_array($selected) && count($selected) > 0) {
            $instanceManager = getService('instance_manager');

            foreach ($selected as $id) {
                $instance = $instanceManager->read($id);
                if ($instance === false) {
                    m::add(sprintf(_('Unable to find the instance with the id %d'), $id), m::ERROR);
                } else {
                    $instanceManager->changeActivated($id, $status);
                    m::add(sprintf(_("Instance %d updated successfully."), $id), m::SUCCESS);
                }
            }
        }

        return new Response('ok', 200);
    }
}
