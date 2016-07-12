<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\Controller;

use Onm\Framework\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

/**
 * Displays a page when maintenance mode is enabled
 */
class MaintenanceController extends Controller
{
    /**
     * Shows the maintenance mode page.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     */
    public function defaultAction(Request $request)
    {
        $preferedLanguage   = $request->getPreferredLanguage();
        $availableLanguages = $this->getParameter('available_languages');
        $locale             = '';

        foreach (array_keys($availableLanguages) as $lang) {
            if (strpos($lang, $preferedLanguage) === 0) {
                $locale = $lang.'.UTF-8';
                break;
            }
        }

        $localeDir = realpath(APP_PATH . '/Resources/locale/');
        $domain    = 'messages';

        putenv("LC_MESSAGES=$locale");
        setlocale(LC_ALL, $locale);
        bindtextdomain($domain, $localeDir);
        textdomain($domain);

        $this->view = $this->get('core.template.admin');

        $themes = $this->get('orm.loader')->getPlugins();
        $themes = array_filter($themes, function ($a) {
            return $a->uuid === 'es.openhost.theme.admin';
        });

        $this->view->addActiveTheme($themes[0]);

        return new Response($this->renderView('maintenance/index.tpl'), 503);
    }
}
