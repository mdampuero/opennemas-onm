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
use Common\Core\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Handles the actions for the system information
 *
 * @package Backend_Controllers
 */
class SearchController extends Controller
{
    /**
     * Handles the search form and shows the search contents
     *
     * @Security("hasExtension('ADVANCED_SEARCH')
     *     and hasPermission('SEARCH_ADMIN')")
     */
    public function defaultAction()
    {
        $contentTypesAvailable = \ContentManager::getContentTypesFiltered();
        unset($contentTypesAvailable['comment']);

        $types = [
            [ 'name' => _('All'), 'value' => null ]
        ];

        foreach ($contentTypesAvailable as $key => $value) {
            $types[] = [ 'name' => _($value), 'value' => $key ];
        }

        return $this->render('search_advanced/list.tpl', ['types' => $types ]);
    }

    /**
     * Shows a list of contents that matches a search for content-providers
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("hasExtension('ADVANCED_SEARCH')")
     */
    public function contentProviderAction(Request $request)
    {
        $searchString = $request->query->filter('search_string', '', FILTER_SANITIZE_STRING);
        $page         = $request->query->getDigits('page', 1);
        $related      = (bool) $request->query->get('related', true);

        $this->get('core.locale')->setContext('frontend');

        $this->view->assign('related', $related);

        if (!empty($searchString)) {
            $fm     = $this->get('data.manager.filter');
            $tokens = $fm->set($searchString)->filter('tags')->get();
            $tokens = explode(',', $tokens);

            $er = $this->get('entity_repository');

            // Build field search with LIKE
            $fields = ['title'];
            $search = [];
            foreach ($fields as $field) {
                $searchChunk = [];
                foreach ($tokens as $token) {
                    $searchChunk[] = $field . " LIKE '%" . trim($token) . "%'";
                }

                $search[] = "(" . implode(' AND ', $searchChunk) . ") ";
            }

            //Clean the input words
            $tagsWords = $this->get('api.service.tag')
                ->getTagIdsFromStr($searchString);

            //Create the query if exist tagsWords
            if (!empty($tagsWords)) {
                $countTagsWords = count($tagsWords) - 1;
                $tagsWords      = implode(',', $tagsWords);
                $search[]       = ' pk_content in (SELECT contents_tags.content_id'
                . " FROM contents_tags WHERE contents_tags.tag_id IN ($tagsWords)"
                . ' GROUP BY contents_tags.content_id'
                . ' HAVING COUNT(contents_tags.tag_id) >'
                . $countTagsWords . ')';
            }

            // Final search
            $search = "(" . implode(' OR ', $search) . ")";

            // Complete where clause
            $criteria = ' in_litter = 0 AND content_status = 1 '
                . ' AND fk_content_type IN (1, 2, 4, 7, 9, 11, 12)'
                . ' AND ' . $search;

            $order = [ 'starttime' => 'desc' ];
            $total = true;

            $results = $er->findBy($criteria, $order, 8, $page, 0, $total);

            foreach ($results as $content) {
                $content->content_partial_path =
                    $content->content_type_name . '/content-provider/'
                    . $content->content_type_name . '.tpl';
            }

            // Build the pagination
            $pagination = $this->get('paginator')->get([
                'boundary'    => true,
                'directional' => true,
                'epp'         => 8,
                'page'        => $page,
                'total'       => $total,
                'route'       => [
                    'name'   => 'admin_search_content_provider',
                    'params' => [ 'search_string' => $searchString, 'related' => $related ]
                ],
            ]);

            $this->view->assign([
                'results'       => $results,
                'search_string' => $searchString,
                'pagination'    => $pagination
            ]);

            if ($related == true) {
                return $this->render(
                    'search_advanced/content-provider-related.tpl',
                    [
                        'contents'    => $results,
                        'contentType' => 'Content',
                    ]
                );
            } else {
                return $this->render(
                    'search_advanced/content-provider.tpl',
                    [
                        'contents'    => $results,
                        'contentType' => 'Content',
                    ]
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
}
