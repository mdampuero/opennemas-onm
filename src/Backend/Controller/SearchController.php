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
     * Common code for all the actions
     *
     * @return void
     **/
    public function init()
    {
        //Check if module is activated in this onm instance
        \Onm\Module\ModuleManager::checkActivatedOrForward('ADVANCED_SEARCH');
    }

    /**
     * Handles the search form and shows the search contents
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("has_role('SEARCH_ADMIN')")
     **/
    public function defaultAction(Request $request)
    {
        $contentTypesAvailable = $this->getContentTypesFiltered();
        unset($contentTypesAvailable['comment']);

        return $this->render(
            'search_advanced/index.tpl',
            array('content_types' => $contentTypesAvailable)
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

            $szWhere = '';
            if (count($tokens) > 0) {
                foreach ($tokens as &$meta) {
                    $szWhere []= "`metadata` LIKE '%".trim($meta)."%'";
                }
                $szWhere = "AND  (".implode(' AND ', $szWhere).") ";
            }

            $sql = "SELECT pk_content, fk_content_type FROM contents"
                  ." WHERE contents.content_status=1 "
                  ." AND fk_content_type IN (1, 2, 4, 7, 9, 10, 11, 12) "
                  .$szWhere
                  ." ORDER BY starttime DESC";

            $rs  = $GLOBALS['application']->conn->GetArray($sql);

            $results = array();
            if ($rs !== false) {
                $resultSetSize = count($rs);
                $rs            = array_splice($rs, ($page-1)*9, 9);

                foreach ($rs as $content) {
                    $content = new \Content($content['pk_content']);
                    $content->content_partial_path =
                        $content->content_type_name.'/content-provider/'.$content->content_type_name.'.tpl';
                    $results[] = $content;
                }

                // Build the pager
                $pagination = \Onm\Pager\Slider::create(
                    $resultSetSize,
                    s::get('items_per_page') ?: 20,
                    $this->generateUrl(
                        'admin_search_content_provider',
                        array('search_string' => $searchString, 'related' => $related)
                    ).'&page=%d'
                );
                $this->view->assign('pagination', $pagination->links);
            }
            $this->view->assign('results', $results);

            $this->view->assign('search_string', $searchString);

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
