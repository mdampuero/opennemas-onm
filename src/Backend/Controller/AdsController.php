<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Backend\Controller;

use Common\Core\Annotation\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Common\Core\Controller\Controller;

/**
 * Handles the actions for managing ads
 *
 * @package Backend_Controllers
 */
class AdsController extends Controller
{
    /**
     * Common code for all the actions.
     */
    public function init()
    {
        $contentType = \ContentManager::getContentTypeIdFromName('advertisement');

        // Sometimes category is array. When create & update advertisement
        $this->category = $this->get('request_stack')->getCurrentRequest()
            ->query->getDigits('category', 0);

        // Fetch categories to all internal categories
        $contentTypes = [$contentType, 7, 9, 11, 14];
        $ccm          = \ContentCategoryManager::get_instance();

        list($this->parentCategories, $this->subcat, $this->categoryData) =
            $ccm->getArraysMenu($this->category, $contentTypes);

        $this->view->assign([
            'subcat'       => $this->subcat,
            'allcategorys' => $this->parentCategories,
            'datos_cat'    => $this->categoryData,
            'category'     => $this->category,
        ]);
    }

    /**
     * Lists all the available ads.
     *
     * @return Response The response object.
     *
     * @Security("hasExtension('ADS_MANAGER')
     *     and hasPermission('ADVERTISEMENT_ADMIN')")
     */
    public function listAction()
    {
        // Get ads positions
        $positionManager = $this->get('core.helper.advertisement');
        $map             = $positionManager->getPositions();
        $adsNames        = $positionManager->getPositionNames();

        $typeAdvertisement = [ [ 'name' => _("All"), 'value' => -1 ] ];

        foreach ($adsNames as $key => $value) {
            $typeAdvertisement[] = [ 'name' => $value, 'value' => $key];
        }

        $types = [
            [ 'name' => _("All"), 'value' => -1 ],
            [ 'name' => _("Multimedia"), 'value' => 0 ],
            [ 'name' => _("Javascript"), 'value' => 1 ],
            [ 'name' => _("OpenX"), 'value' => 2 ],
            [ 'name' => _("Google DFP"), 'value' => 3 ]
        ];

        $categories = [
            [ 'name' => _('All'), 'value' => -1 ],
            [ 'name' => _('HOMEPAGE'), 'value' => 0, 'group' => _('Special elements') ],
            [ 'name' => _('OPINION'), 'value' => 4, 'group' => _('Special elements') ],
            [ 'name' => _('ALBUM'), 'value' => 3, 'group' => _('Special elements') ],
            [ 'name' => _('VIDEO'), 'value' => 6, 'group' => _('Special elements') ]
        ];

        foreach ($this->parentCategories as $key => $category) {
            $categories[] = [
                'name' => $category->title,
                'value' => $category->id,
                'group' => _('Categories')
            ];

            foreach ($this->subcat[$key] as $subcategory) {
                $categories[] = [
                    'name' => '&rarr; ' . $subcategory->title,
                    'value' => $subcategory->id,
                    'group' => _('Categories')
                ];
            }
        }

        return $this->render(
            'advertisement/list.tpl',
            [
                'categories'        => $categories,
                'typeAdvertisement' => $typeAdvertisement,
                'types'             => $types,
                'map'               => json_encode($map)
            ]
        );
    }

    /**
     * Handles the form for create a new ad.
     *
     * @param  Request  $request The request object.
     *
     * @return Response The response object.
     *
     * @Security("hasExtension('ADS_MANAGER')
     *     and hasPermission('ADVERTISEMENT_CREATE')")
     */
    public function createAction(Request $request)
    {
        $page   = $request->request->getDigits('page', 1);
        $filter = $request->query->get('filter');

        if ('POST' !== $request->getMethod()) {
            $adsPositions = $this->container->get('core.helper.advertisement');

            $serverUrl = '';
            if ($openXsettings = $this->get('setting_repository')->get('revive_ad_server')) {
                $serverUrl = $openXsettings['url'];
            }

            $advertisement = new \Advertisement();

            $ads = $this->get('core.helper.advertisement')->getPositionsForTheme();

            return $this->render('advertisement/new.tpl', [
                'advertisement' => $advertisement,
                'ads_positions' => $adsPositions,
                'categories'    => $this->getCategories(),
                'user_groups'   => $this->getUserGroups(),
                'themeAds'      => $ads,
                'filter'        => $filter,
                'page'          => $page,
                'server_url'    => $serverUrl,
            ]);
        }

        $advertisement = new \Advertisement();
        $categories    = json_decode($request->request->get('categories', ''), true);

        if (is_array($categories) && empty($categories)) {
            $categories = null;
        }

        $data = [
            'title'              =>
                $request->request->filter('title', '', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES),
            'metadata'           =>
                \Onm\StringUtils::normalizeMetadata($request->request->filter('metadata', '', FILTER_SANITIZE_STRING)),
            'category'           => !empty($categories) ? $categories[0] : 0,
            'categories'         => is_array($categories) ? implode(',', $categories) : $categories,
            'available'          => $request->request->filter('content_status', 0, FILTER_SANITIZE_STRING),
            'content_status'     => $request->request->filter('content_status', 0, FILTER_SANITIZE_STRING),
            'with_script'        => $request->request->getDigits('with_script', 0),
            'img1'               => $request->request->filter('img1', '', FILTER_SANITIZE_STRING),
            'overlap'            => $request->request->filter('overlap', '', FILTER_SANITIZE_STRING),
            'type_medida'        => $request->request->filter('type_medida', '', FILTER_SANITIZE_STRING),
            'num_clic'           => $request->request->filter('num_clic', '', FILTER_SANITIZE_STRING),
            'num_view'           => $request->request->filter('num_view', '', FILTER_SANITIZE_STRING),
            'starttime'          => $request->request->filter('starttime', '', FILTER_SANITIZE_STRING),
            'endtime'            => $request->request->filter('endtime', '', FILTER_SANITIZE_STRING),
            'timeout'            => $request->request->filter('timeout', '', FILTER_SANITIZE_STRING),
            'url'                => $request->request->filter('url', '', FILTER_SANITIZE_STRING),
            'img'                => $request->request->filter('img', '', FILTER_SANITIZE_STRING),
            'script'             => $request->request->get('script', ''),
            'type_advertisement' => $request->request->filter('type_advertisement', '', FILTER_SANITIZE_STRING),
            'fk_author'          => $this->getUser()->id,
            'fk_publisher'       => $this->getUser()->id,
            'params'             => [
                'sizes'             => json_decode($request->request->get('sizes', ''), true),
                'openx_zone_id'     => $request->request->getDigits('openx_zone_id', ''),
                'googledfp_unit_id' => $request->request->filter('googledfp_unit_id', '', FILTER_SANITIZE_STRING),
                'user_groups'       => json_decode($request->request->get('user_groups'), true),
                'orientation'       => $request->request->get('orientation', 'horizontal'),
                'devices'           => [
                    'desktop' => (int) $request->request->get('restriction_devices_desktop', 0),
                    'tablet'  => (int) $request->request->get('restriction_devices_tablet', 0),
                    'phone'   => (int) $request->request->get('restriction_devices_phone', 0),
                ],
            ]
        ];

        $level   = 'error';
        $message = _('Unable to create the new advertisement.');

        if ($advertisement->create($data)) {
            $level   = 'success';
            $message = _('Advertisement successfully created.');
        }

        $this->get('session')->getFlashBag()->add($level, $message);
        return $this->redirect(
            $this->generateUrl(
                'admin_ad_show',
                [
                    'id'     => $advertisement->id,
                    'filter' => $filter,
                    'page'   => $page
                ]
            )
        );
    }

    /**
     * Shows the editing form for a advertisement given its id.
     *
     * @param  Request  $request The request object.
     *
     * @return Response The response object.
     *
     * @Security("hasExtension('ADS_MANAGER')
     *     and hasPermission('ADVERTISEMENT_UPDATE')")
     */
    public function showAction(Request $request)
    {
        $id     = $request->query->getDigits('id', null);
        $filter = $request->query->get('filter');
        $page   = $request->query->getDigits('page', 1);

        $adsPositions = $this->container->get('core.helper.advertisement');
        $serverUrl    = '';
        if ($openXsettings = $this->get('setting_repository')->get('revive_ad_server')) {
            $serverUrl = $openXsettings['url'];
        }

        $ad = new \Advertisement($id);
        if (is_null($ad->id)) {
            $this->get('session')->getFlashBag()->add(
                'error',
                sprintf(_('Unable to find the advertisement with the id "%d"'), $id)
            );

            return $this->redirect($this->generateUrl('admin_ads'));
        }

        if ($ad->fk_publisher != $this->getUser()->id
            && (!$this->get('core.security')->hasPermission('CONTENT_OTHER_UPDATE'))
        ) {
            $this->get('session')->getFlashBag()->add(
                'error',
                _("You can't modify this content because you don't have enough privileges.")
            );

            return $this->redirect($this->generateUrl('admin_ads'));
        }

        if (!is_array($ad->fk_content_categories) && !empty($ad->fk_content_categories)) {
            $ad->fk_content_categories = explode(',', $ad->fk_content_categories);
        }

        if (!empty($ad->img)) {
            //Buscar foto where pk_foto=img1
            $photo1 = new \Photo($ad->img);
            $this->view->assign('photo1', $photo1);
        }

        $ah = $this->container->get('core.helper.advertisement');

        return $this->render('advertisement/new.tpl', [
            'ads_positions' => $adsPositions,
            'advertisement' => $ad,
            'categories'    => $this->getCategories(),
            'filter'        => $filter,
            'page'          => $page,
            'safeFrame'     => $ah->isSafeFrameEnabled(),
            'server_url'    => $serverUrl,
            'themeAds'      => $ah->getPositionsForTheme(),
            'user_groups'   => $this->getUserGroups(),
        ]);
    }

    /**
     * Updates the advertisement information given data send by POST.
     *
     * @param  Request  $request The request object.
     *
     * @return Response The response object.
     *
     * @Security("hasExtension('ADS_MANAGER')
     *     and hasPermission('ADVERTISEMENT_UPDATE')")
     */
    public function updateAction(Request $request)
    {
        $id     = $request->query->getDigits('id');
        $filter = $request->query->get('filter');
        $page   = $request->query->getDigits('page', 1);

        $ad = new \Advertisement($id);
        if (is_null($ad->id)) {
            $this->get('session')->getFlashBag()->add(
                'error',
                sprintf(_('Unable to find the advertisement with the id "%d"'), $id)
            );

            return $this->redirect($this->generateUrl('admin_ads'));
        }

        if (!$ad->isOwner($this->getUser()->id)
            && (!$this->get('core.security')->hasPermission('CONTENT_OTHER_UPDATE'))
        ) {
            $this->get('session')->getFlashBag()->add(
                'error',
                _("You can't modify this content because you don't have enough privileges.")
            );

            return $this->redirect($this->generateUrl('admin_ads'));
        }

        $categories = json_decode($request->request->get('categories', ''), true);

        if (is_array($categories) && empty($categories)) {
            $categories = null;
        }

        $data = [
            'id'                 => $ad->id,
            'title'              =>
                $request->request->filter('title', '', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES),
            'metadata'           =>
                \Onm\StringUtils::normalizeMetadata($request->request->filter('metadata', '', FILTER_SANITIZE_STRING)),
            'category'           => !empty($categories) ? $categories[0] : 0,
            'categories'         => is_array($categories) ? implode(',', $categories) : $categories,
            'available'          => $request->request->filter('content_status', 0, FILTER_SANITIZE_STRING),
            'content_status'     => $request->request->filter('content_status', 0, FILTER_SANITIZE_STRING),
            'with_script'        => $request->request->getDigits('with_script', 0),
            'overlap'            => $request->request->filter('overlap', '', FILTER_SANITIZE_STRING),
            'type_medida'        => $request->request->filter('type_medida', '', FILTER_SANITIZE_STRING),
            'num_clic'           => $request->request->filter('num_clic', '', FILTER_SANITIZE_STRING),
            'num_view'           => $request->request->filter('num_view', '', FILTER_SANITIZE_STRING),
            'starttime'          => $request->request->filter('starttime', '', FILTER_SANITIZE_STRING),
            'endtime'            => $request->request->filter('endtime', '', FILTER_SANITIZE_STRING),
            'timeout'            => $request->request->filter('timeout', '', FILTER_SANITIZE_STRING),
            'url'                => $request->request->filter('url', '', FILTER_SANITIZE_STRING),
            'img'                => $request->request->filter('img', '', FILTER_SANITIZE_STRING),
            'script'             => $request->request->get('script', ''),
            'type_advertisement' => $request->request->filter('type_advertisement', '', FILTER_SANITIZE_STRING),
            'fk_author'          => $this->getUser()->id,
            'fk_publisher'       => $this->getUser()->id,
            'params'             => [
                'sizes'             => json_decode($request->request->get('sizes', ''), true),
                'openx_zone_id'     => $request->request->getDigits('openx_zone_id', ''),
                'googledfp_unit_id' => $request->request->filter('googledfp_unit_id', '', FILTER_SANITIZE_STRING),
                'user_groups'       => json_decode($request->request->get('user_groups', ''), true),
                'orientation'       => $request->request->get('orientation', 'horizontal'),
                'devices'           => [
                    'desktop' => (int) $request->request->get('restriction_devices_desktop', 0),
                    'tablet'  => (int) $request->request->get('restriction_devices_tablet', 0),
                    'phone'   => (int) $request->request->get('restriction_devices_phone', 0),
                ],
            ]
        ];

        if ($ad->update($data)) {
            $this->get('session')->getFlashBag()->add(
                'success',
                _('Advertisement successfully updated.')
            );
        } else {
            $this->get('session')->getFlashBag()->add(
                'error',
                _('Unable to update the advertisement data.')
            );
        }

        return $this->redirect($this->generateUrl('admin_ad_show', [
            'id'     => $data['id'],
            'filter' => $filter,
            'page'   => $page
        ]));
    }

    /**
     * Lists the available advertisements for the frontpage manager.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     *
     * @Security("hasExtension('ADS_MANAGER')")
     */
    public function contentProviderAction(Request $request)
    {
        $categoryId   = $request->query->getDigits('category', 0);
        $page         = $request->query->getDigits('page', 1);
        $itemsPerPage = 8;

        $filters = array(
            'type_advertisement' => [[ 'value' => 37 ]],
            'content_type_name'  => [[ 'value' => 'advertisement' ]],
            'in_litter'          => [[ 'value' => 1, 'operator' => '!=' ]]
        );

        $em  = $this->get('advertisement_repository');
        $ads = $em->findBy($filters, [ 'created' => 'desc' ], $itemsPerPage, $page);

        $countAds = $em->countBy($filters);

        $pagination = $this->get('paginator')->get([
            'boundary'    => true,
            'directional' => true,
            'epp'         => $itemsPerPage,
            'page'        => $page,
            'total'       => $countAds,
            'route'       => [
                'name'   => 'admin_ads_content_provider',
                'params' => ['category' => $categoryId]
            ],
        ]);

        return $this->render(
            'advertisement/content-provider.tpl',
            array(
                'ads'        => $ads,
                'pagination' => $pagination,
            )
        );
    }

    /**
     * Handles and shows the advertisement configuration form.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     *
     * @Security("hasExtension('ADS_MANAGER')
     *     and hasPermission('ADVERTISEMENT_ADMIN')")
     */
    public function configAction(Request $request)
    {
        if ('POST' == $this->request->getMethod()) {
            $formValues = $request->request;

            $settings = [
                'ads_settings' => [
                    'lifetime_cookie' => $formValues->getDigits('ads_settings_lifetime_cookie'),
                    'no_generics'     => is_null($formValues->get('ads_settings_no_generics')) ? 1 : 0,
                    'safe_frame'      => empty($formValues->get('safe_frame')) ? 0 : 1
                ],
                'revive_ad_server' => [
                    'url'     => $formValues->filter('revive_ad_server_url', '', FILTER_SANITIZE_STRING),
                    'site_id' => $formValues->getDigits('revive_ad_server_site_id'),
                ],
                'dfp_options' => [
                    'target'     => $formValues->filter('dfp_options_target', '', FILTER_SANITIZE_STRING),
                    'module'     => $formValues->filter('dfp_options_module', '', FILTER_SANITIZE_STRING),
                    'content_id' => $formValues->filter('dfp_options_content_id', '', FILTER_SANITIZE_STRING),
                ],
                'tradedoubler_id' => $formValues->getDigits('tradedoubler_id'),
                'iadbox_id'       => $formValues->filter('iadbox_id', '', FILTER_SANITIZE_STRING),
                'ads_txt'         => $formValues->filter('ads_txt', '', FILTER_SANITIZE_STRING),
            ];

            if ($this->getUser()->isMaster()) {
                $settings['dfp_custom_code'] =
                    base64_encode($formValues->get('dfp_custom_code'));
            }

            foreach ($settings as $key => $value) {
                $this->get('setting_repository')->set($key, $value);
            }

            $this->get('session')->getFlashBag()->add(
                'success',
                _('Settings saved successfully.')
            );

            // Delete caches for frontpages
            $this->get('core.dispatcher')->dispatch('setting.update');

            return $this->redirect($this->generateUrl('admin_ads_config'));
        } else {
            $keys = [
                'ads_settings', 'dfp_options',  'iadbox_id', 'revive_ad_server',
                'tradedoubler_id', 'dfp_custom_code', 'ads_txt'
            ];

            $configurations = $this->get('setting_repository')->get($keys);

            return $this->render(
                'advertisement/config.tpl',
                [ 'configs' => $configurations ]
            );
        }
    }

    /**
     * Returns the list of categories.
     *
     * @return array The list of categories.
     */
    protected function getCategories()
    {
        $categories = $this->get('orm.manager')
            ->getRepository('Category')
            ->findBy(
                'internal_category in [1, 9, 7, 11]'
                . ' order by internal_category asc, title asc'
            );

        $fm = $this->get('data.manager.filter');
        // Sometimes category is array. When create & update advertisement
        $categories = $fm->set($categories)->filter('localize', [
            'keys' => \ContentCategory::getMultiLanguageFields(),
            'locale' => $this->getLocaleData('frontend')['default']
        ])->get();

        $categories = array_map(function ($a) {
            return [
                'id'     => (int) $a->pk_content_category,
                'name'   => $a->title,
                'type'   => $a->internal_category,
                'parent' => (int) $a->fk_content_category
            ];
        }, $categories);

        array_unshift(
            $categories,
            [ 'id' => 0, 'name' => _('Home'), 'type' => 0, 'parent' => 0 ]
        );

        return array_values($categories);
    }

    /**
     * Returns the list of public user groups.
     *
     * @return array The list of public user groups.
     */
    protected function getUserGroups()
    {
        $userGroups = $this->get('orm.manager')
            ->getRepository('UserGroup')->findBy();

        // Show only public groups ()
        $userGroups = array_filter($userGroups, function ($a) {
            return in_array(223, $a->privileges);
        });

        $userGroups = array_map(function ($a) {
            return [ 'id' => $a->pk_user_group, 'name' => $a->name ];
        }, $userGroups);

        return array_values($userGroups);
    }
}
