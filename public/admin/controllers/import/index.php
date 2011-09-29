<?php
//error_reporting(E_ALL);
require_once('../../../bootstrap.php');
require_once('../../session_bootstrap.php');

$tpl = new \TemplateAdmin(TEMPLATE_ADMIN);

/**
 * Fetch request variables
*/
$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING,
                       array('options' => array('default' => 'list')));
$page   = filter_input(INPUT_GET, 'page', FILTER_SANITIZE_STRING,
                       array('options' => array('default' => 0)));


switch($action) {

    case 'list':

        $config = array(
                        'server' => 'FTP1.europapress.es',
                        'user' => 'NVTRIBUNA',
                        'password' => 'tribNaEP574',
                        );
        
        $agencyImporter = Onm_Import_Europapress::getInstance($config);
    
        $agencyImporter->findAll();
        

    break;

    default: {
        Application::forward($_SERVER['SCRIPT_NAME'].'?action=list');
    } break;
    
} //switch

$tpl->display('importer.tpl');
