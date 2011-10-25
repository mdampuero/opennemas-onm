<?php
/**
 * Setup app
*/
require_once(dirname(__FILE__).'/../../../bootstrap.php');

use \Onm\Instance\InstanceManager as im,
    \Onm\Module\ModuleManager as ModuleManager,
    \Onm\Message as m;

/**
 * Setup view
*/
$tpl = new \TemplateManager(TEMPLATE_ADMIN);
$im = im::getInstance();

session_start();

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
            'defaultDatabaseAuth' => $onmInstancesConnection,
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
        
        if(isset($_POST['settings']) && !empty($_POST['settings']) ) {
            $settings = $_POST['settings'];
        } else {
            $settings = array(
                'TEMPLATE_USER' => "default",
                'MEDIA_URL' => "",
                'BD_TYPE' => "mysqli",
                'BD_HOST' => "localhost",
                'BD_USER' => "opennemas",
                'BD_PASS' => "12OpenNeMaS34",
                'BD_DATABASE' => "onm-".$_POST['internal_name'],
            );
        }
        $data = array(
            'id' => filter_input(INPUT_POST, 'id' , FILTER_SANITIZE_STRING),
            'contact_IP' => filter_input(INPUT_POST, 'contact_IP' , FILTER_SANITIZE_STRING),
            'name' => filter_input(INPUT_POST, 'site_name' , FILTER_SANITIZE_STRING),
            'internal_name' => filter_input(INPUT_POST, 'internal_name' , FILTER_SANITIZE_STRING),
            'domains' => filter_input(INPUT_POST, 'domains' , FILTER_SANITIZE_STRING),
            'activated' => filter_input(INPUT_POST, 'activated' , FILTER_SANITIZE_NUMBER_INT),
            'settings' => $settings,
        );
        $errors = array();
        
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
                                    
            foreach ($configurationsKeys as $key) {
                if(!isset($_POST[$key])){
                    $_POST[$key]= ucfirst($key);
                }
            }
            $errors = $im->update($data);
            if (is_array($errors) && count($errors) > 0) {
                m::add($errors);
                Application::forward('?action=edit&id='.$data['id']);
            }
        } else {
            $errors = $im->create($data);
            $site_created = filter_input(INPUT_POST, 'site_created' , FILTER_DEFAULT);
            
            if (empty($site_created)){
                $_POST['created'] = time();
            }
            if (is_array($errors) && count($errors) > 0) {
                m::add($errors);
                Application::forward('?action=new');
            }
        }
                
        //TODO: PROVISIONAL WHILE DONT DELETE $GLOBALS['application']->conn // is used in settings set
        $GLOBALS['application']->conn = $im->getConnection( $settings );

        foreach ($_POST as $key => $value ) {
            if(in_array($key, $configurationsKeys)) {
                Onm\Settings::set($key, $value);
            }
        }

        m::add('Instance saved successfully.');
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
        
        $instances = $im->findAll();

        foreach($instances as &$instance) {
             list($instance->totals, $instance->configs) =
                     $im->getDBInformation($instance->settings);
            
            $instance->domains = preg_split("@, @", $instance->domains);

        }

        $_SESSION['desde'] = 'instances';

        $tpl->assign('instances', $instances);
        $tpl->display('instances/list.tpl');
        break;
}
