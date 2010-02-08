<?php
//error_reporting(E_ALL);
require('config.inc.php');
require_once('core/application.class.php');

Application::import_libs('*');
$app = Application::load();

require_once('core/content_manager.class.php');
require_once('core/content.class.php');
require_once('core/content_category_manager.class.php');
require_once('core/advertisement.class.php');

if(isset($_REQUEST['action']) ) {
    switch($_REQUEST['action']) {
        case 'get':                                    
            // Banner Id
            $id = $_GET['id']; // pk_advertisement
            
            $advertisement = new Advertisement();
            /* $banner = $advertisement->cache->read($id); */
            $banner = $advertisement->read($id);
            
            // Assign to template
            $tpl = new Template(TEMPLATE_USER);
            $tpl->assign('banner', $banner);            
            $tpl->display('advertisement.tpl');
            
            exit(0); // Prevent future errors
        break;
        
        case 'show': // Redirect to advertisement
            $advertisement = new Advertisement($_GET['publi_id']);
            $url = $advertisement->get_url($_GET['publi_id']);
            
            if($url){
                $advertisement->set_numclic($_GET['publi_id']);
                
                // Application::forward( $url);
                header("Location: $url");    
            } else {
                // Deshabilitado a petición de Xornal.com
                /* echo '<script type="text/javascript">
                    alert(\'No disponible\');
                    window.close();
                </script>'; */
                echo '<script type="text/javascript">
                    window.close();
                </script>';
            }
        break;
    
        default:
            // NADA
        break;
    }
}

