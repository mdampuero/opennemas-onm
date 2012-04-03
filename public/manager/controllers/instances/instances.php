<?php
/**
 * Setup app
*/
require_once(dirname(__FILE__).'/../../../bootstrap.php');

use \Onm\Instance\InstanceManager as im,
    \Onm\Module\ModuleManager as ModuleManager,
    \Onm\Message as m,
    \Onm\Settings as s;

/**
 * Setup view
*/
$tpl = new \TemplateManager(TEMPLATE_ADMIN);
$im = im::getInstance();

session_start();

// Initialize request parameters
$page   = filter_input( INPUT_GET, 'page' , FILTER_SANITIZE_NUMBER_INT, array('options' => array('default' => '1')) );
// Widget instance
$action = (isset($_REQUEST['action']))? $_REQUEST['action']: null;


switch($action) {

    case 'edit':

        $templates = im::getAvailableTemplates();
        $tpl->assign('templates', $templates);

        $id = $_REQUEST['id'];
        $instance = $im->read($id);

        list($instance->totals, $instance->configs) = $im->getDBInformation($instance->settings);

        $tpl->assign( 'instance', $instance);

        $tpl->assign(
             array(
                    'configs' => $instance->configs,
                    'available_modules' => ModuleManager::getAvailableModules(),
                    'timezones' => \DateTimeZone::listIdentifiers(),
                    'languages' => array('en_US' => _("English"), 'es_ES' => _("Spanish"), 'gl_ES' => _("Galician")),
                    'logLevels' => array('normal' => _('Normal'), 'verbose' => _('Verbose'), 'all' => _('All (Paranoic mode)') ),
                )
            );

        $tpl->display('instances/edit.tpl');
        break;

    case 'new':

        $templates = im::getAvailableTemplates();

        $tpl->assign(array(
            'templates' => $templates,
            //'defaultDatabaseAuth' => $onmInstancesConnection,
        ));

        $tpl->assign(
         array(
                'configs' => array( 'activated_modules' => ModuleManager::getAvailableModules()),
                'available_modules' => ModuleManager::getAvailableModules(),
                'timezones' => \DateTimeZone::listIdentifiers(),
                'languages' => array('en_US' => _("English"), 'es_ES' => _("Spanish"), 'gl_ES' => _("Galician")),
                'logLevels' => array('normal' => _('Normal'), 'verbose' => _('Verbose'), 'all' => _('All (Paranoic mode)') ),
            )
        );

        $tpl->display('instances/edit.tpl');
        break;

    case 'delete':

        $id = $_REQUEST['id'];
        $deletion = $im->delete($id);

        Application::forward('?action=list');
        break;

    case 'save':
        //Get internal_name from domains
        $internalName = "";
        if (isset($_POST['internal_name']) && !empty($_POST['internal_name'])) {
            $internalName = $_POST['internal_name'];
        } else {
            $internal = explode(".", filter_input(INPUT_POST, 'domains' , FILTER_SANITIZE_STRING) );
            $internalName = $internal[0];
        }
        //Force internal_name lowercase
        $internalName = strtolower($internalName);

        //If is creating a new instance, get DB params on the fly
        $actionName = filter_input(INPUT_POST, 'action_name' , FILTER_SANITIZE_STRING);
        $internalNameShort = trim(substr($internalName, 0, 13));
        $settings = "";
        if($actionName == "edit") {
            $settings = $_POST['settings'];
        } else {
            $password = StringUtils::generatePassword(16);
            $settings = array(
                'TEMPLATE_USER' => "retrincos",
                'MEDIA_URL' => "http://media.opennemas.com",
                'BD_TYPE' => "mysqli",
                'BD_HOST' => "localhost",
                'BD_USER' => $internalNameShort,
                'BD_PASS' => $password,
                'BD_DATABASE' => $internalNameShort,
            );
        }

        //Get all the Post data
        $data = array(
            'id' => filter_input(INPUT_POST, 'id' , FILTER_SANITIZE_STRING),
            'contact_IP' => filter_input(INPUT_POST, 'contact_IP' , FILTER_SANITIZE_STRING),
            'name' => filter_input(INPUT_POST, 'site_name' , FILTER_SANITIZE_STRING),
            'user_name' => filter_input(INPUT_POST, 'contact_name' , FILTER_SANITIZE_STRING),
            'user_mail' => filter_input(INPUT_POST, 'contact_mail' , FILTER_SANITIZE_STRING),
            'user_pass' => filter_input(INPUT_POST, 'password' , FILTER_SANITIZE_STRING),
            'internal_name' => $internalName,
            'domains' => filter_input(INPUT_POST, 'domains' , FILTER_SANITIZE_STRING),
            'activated' => filter_input(INPUT_POST, 'activated' , FILTER_SANITIZE_NUMBER_INT),
            'settings' => $settings,
            'site_created' => filter_input(INPUT_POST, 'site_created' , FILTER_SANITIZE_STRING,
                                        array('options' => array('default' => date("d-m-Y - H:m"))))
        );

        //Also get timezone if comes from openhost form
        if (isset($_POST['timezone']) && !empty ($_POST['timezone'])) {
            $allTimezones = \DateTimeZone::listIdentifiers();
            foreach ($allTimezones as $key => $value) {
                if ($_POST['timezone'] == $value) {
                    $data['timezone'] = $key;
                }
            }
        }

        $errors = array();
        // Check for reapeted internalnameshort and if so , add a number at the end
        $data = $im->checkInternalShortName($data);

        if (intval($data['id']) > 0) {
            $configurationsKeys = array(
                    'site_title', 'site_description','site_keywords',
                    'site_agency','site_name','site_created',
                    'contact_mail','contact_name','contact_IP',
                    'time_zone','site_language','mail_server',
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

            //Delete the 'activated_modules' apc_cache for this instance
            s::invalidate('activated_modules', $data['internal_name']);
            //Delete the 'site_name' apc_cache for this instance
            s::invalidate('site_name', $data['internal_name']);

            //TODO: PROVISIONAL WHILE DONT DELETE $GLOBALS['application']->conn // is used in settings set
            $GLOBALS['application']->conn = $im->getConnection( $settings );

            foreach ($_POST as $key => $value ) {
                if(in_array($key, $configurationsKeys)) {
                   s::set($key, $value);
                }
            }
            $errors = $im->update($data);
            if (is_array($errors) && count($errors) > 0) {
                m::add($errors);
                Application::forward('?action=edit&id='.$data['id']);
            }
        } else {
            $errors = $im->create($data);

            if (is_array($errors) && count($errors) > 0) {
                m::add($errors);
                Application::forward('?action=new');
            }
        }

        if ($errors){
            m::add('Instance saved successfully.');
        }
        Application::forward('?action=list');

        break;

    case 'changeactivated':

        $instance = $im->read($_REQUEST['id']);

        $available = ($instance->activated+1) % 2;
        $im->changeActivated($instance->id, $available);

        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
            list($img, $text)  = ($available)? array('g', 'PUBLICADO'): array('r', 'PENDIENTE');

            echo '<img src="' . $tpl->image_dir . 'publish_' . $img . '.png" border="0" title="' . $text . '" />';
            exit(0);
        }

        Application::forward($_SERVER['PHP_SELF'].'?action=list');
        break;

    case 'list':
    default:

        // QUIRK MODE: If I don't star the session Onm\Message doesn't show
        // available messages
        // session_start();

        $find_params = array(
            'name' => filter_input(
                INPUT_GET, 'filter_name', FILTER_SANITIZE_STRING,
                array('options' => array('default' => '*'))
            ),
            'per_page' => filter_input(
                INPUT_GET, 'filter_per_page', FILTER_SANITIZE_STRING,
                array('options' => array('default' => '20'))
            ),
        );

        $instances = $im->findAll($find_params);

        foreach($instances as &$instance) {
             list($instance->totals, $instance->configs) =
                     $im->getDBInformation($instance->settings);

            $instance->domains = preg_split("@, @", $instance->domains);

        }

        $items_page =  $find_params['per_page'];

        // Pager
        $pager_options = array(
            'mode'        => 'Sliding',
            'perPage'     => $items_page,
            'delta'       => 4,
            'clearIfVoid' => true,
            'urlVar'      => 'page',
            'totalItems'  => count($instances),
        );
        $pager = Pager::factory($pager_options);

        $instances = array_slice($instances, ($page-1)*$items_page, $items_page);

        $tpl->assign(
            array(
                'instances'      => $instances,
                'per_page'      => $items_page,
                'pagination'    =>  $pager,
            )
        );

        $_SESSION['desde'] = 'instances';
        $tpl->display('instances/list.tpl');
        break;
}
