<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace BackendWebService\Controller;

use Common\Core\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class PickerController extends Controller
{
    /**
     * Returns the list of media items.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     */
    public function listAction(Request $request)
    {
        $epp          = $request->query->getDigits('epp', 1);
        $date         = $request->query->filter('date', '', FILTER_SANITIZE_STRING);
        $page         = $request->query->getDigits('page', 1);
        $title        = $request->query->filter('title', '', FILTER_SANITIZE_STRING);
        $from         = $request->query->filter('from', '', FILTER_SANITIZE_STRING);
        $to           = $request->query->filter('to', '', FILTER_SANITIZE_STRING);
        $contentTypes = $request->query->filter('content_type_name', [], FILTER_SANITIZE_STRING);
        $category     = $request->query->filter('category', null, FILTER_SANITIZE_STRING);

        $filter = [ "in_litter = 0" ];

        if (!empty($contentTypes)) {
            if ($contentTypes[0] == 'contents-in-frontpage') {
                return $this->listFrontpageContents();
            }

            $types = [];
            foreach ($contentTypes as $type) {
                $types[] = "content_type_name = '$type'";
            }

            $filter[] = '(' . implode(' OR ', $types) . ')';
        }

        if (!empty($date)) {
            $filter[] = "DATE_FORMAT(created, '%Y-%m') = '$date'";
        }

        if (!empty($from)) {
            $filter[] = "created >= '$from 00:00:00'";
        }

        if (!empty($to)) {
            $filter[] = "created <= '$to 00:00:00'";
        }

        if (!empty($title)) {
            $titleSql = "(description LIKE '%$title%' OR title LIKE '%$title%'";
            $tagSearcheableWord = $this
                ->get('api.service.tag')->createSearchableWord($title);
            if (!empty($tagSearcheableWord)) {
                $titleSql .= ' OR EXISTS(SELECT 1 FROM tags' .
                    ' INNER JOIN contents_tags ON contents_tags.tag_id = tags.id' .
                    " WHERE contents_tags.content_id = contents.pk_content AND tags.slug LIKE '%$tagSearcheableWord%')";
            }
            $titleSql .= ')';
            $filter[] = $titleSql;
        }

        if (!empty($category)) {
            $filter[] = "contents_categories.pk_fk_content_category = $category";
        }

        $filter[] = "in_litter != 1";

        $em = $this->get('entity_repository');

        $filter = implode(' AND ', $filter);
        $query  = "FROM contents  WHERE " . $filter;

        if (!in_array('photo', $contentTypes)) {
            $query = "FROM contents LEFT JOIN contents_categories ON contents_categories.pk_fk_content = "
                . "contents.pk_content WHERE " . $filter;
        }

        $contentMap = $em->dbConn->executeQuery(
            "SELECT content_type_name, pk_content " . $query . " ORDER BY CREATED DESC LIMIT " .
            (($page - 1) * $epp) . ", " . $epp
        )->fetchAll();
        $contentMap = array_map(function ($row) {
            return [$row['content_type_name'], $row['pk_content']];
        }, $contentMap);
        $results = $em->findMulti($contentMap);

        $languageData = $this->getLocaleData('frontend');
        $fm           = $this->get('data.manager.filter');
        $results      = $fm->set($results)->filter('localize', [
            'keys'      => ['title', 'name', 'description'],
            'locale'    => $languageData['default']
        ])->get();
        $results      = \Onm\StringUtils::convertToUtf8($results);

        $this->get('core.locale')->setContext('frontend');

        $contentMap = $em->dbConn->executeQuery("SELECT count(1) as resultNumber " . $query)->fetchAll();
        $total      = 0;
        if (count($contentMap) > 0) {
            $total = $contentMap[0]['resultNumber'];
        }

        return new JsonResponse([
            'epp'     => $epp,
            'page'    => $page,
            'results' => $results,
            'total'   => $total,
        ]);
    }

    /**
     * Returns the parameters needed by the media picker.
     *
     * @param  array $mode The current media picker enabled modes.
     *
     * @return JsonResponse The response object.
     */
    public function pickerAction(Request $request)
    {
        $mode = $request->get('mode');

        if (!is_array($mode)) {
            return new JsonResponse('Invalid mode', 400);
        }

        $params = [];

        if (in_array('explore', $mode)) {
            $params['explore'] = $this->exploreMode();
        }

        if (in_array('upload', $mode)) {
            $params['upload'] = $this->uploadMode();
        }

        return new JsonResponse($params);
    }

    /**
     * Saved the description for a content.
     *
     * @param Request $request The request object.
     * @param integer $id      The content id.
     *
     * @return JsonResponse The response object.
     */
    public function saveDescriptionAction(Request $request, $id)
    {
        $description = $request->request->filter('description', '', FILTER_SANITIZE_STRING);

        try {
            $photo = $this->get('entity_repository')->find('Photo', $id);

            // Check if the photo exists
            if (!is_object($photo)) {
                return new JsonResponse('Photo doesnt exists', 404);
            }

            $this->get('orm.manager')->getConnection('instance')->executeUpdate(
                "UPDATE contents SET `description`=? WHERE pk_content=?",
                [ $description, $id ]
            );

            // Invalidate the cache for the photo
            dispatchEventWithParams('content.update', [ 'content' => $photo ]);
            dispatchEventWithParams(
                $photo->content_type_name . '.update',
                [ 'content' => $photo ]
            );

            return new JsonResponse('ok');
        } catch (\Exception $e) {
            return new JsonResponse($e->getMessage(), 500);
        }
    }

    /**
     * Returns the available months registered in images.
     *
     * @return JsonResponse the object response
     */
    private function getDates()
    {
        $years = [];

        $conn = $this->get('orm.manager')->getConnection('instance');

        $results = $conn->fetchAll(
            "SELECT DISTINCT(DATE_FORMAT(created, '%Y-%m')) as date_month FROM contents
            WHERE fk_content_type = 8 AND created IS NOT NULL ORDER BY date_month DESC"
        );

        foreach ($results as $value) {
            $date = \DateTime::createFromFormat('Y-n', $value['date_month']);
            $fmt  = new \IntlDateFormatter(CURRENT_LANGUAGE, null, null, null, null, 'MMMM');

            if (!is_null($fmt)) {
                $years[$date->format('Y')]['name']     = $date->format('Y');
                $years[$date->format('Y')]['months'][] = [
                    'name'  => ucfirst($fmt->format($date)),
                    'value' => $value['date_month']
                ];
            }
        }

        return array_values($years);
    }

    /**
     * Returns the translated strings for the explore mode.
     *
     * @return array The translated strings.
     */
    private function exploreMode()
    {
        $contentTypes         = \ContentManager::getContentTypes();
        $contentTypesFiltered = [ [
            'name'  => null,
            'title' => _('All content types'),
        ], [
            'name'  => 'contents-in-frontpage',
            'title' => _('Contents in frontpage'),
        ] ];

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

            $moduleName = strtoupper($moduleName . '_MANAGER');

            if ($this->get('core.security')->hasExtension($moduleName)) {
                $contentTypesFiltered[] = [
                    'name'  => $contentType['name'],
                    'title' => $contentType['title']
                ];
            }
        }

        $fm         = $this->get('data.manager.filter');
        $categories = $this->get('orm.manager')->getRepository('Category')
            ->findBy('internal_category = 1 order by title asc');

        $cleanCategories = [ [
            'pk_content_category' => null,
            'name'                => null,
            'fk_content_category' => 0,
            'title'               => _('All categories'),
        ] ];

        foreach ($categories as $category) {
            $cleanCategories[] = [
                'pk_content_category' => $category->pk_content_category,
                'name'                => $category->name,
                'fk_content_category' => $category->fk_content_category,
                'title'               => $fm->set($category->title)
                    ->filter('localize')
                    ->get(),
            ];
        }

        $categories = $cleanCategories;

        return [
            'allMonths'           => _('All months'),
            'category'            => _('Category'),
            'categories'          => $categories,
            'contentTypes'        => $contentTypesFiltered,
            'created'             => _('Created'),
            'dates'               => $this->getDates(),
            'description'         => _('Description'),
            'from'                => _('From'),
            'header'              => _('Pick the item to insert'),
            'insert'              => _('Insert'),
            'itemDetails'         => _('Item details'),
            'itemsSelected'       => _('items selected'),
            'loadMore'            => _('Load more'),
            'loading'             => _('Loading...'),
            'menuItem'            => _('Browse'),
            'search'              => _('Search by name'),
            'title'               => _('Title'),
            'to'                  => _('To'),
            'thumbnailDetails'    => _('Thumbnail details'),
            'enhance'             => _('Enhance'),
        ];
    }

    /**
     * Returns the list of contents in frontpage.
     *
     * @return JsonResponse The response object.
     */
    private function listFrontpageContents()
    {
        // Get contents for this home
        list($frontpageVersion, $contentPositions, $results) =
            $this->get('api.service.frontpage_version')
                ->getPublicContentsForFrontpageData(0);

        $results = array_filter($results, function ($value) {
            return $value->content_type_name != 'widget';
        });

        $results = \Onm\StringUtils::convertToUtf8($results);

        $this->get('core.locale')->setContext('frontend');

        return new JsonResponse([
            'epp'     => count($results),
            'page'    => 1,
            'results' => $results,
            'total'   => count($results)
        ]);
    }

    /**
     * Returns the translated strings for the upload mode.
     *
     * @return array The translated strings.
     */
    private function uploadMode()
    {
        return [
            'add'         => _('Add files...'),
            'click'       => _('Click here to upload'),
            'drop'        => _('Drop files or click here'),
            'explanation' => _('Drop files anywhere here to upload or click on the "Add Files..." button above.'),
            'header'      => _('Upload new media'),
            'invalid'     => _('This file type is not supported'),
            'menuItem'    => _('Upload'),
            'upload'      => _('to upload')
        ];
    }
}
