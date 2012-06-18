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

use Onm\Framework\Controller\Controller;
use Onm\Message as m;
use Symfony\Component\HttpFoundation\Response;
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
        \Onm\Module\ModuleManager::checkActivatedOrForward('FILE_MANAGER');
        \Acl::checkOrForward('FILE_ADMIN');

        $this->view = new \TemplateAdmin(TEMPLATE_ADMIN);

        $this->contentType = \Content::getIDContentType('attachment');
        $this->category = $this->request->query->filter('category', 0, FILTER_SANITIZE_STRING);
        $this->ccm = \ContentCategoryManager::get_instance();
        list($this->parentCategories, $this->subcat, $this->datos_cat) = $this->ccm->getArraysMenu($this->category, $this->contentType);

        $this->view->assign(array(
            'subcat'       => $this->subcat,
            'allcategorys' => $this->parentCategories,
            'datos_cat'    => $this->datos_cat,
            'category'     => $this->category,
        ));
    }

    /**
     * Description of this action
     *
     * @return Response the response object
     **/
    public function createAction()
    {
        $content = 'default content';

        return new Response($content);
    }

    /**
     * Lists the files for a given category
     * @return Response the response object
     **/
    public function listAction()
    {
        $cm = new \ContentManager();

        $category = $this->request->query->filter('category', 0, FILTER_SANITIZE_STRING);
        $page = $this->request->query->filter('page', 0, FILTER_SANITIZE_STRING);


        list($attaches, $pager)= $cm->find_pages(
            'Attachment',
            'fk_content_type=3 ',
            'ORDER BY  created DESC ',
            $page,
            ITEMS_PAGE,
            $category
        );
        $this->view->assign('paginacion', $pager);

        $i = 0;

        $status = array();
        if ($attaches) {
            foreach ($attaches as &$archivo) {
                $dir_date = preg_replace("/\-/", '/', substr($archivo->created, 0, ITEMS_PAGE));
                $ruta = MEDIA_PATH.'/'.FILE_DIR.'/'.$archivo->path ;

                if (is_file($ruta)) {
                    $status[$i]='1'; //Si existe
                } else {
                    $status[$i]='0';
                    $archivo->set_available(0, $_SESSION['userid']);
                }
                $i++;
            }
        }

        $alert = (isset($_REQUEST['alerta']))? $_REQUEST['alerta'] : null;

        $this->view->assign(array(
            'status'   => $status,
            'attaches' => $attaches,
            'alerta'   => $alert
        ));

        $this->view->assign('category', $this->category);

        return $this->render('files/list.tpl');
    }

    /**
     * Shows the files in the widget
     *
     * @return Response the response object
     **/
    public function widgetAction()
    {
        $cm = new \ContentManager();

        $category = $this->request->query->filter('category', 0, FILTER_SANITIZE_STRING);
        $page = $this->request->query->filter('page', 0, FILTER_SANITIZE_STRING);

        $attaches = $cm->find_all(
            'Attachment',
            'in_home =1',
            'ORDER BY created DESC'
        );
        $status = array();
        $i = 0;
        if (!empty($attaches)) {
            foreach ($attaches as &$attach) {
                $status[$i]='1'; // Si existe
                $attach->category_name = $this->ccm->get_name($attach->category);
                $attach->category_title = $this->ccm->get_title($attach->category_name);
                $i++;
            }
        }

        return $this->render('files/list.tpl', array(
            'status'   => $status,
            'attaches' => $attaches,
            'category' => 'widget'
        ));
    }

    /**
     * Description of the action
     *
     * @return Response the response object
     **/
    public function statisticsAction()
    {
        $nameCategory = 'GLOBAL';
        $cm = new \ContentManager();
        $total_num_photos=0;
        $files = $size = $sub_size = $num_photos = array();
        $fullcat = $this->ccm->order_by_posmenu($this->ccm->categories);

        foreach ($this->parentCategories as $k => $v) {
            $num_photos[$k]= $this->ccm->countContentByType($v->pk_content_category, $this->contentType);
            $total_num_photos += $num_photos[$k];
            $files[$v->pk_content_category] = $cm->find_all(
                'Attachment',
                'fk_content_type = 3 AND category = '.$v->pk_content_category ,
                'ORDER BY created DESC'
            );
            if (!empty($fullcat)){
                foreach ($fullcat as $child) {
                    if ($v->pk_content_category == $child->fk_content_category) {
                        $num_sub_photos[$k][$child->pk_content_category] = $this->ccm->countContentByType($child->pk_content_category, 3);
                        $total_num_photos += $num_sub_photos[$k][$child->pk_content_category];
                        $sub_files[$child->pk_content_category][] = $cm->find_all('Attachment',
                                         'fk_content_type = 3 AND category = '.$child->pk_content_category ,
                                         'ORDER BY created DESC' );
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
                        if (file_exists(MEDIA_PATH.'/'.FILE_DIR.'/'.$value->path)) {
                            $size[$i] += filesize(MEDIA_PATH.'/'.FILE_DIR.'/'.$value->path);

                        }
                    }
                }
            }$total_size += $size[$i];
            $i++;
        }
        if(!empty($parentCategories) && !empty ($aux_categories)) {
            foreach ($parentCategories as $k => $v) {
                foreach ($aux_categories as $ind) {
                    if (!empty ($sub_files[$ind][0])) {
                        foreach ($sub_files[$ind][0] as $value) {
                            if ($v->pk_content_category == $ccm->get_id($ccm->get_father($value->catName))) {
                                if ($ccm->get_id($ccm->get_father($value->catName)) ) {
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

        return $this->render('files/statistics.tpl', array(
            'total_img'    => $total_num_photos,
            'total_size'   => $total_size,
            'size'         => $size,
            'sub_size'     => $sub_size,
            'num_photos'   => $num_photos,
            'categorys'    => $this->parentCategories,
            'subcategorys' => $this->subcat,
        ));
    }

    /**
     * Shows file data given its id
     *
     * @return Response the response object
     **/
    public function readAction()
    {
        \Acl::checkOrForward('FILE_UPDATE');

        $id = $this->request->query->getDigits('id');
        $attaches = new \Attachment($id);

        // If the file doesn't exists redirect to the listing
        // and show error message
        if (is_null($attaches->pk_attachment)) {
            m::add(sprintf(_('Unable to find the file with the id "%s"'), $id));

            return $this->redirect($this->generateUrl('admin_files'));
        }

        // Show the
        return $this->render('files/form.tpl', array(
            'attaches' => $attaches
        ));
    }

    /**
     * Updates a file given the data sent by POST
     *
     * @return Response the response object
     **/
    public function updateAction()
    {
        \Acl::checkOrForward('FILE_UPDATE');

        $id       = $this->request->query->getDigits('id');
        $category = $this->request->request->getDigits('category');
        $page     = $this->request->request->getDigits('page');

        $file = new \Attachment($id);
        if ($file->update($_REQUEST)) {
            m::add(sprintf(_('File information updated successfuly.')), m::SUCCESS);
        } else {
            m::add(sprintf(_('There was a problem while saving the file information.')), m::ERROR);
        }

        return $this->redirect($this->generateUrl(
            'admin_files',
            array(
                'category' => $category,
                'page'     => $page
            )
        ));
    }

} // END class FilesController