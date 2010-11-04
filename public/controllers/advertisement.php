<?php

/**
 * Start up and setup the app
*/
require_once('../bootstrap.php');

if (isset($_REQUEST['action'])) {
    
    switch ($_REQUEST['action']) {
        
        case 'get':
            // Banner Id
            $id = $_GET['id']; // pk_advertisement
            $advertisement = new Advertisement();
            /* $banner = $advertisement->cache->read($id); */
            $banner = $advertisement->read($id);
            // Assign to template
            $tpl = new Template(TEMPLATE_USER);
            $tpl->assign('banner', $banner);
            $tpl->display('ads/advertisement.tpl');
            exit(0); // Prevent future errors
            
        break;
    
        case 'show': // Redirect to advertisement
            $advertisement = new Advertisement($_GET['publi_id']);
            $url = $advertisement->getUrl($_GET['publi_id']);
            if ($url) {
                $advertisement->setNumClics($_GET['publi_id']);
                // Application::forward( $url);
                header("Location: $url");
            } else {
                // Deshabilitado a petición de Xornal.com
                /* echo '<script type="text/javascript">
                    alert(\'No disponible\');
                    window.close();
                </script>'; */
                echo '<script type="text/javascript">window.close();</script>';
            }
        break;
    
        default:
            // EMPTY ACTION
        break;
    }
}
