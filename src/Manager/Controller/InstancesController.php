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
     * @param  Request  $request The request object.
     * @return Response          The response.
     */
    public function listAction(Request $request)
    {
        return $this->render('instances/list.tpl');
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

        $instanceManager = $this->get('instance_manager');

        $instance = $instanceManager->read($id);

        if ($instance === false) {
            m::add(sprintf(_('Unable to find an instance with the id "%d"'), $id), m::ERROR);

            return $this->redirect($this->generateUrl('manager_instances'));
        }

        list($instance->totals, $instance->configs) = $instanceManager->getDBInformation($instance->settings);
        $instance->domains = explode(',', $instance->domains);

        $size = explode("\t", shell_exec('du -s '.SITE_PATH."media".DS.$instance->internal_name.'/'));
        if (is_array($size)) {
            $size = $size[0] / 1024;
        }

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
                'size'              => $size,
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
                'BD_DATABASE'   => "c-".$internalNameShort,
            );

            //Get all the Post data
            $data = array(
                'contact_IP'    => $request->request->filter('contact_IP', '', FILTER_SANITIZE_STRING),
                'name'          => $request->request->filter('site_name', '', FILTER_SANITIZE_STRING),
                'user_mail'     => $request->request->filter('contact_mail', '', FILTER_SANITIZE_STRING),
                'internal_name' => $internalName,
                'domains'       => $request->request->filter('domains', '', FILTER_SANITIZE_STRING),
                'domain_expire' => $request->request->filter('domain_expire', '', FILTER_SANITIZE_STRING),
                'max_users'     => $request->request->filter('max_users', '', FILTER_SANITIZE_NUMBER_INT),
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
            $data['settings']['BD_DATABASE'] = $instanceManager
                ->checkInternalName($data['settings']['BD_DATABASE']);

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
            'BD_DATABASE'   => filter_var($settingsRAW['BD_DATABASE'], FILTER_SANITIZE_STRING),
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
            'main_domain'   => $request->request->filter('main_domain', '', FILTER_SANITIZE_STRING),
            'domain_expire' => $request->request->filter('domain_expire', '', FILTER_SANITIZE_STRING),
            'max_users'     => $request->request->filter('max_users', '', FILTER_SANITIZE_NUMBER_INT),
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
            'activated_modules',
            'contact_IP',
            'contact_mail',
            'contact_name',
            'domain_expire',
            'last_invoice',
            'max_mailing',
            'max_users',
            'pass_level',
            'piwik',
            'site_created',
            'site_language',
            'site_name',
            'time_zone',
        );

        $instanceManager = getService('instance_manager');

        //TODO: PROVISIONAL WHILE DONT DELETE $GLOBALS['application']->conn // is used in settings set
        $GLOBALS['application']->conn = $instanceManager->getConnection($settings);

        // Update instance data
        $errors = $instanceManager->update($data);

        // Update instance configurations
        foreach ($request->request->all() as $key => $value) {
            if (in_array($key, $configurationsKeys)) {
                // Delete from cache service for this instance
                s::invalidate($key, $internalName);
                s::set($key, $value);
            }
        }

        // Delete the 'activated_modules' from cache service for this instance
        s::invalidate('activated_modules', $data['internal_name']);
        s::invalidate('site_name', $data['internal_name']);
        s::invalidate('last_invoice', $data['internal_name']);

        if (is_array($errors) && count($errors) > 0) {
            m::add($errors, m::ERROR);
        } else {
            m::add('Instance saved successfully.', m::SUCCESS);
        }

        return $this->redirect(
            $this->generateUrl('manager_instance_show', array('id' => $id))
        );
    }
}
