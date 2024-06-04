<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Api\Controller\V1\Backend;

use Api\Controller\V1\ApiController;
use Symfony\Component\HttpFoundation\Request;

/**
 * Displays, saves, modifies and removes menus.
 */
class MenuController extends ApiController
{
    /**
     * {@inheritdoc}
     */
    protected $extension = 'MENU_MANAGER';

    /**
     * {@inheritdoc}
     */
    protected $getItemRoute = 'api_v1_backend_menu_get_item';

    /**
     * {@inheritdoc}
     */
    protected $permissions = [
        'create' => 'MENU_CREATE',
        'delete' => 'MENU_DELETE',
        'list'   => 'MENU_ADMIN',
        'patch'  => 'MENU_UPDATE',
        'save'   => 'MENU_CREATE',
        'show'   => 'MENU_UPDATE',
        'update' => 'MENU_UPDATE',
    ];

    protected $modulePages = [
        [
            'module' => 'OPINION_MANAGER',
            'title' => 'Opinions',
            'route' => 'frontend_opinion_frontpage'
        ],
        [
            'module' => 'BLOG_MANAGER',
            'title' => 'Blogs',
            'route' => 'frontend_blog_frontpage'
        ],
        [
            'module' => 'ALBUM_MANAGER',
            'title' => 'Albums',
            'route' => 'frontend_album_frontpage'
        ],
        [
            'module' => 'VIDEO_MANAGER',
            'title' => 'Videos',
            'route' => 'frontend_video_frontpage'
        ],
        [
            'module' => 'POLL_MANAGER',
            'title' => 'Polls',
            'route' => 'frontend_poll_frontpage'
        ],
        [
            'module' => 'es.openhost.module.events',
            'title' => 'Events',
            'route' => 'frontend_events'
        ],
        [
            'module' => 'es.openhost.module.companies',
            'title' => 'Companies',
            'route' => 'frontend_companies'
        ],
        [
            'module' => 'es.openhost.module.obituaries',
            'title' => 'Obituaries',
            'route' => 'frontend_obituaries'
        ],
        [
            'module' => 'LETTER_MANAGER',
            'title' => 'Letters to the Editor',
            'route' => 'frontend_letter_frontpage'
        ],
        [
            'module' => 'KIOSKO_MANAGER',
            'title' => 'News Stand',
            'route' => 'frontend_newsstand_frontpage'
        ],
        [
            'module' => 'FORM_MANAGER',
            'title' => 'Form',
            'route' => 'frontend_participa_frontpage'
        ],
        [
            'module' => 'NEWSLETTER_MANAGER',
            'title' => 'Newsletter',
            'route' => 'frontend_newsletter_subscribe_show'
        ],
        [
            'module' => 'LIBRARY_MANAGER',
            'title' => 'Archive',
            'route' => 'frontend_archive',
            'params' => [ 'component' => 'content' ]
        ],
    ];

    /**
     * {@inheritdoc}
     */
    protected $service = 'api.service.menu';

    /**
     * {@inheritdoc}
     */
    protected function getExtraData()
    {
        $params = [
            'category'         => $this->getCategories(),
            'locale'           => $this->get('core.helper.locale')->getConfiguration(),
            'menu_positions'   => $this->getMenuPositions(),
            'internal'         => $this->getModulePages(),
            'static'           => $this->getStaticPages(),
            'syncBlogCategory' => $this->getSyncSites(),
            'tags'             => $this->getTagsByOQL(),
            'keys'             => $this->getL10nKeys(),
            'multilanguage'    => in_array(
                'es.openhost.module.multilanguage',
                $this->get('core.instance')->activated_modules
            )
        ];

        return $params;
    }

    private function getTagsByOQL()
    {
        $oql = '';

        try {
            $response = $this->get('api.service.tag')->getList($oql);

            return array_map(function ($a) {
                return [
                    'title' => $a->name,
                    'slug'  => $a->slug,
                    'locale' => $a->locale,
                    'id'    => $a->id,
                ];
            }, $response['items']);
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Returns a list of static pages and their slugs
     *
     * @return array the list of static pages
     */
    private function getStaticPages()
    {
        $context = $this->get('core.locale')->getContext();
        $this->get('core.locale')->setContext('frontend');

        $oql = 'content_type_name = "static_page" and in_litter = 0 and content_status = 1 '
           . ' order by created desc';

        $response = $this->get('api.service.content')->getListWithoutLocalizer($oql);
        $this->get('core.locale')->setContext($context);

        return array_map(function ($a) {
            return [
                'title'      => $a->title,
                'slug'       => $a->slug,
                'pk_content' => $a->pk_content
            ];
        }, $response['items']);
    }

    /**
     * Returns the list of synchronized sites.
     *
     * @return array The list of synchronized sites.
     */
    private function getSyncSites()
    {
        $syncSites = $this->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('sync_params');

        if (empty($syncSites)) {
            return [];
        }

        return $syncSites;
    }

    protected function getCategories($items = null)
    {
        $context = $this->get('core.locale')->getContext();
        $this->get('core.locale')->setContext('frontend');

        $oql = 'visible = 1 and enabled = 1'
        . ' order by title asc';

        $categories = $this->get('api.service.category')->getListWithoutLocalizer($oql);
        $this->get('core.locale')->setContext($context);

        return $this->get('api.service.category')
            ->responsify($categories['items']);
    }

    /**
     * Returns the list of menu positions
     *
     * @return array the list of menu positions
     */
    private function getMenuPositions()
    {
        $avaliableMenus = $this->get('core.theme')->getMenus();
        $menuPositions  = [
            '' => _('Without position')
        ];
        foreach ($avaliableMenus as $menuKey => $menuValue) {
            $menuPositions[$menuKey] = _($menuValue['name']);
        }
        return $menuPositions;
    }

     /**
     * Returns the list of l10n keys.
     *
     * @return array The list of l10n keys.
     */
    protected function getL10nKeys()
    {
        return $this->get($this->service)->getL10nKeys();
    }

    /**
     * Returns a list of activated module pages
     *
     * @return array the list of module pages
     */
    private function getModulePages()
    {
        $default = [ ['title' => _("Frontpage"),'link' => "/"] ];
        $modules = array_filter(array_map(function ($page) {
            if (!$this->get('core.security')->hasExtension($page['module'])) {
                return null;
            }

            $link = array_key_exists('params', $page) ?
                ltrim($this->get('router')->generate($page['route'], $page['params']), '/') :
                ltrim($this->get('router')->generate($page['route']), '/');

            return [ 'title' => _($page['title']), 'link'  => $link ];
        }, $this->modulePages));

        return array_merge($default, $modules);
    }

    protected function getItemId($item)
    {
        return $item->pk_menu;
    }
}
