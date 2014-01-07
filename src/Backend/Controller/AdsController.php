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

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
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
     * Common code for all the actions
     *
     * @return void
     **/
    public function init()
    {
        \Onm\Module\ModuleManager::checkActivatedOrForward('ADS_MANAGER');

        $this->checkAclOrForward('ADVERTISEMENT_ADMIN');

        $contentType = \ContentManager::getContentTypeIdFromName('advertisement');

        // Sometimes category is array. When create & update advertisement
        $this->category = $this->get('request')->query->getDigits('category', 0);

        $ccm = \ContentCategoryManager::get_instance();
        list($this->parentCategories, $this->subcat, $this->categoryData) =
            $ccm->getArraysMenu($this->category, $contentType);

        $this->view->assign(
            array(
                'subcat'       => $this->subcat,
                'allcategorys' => $this->parentCategories,
                'datos_cat'    => $this->categoryData,
                'category'     => $this->category
            )
        );
    }

    /**
     * Lists all the available ads
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function listAction(Request $request)
    {
        // Get ads positions
        $positionManager = $this->container->getParameter('instance')->theme->getAdsPositionManager();
        $map        = $positionManager->getAllAdsPositions();
        $adsNames   = $positionManager->getAllAdsNames();
        $filtersUrl = $request->query->get('filter');

        // Get page
        $page = $request->query->getDigits('page', 1);
        list($filter, $queryString) = $this->buildFilter(
            $request,
            'in_litter != 1 AND fk_content_categories LIKE \'%' . $this->category . '%\''
        );

        // Filters
        $filterOptions = array(
            'type_advertisement' => array('-1' => _("-- All --")) + $adsNames,
            'available' => array(
                '-1' => _("-- All --"),
                '0'  => _("No published"),
                '1'  => _("Published")
            ),
            'type'  => array(
                '-1' => _("-- All --"),
                '0' => _("Multimedia"),
                '1' => _("Javascript")
            ),
        );

        if ($this->category == 0) {
            $categoryFilter = null;
        } else {
            $categoryFilter = $this->category;
        }

        $itemsPerPage = s::get('items_per_page');

        $cm = new \ContentManager();
        $ads = $cm->find_all(
            'Advertisement',
            $filter,
            'ORDER BY created DESC '
        );

        foreach ($ads as $key => &$ad) {
            //Distinguir entre flash o no flash
            $img = new \Photo($ad->path);
            if ($img->type_img == "swf") {
                $ad->is_flash = 1;
            } else {
                $ad->is_flash = 0;
            }
            $ad->fk_content_categories = explode(',', $ad->fk_content_categories);

            //Get the name of the advertisement placeholder
            $adv_placeholder = $ad->getNameOfAdvertisementPlaceholder($ad->type_advertisement);
            $ad->advertisement_placeholder = $adv_placeholder;

            if (!in_array($this->category, $ad->fk_content_categories)) {
                unset($ads[$key]);
            }
        }

        $filteredAds = array_slice($ads, ($page-1)*$itemsPerPage, $itemsPerPage);

        // Build the pager
        $pagination = \Pager::factory(
            array(
                'mode'        => 'Sliding',
                'perPage'     => $itemsPerPage,
                'append'      => false,
                'path'        => '',
                'delta'       => 4,
                'clearIfVoid' => true,
                'urlVar'      => 'page',
                'totalItems'  => count($ads),
                'fileName'    => $this->generateUrl('admin_ads').'?'.$queryString.'&page=%d',
            )
        );

        $_SESSION['desde'] = 'advertisement';

        return $this->render(
            'advertisement/list.tpl',
            array(
                'pagination'     => $pagination,
                'advertisements' => $filteredAds,
                'filter_options' => $filterOptions,
                'map'            => $map,
                'page'           => $page,
                'filter'         => $filtersUrl,
            )
        );
    }

    /**
     * Handles the form for create a new ad
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function createAction(Request $request)
    {
        $this->checkAclOrForward('ADVERTISEMENT_CREATE');
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
                'publisher'          => $_SESSION['userid'],
                'params'             => array(
                    'width'          => $request->request->getDigits('params_width', ''),
                    'height'         => $request->request->getDigits('params_height', ''),
                )
            );

            if ($advertisement->create($data)) {
                m::add(_('Advertisement successfully created.'), m::SUCCESS);
            } else {
                m::add(_('Unable to create the new advertisement.'), m::ERROR);
            }

            return $this->redirect(
                $this->generateUrl(
                    'admin_ads',
                    array('category' => $firstCategory, 'page' => $page, 'filter'   => $filter)
                )
            );
        } else {
            $positionManager = $this->container->getParameter('instance')->theme->getAdsPositionManager();
            return $this->render(
                'advertisement/new.tpl',
                array(
                    'themeAds' => $positionManager->getThemeAdsPositions(),
                    'filter'   => $filter,
                    'page'     => $page,
                )
            );
        }
    }

    /**
     * Shows the editing form for a advertisement given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function showAction(Request $request)
    {
        $this->checkAclOrForward('ADVERTISEMENT_UPDATE');

        $id     = $request->query->getDigits('id', null);
        $filter = $request->query->get('filter');
        $page   = $request->query->getDigits('page', 1);

        $ad = new \Advertisement($id);
        if (is_null($ad->id)) {
            m::add(sprintf(_('Unable to find the advertisement with the id "%d"'), $id));

            return $this->redirect($this->generateUrl('admin_ads'));
        }
        if ($ad->fk_user != $_SESSION['userid']
            && (!\Acl::check('CONTENT_OTHER_UPDATE'))
        ) {
            m::add(_("You can't modify this content because you don't have enought privileges."));

            return $this->redirect($this->generateUrl('admin_ads'));
        }

        $ad->fk_content_categories = explode(',', $ad->fk_content_categories);

        if (!empty($ad->img)) {
            //Buscar foto where pk_foto=img1
            $photo1 = new \Photo($ad->img);
            $this->view->assign('photo1', $photo1);
        }

        $positionManager = $this->container->getParameter('instance')->theme->getAdsPositionManager();
        return $this->render(
            'advertisement/new.tpl',
            array(
                'advertisement' => $ad,
                'themeAds'      => $positionManager->getThemeAdsPositions(),
                'filter'        => $filter,
                'page'          => $page,
            )
        );

    }
    /**
     * Updates the advertisement information given data send by POST
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function updateAction(Request $request)
    {
        $this->checkAclOrForward('ADVERTISEMENT_UPDATE');

        $id = $request->query->getDigits('id');
        $filter = $request->query->get('filter');
        $page   = $request->query->getDigits('page', 1);

        $ad = new \Advertisement($id);
        if (is_null($ad->id)) {
            m::add(sprintf(_('Unable to find the advertisement with the id "%d"'), $id));

            return $this->redirect($this->generateUrl('admin_ads'));
        }
        if ($ad->fk_user != $_SESSION['userid']
            && (!\Acl::check('CONTENT_OTHER_UPDATE'))
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
            'publisher'          => $_SESSION['userid'],
            'params'             => array(
                'width'          => $request->request->getDigits('params_width', ''),
                'height'         => $request->request->getDigits('params_height', ''),
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
     * Description of this action
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function deleteAction(Request $request)
    {
        $this->checkAclOrForward('ADVERTISEMENT_DELETE');

        $id       = $request->query->getDigits('id');
        $category = $request->query->filter('category', 'all', FILTER_SANITIZE_STRING);
        $page     = $request->query->getDigits('page', 1);
        $filter   = $request->query->get('filter');

        if (!empty($id)) {
            $ad = new \Advertisement($id);

            $ad->delete($id, $_SESSION['userid']);
            m::add(_("Advertisement deleted successfully."), m::SUCCESS);
        } else {
            m::add(_('You must give an id for delete an advertisement.'), m::ERROR);
        }

        if (!$request->isXmlHttpRequest()) {
            return $this->redirect(
                $this->generateUrl(
                    'admin_ads',
                    array(
                        'category' => $category,
                        'page'     => $page
                    )
                )
            );
        } else {
            return new Response('Ok', 200);
        }
    }

    /**
     * Deletes multiple ads at once given their ids
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function batchDeleteAction(Request $request)
    {
        $this->checkAclOrForward('ADVERTISEMENT_DELETE');

        $selected = $request->query->get('selected_fld', null);
        $category = $request->query->getDigits('category', 'all');
        $page     = $request->query->getDigits('page', 1);

        if (is_array($selected)
            && count($selected) > 0
        ) {
            $changes = 0;
            foreach ($selected as $id) {
                $ad = new \Advertisement((int) $id);
                if (!is_null($ad->id)) {
                    $ad->delete($id, $_SESSION['userid']);
                    $changes++;
                } else {
                    m::add(sprintf(_('Unable to find an ad with the id "%d"'), $id), m::ERROR);
                }
            }
        }
        if ($changes > 0) {
            m::add(sprintf(_('Successfully deleted %d ads'), $changes), m::SUCCESS);
        }

        if (!$request->isXmlHttpRequest()) {
            return $this->redirect(
                $this->generateUrl(
                    'admin_ads',
                    array(
                        'category' => $category,
                        'page' => $page,
                    )
                )
            );
        } else {
            return new Response('Ok', 200);
        }

    }

    /**
     * Sets the available status for multiple ads at once
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function batchPublishAction(Request $request)
    {
        $this->checkAclOrForward('ADVERTISEMENT_AVAILA');

        $status   = $request->query->getDigits('status', 0);
        $selected = $request->query->get('selected_fld', null);
        $category = $request->query->getDigits('category', 0);
        $page     = $request->query->getDigits('page', 1);

        if (is_array($selected)
            && count($selected) > 0
        ) {
            $changes = 0;
            foreach ($selected as $id) {
                $ad = new \Advertisement((int) $id);
                if (!is_null($ad->id)) {
                    $ad->set_available($status, $_SESSION['userid']);
                    $changes++;
                } else {
                    m::add(sprintf(_('Unable to find an advertisement with the id "%d"'), $id), m::ERROR);
                }
            }
        }

        if ($changes > 0) {
            m::add(sprintf(_('Successfully changed the available status of %d ads'), $changes), m::SUCCESS);
        }

        return $this->redirect(
            $this->generateUrl(
                'admin_ads',
                array(
                    'category' => $category,
                    'page'     => $page,
                )
            )
        );
    }

    /**
     * Change available status for one ad given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function toggleAvailableAction(Request $request)
    {
        $this->checkAclOrForward('ADVERTISEMENT_AVAILA');

        $id       = $request->query->getDigits('id', 0);
        $status   = $request->query->getDigits('status', 0);
        $filter   = $request->query->filter('filter', '', FILTER_SANITIZE_STRING);
        $category = $request->query->filter('category', 'all', FILTER_SANITIZE_STRING);
        $page     = $request->query->getDigits('page', 1);

        $ad = new \Advertisement($id);

        if (is_null($ad->id)) {
            m::add(sprintf(_('Unable to find an ad with the id "%d"'), $id), m::ERROR);
        } else {
            $ad->set_available($status, $_SESSION['userid']);
            m::add(sprintf(_('Successfully changed availability for the ad "%s"'), $ad->title), m::SUCCESS);
        }

        return $this->redirect(
            $this->generateUrl(
                'admin_ads',
                array(
                    'category' => $category,
                    'page'     => $page,
                    'filter'   => $filter,
                )
            )
        );
    }

    /**
     * Lists the available advertisements for the frontpage manager
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function contentProviderAction(Request $request)
    {
        $category = $request->query->filter('category', 'home', FILTER_SANITIZE_STRING);
        $page = $request->query->getDigits('page', 1);
        if ($category == 'home') {
            $category = 0;
        }
        $itemsPerPage = 8;

        $cm = new \ContentManager();

        // Get contents for this home
        $contentElementsInFrontpage  = $cm->getContentsIdsForHomepageOfCategory($category);

        // Fetching opinions
        $sqlExcludedAds = '';
        if (count($contentElementsInFrontpage) > 0) {
            $adsExcluded    = implode(', ', $contentElementsInFrontpage);
            $sqlExcludedAds = ' AND `pk_advertisement` NOT IN ('.$adsExcluded.')';
        }

        list($countAds, $ads) = $cm->getCountAndSlice(
            'Advertisement',
            null,
            'contents.available=1 AND in_litter != 1 AND type_advertisement = 37 '. $sqlExcludedAds,
            'ORDER BY created DESC ',
            $page,
            $itemsPerPage
        );

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
                    array('category' => $category,)
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
     * Builds the sql
     *
     * @param Request $request the request object
     * @param string $filter the sql filter to build the final filter
     *
     * @return Response the response object
     **/
    private function buildFilter($request, $filter)
    {
        $filters = array();
        $url     = array();

        $filters []= $filter;

        $definedFilters = $request->query->get('filter');

        $url []= 'category='.$request->query->getDigits('category', 0);

        if (isset($definedFilters['type_advertisement'])
           && ($definedFilters['type_advertisement'] >= 0)
        ) {
            $filters[] = '`type_advertisement`=' . intval($definedFilters['type_advertisement']);

            $url[] = 'filter[type_advertisement]=' . intval($definedFilters['type_advertisement']);
        }

        if (isset($definedFilters['available'])
           && ($definedFilters['available'] >= 0)
        ) {
            if ($definedFilters['available']==1) {
                $filters[] = '`available`=1';
            } else {
                $filters[] = '(`available`<>1 OR `available` IS NULL)';
            }

            $url[] = 'filter[available]=' . $definedFilters['available'];
        }

        if (isset($definedFilters['type'])
           && ($definedFilters['type'] >= 0)) {
            // with_script == 1 => is script banner, otherwise is a media banner
            if ($definedFilters['type']==1) {
                $filters[] = '`with_script`=1';
            } else {
                $filters[] = '(`with_script`<>1 OR `with_script` IS NULL)';
            }

            $url[] = 'filter[type]=' . $definedFilters['type'];
        }

        return array(
            implode(' AND ', $filters),
            implode('&', $url)
        );
    }

    /**
     * Handles and shows the advertisement configuration form
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function configAction(Request $request)
    {
        if ('POST' == $this->request->getMethod()) {

            $formValues = $this->get('request')->request;

            $settings = array(
                'ads_settings' => array(
                    'lifetime_cookie' => $formValues->getDigits('ads_settings_lifetime_cookie'),
                    'no_generics'      => $formValues->getDigits('ads_settings_no_generics'),
                )
            );

            foreach ($settings as $key => $value) {
                s::set($key, $value);
            }

            m::add(_('Settings saved successfully.'), m::SUCCESS);

            return $this->redirect($this->generateUrl('admin_ads_config'));
        } else {
            $configurationsKeys = array('ads_settings',);
            $configurations = s::get($configurationsKeys);

            return $this->render(
                'advertisement/config.tpl',
                array('configs'   => $configurations,)
            );
        }
    }
}
