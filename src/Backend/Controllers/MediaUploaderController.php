<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Backend\Controllers;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Onm\Framework\Controller\Controller;
use Onm\Message as m;
use Onm\Settings as s;

/**
 * Handles the actions for media uploader
 *
 * @package Backend_Controllers
 **/
class MediaUploaderController extends Controller
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
     * Shows the media uploader
     *
     * @return Response the response object
     **/
    public function showAction(Request $request)
    {
        if ($request->getMethod() == 'POST') {
            return new Response(var_export($request->files));
        }

        return $this->render('media_uploader/index.tpl');
    }

    /**
     * Returns the available months registered in images
     *
     * @return Response the object response
     **/
    public function getMonthsAction(Request $request)
    {
        $years = array();

        $GLOBALS['application']->conn->SetFetchMode(ADODB_FETCH_ASSOC);
        $rs = $GLOBALS['application']->conn->Execute(
            "SELECT DISTINCT(DATE_FORMAT(created, '%Y-%c')) as date_month
            FROM contents WHERE fk_content_type = 8 ORDER BY date_month DESC"
        );

        $rawMonths = $rs->GetArray();
        foreach ($rawMonths as $value) {
            $date = \DateTime::createFromFormat('Y-n', $value['date_month']);
            $fmt = new \IntlDateFormatter(CURRENT_LANGUAGE, null, null, null, null, 'MMMM');

            $years[$date->format('Y')]['name'] = $date->format('Y');
            $years[$date->format('Y')]['months'] []= array('name' => $fmt->format($date), 'value' => $value['date_month']);
        }

        $years = array_values($years);

        $response = new Response();
        $response->setContent(json_encode($years));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * Performs searches through images and returns an array of objects
     *
     * @return Response the response object
     **/
    public function browserAction(Request $request)
    {
        $id           = $request->query->getDigits('id', null);
        $searchString = $request->query->filter('search_string', '', FILTER_SANITIZE_STRING);
        $month        = $request->query->filter('month', '', FILTER_SANITIZE_STRING);
        $filterBy     = $request->query->filter('media_type', '', FILTER_SANITIZE_STRING);
        $page         = $request->query->getDigits('page', 1);

        $itemsPerPage = 16;

        if ($page == 1) {
            $limit    = "LIMIT {$itemsPerPage}";
        } else {
            $limit    = "LIMIT ".($page-1) * $itemsPerPage .', '.$itemsPerPage;
        }

        $filter = '';
        if (!empty($month)) {
            $filter .= " AND DATE_FORMAT(created, '%Y-%c') = '$month'";
        }

        if (!empty($id)) {
            $filter .= " AND pk_content = ".$id;
        }

        $category = null;
        if ($filterBy == 'ads') {
            $category = 2;
        }

        if (!empty($searchString)) {
            $filter .= " AND (description LIKE '%$searchString%' OR title LIKE '%$searchString%') ";
        }

        $cm = new \ContentManager();
        $er = $this->get('entity_repository');

        $filter = 'contents.fk_content_type = 8 AND photos.media_type="image" '
            .'AND contents.content_status=1 '.$filter;
        if (is_null($category)) {
            $photos = $cm->find(
                'Photo',
                $filter,
                'ORDER BY created DESC '.$limit
            );
        } else {
            $photos = $cm->find_by_category(
                'Photo',
                $category,
                $filter,
                'ORDER BY created DESC '.$limit
            );
        }

        foreach ($photos as &$photo) {
            $photo->image_path = INSTANCE_MEDIA.'images'.$photo->path_file.'/'.$photo->name;
            $photo->crop_thumbnail_url = $this->generateUrl(
                'asset_image',
                array(
                    'parameters' => 'zoomcrop,120,120,center,center',
                    'real_path' => INSTANCE_MEDIA.'images'.$photo->path_file.'/'.$photo->name
                )
            );
            $photo->thumbnail_url = $this->generateUrl(
                'asset_image',
                array(
                    'parameters' => 'thumbnail,300,300',
                    'real_path' => INSTANCE_MEDIA.'images'.$photo->path_file.'/'.$photo->name
                )
            );
        }

        $response = new Response();
        $response->setContent(json_encode($photos));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * Updates an image description
     *
     *
     * @return Response the response object
     **/
    public function updateContentAction(Request $request)
    {
        $id = $request->request->getDigits('content_id');
        $description = $request->request->filter('description', '', FILTER_SANITIZE_STRING);

        $rs = $GLOBALS['application']->conn->Execute("UPDATE contents SET `description`=? WHERE pk_content=?", array($description, $id));

        if ($rs) {
            $photo = new \Photo($id);
            $photo->crop_thumbnail_url = $this->generateUrl(
                'asset_image',
                array(
                    'parameters' => 'zoomcrop,120,120,center,center',
                    'real_path' => INSTANCE_MEDIA.'images'.$photo->path_file.'/'.$photo->name
                )
            );
            $photo->thumbnail_url = $this->generateUrl(
                'asset_image',
                array(
                    'parameters' => 'thumbnail,300,300',
                    'real_path' => INSTANCE_MEDIA.'images'.$photo->path_file.'/'.$photo->name
                )
            );
            return new Response(json_encode($photo));
        } else {
            return new Response('error while saving', 500);
        }
    }
}
