<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
//Start up and setup the app
require_once '../bootstrap.php';

$action = $request->query->filter('action', null, FILTER_SANITIZE_STRING);

switch ($action) {

    case 'get':
        // Banner Id
        $id = $request->query->filter('id', null, FILTER_SANITIZE_STRING);

        if (isset($id)) {
            $advertisement = new Advertisement();
            /* $banner = $advertisement->cache->read($id); */
            $advertisement->setNumClics($id);
            $banner = $advertisement->read($id);
            // Assign to template
            $tpl = new Template(TEMPLATE_USER);
            $tpl->assign('banner', $banner);
            $tpl->display('ads/advertisement.tpl');
        }
        break;

    case 'show': // Redirect to advertisement
        $publi_id = $request->query->filter('publi_id', null, FILTER_SANITIZE_STRING);
        if (isset($publi_id)) {
            $advertisement = new Advertisement($publi_id);
            $url = $advertisement->getUrl($publi_id);
            if ($url) {
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
