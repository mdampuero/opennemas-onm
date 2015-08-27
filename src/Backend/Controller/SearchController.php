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
class SearchController extends Controller
{
    /**
     * Handles the search form and shows the search contents
     *
     * @return void
     *
     * @Security("has_role('SEARCH_ADMIN')")
     *
     * @CheckModuleAccess(module="ADVANCED_SEARCH")
     **/
    public function defaultAction()
    {
        $contentTypesAvailable = $this->getContentTypesFiltered();
        unset($contentTypesAvailable['comment']);

        $types = [
            [ 'name' => _('All'), 'value' => -1 ]
        ];

        foreach ($contentTypesAvailable as $key => $value) {
            $types[] = [ 'name' => _($value), 'value' => $key ];
        }

        return $this->render(
            'search_advanced/list.tpl',
            array('types' => $types)
        );
    }

    /**
     * Shows a list of contents that matches a search for content-providers
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * #@Security("has_role('SEARCH_ADMIN')")
     *
     * @CheckModuleAccess(module="ADVANCED_SEARCH")
     **/
    public function contentProviderAction(Request $request)
    {
        $searchString = $request->query->filter('search_string', '', FILTER_SANITIZE_STRING);
        $page         = $request->query->getDigits('page', 1);
        $related      = (bool) $request->query->get('related', true);

        $this->view->assign('related', $related);

        if (!empty($searchString)) {
            $tokens = \Onm\StringUtils::getTags($searchString);
            $tokens = explode(', ', $tokens);

            $er = $this->get('entity_repository');

            // Build field search with LIKE
            $fields = ['metadata', 'title'];
            $search = '';
            foreach ($fields as $field) {
                $searchChunk = '';
                foreach ($tokens as $token) {
                    $searchChunk []= $field." LIKE '%".trim($token)."%'";
                }
                $search []= "(".implode(' AND ', $searchChunk).") ";
            }

            // Final search
            $search = "(".implode(' OR ', $search).")";

            // Complete where clause
            $criteria = ' in_litter = 0 AND content_status = 1 '.
                        ' AND fk_content_type IN (1, 2, 4, 7, 9, 10, 11, 12)'.
                        ' AND '.$search;

            $order    = [ 'starttime' => 'desc' ];

            $results = $er->findBy($criteria, $order, 8, $page);
            $total = $er->countBy($criteria);

            foreach ($results as $content) {
                $content->content_partial_path =
                    $content->content_type_name.'/content-provider/'.
                    $content->content_type_name.'.tpl';
            }

            // Build the pager
            $pagination = $this->get('paginator')->create([
                'spacesBeforeSeparator' => 0,
                'spacesAfterSeparator'  => 0,
                'firstLinkTitle'        => '',
                'lastLinkTitle'         => '',
                'separator'             => '',
                'firstPagePre'          => '',
                'firstPageText'         => '',
                'firstPagePost'         => '',
                'lastPagePre'           => '',
                'lastPageText'          => '',
                'lastPagePost'          => '',
                'prevImg'               => _('Previous'),
                'nextImg'               => _('Next'),
                'elements_per_page'     => 8,
                'total_items'           => $total,
                'delta'                 => 1,
                'base_url'              => $this->generateUrl(
                    'admin_search_content_provider',
                    array('search_string' => $searchString, 'related' => $related)
                ).'&page=%d',
            ]);

            $this->view->assign('pagination', $pagination->links);

            $this->view->assign([
                'results' => $results,
                'search_string' => $searchString
            ]);

            if ($related == true) {
                return $this->render(
                    'search_advanced/content-provider-related.tpl',
                    array(
                        'contents'    => $results,
                        'contentType' => 'Content',
                        'pager'       => $pagination,
                    )
                );

            } else {
                return $this->render(
                    'search_advanced/content-provider.tpl',
                    array(
                        'contents'    => $results,
                        'contentType' => 'Content',
                        'pager'       => $pagination,
                    )
                );
            }
        } else {
            if (!is_null($related)) {
                return $this->render('search_advanced/content-provider-related.tpl');
            } else {
                return $this->render('search_advanced/content-provider.tpl');
            }
        }
    }

    /**
     * Returns the list of content types for the modules activated
     *
     * @return array the list of content types
     **/
    private function getContentTypesFiltered()
    {
        $contentTypes = \ContentManager::getContentTypes();
        $contentTypesFiltered = array();

        foreach ($contentTypes as $contentType) {
            switch ($contentType['name']) {
                case 'advertisement':
                    $moduleName = 'ads';
                    break;
                case 'attachment':
                    $moduleName = 'file';
                    break;
                case 'photo':
                    $moduleName = 'image';
                    break;
                case 'static_page':
                    $moduleName = 'static_pages';
                    break;
                default:
                    $moduleName = $contentType['name'];
                    break;
            }
            $moduleName = strtoupper($moduleName.'_MANAGER');

            if (\Onm\Module\ModuleManager::moduleExists($moduleName) &&
                \Onm\Module\ModuleManager::isActivated($moduleName)
            ) {
                $contentTypesFiltered[$contentType['name']] = $contentType['title'];
            }
        }

        return $contentTypesFiltered;
    }
}
