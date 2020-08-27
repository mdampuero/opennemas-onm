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

use Api\Exception\GetItemException;
use Common\Core\Annotation\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Common\Core\Controller\Controller;

/**
 * Handles the actions for managing ads
 *
 * @package Backend_Controllers
 */
class AdvertisementsController extends Controller
{
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

        $adsPositions = [ [ 'name' => _("All"), 'value' => null ] ];

        foreach ($adsNames as $key => $value) {
            $adsPositions[] = [ 'name' => $value, 'value' => $key];
        }

        $types = [
            [ 'name' => _("All"), 'value' => null ],
            [ 'name' => _("Multimedia"), 'value' => 0 ],
            [ 'name' => _("Javascript"), 'value' => 1 ],
            [ 'name' => _("OpenX"), 'value' => 2 ],
            [ 'name' => _("Google DFP"), 'value' => 3 ],
            [ 'name' => _("Smart"), 'value' => 4 ]
        ];

        return $this->render('advertisement/list.tpl', [
            'advertisement_positions' => $adsPositions,
            'types'                   => $types,
            'map'                     => json_encode($map)
        ]);
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
        // If the action is not to save the ad, just show the form
        if ('POST' !== $request->getMethod()) {
            return $this->render('advertisement/new.tpl', array_merge(
                $this->getExtraParameters(),
                [ 'advertisement' => new \Advertisement(), ]
            ));
        }

        $categories = json_decode($request->request->get('categories', ''), true);

        if (is_array($categories) && empty($categories)) {
            $categories = null;
        }

        $title = $request->request->filter('title', '', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);

        $data = [
            'title'              => $title,
            'tags'               => $this->getTags($title),
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
            'path'               => $request->request->filter('path', '', FILTER_SANITIZE_STRING),
            'script'             => $request->request->get('script', ''),
            'positions'          => $request->request->get('positions', []),
            'fk_author'          => $this->getUser()->id,
            'fk_publisher'       => $this->getUser()->id,
            'params'             => [
                'sizes'             => json_decode($request->request->get('sizes', ''), true),
                'openx_zone_id'     => $request->request->getDigits('openx_zone_id', ''),
                'smart_page_id'     => $request->request->getDigits('smart_page_id', ''),
                'smart_format_id'   => $request->request->getDigits('smart_format_id', ''),
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

        $advertisement = new \Advertisement();
        if ($advertisement->create($data)) {
            $level   = 'success';
            $message = _('Advertisement successfully created.');
        }

        $this->get('session')->getFlashBag()->add($level, $message);
        return $this->redirect(
            $this->generateUrl('admin_ad_show', [ 'id' => $advertisement->id ])
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
        $id = $request->query->getDigits('id', null);

        $advertisement = new \Advertisement($id);

        if (is_null($advertisement->id)) {
            $this->get('session')->getFlashBag()->add(
                'error',
                sprintf(_('Unable to find the advertisement with the id "%d"'), $id)
            );

            return $this->redirect($this->generateUrl('admin_ads'));
        }

        if ($advertisement->fk_publisher != $this->getUser()->id
            && (!$this->get('core.security')->hasPermission('CONTENT_OTHER_UPDATE'))
        ) {
            $this->get('session')->getFlashBag()->add(
                'error',
                _("You can't modify this content because you don't have enough privileges.")
            );

            return $this->redirect($this->generateUrl('admin_ads'));
        }

        if (!is_array($advertisement->fk_content_categories) && !empty($advertisement->fk_content_categories)) {
            $advertisement->fk_content_categories = explode(',', $advertisement->fk_content_categories);
        }

        // If the advertisement has photo assigned retrieve it
        try {
            $photo1 = getService('api.service.photo')->getItem($advertisement->path);
            $this->view->assign('photo1', $photo1);
        } catch (GetItemException $e) {
        }

        return $this->render('advertisement/new.tpl', array_merge(
            $this->getExtraParameters(),
            [ 'advertisement' => $advertisement ]
        ));
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
        $id = $request->query->getDigits('id', null);

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

        $title = $request->request->filter('title', '', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);

        $data = [
            'id'                 => $ad->id,
            'title'              => $title,
            'tags'               => $this->getTags($title),
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
            'path'               => $request->request->filter('path', '', FILTER_SANITIZE_STRING),
            'script'             => $request->request->get('script', ''),
            'positions'          => $request->request->get('positions', []),
            'fk_author'          => $this->getUser()->id,
            'fk_publisher'       => $this->getUser()->id,
            'params'             => [
                'sizes'             => json_decode($request->request->get('sizes', ''), true),
                'openx_zone_id'     => $request->request->getDigits('openx_zone_id', ''),
                'smart_page_id'     => $request->request->getDigits('smart_page_id', ''),
                'smart_format_id'   => $request->request->getDigits('smart_format_id', ''),
                'googledfp_unit_id' => $request->request->filter('googledfp_unit_id', '', FILTER_SANITIZE_STRING),
                'user_groups'       => json_decode($request->request->get('user_groups', ''), true),
                'orientation'       => $request->request->get('orientation', 'horizontal'),
                'mark_text'         => $request->request->filter('mark_text', '', FILTER_SANITIZE_STRING),
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
            'id' => $data['id'],
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
        $categoryId = $request->query->getDigits('category', 1);
        $page       = $request->query->getDigits('page', 1);
        $epp        = 8;

        $oql = sprintf(
            'content_type_name="advertisement" and in_litter="0" '
            . 'and position="37" order by created desc limit %s offset %s',
            $epp,
            ($page - 1) * $epp
        );

        $em = $this->get('advertisement_repository');

        list($criteria, $order, $epp, $page) = $this->get('core.helper.oql')->getFiltersFromOql($oql);

        $results = $em->findBy($criteria, $order, $epp, $page);
        $results = \Onm\StringUtils::convertToUtf8($results);
        $total   = $em->countBy($criteria);

        $pagination = $this->get('paginator')->get([
            'boundary'    => true,
            'directional' => true,
            'epp'         => $epp,
            'page'        => $page,
            'total'       => $total,
            'route'       => [
                'name'   => 'admin_ads_content_provider',
                'params' => ['category' => $categoryId]
            ],
        ]);

        return $this->render('advertisement/content-provider.tpl', [
            'ads'        => $results,
            'pagination' => $pagination,
        ]);
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
        $ds = $this->get('orm.manager')->getDataSet('Settings', 'instance');

        if ('POST' !== $request->getMethod()) {
            $keys = [
                'adsense_id', 'ads_settings', 'ads_txt','dfp_custom_code',
                'dfp_options', 'iadbox_id', 'revive_ad_server',
                'smart_ad_server', 'smart_custom_code', 'tradedoubler_id',
            ];

            return $this->render('advertisement/config.tpl', [
                'configs' => $ds->get($keys)
            ]);
        }

        $formValues = $request->request;

        $settings = [
            'adsense_id'       => $formValues->filter('adsense_id', '', FILTER_SANITIZE_STRING),
            'ads_settings'     => [
                'lifetime_cookie' => $formValues->getDigits('ads_settings_lifetime_cookie'),
                'no_generics'     => is_null($formValues->get('ads_settings_no_generics')) ? 1 : 0,
                'safe_frame'      => (int) $this->container->get('core.helper.advertisement')->isSafeFrameEnabled(),
                'default_mark'    => $formValues->filter('ads_settings_mark_default', '', FILTER_SANITIZE_STRING),
            ],
            'ads_txt'          => $formValues->filter('ads_txt', '', FILTER_SANITIZE_STRING),
            'dfp_options'      => [
                'target'     => $formValues->filter('dfp_options_target', '', FILTER_SANITIZE_STRING),
                'module'     => $formValues->filter('dfp_options_module', '', FILTER_SANITIZE_STRING),
                'content_id' => $formValues->filter('dfp_options_content_id', '', FILTER_SANITIZE_STRING),
            ],
            'iadbox_id'        => $formValues->filter('iadbox_id', '', FILTER_SANITIZE_STRING),
            'smart_ad_server'  => [
                'domain'      => $formValues->filter('smart_ad_server_domain', '', FILTER_SANITIZE_STRING),
                'tags_format' => $formValues->filter('smart_ad_server_tags_format', '', FILTER_SANITIZE_STRING),
                'network_id'  => $formValues->getDigits('smart_ad_server_network_id'),
                'site_id'     => $formValues->getDigits('smart_ad_server_site_id'),
                'page_id'     => [
                    'frontpage'         => $formValues->getDigits('smart_ad_server_page_id_frontpage'),
                    'article_inner'     => $formValues->getDigits('smart_ad_server_page_id_article_inner'),
                    'opinion_frontpage' => $formValues->getDigits('smart_ad_server_page_id_opinion_frontpage'),
                    'opinion_inner'     => $formValues->getDigits('smart_ad_server_page_id_opinion_inner'),
                    'video_frontpage'   => $formValues->getDigits('smart_ad_server_page_id_video_frontpage'),
                    'video_inner'       => $formValues->getDigits('smart_ad_server_page_id_video_inner'),
                    'album_frontpage'   => $formValues->getDigits('smart_ad_server_page_id_album_frontpage'),
                    'album_inner'       => $formValues->getDigits('smart_ad_server_page_id_album_inner'),
                    'polls_frontpage'   => $formValues->getDigits('smart_ad_server_page_id_polls_frontpage'),
                    'polls_inner'       => $formValues->getDigits('smart_ad_server_page_id_polls_inner'),
                    'comment'           => $formValues->getDigits('smart_ad_server_page_id_comment'),
                    'other'             => $formValues->getDigits('smart_ad_server_page_id_other'),
                ],
                'header_bidding'     => empty($formValues->get('smart_ad_server_header_bidding')) ? 0 : 1,
                'category_targeting' =>
                    $formValues->filter('smart_ad_server_category_targeting', '', FILTER_SANITIZE_STRING),
                'module_targeting'   =>
                    $formValues->filter('smart_ad_server_module_targeting', '', FILTER_SANITIZE_STRING),
                'url_targeting'      =>
                    $formValues->filter('smart_ad_server_url_targeting', '', FILTER_SANITIZE_STRING),
            ],
            'revive_ad_server' => [
                'url'     => $formValues->filter('revive_ad_server_url', '', FILTER_SANITIZE_STRING),
                'site_id' => $formValues->getDigits('revive_ad_server_site_id'),
            ],
            'tradedoubler_id'  => $formValues->getDigits('tradedoubler_id'),
        ];

        if ($this->get('core.security')->hasPermission('MASTER')) {
            $settings['ads_settings']['safe_frame'] = empty($formValues->get('safe_frame')) ? 0 : 1;
            $settings['dfp_custom_code']            = base64_encode($formValues->get('dfp_custom_code'));
            $settings['smart_custom_code']          = base64_encode($formValues->get('smart_custom_code'));
        }
        try {
            $ds->set($settings);

            $type    = 'success';
            $message = _('Settings saved successfully.');
        } catch (\Exception $e) {
            $type    = 'error';
            $message = _('Unable to save the settings.');
        }

        $this->get('session')->getFlashBag()->add($type, $message);

        // Delete caches for frontpages
        $this->get('core.dispatcher')->dispatch('setting.update');

        return $this->redirect($this->generateUrl('admin_ads_config'));
    }

    /**
     * Returns the list of extra parameters needed
     * while showing the advertisement form
     *
     * @return array the list of extra parameters to use in the tempalte
     */
    public function getExtraParameters()
    {
        $adsPositions = $this->container->get('core.helper.advertisement');
        $renderer     = $this->container->get('frontend.renderer.advertisement');
        $settings     = $this->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get([ 'revive_ad_server', 'smart_ad_server' ]);

        // OpenX
        $openxServerUrl = '';
        if (!empty($settings['revive_ad_server'])
            && is_array($settings['revive_ad_server'])
            && array_key_exists('url', $settings['revive_ad_server'])
        ) {
            $openxServerUrl = $settings['revive_ad_server']['url'];
        }

        // Smart+
        $smartServerUrl = '';
        if (!empty($settings['smart_ad_server'])
            && is_array($settings['smart_ad_server'])
            && array_key_exists('domain', $settings['smart_ad_server'])
            && array_key_exists('network_id', $settings['smart_ad_server'])
            && array_key_exists('site_id', $settings['smart_ad_server'])
        ) {
            $smartServerUrl = $settings['smart_ad_server']['domain'];
        }

        return [
            'ads_positions_manager' => $adsPositions,
            'extra'                 => [
                'safeFrame'                 => $adsPositions->isSafeFrameEnabled(),
                'aditional_theme_positions' => $adsPositions->getPositionsForTheme(),
                'ads_positions'             => $adsPositions->getPositionNames(),
                'categories'                => $this->getCategories(),
                'openx_server_url'          => $openxServerUrl,
                'smart_server_url'          => $smartServerUrl,
                'user_groups'               => $this->getSubscriptions(),
                'default_mark'              => $renderer->getMark(),
            ],
        ];
    }

    /**
     * Returns the list of public user groups.
     *
     * @return array The list of public user groups.
     */
    protected function getSubscriptions()
    {
        $subscriptions = $this->get('api.service.subscription')
            ->setCount(false)
            ->getList();

        $subscriptions = array_map(function ($a) {
            return [ 'id' => $a->pk_user_group, 'name' => $a->name ];
        }, $subscriptions['items']);

        return array_values($subscriptions);
    }

    /**
     * Returns the list of tag ids basing on the advertisement title.
     *
     * @param string $title The advertisement title.
     *
     * @return array The list of tag ids.
     */
    protected function getTags($title)
    {
        return array_map(function ($tag) {
            return $tag->id;
        }, $this->get('api.service.tag')->getListByString($title)['items']);
    }
}
