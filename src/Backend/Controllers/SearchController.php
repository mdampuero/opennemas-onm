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
namespace Backend\Controllers;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Onm\Framework\Controller\Controller;
use Onm\Settings as s;
use Onm\Module\ModuleManager as mod;

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
        $this->view = new \TemplateAdmin(TEMPLATE_ADMIN);
    }

    /**
     * Handles the search form and shows the search contents
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function defaultAction(Request $request)
    {
        $contentTypes = $this->getContentTypesFiltered();

        $searchString = $this->request->query->filter('search_string', null, FILTER_SANITIZE_STRING);
        $contentTypesSelected = $this->request->get('content_types', array());
        $page         = $this->request->query->filter('page', null, FILTER_VALIDATE_INT);

        $itemsPerPage = s::get('items_per_page') ?: 20;

        // If search string is empty skip executing some logic
        if (!empty($searchString)) {

            $htmlChecks     = null;
            $contentTypesChecked = $this->checkTypes($contentTypesSelected);
            $szTags         = trim($searchString);
            $objSearch      = new \cSearch();
            $contents   = $objSearch->SearchContentsSelectMerge(
                "contents.title as titule, contents.metadata, contents.slug,
                contents.description, contents.created, contents.pk_content as id,
                contents_categories.catName, contents_categories.pk_fk_content_category as category,
                content_types.title as type, contents.available, contents.content_status,
                contents.in_litter, content_types.name as content_type",
                $szTags,
                $contentTypesChecked,
                "pk_content = pk_fk_content AND fk_content_type = pk_content_type",
                "contents_categories, content_types",
                100
            );

            $szTagsArray  = explode(', ', \StringUtils::get_tags($szTags));

            foreach ($contents as &$content) {
                for ($ind=0; $ind < sizeof($szTagsArray); $ind++) {
                    $content['titule']   = \Onm\StringUtils::extStrIreplace(
                        $szTagsArray[$ind],
                        '<span class="highlighted">$1</span>',
                        $content['titule']
                    );
                    $content['metadata'] = \Onm\StringUtils::extStrIreplace(
                        $szTagsArray[$ind],
                        '<span class="highlighted">$1</span>',
                        $content['metadata']
                    );
                }
            }

            $this->view->assign(
                array(
                    'search_string'          => $searchString,
                    'contents'               => $contents,
                    'content_types'          => $contentTypes,
                    'content_types_selected' => $contentTypesSelected,
                )
            );
        }

        return $this->render(
            'search_advanced/index.tpl',
            array('arrayTypes' => $contentTypes)
        );
    }

    /**
     * Shows a list of contents that matches a search for content-providers
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function contentProviderAction(Request $request)
    {
        $searchString = $request->query->filter('search_string', '', FILTER_SANITIZE_STRING);
        $page         = $request->query->getDigits('page', 1);
        $related      = (bool) $request->query->get('related', true);

        $this->view->assign('related', $related);

        if (!empty($searchString)) {

            $searchStringArray = array_map(
                function ($element) {
                    return trim($element);
                },
                explode(',', $searchString)
            );

            $searcher    = \cSearch::getInstance();
            $matchString = '';

            foreach ($searchStringArray as $key) {
                $matchString[] = $searcher->defineMatchOfSentence($key);
            }

            $matchString = implode($matchString, ' AND ');

            $sql = "SELECT pk_content, fk_content_type FROM contents".
                  " WHERE contents.available=1 AND fk_content_type ".
                  " IN(1, 2, 3, 4, 7, 8, 9, 10, 11) AND ".$matchString.
                  " ORDER BY starttime DESC";

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

                $pagination = \Pager::factory(
                    array(
                        'mode'        => 'Sliding',
                        'perPage'     => s::get('items_per_page') ?: 20,
                        'append'      => false,
                        'path'        => '',
                        'delta'       => 1,
                        'clearIfVoid' => true,
                        'urlVar'      => 'page',
                        'totalItems'  => $resultSetSize,
                        'fileName'    => $this->generateUrl(
                            'admin_search_content_provider',
                            array('search_string' => $searchString, 'related' => $related)
                        ).'&page=%d',
                    )
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
                    )
                );

            } else {
                return $this->render(
                    'search_advanced/content-provider.tpl',
                    array(
                        'contents'    => $results,
                        'contentType' => 'Content',
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
     * Converts and filters a list of content types into a SQL sentence
     *
     * @param array $selected array of content types selected
     *
     * @return  string string with all the content types comma separated.
     */
    private function checkTypes($selected)
    {
        $contentTypes = \Content::getContentTypes();
        $szTypes =  '';
        foreach ($contentTypes as $contentType) {
            if ($contentType['name']== 'advertisement') {
                $contentType['name']= 'ads';
            }
            if ($contentType['name']== 'attachment') {
                $contentType['name']= 'file';
            }
            if ($contentType['name']== 'photo') {
                $contentType['name']= 'image';
            }
            if ($contentType['name']== 'static_page') {
                $contentType['name']= 'static_pages';
            }

            if (mod::moduleExists(strtoupper($contentType['name']).'_MANAGER')
                && mod::isActivated(strtoupper($contentType['name']).'_MANAGER')
                && in_array($contentType['name'], $selected)
            ) {
                $szTypes []= $contentType['name'];
            }
        }

        return implode(',', $szTypes);
    }

    /**
     * Returns the list of content types for the modules activated
     *
     * @return array the list of content types
     **/
    public function getContentTypesFiltered()
    {
        $contentTypes = \Content::getContentTypes();
        $contentTypesFiltered = array();

        foreach ($contentTypes as $contentType) {
            if (mod::moduleExists(strtoupper($contentType['name']).'_MANAGER')
                && mod::isActivated(strtoupper($contentType['name']).'_MANAGER')
            ) {
                $contentTypesFiltered [] = $contentType;
            }
        }

        return $contentTypesFiltered;
    }
}
