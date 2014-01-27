<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Framework\Controller;

use Onm\Framework\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

/**
 * Handles all the request for Maintenance mode actions
 *
 * @package Framework_Controllers
 **/
class MaintenanceController extends Controller
{
    /**
     * Shows the maintenance mode page
     *
     * @return string the response string
     **/
    public function defaultAction(Request $request)
    {
        $preferedLanguage = $request->getPreferredLanguage();

        global $kernel;
        $availableLanguages = $kernel->getContainer()->getParameter('available_languages');

        $locale = '';
        foreach ($availableLanguages as $lang => $name) {
            if (strpos($lang, $preferedLanguage) === 0) {
                $locale = $lang.'.UTF-8';
                break;
            }
        }

        $localeDir = realpath(APP_PATH.'/Resources/locale/');
        $domain = 'messages';

        putenv("LC_MESSAGES=$locale");
        setlocale(LC_ALL, $locale);
        bindtextdomain($domain, $localeDir);
        textdomain($domain);

        $this->view = new \TemplateAdmin();
        $output = $this->renderView('maintenance/index.tpl');

        return new Response($output, 503);
    }
}
