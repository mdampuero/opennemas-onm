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
use Onm\Message as m;

/**
 * Handles the actions for the system information
 *
 * @package Backend_Controllers
 **/
class FilesController extends Controller
{
    /**
     * Common code for all the actions
     *
     * @return void
     **/
    public function init()
    {
        //Check if module is activated in this onm instance
        \Onm\Module\ModuleManager::checkActivatedOrForward('FILE_MANAGER');

        $this->contentType = \ContentManager::getContentTypeIdFromName('attachment');
        $this->category    = $this->get('request')->query->filter('category', 'all', FILTER_SANITIZE_STRING);
        $this->ccm         = \ContentCategoryManager::get_instance();
        list($this->parentCategories, $this->subcat, $this->datos_cat) =
            $this->ccm->getArraysMenu($this->category, $this->contentType);

        $timezones = \DateTimeZone::listIdentifiers();
        $timezone  = new \DateTimeZone($timezones[s::get('time_zone', 'UTC')]);

        $this->view->assign(
            array(
                'subcat'       => $this->subcat,
                'allcategorys' => $this->parentCategories,
                'datos_cat'    => $this->datos_cat,
                'category'     => $this->category,
                'timezone'     => $timezone->getName()
            )
        );

        // Optimize  this crap from this ---------------------------------------
        $this->fileSavePath = INSTANCE_MEDIA_PATH.FILE_DIR;

        // Create folder if it doesn't exist
        if (!file_exists($this->fileSavePath)) {
            \FilesManager::createDirectory($this->fileSavePath);
        }
    }

    /**
     * Lists the files for a given category
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("has_role('FILE_ADMIN')")
     **/
    public function listAction(Request $request)
    {
        return $this->render('files/list.tpl');
    }

    /**
     * Shows the files in the widget
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("has_role('FILE_ADMIN')")
     **/
    public function widgetAction(Request $request)
    {
        return $this->render(
            'files/list.tpl',
            array(
                'category' => 'widget'
            )
        );
    }

    /**
     * Shows the file usage statistics
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("has_role('FILE_ADMIN')")
     **/
    public function statisticsAction(Request $request)
    {
        $cm               = new \ContentManager();
        $total_num_photos = 0;
        $files            = $size = $sub_size = $num_photos = array();
        $fullcat          = $this->ccm->order_by_posmenu($this->ccm->categories);

        $num_sub_photos = array();
        $sub_files = array();
        $aux_categories = array();

        foreach ($this->parentCategories as $k => $v) {
            $num_photos[$k] =
                $this->ccm->countContentByType($v->pk_content_category, $this->contentType);
            $total_num_photos += $num_photos[$k];

            $files[$v->pk_content_category] = $cm->find_all(
                'Attachment',
                'fk_content_type = 3 AND category = '.$v->pk_content_category,
                'ORDER BY created DESC'
            );

            if (!empty($fullcat)) {
                foreach ($fullcat as $child) {
                    if ($v->pk_content_category == $child->fk_content_category) {
                        $num_sub_photos[$k][$child->pk_content_category] =
                            $this->ccm->countContentByType($child->pk_content_category, 3);
                        $total_num_photos +=
                            $num_sub_photos[$k][$child->pk_content_category];
                        $sub_files[$child->pk_content_category][] =
                            $cm->find_all(
                                'Attachment',
                                'fk_content_type = 3 AND category = '.$child->pk_content_category,
                                'ORDER BY created DESC'
                            );
                        $aux_categories[] = $child->pk_content_category;
                        $sub_size[$k][$child->pk_content_category] = 0;
                        $this->view->assign('num_sub_photos', $num_sub_photos);
                    }
                }
            }
        }

        //Calculo del tamaño de los ficheros por categoria/subcategoria
        $i = 0;
        $total_size = 0;
        foreach ($files as $categories => $contenido) {
            $size[$i] = 0;
            if (!empty($contenido)) {
                foreach ($contenido as $value) {
                    if ($categories == $value->category) {
                        if (file_exists($this->fileSavePath.'/'.$value->path)) {
                            $size[$i] += filesize($this->fileSavePath.'/'.$value->path);
                        }
                    }
                }
            }
            $total_size += $size[$i];
            $i++;
        }
        if (!empty($parentCategories) && !empty($aux_categories)) {
            foreach ($parentCategories as $k => $v) {
                foreach ($aux_categories as $ind) {
                    if (!empty ($sub_files[$ind][0])) {
                        foreach ($sub_files[$ind][0] as $value) {
                            if ($v->pk_content_category == $ccm->get_id($ccm->get_father($value->catName))) {
                                if ($ccm->get_id($ccm->get_father($value->catName))) {
                                    $sub_size[$k][$ind] += filesize(MEDIA_PATH.'/'.FILE_DIR.'/'.$value->path);
                                }
                            }
                        }
                    }
                    if (isset($sub_size[$k][$ind])) {
                        $total_size += $sub_size[$k][$ind];
                    }
                }
            }
        }

        return $this->render(
            'files/statistics.tpl',
            array(
                'total_img'    => $total_num_photos,
                'total_size'   => $total_size,
                'size'         => $size,
                'sub_size'     => $sub_size,
                'num_photos'   => $num_photos,
                'categorys'    => $this->parentCategories,
                'subcategorys' => $this->subcat,
            )
        );
    }

    /**
     * Creates a file
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("has_role('FILE_CREATE')")
     **/
    public function createAction(Request $request)
    {
        if ('POST' != $request->getMethod()) {
            return $this->render('files/new.tpl', array('category' => $this->category,));
        } else {
            set_time_limit(0);

            if (isset($_FILES['path']['name'])
               && !empty($_FILES['path']['name'])
            ) {
                $date          = new \DateTime();
                $directoryDate = $date->format("/Y/m/d/");
                $basePath      = $this->fileSavePath.$directoryDate;

                $fileName      = \Onm\StringUtils::cleanFileName($_FILES['path']['name']);
                // Create folder if it doesn't exist
                if (!file_exists($basePath)) {
                    \FilesManager::createDirectory($basePath);
                }

                $data = array(
                    'title'        => $request->request->filter('title', null, FILTER_SANITIZE_STRING),
                    'path'         => $directoryDate.$fileName,
                    'category'     => $request->request->filter('category', null, FILTER_SANITIZE_STRING),
                    'available'    => 1,
                    'description'  => $request->request->filter('description', null, FILTER_SANITIZE_STRING),
                    'metadata'     => $request->request->filter('metadata', null, FILTER_SANITIZE_STRING),
                    'fk_publisher' => $_SESSION['userid'],
                );

                // Move uploaded file
                $uploadStatus = move_uploaded_file($_FILES['path']['tmp_name'], $basePath.$fileName);

                if ($uploadStatus !== false) {
                    $attachment = new \Attachment();
                    if ($attachment->create($data)) {
                        m::add(_("File created successfuly."), m::SUCCESS);

                    } else {
                        m::add(_('Unable to upload the file: A file with the same name already exists.'), m::ERROR);
                    }
                } else {
                    m::add(
                        _(
                            'There was an error while uploading the file. <br />'
                            .'Please, contact your system administrator.'
                        ),
                        m::ERROR
                    );
                }

                return $this->redirect(
                    $this->generateUrl('admin_files', array('category' => $this->category,))
                );

            } else {
                m::add(_('Please select a file before send the form'), m::ERROR);
            }
        }
    }

    /**
     * Shows file data given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("has_role('FILE_UPDATE')")
     **/
    public function showAction(Request $request)
    {
        $id      = $request->query->getDigits('id');
        $page    = $request->query->getDigits('page');

        $file = new \Attachment($id);

        // If the file doesn't exists redirect to the listing
        // and show error message
        if (is_null($file->pk_attachment)) {
            m::add(sprintf(_('Unable to find the file with the id "%s"'), $id));

            return $this->redirect($this->generateUrl('admin_files'));
        }

        // Show the
        return $this->render(
            'files/new.tpl',
            array(
                'attaches' => $file,
                'page'     => $page,
            )
        );
    }

    /**
     * Updates a file given the data sent by POST
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("has_role('FILE_UPDATE')")
     **/
    public function updateAction(Request $request)
    {
        $id = $request->request->getDigits('id');

        $file = new \Attachment($id);
          $data = array(
                'title'        => $request->request->filter('title', null, FILTER_SANITIZE_STRING),
                'category'     => $request->request->filter('category', null, FILTER_SANITIZE_STRING),
                'available'    => 1,
                'id'           => $id,
                'description'  => $request->request->filter('description', null, FILTER_SANITIZE_STRING),
                'metadata'     => $request->request->filter('metadata', null, FILTER_SANITIZE_STRING),
                'fk_publisher' => $_SESSION['userid'],
            );

        if ($file->update($data)) {
            m::add(sprintf(_('File information updated successfully.')), m::SUCCESS);
        } else {
            m::add(sprintf(_('There was a problem while saving the file information.')), m::ERROR);
        }

        return $this->redirect(
            $this->generateUrl('admin_file_show', array('id' => $id,))
        );
    }

    /**
     * Save positions for widget
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("has_role('FILE_ADMIN')")
     **/
    public function savePositionsAction(Request $request)
    {
        $request = $this->get('request');

        $positions = $request->request->get('positions');

        $result = true;
        if (isset($positions)
            && is_array($positions)
            && count($positions) > 0
        ) {
            $pos = 1;
            foreach ($positions as $id) {
                $file= new \Attachment($id);
                $result = $result && $file->setPosition($pos);

                $pos += 1;
            }
        }

        if ($result) {
            $msg = "<div class='alert alert-success'>"
                ._("Positions saved successfully.")
                .'<button data-dismiss="alert" class="close">×</button></div>';
        } else {
            $msg = "<div class='alert alert-error'>"
                ._("Unable to save the new positions. Please contact with your system administrator.")
                .'<button data-dismiss="alert" class="close">×</button></div>';
        }

        return new Response($msg);
    }


    /**
     * Return list of files for content rovider & related contents
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function contentListProviderAction(Request $request)
    {
        $category = $request->query->getDigits('category', 0);
        $page     = $request->query->getDigits('page', 1);
        $itemsPerPage = s::get('items_per_page') ?: 20;

        $cm = new  \ContentManager();

        list($countPolls, $polls) = $cm->getCountAndSlice(
            'Attachment',
            null,
            'contents.available=1',
            ' ORDER BY starttime DESC, contents.title ASC ',
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
                'totalItems'  => $countPolls,
                'fileName'    => $this->generateUrl(
                    'admin_files_content_provider',
                    array( 'category' => $category,)
                ).'&page=%d',
            )
        );

        return $this->render(
            'common/content_provider/_container-content-list.tpl',
            array(
                'contentType'           => 'Attachment',
                'contents'              => $polls,
                'contentTypeCategories' => $this->parentCategories,
                'category'              => $this->category,
                'pagination'            => $pagination->links,
                'contentProviderUrl'    => $this->generateUrl('admin_files_content_provider'),
            )
        );
    }
}
