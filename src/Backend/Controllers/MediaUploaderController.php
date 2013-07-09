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
     * Description of the action
     *
     * @return Response the response object
     **/
    public function showAction(Request $request)
    {
        $months = array();

        $GLOBALS['application']->conn->SetFetchMode(ADODB_FETCH_ASSOC);
        $rs = $GLOBALS['application']->conn->Execute(
            "SELECT DISTINCT(DATE_FORMAT(created, '%Y-%c')) as date_month
            FROM contents WHERE fk_content_type = 8 ORDER BY date_month"
        );

        $rawMonths = $rs->GetArray();
        foreach ($rawMonths as $value) {
            $months [$value['date_month']]= $value['date_month'];
        }

        return $this->render('media_uploader/media_uploader.tpl', array('months' => $months));
    }

    /**
     *
     *
     * @return void
     * @author
     **/
    public function browserAction(Request $request)
    {
        $searchString = $request->query->filter('search_string', '', FILTER_SANITIZE_STRING);
        $month = $request->query->filter('month', '');
        $page     = $request->query->getDigits('page', 1);

        $itemsPerPage = 16;

        if ($page == 1) {
            $limit    = "LIMIT {$itemsPerPage}";
        } else {
            $limit    = "LIMIT ".($page-1) * $itemsPerPage .', '.$itemsPerPage;
        }

        $sqlString = '';
        if (!empty($searchString)) {
            $sqlString = " AND description LIKE '%$searchString%' ";
        }

        $cm = new \ContentManager();
        $er = $this->get('entity_repository');

        $photos = $cm->find(
            'Photo',
            'contents.fk_content_type = 8 AND photos.media_type="image" '
            .'AND contents.content_status=1 '.$sqlString,
            'ORDER BY created DESC '.$limit
        );

        foreach ($photos as &$photo) {
            $photo->image_path = INSTANCE_MEDIA.'images'.$photo->path_file.'/'.$photo->name;
            $photo->thumbnail_url = $this->generateUrl(
                'asset_image',
                array(
                    'parameters' => 'zoomcrop,120,120,center,center',
                    'real_path' => INSTANCE_MEDIA.'images'.$photo->path_file.'/'.$photo->name
                )
            );
        }

        // var_dump($photos);die();
        $response = new Response();
        $response->setContent(json_encode($photos));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}
