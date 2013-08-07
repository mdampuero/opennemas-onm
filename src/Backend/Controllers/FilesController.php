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

        $this->checkAclOrForward('FILE_ADMIN');

        $request = $this->request;

        $this->contentType = \ContentManager::getContentTypeIdFromName('attachment');
        $this->category    = $request->query->filter('category', 'all', FILTER_SANITIZE_STRING);
        $this->ccm         = \ContentCategoryManager::get_instance();
        list($this->parentCategories, $this->subcat, $this->datos_cat) =
            $this->ccm->getArraysMenu($this->category, $this->contentType);

        $this->view->assign(
            array(
                'subcat'       => $this->subcat,
                'allcategorys' => $this->parentCategories,
                'datos_cat'    => $this->datos_cat,
                'category'     => $this->category,
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
     **/
    public function listAction(Request $request)
    {
        $cm           = new \ContentManager();
        $itemsPerPage = s::get('items_per_page');

        $page     = $request->query->getDigits('page', 1);
        $listingStatus   = $request->query->getDigits('listing-status');

        if (empty($page)) {
            $limit = "LIMIT ".($itemsPerPage+1);
        } else {
            $limit = "LIMIT ".($page-1) * $itemsPerPage .', '.$itemsPerPage;
        }

        if ($this->category == 'all' || empty($this->category)) {
            $categoryForLimit = null;
        } else {
            $categoryForLimit = $this->category;
        }

        $filter = ' contents.in_litter != 1 ';
        if (($listingStatus != '') && ($listingStatus != null)) {
            $filter .= ' AND contents.available = '. $listingStatus;
        }

        list($filesCount, $files) = $cm->getCountAndSlice(
            'attachment',
            $categoryForLimit,
            $filter,
            'ORDER BY created DESC',
            $page,
            $itemsPerPage
        );

        if ($filesCount > 0) {
            foreach ($files as &$file) {
                $file->category_name  = $this->ccm->get_name($file->category);
                $file->category_title = $this->ccm->get_title($file->category_name);
            }
        }

         // Build the pager
        $pagination = \Pager::factory(
            array(
                'mode'        => 'Sliding',
                'perPage'     => $itemsPerPage,
                'append'      => false,
                'path'        => '',
                'delta'       => 1,
                'clearIfVoid' => true,
                'urlVar'      => 'page',
                'totalItems'  => $filesCount,
                'fileName'    => $this->generateUrl(
                    'admin_files',
                    array('category' => $this->category)
                ).'&page=%d',
            )
        );

        $this->view->assign(
            array(
                'listingStatus' => $listingStatus,
                'pagination'    => $pagination,
                'attaches'      => $files,
                'page'          => $page,
            )
        );

        return $this->render('files/list.tpl');
    }

    /**
     * Shows the files in the widget
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function widgetAction(Request $request)
    {
        $request = $this->request;
        $cm      = new \ContentManager();
        $category = $request->query->filter('category', 'widget', FILTER_SANITIZE_STRING);
        $page     = $request->query->getDigits('page', 1);

        $files = $cm->find_all(
            'Attachment',
            'in_home =1',
            'ORDER BY created DESC'
        );

        if (!empty($files)) {
            foreach ($files as &$file) {
                $file->category_name  = $this->ccm->get_name($file->category);
                $file->category_title = $this->ccm->get_title($file->category_name);
            }
        }

        return $this->render(
            'files/list.tpl',
            array(
                'attaches' => $files,
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
     **/
    public function statisticsAction(Request $request)
    {
        $nameCategory     = 'GLOBAL';
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
     **/
    public function createAction(Request $request)
    {
        $this->checkAclOrForward('FILE_CREATE');

        $request = $this->request;

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

                $fileType      = $_FILES['path']['type'];
                $fileSize      = $_FILES['path']['size'];
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
                    $this->generateUrl(
                        'admin_files',
                        array(
                            'category' => $this->category,
                        )
                    )
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
     **/
    public function showAction(Request $request)
    {
        $this->checkAclOrForward('FILE_UPDATE');

        $request = $this->request;
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
     **/
    public function updateAction(Request $request)
    {
        $this->checkAclOrForward('FILE_UPDATE');

        $request = $this->request;
        $id      = $request->request->getDigits('id');

        $category = $request->request->filter('category', null, FILTER_SANITIZE_STRING);
        $page     = $request->request->getDigits('page', 1);

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
            m::add(sprintf(_('File information updated successfuly.')), m::SUCCESS);
        } else {
            m::add(sprintf(_('There was a problem while saving the file information.')), m::ERROR);
        }

        return $this->redirect(
            $this->generateUrl(
                'admin_files_show',
                array(
                    'id' => $id,
                )
            )
        );
    }

    /**
     * Deletes a file given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function deleteAction(Request $request)
    {
        $this->checkAclOrForward('FILE_DELETE');

        $request = $this->request;
        $id      = $request->query->getDigits('id');

        if (is_null($id)) {
            m::add(sprintf(_("Unable to find the file with the id '%d'."), $id), m::ERROR);

            return $this->redirect($this->generateUrl('admin_files'));
        }

        $file = new \Attachment($id);

        //Delete relations
        $rel= new \RelatedContent();
        $rel->deleteAll($id);

        $file->delete($id, $_SESSION['userid']);
        m::add(sprintf(_("File with id '%d' deleted successfuly."), $id), m::SUCCESS);

        return $this->redirect(
            $this->generateUrl(
                'admin_files',
                array(
                    'category' => $this->category
                )
            )
        );
    }

    /**
     * Toggles the content favorite state given the content id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function toggleFavoriteAction(Request $request)
    {
        $this->checkAclOrForward('FILE_AVAILABLE');

        $request = $this->request;
        $id      = $request->query->getDigits('id');
        $page    = $request->query->getDigits('page', 1);
        $status  = $request->query->getDigits('status', 0);

        $file   = new \Attachment($id);

        if (!is_null($file->id)) {
            if ($file->available == 1) {
                $file->set_favorite($status);
            } else {
                m::add(_("This file is not published so you can't define it as favorite."));
            }
        } else {
            m::add(sprintf(_("Unable to find the file with id '%d'"), $id), m::ERROR);
        }

        return $this->redirect(
            $this->generateUrl(
                'admin_files',
                array(
                    'category' => $this->category,
                    'page'     => $page,
                )
            )
        );
    }

    /**
     * Toggles the in home state given the content id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function toggleInHomeAction(Request $request)
    {
        $this->checkAclOrForward('FILE_AVAILABLE');

        $request = $this->request;
        $id      = $request->query->getDigits('id');
        $page    = $request->query->getDigits('page', 1);
        $status  = $request->query->getDigits('status', 0);

        $file = new \Attachment($id);

        if (!is_null($file->id)) {
            if ($file->available == 1) {
                $file->set_inhome($status, $_SESSION['userid']);
            } else {
                m::add(_("This file is not published so you can't define it as widget home content."));
            }
        } else {
            m::add(sprintf(_("Unable to find the file with id '%d'"), $id), m::ERROR);
        }

        return $this->redirect(
            $this->generateUrl(
                'admin_files',
                array(
                    'page' => $page,
                    'status' => $status,
                    'category' => $this->category,
                )
            )
        );
    }
    /**
     * Toggles the available status given the content id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function toggleAvailableAction(Request $request)
    {
        $this->checkAclOrForward('FILE_AVAILABLE');

        $request = $this->request;
        $id      = $request->query->getDigits('id');
        $page    = $request->query->getDigits('page', 1);
        $status  = $request->query->getDigits('status', 0);

        $file = new \Attachment($id);

        if (!is_null($file->id)) {
            $file->set_available($status, $_SESSION['userid']);
        } else {
            m::add(sprintf(_("Unable to find the file with id '%d'"), $id), m::ERROR);
        }

        return $this->redirect(
            $this->generateUrl(
                'admin_files',
                array(
                    'page' => $page,
                    'status' => $status,
                    'category' => $this->category,
                )
            )
        );
    }



    /**
     * Deletes multiple books at once given its ids
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function batchDeleteAction(Request $request)
    {
        $this->checkAclOrForward('FILE_DELETE');

        $request = $this->request;
        $page = $request->query->getDigits('page', 1);
        $selectedItems = $request->query->get('selected_fld');

        if (is_array($selectedItems)
            && count($selectedItems) > 0
        ) {
            foreach ($selectedItems as $element) {
                $file = new \Attachment($element);

                $relations = array();
                $relations = \RelatedContent::getContentRelations($element);

                $file->delete($element, $_SESSION['userid']);

                m::add(sprintf(_('Files "%s" deleted successfully.'), $file->title), m::SUCCESS);
            }
        }

        return $this->redirect(
            $this->generateUrl(
                'admin_files',
                array(
                    'page' => $page,
                    'status' => $status,
                    'category' => $this->category,
                )
            )
        );
    }
    /**
     * Set the published flag for contents in batch
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function batchPublishAction(Request $request)
    {
        $this->checkAclOrForward('FILE_AVAILABLE');

        $request  = $this->request;
        $status   = $request->query->getDigits('status', 0);
        $selected = $request->query->get('selected_fld', null);
        $page     = $request->query->getDigits('page', 1);

        if (is_array($selected)
            && count($selected) > 0
        ) {
            foreach ($selected as $id) {
                $file = new \Attachment($id);
                $file->set_available($status, $_SESSION['userid']);
                if ($status == 0) {
                    $file->set_favorite($status, $_SESSION['userid']);
                }
            }
        }

        return $this->redirect(
            $this->generateUrl(
                'admin_files',
                array(
                    'page' => $page,
                    'status' => $status,
                    'category' => $this->category,
                )
            )
        );
    }

    /**
     * Save positions for widget
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function savePositionsAction(Request $request)
    {
        $request = $this->get('request');

        $positions = $request->request->get('positions');

        $msg = '';
        if (isset($positions)
            && is_array($positions)
            && count($positions) > 0
        ) {
            $_positions = array();
            $pos = 1;

            foreach ($positions as $id) {
                $_positions[] = array($pos, '1', $id);
                $pos += 1;
            }

            $file= new \Attachment();
            $msg = $file->set_position($_positions, $_SESSION['userid']);

        }

        if ($msg) {
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
