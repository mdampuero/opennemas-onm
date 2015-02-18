<?php
/**
 * Handles the actions for the system information
 *
 * @package Backend_Controllers
 **/
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Backend\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Backend\Annotation\CheckModuleAccess;
use Onm\Framework\Controller\Controller;
use Onm\Settings as s;

/**
 * Handles the actions for the system information
 *
 * @package Backend_Controllers
 **/
class CacheManagerController extends Controller
{
    /**
     * Lists cache files and perform searches across them
     *
     * @param Request $request the request object
     *
     * @return string the string response
     *
     * @Security("has_role('CACHE_TPL_ADMIN')")
     *
     * @CheckModuleAccess(module="CACHE_MANAGER")
     **/
    public function defaultAction(Request $request)
    {
        return $this->render('tpl_manager/tpl_manager.tpl');
    }

    /**
     * Deletes all the frontend cache files
     * DANGER: this action has really CPU expensive
     *
     * @return string the result string
     *
     * @Security("has_role('CACHE_TPL_ADMIN')")
     *
     * @CheckModuleAccess(module="CACHE_MANAGER")
     **/
    public function deleteAllAction()
    {
        // Initialization of the frontend template object
        $frontpageTemplate = new \Template(TEMPLATE_USER);
        $frontpageTemplate->clearAllCache();

        return $this->redirect($this->generateUrl('admin_tpl_manager'));
    }

    /**
     * Show the configuration form and stores it information
     *
     * @param Request $request the request object
     *
     * @return string the string response
     *
     * @Security("has_role('CACHE_TPL_ADMIN')")
     *
     * @CheckModuleAccess(module="CACHE_MANAGER")
     **/
    public function configAction(Request $request)
    {
        // Initialization of the frontend template object
        $frontpageTemplate = new \Template(TEMPLATE_USER);

        // Initialization of the template cache manager
        $this->cacheManager = $this->get('template_cache_manager');
        $this->cacheManager->setSmarty($frontpageTemplate);

        $configDir     = $frontpageTemplate ->config_dir[0];
        $configManager = $this->container->get('template_cache_config_manager')->setConfigDir(
            $configDir
        );

        if ($this->request->getMethod() == 'POST') {
            $config = array();
            $cacheGroups             = $request->request->get('groups');
            $cacheGroupsCacheEnabled = $request->request->get('enabled');
            $cacheGroupsLifeTime     = $request->request->get('cache_lifetime');

            foreach ($cacheGroups as $section) {
                $caching          = (isset($cacheGroupsCacheEnabled[$section]))? 1: 0;
                $cache_lifetime   = intval($cacheGroupsLifeTime[$section]);

                $config[$section] = array(
                    'caching'        => $caching,
                    'cache_lifetime' => $cache_lifetime,
                );
            }
            $saved = $configManager->save($config);

            if ($saved) {
                $this->get('session')->getFlashBag()->add(
                    'success',
                    _('Cache configuration saved successfully.')
                );
            } else {
                $this->get('session')->getFlashBag()->add(
                    'error',
                    _('Unable to save the cache configuration.')
                );
            }

            return $this->redirect($this->generateUrl('admin_tpl_manager_config'));
        } else {
            $config = $configManager->load();

            return $this->render(
                'tpl_manager/config.tpl',
                array(
                    'config'    => $config,
                    'groupName' => array(
                        'frontpages'        => _('Frontpage'),
                        'frontpage-mobile'  => _('Frontpage mobile version'),
                        'articles'          => _('Inner Article'),
                        'articles-mobile'   => _('Inner Article mobile version'),
                        'opinion'           => _('Inner Opinion'),
                        'rss'               => _('RSS'),
                        'sitemap'           => _('Sitemap'),
                        'video'             => _('Frontpage videos'),
                        'video-inner'       => _('Inner video'),
                        'gallery-frontpage' => _('Gallery frontpage'),
                        'gallery-inner'     => _('Gallery Inner'),
                        'poll-frontpage'    => _('Polls frontpage'),
                        'poll-inner'        => _('Poll inner'),
                    ),
                    'groupIcon' => array(
                        'frontpages'        => 'frontpage.png',
                        'frontpage-mobile'  => 'mobile.png',
                        'articles'          => 'article.png',
                        'articles-mobile'   => 'mobile.png',
                        'opinion'           => 'opinion.png',
                        'rss'               => 'rss.png',
                        'video'             => 'video.png',
                        'video-inner'       => 'video.png',
                        'gallery-frontpage' => 'album.png',
                        'gallery-inner'     => 'album.png',
                        'poll-frontpage'    => 'polls.png',
                        'poll-inner'        => 'polls.png',
                        'sitemap'           => 'sitemap.png',
                    ),
                )
            );
        }
    }

    /**
     * Builds the search filter for listing the listing cache action
     *
     * @param Request $request the request object
     *
     * @return array
     **/
    private function buildFilter($request)
    {
        $section      = $request->query->get('section', null);
        $type         = $request->query->get('type', null);
        $page         = $request->query->get('page', 1);
        $itemsPerPage = $request->query->get('items_page', 15);
        if (empty($itemsPerPage)) {
            $itemsPerPage = 15;
        }

        // If section is defined include it in filter and params
        $filter = '';
        $params = array();
        if (isset($section) && !empty($section)) {
            $filter  .= '^'.preg_quote($section).'\^.*?';
            $params[] = 'section='.$section;
        }

        // If cache file type is defined include it in filter and params
        if (isset($type) && !empty($type)) {
            $regexp = array(
                'frontpages'         => 'frontpage\.tpl\.php$',
                'opinions'           => 'opinion\.tpl\.php$',
                'frontpage-opinions' => 'opinion_author_index\.tpl\.php$',
                'articles'           => 'article\.tpl\.php$',
                'rss'                => '\^RSS[0-9]*\^',
                'mobilepages'        => 'frontpage-mobile\.tpl\.php$',
                'poll'               => 'poll\.tpl\.php$',
                'video-frontpage'    => 'video_frontpage\.tpl\.php$',
                'video-inner'        => 'video_inner\.tpl\.php$',
                'gallery-frontpage'  => 'album_frontpage\.tpl\.php$',
                'gallery-inner'      => 'album\.tpl\.php$',
                'poll-frontpage'     => 'poll_frontpage\.tpl\.php$',
                'poll-inner'         => 'poll.tpl\.php$',
            );
            $filter  .= $regexp[ $_REQUEST['type'] ];
            $params[] = 'type='.$_REQUEST['type'];
        }

        // If page is defined include it in params and page
        if (!empty($page)) {
            $params[] = 'page='.$page;
        }

        $params[] = 'items_page='.$itemsPerPage;
        if (!empty($filter)) {
            $filter = '@'.$filter.'@';
        }

        // return $filter and URI $params
        return array($filter, implode('&', $params), $page, $itemsPerPage);
    }
}
