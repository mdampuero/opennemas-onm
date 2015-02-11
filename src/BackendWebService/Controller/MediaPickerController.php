<?php

namespace BackendWebService\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class MediaPickerController extends Controller
{
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
        return [
            'allMonths'     => _('All months'),
            'details'       => _('Thumbnail details'),
            'description'   => _('Description'),
            'header'        => _('Pick the item to insert'),
            'insert'        => _('Insert'),
            'itemsSelected' => _('items selected'),
            'menuItem'      => _('Browser'),
            'dates'         => $this->getDates(),
            'search'        => _('Search by name'),
        ];
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
