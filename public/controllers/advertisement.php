<?php

/**
 * Start up and setup the app
*/
require_once('../bootstrap.php');

if (isset($_REQUEST['action'])) {

    switch ($_REQUEST['action']) {

        case 'get':
            // Banner Id
            $id = $request->query->filter('id', null , FILTER_SANITIZE_STRING);

            if(isset($id)) {
                $advertisement = new Advertisement();
                /* $banner = $advertisement->cache->read($id); */
                $advertisement->setNumClics($id);
                $banner = $advertisement->read($id);
                // Assign to template
                $tpl = new Template(TEMPLATE_USER);
                $tpl->assign('banner', $banner);
                $tpl->display('ads/advertisement.tpl');
            }
            exit(0); // Prevent future errors

        break;

        case 'show': // Redirect to advertisement
            $publi_id = $request->query->filter('publi_id', null , FILTER_SANITIZE_STRING);
            if (isset($publi_id)) {

                $advertisement = new Advertisement($publi_id);
                $url = $advertisement->getUrl($publi_id);
                if ($url) {
                    var_dump($url);
                    $advertisement->setNumClics($publi_id);
                    // Application::forward( $url);
                    header("Location: $url");
                } else {
                    $advertisement->setNumClics($publi_id);
                    echo '<script type="text/javascript">window.close();</script>';
                }
            }
        break;

        default:
            // EMPTY ACTION
        break;
    }
}
