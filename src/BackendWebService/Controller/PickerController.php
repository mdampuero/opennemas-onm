<?php

namespace BackendWebService\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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
        $contentTypes = $request->query->filter('content_type_name', null, FILTER_SANITIZE_STRING);
        $category     = $request->query->filter('category', null, FILTER_SANITIZE_STRING);


        $filter = [ "in_litter = 0" ];
        $order = [
            'created' => 'desc'
        ];

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
            $filter[] = "DATE_FORMAT(created, '%Y-%c') = '$date'";
        }

        if (!empty($title)) {
            $filter[] = "(description LIKE '%$title%' OR title LIKE '%$title%')";
        }

        if (!empty($category)) {
            $filter[] = "(category_name = '$category')";
        }

        $em = $this->get('entity_repository');

        $filter = implode(' AND ', $filter);

        $results = $em->findBy($filter, $order, $epp, $page);
        $total   = $em->countBy($filter);

        return new JsonResponse(
            array(
                'epp'     => $epp,
                'page'    => $page,
                'results' => $results,
                'total'   => $total,
            )
        );
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
        $sql         = "UPDATE contents SET `description`=? WHERE pk_content=?";

        $conn = $this->get('dbal_connection');

        try {
            $conn->executeUpdate($sql, array($description, $id));
            return new JsonResponse();
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
        $years = array();

        $conn = $this->get('dbal_connection');

        $results = $conn->fetchAll(
            "SELECT DISTINCT(DATE_FORMAT(created, '%Y-%c')) as date_month
            FROM contents WHERE fk_content_type = 8 ORDER BY date_month DESC"
        );

        foreach ($results as $value) {
            $date = \DateTime::createFromFormat('Y-n', $value['date_month']);
            $fmt = new \IntlDateFormatter(CURRENT_LANGUAGE, null, null, null, null, 'MMMM');

            $years[$date->format('Y')]['name'] = $date->format('Y');
            $years[$date->format('Y')]['months'][]= array(
                'name' => $fmt->format($date),
                'value' => $value['date_month']
            );
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

        $ccm = \ContentCategoryManager::get_instance();

        return [
            'allCategories'       => _('All categories'),
            'allContentTypes'     => _('All content types'),
            'allMonths'           => _('All months'),
            'category'            => _('Category'),
            'categories'          => $ccm->find(),
            'contentsInFrontpage' => _('Contents in frontpage'),
            'contentTypes'        => $contentTypesFiltered,
            'created'             => _('Created'),
            'dates'               => $this->getDates(),
            'description'         => _('Description'),
            'header'              => _('Pick the item to insert'),
            'insert'              => _('Insert'),
            'itemDetails'         => _('Item details'),
            'itemsSelected'       => _('items selected'),
            'menuItem'            => _('Browse'),
            'search'              => _('Search by name'),
            'title'               => _('Title'),
            'thumbnailDetails'    => _('Thumbnail details'),
        ];
    }

    /**
     * Returns the list of contents in frontpage.
     *
     * @return JsonResponse The response object.
     */
    private function listFrontpageContents()
    {
        $cm = new \ContentManager();

        // Get contents for this home
        $results = $cm->getContentsForHomepageOfCategory(0);

        $results = array_filter($results, function($value) {
            return $value->content_type_name != 'widget';
        });

        return new JsonResponse(
            array(
                'epp'     => count($results),
                'page'    => 1,
                'results' => $results,
                'total'   => count($results)
            )
        );
    }

    /**
     * Returns the translated strings for the upload mode.
     *
     * @return array The translated strings.
     */
    private function uploadMode()
    {
        return [
            'header'      => _('Upload new media'),
            'menuItem'    => _('Upload'),
            'add'         => _('Add files...'),
            'explanation' => _('Drop files anywhere here to upload or click on the "Add Files..." button above.'),
        ];
    }
}
