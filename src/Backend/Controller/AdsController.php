<?php
/**
 * Handles the actions for managing ads
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
use Onm\Security\Acl;
use Onm\Framework\Controller\Controller;
use Onm\Settings as s;
use Onm\Message as m;

/**
 * Handles the actions for managing ads
 *
 * @package Backend_Controllers
 **/
class AdsController extends Controller
{
    /**
     * Common code for all the actions.
     */
    public function init()
    {
        \Onm\Module\ModuleManager::checkActivatedOrForward('ADS_MANAGER');

        $contentType = \ContentManager::getContentTypeIdFromName('advertisement');

        // Sometimes category is array. When create & update advertisement
        $this->category = $this->get('request')->query->getDigits('category', 0);

        $ccm = \ContentCategoryManager::get_instance();
        list($this->parentCategories, $this->subcat, $this->categoryData) =
            $ccm->getArraysMenu($this->category, $contentType);

        $timezones = \DateTimeZone::listIdentifiers();
        $timezone  = new \DateTimeZone($timezones[s::get('time_zone', 'UTC')]);

        $this->view->assign(
            array(
                'subcat'       => $this->subcat,
                'allcategorys' => $this->parentCategories,
                'datos_cat'    => $this->categoryData,
                'category'     => $this->category,
                'timezone'     => $timezone->getName()
            )
        );
    }

    /**
     * Lists all the available ads.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     *
     * @Security("has_role('ADVERTISEMENT_ADMIN')")
     */
    public function listAction(Request $request)
    {
        // Get ads positions
        $positionManager = $this->container->get('instance_manager')
            ->current_instance->theme->getAdsPositionManager();
        $map      = $positionManager->getAllAdsPositions();
        $adsNames = $positionManager->getAllAdsNames();

        // Filters
        $filterOptions = array(
            'type_advertisement' => array('-1' => _("-- All --")) + $adsNames,
            'content_status' => array(
                '-1' => _("-- All --"),
                '0'  => _("No published"),
                '1'  => _("Published")
            ),
            'type'  => array(
                '-1' => _("-- All --"),
                '0' => _("Multimedia"),
                '1' => _("Javascript"),
                '2' => _("OpenX"),
                '3' => _("Google DFP")
            ),
        );

        return $this->render(
            'advertisement/list.tpl',
            array(
                'filter_options' => $filterOptions,
                'map'            => json_encode($map)
            )
        );
    }

    /**
     * Handles the form for create a new ad.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     *
     * @Security("has_role('ADVERTISEMENT_CREATE')")
     */
    public function createAction(Request $request)
    {
        $page = $request->request->getDigits('page', 1);
        $filter = $request->query->get('filter');

        if ('POST' == $request->getMethod()) {

            $advertisement = new \Advertisement();

            $categories = $request->request->get('category', '', FILTER_SANITIZE_STRING);
            $firstCategory = $categories[0];

            $data = array(
                'title'              => $request->request->filter('title', '', FILTER_SANITIZE_STRING),
                'metadata'           => $request->request->filter('metadata', '', FILTER_SANITIZE_STRING),
                'category'           => $firstCategory,
                'categories'         => implode(',', $categories),
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
                'script'             => $request->request->filter('script', '', FILTER_SANITIZE_STRING),
                'type_advertisement' => $request->request->filter('type_advertisement', '', FILTER_SANITIZE_STRING),
                'fk_author'          => $_SESSION['userid'],
                'fk_publisher'       => $_SESSION['userid'],
                'params'             => array(
                    'width'             => $request->request->getDigits('params_width', ''),
                    'height'            => $request->request->getDigits('params_height', ''),
                    'openx_zone_id'     => $request->request->getDigits('openx_zone_id', ''),
                    'googledfp_unit_id' => $request->request->filter('googledfp_unit_id', '', FILTER_SANITIZE_STRING),
                )
            );

            if ($advertisement->create($data)) {
                m::add(_('Advertisement successfully created.'), m::SUCCESS);
            } else {
                m::add(_('Unable to create the new advertisement.'), m::ERROR);
            }

            return $this->redirect(
                $this->generateUrl(
                    'admin_ad_show',
                    array(
                        'id'     => $advertisement->id,
                        'filter' => $filter,
                        'page'   => $page
                    )
                )
            );
        } else {
            // Get ads server if exists
            $serverUrl = '';
            if ($openXsettings = s::get('revive_ad_server')) {
                $serverUrl = $openXsettings['url'];
            }

            $positionManager = $this->container->get('instance_manager')->current_instance->theme->getAdsPositionManager();
            return $this->render(
                'advertisement/new.tpl',
                array(
                    'themeAds'   => $positionManager->getThemeAdsPositions(),
                    'filter'     => $filter,
                    'page'       => $page,
                    'server_url' => $serverUrl,
                )
            );
        }
    }

    /**
     * Shows the editing form for a advertisement given its id.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     *
     * @Security("has_role('ADVERTISEMENT_UPDATE')")
     */
    public function showAction(Request $request)
    {
        $id     = $request->query->getDigits('id', null);
        $filter = $request->query->get('filter');
        $page   = $request->query->getDigits('page', 1);

        $serverUrl = '';
        if ($openXsettings = s::get('revive_ad_server')) {
            $serverUrl = $openXsettings['url'];
        }

        $ad = new \Advertisement($id);
        if (is_null($ad->id)) {
            m::add(sprintf(_('Unable to find the advertisement with the id "%d"'), $id));

            return $this->redirect($this->generateUrl('admin_ads'));
        }
        if ($ad->fk_publisher != $_SESSION['userid']
            && (false === Acl::check('CONTENT_OTHER_UPDATE'))
        ) {
            m::add(_("You can't modify this content because you don't have enought privileges."));

            return $this->redirect($this->generateUrl('admin_ads'));
        }

        if (!is_array($ad->fk_content_categories)) {
            $ad->fk_content_categories = explode(',', $ad->fk_content_categories);
        }

        if (!empty($ad->img)) {
            //Buscar foto where pk_foto=img1
            $photo1 = new \Photo($ad->img);
            $this->view->assign('photo1', $photo1);
        }

        $positionManager = $this->container->get('instance_manager')->current_instance->theme->getAdsPositionManager();
        return $this->render(
            'advertisement/new.tpl',
            array(
                'advertisement' => $ad,
                'themeAds'      => $positionManager->getThemeAdsPositions(),
                'filter'        => $filter,
                'page'          => $page,
                'server_url'    => $serverUrl,
            )
        );
    }

    /**
     * Updates the advertisement information given data send by POST.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     *
     * @Security("has_role('ADVERTISEMENT_UPDATE')")
     */
    public function updateAction(Request $request)
    {
        $id = $request->query->getDigits('id');
        $filter = $request->query->get('filter');
        $page   = $request->query->getDigits('page', 1);

        $ad = new \Advertisement($id);
        if (is_null($ad->id)) {
            m::add(sprintf(_('Unable to find the advertisement with the id "%d"'), $id));

            return $this->redirect($this->generateUrl('admin_ads'));
        }
        if (!$ad->isOwner($_SESSION['userid'])
            && (false === Acl::check('CONTENT_OTHER_UPDATE'))
        ) {
            m::add(_("You can't modify this content because you don't have enought privileges."));

            return $this->redirect($this->generateUrl('admin_ads'));
        }

        $categories = $request->request->get('category', '', FILTER_SANITIZE_STRING);
        $firstCategory = $categories[0];

        $data = array(
            'id'                 => $ad->id,
            'title'              => $request->request->filter('title', '', FILTER_SANITIZE_STRING),
            'metadata'           => $request->request->filter('metadata', '', FILTER_SANITIZE_STRING),
            'category'           => $firstCategory,
            'categories'         => implode(',', $categories),
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
            'script'             => $request->request->filter('script', '', FILTER_SANITIZE_STRING),
            'type_advertisement' => $request->request->filter('type_advertisement', '', FILTER_SANITIZE_STRING),
            'fk_author'          => $_SESSION['userid'],
            'fk_publisher'       => $_SESSION['userid'],
            'params'             => array(
                'width'             => $request->request->getDigits('params_width', ''),
                'height'            => $request->request->getDigits('params_height', ''),
                'openx_zone_id'     => $request->request->getDigits('openx_zone_id', ''),
                'googledfp_unit_id' => $request->request->filter('googledfp_unit_id', '', FILTER_SANITIZE_STRING),
            )
        );

        if ($ad->update($data)) {
            m::add(_('Advertisement successfully updated.'), m::SUCCESS);
        } else {
            m::add(_('Unable to update the advertisement data.'), m::ERROR);
        }

        return $this->redirect(
            $this->generateUrl(
                'admin_ad_show',
                array(
                    'id'     => $data['id'],
                    'filter' => $filter,
                    'page'   => $page
                )
            )
        );
    }

    /**
     * Lists the available advertisements for the frontpage manager.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     */
    public function contentProviderAction(Request $request)
    {
        $categoryId   = $request->query->getDigits('category', 0);
        $page         = $request->query->getDigits('page', 1);
        $itemsPerPage = 8;

        $em  = $this->get('advertisement_repository');
        $ids = $this->get('frontpage_repository')->getContentIdsForHomepageOfCategory();

        $filters = array(
            'content_type_name'  => array(array('value' => 'advertisement')),
            'type_advertisement' => array(array('value' => 37)),
        );

        $ads      = $em->findBy($filters, array('created' => 'desc'), $itemsPerPage, $page);
        $countAds = $em->countBy($filters);

        $pagination = \Pager::factory(
            array(
                'mode'        => 'Sliding',
                'perPage'     => $itemsPerPage,
                'append'      => false,
                'path'        => '',
                'delta'       => 4,
                'clearIfVoid' => true,
                'urlVar'      => 'page',
                'totalItems'  => $countAds,
                'fileName'    => $this->generateUrl(
                    'admin_ads_content_provider',
                    array('category' => $categoryId)
                ).'&page=%d',
            )
        );

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
     * @Security("has_role('ADVERTISEMENT_ADMIN')")
     */
    public function configAction(Request $request)
    {
        if ('POST' == $this->request->getMethod()) {

            $formValues = $request->request;

            $settings = array(
                'ads_settings' => array(
                    'lifetime_cookie' => $formValues->getDigits('ads_settings_lifetime_cookie'),
                    'no_generics'     => $formValues->getDigits('ads_settings_no_generics'),
                ),
                'revive_ad_server' => array(
                    'url'     => $formValues->filter('revive_ad_server_url', '', FILTER_SANITIZE_STRING),
                    'site_id' => $formValues->getDigits('revive_ad_server_site_id'),
                ),
            );

            foreach ($settings as $key => $value) {
                s::set($key, $value);
            }

            m::add(_('Settings saved successfully.'), m::SUCCESS);

            return $this->redirect($this->generateUrl('admin_ads_config'));
        } else {
            $configurationsKeys = array('ads_settings','revive_ad_server');
            $configurations = s::get($configurationsKeys);

            return $this->render(
                'advertisement/config.tpl',
                array('configs'   => $configurations,)
            );
        }
    }
}
