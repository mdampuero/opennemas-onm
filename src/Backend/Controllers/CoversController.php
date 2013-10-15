<?php
/**
 * Handles the actions for handling the pdf covers
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
 * Handles the actions for handling the pdf covers
 *
 * @package Backend_Controllers
 **/
class CoversController extends Controller
{
    /**
     * Common code for all the actions
     *
     * @return void
     **/
    public function init()
    {
        //Check if module is activated in this onm instance
        \Onm\Module\ModuleManager::checkActivatedOrForward('KIOSKO_MANAGER');

        // Check if the user can admin kiosko
        $this->checkAclOrForward('KIOSKO_ADMIN');

        if (!defined('KIOSKO_DIR')) {
            define('KIOSKO_DIR', "kiosko".SS);
        }

        $contentType = \ContentManager::getContentTypeIdFromName('kiosko');

        $category = $this->get('request')->query->getDigits('category', 'all');

        $ccm = \ContentCategoryManager::get_instance();
        list($parentCategories, $subcat, $categoryData) =
            $ccm->getArraysMenu($category, $contentType);

        $this->view->assign(
            array(
                'category'     => $category,
                'subcat'       => $subcat,
                'allcategorys' => $parentCategories,
                'datos_cat'    => $categoryData
            )
        );
    }

    /**
     * Shows the list of the
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function listAction(Request $request)
    {
        $category = $request->query->getDigits('category', 'all');
        $page = $request->query->getDigits('page', 1);
        $itemsPerPage = s::get('items_per_page');

        $cm = new \ContentManager();
        if (empty($category)) {
            $filter = null;
            $category = 'all';
        } else {
            $filter = $category;
        }

        list($countCovers, $covers) = $cm->getCountAndSlice(
            'kiosko',
            $filter,
            'contents.in_litter != 1',
            'ORDER BY date DESC',
            $page,
            $itemsPerPage
        );

        $aut = new \User();
        $ccm = \ContentCategoryManager::get_instance();
        foreach ($covers as &$cover) {
            $cover->publisher      = $aut->getUserName($cover->fk_publisher);
            $cover->editor         = $aut->getUserName($cover->fk_user_last_editor);
            $cover->category_name  = $ccm->get_name($cover->category);
            $cover->category_title = $ccm->get_title($cover->category_name);
        }

        // Build the pager
        $pagination = \Pager::factory(
            array(
                'mode'        => 'Sliding',
                'perPage'     => $itemsPerPage,
                'append'      => false,
                'path'        => '',
                'delta'       => 4,
                'clearIfVoid' => true,
                'urlVar'      => 'page',
                'totalItems'  => $countCovers,
                'fileName'    => $this->generateUrl(
                    'admin_covers',
                    array('category' => $category)
                ).'&page=%d',
            )
        );

        return $this->render(
            'covers/list.tpl',
            array(
                'KIOSKO_IMG_URL' => INSTANCE_MEDIA.KIOSKO_DIR,
                'pagination'     => $pagination,
                'covers'         => $covers,
                'category'       => $category,
            )
        );
    }

    /**
     * Show the list of the covers with favorite flag enabled
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function widgetAction(Request $request)
    {
        $category = 'widget';
        $page = $request->query->getDigits('page', 1);
        $itemsPerPage = s::get('items_per_page');

        $cm = new \ContentManager();
        $filter = null;

        list($countCovers, $covers) = $cm->getCountAndSlice(
            'kiosko',
            $filter,
            'contents.in_litter != 1 AND favorite = 1',
            'ORDER BY position ASC, date DESC',
            $page,
            $itemsPerPage
        );

        $aut = new \User();
        $ccm = \ContentCategoryManager::get_instance();
        foreach ($covers as &$cover) {
            $cover->publisher      = $aut->getUserName($cover->fk_publisher);
            $cover->editor         = $aut->getUserName($cover->fk_user_last_editor);
            $cover->category_name  = $ccm->get_name($cover->category);
            $cover->category_title = $ccm->get_title($cover->category_name);
        }

        // Build the pager
        $pagination = \Pager::factory(
            array(
                'mode'        => 'Sliding',
                'perPage'     => $itemsPerPage,
                'append'      => false,
                'path'        => '',
                'delta'       => 4,
                'clearIfVoid' => true,
                'urlVar'      => 'page',
                'totalItems'  => $countCovers,
                'fileName'    => $this->generateUrl(
                    'admin_covers',
                    array('category' => $category)
                ).'&page=%d',
            )
        );

        return $this->render(
            'covers/list.tpl',
            array(
                'KIOSKO_IMG_URL' => INSTANCE_MEDIA.KIOSKO_DIR,
                'pagination'     => $pagination,
                'covers'         => $covers,
                'category'       => $category,
            )
        );
    }

    /**
     * Displays the cover information form
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function showAction(Request $request)
    {
        $this->checkAclOrForward('KIOSKO_UPDATE');

        $id = $this->request->query->getDigits('id', null);

        $cover = new \Kiosko($id);

        if (is_null($cover->id)) {
            m::add(sprintf(_('Unable to find the cover with the id "%d"'), $id));

            return $this->redirect($this->generateUrl('admin_videos'));
        }

        return $this->render(
            'covers/read.tpl',
            array(
                'cover'          => $cover,
                'KIOSKO_IMG_URL' => INSTANCE_MEDIA.KIOSKO_DIR,
            )
        );
    }

    /**
     * Handles the creation of new covers
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function createAction(Request $request)
    {
        $this->checkAclOrForward('KIOSKO_CREATE');

        if ('POST' == $request->getMethod()) {
            $postInfo = $request->request;

            $coverData = array(
                'title'        => $postInfo->filter('title', null, FILTER_SANITIZE_STRING),
                'metadata'     => $postInfo->filter('metadata', null, FILTER_SANITIZE_STRING),
                'type'         => (int) $postInfo->getDigits('type', 0),
                'category'     => (int) $postInfo->getDigits('category'),
                'available'    => (int) $postInfo->getDigits('available', 1),
                'favorite'     => (int) $postInfo->getDigits('favorite', 1),
                'date'         => $postInfo->filter('date', null, FILTER_SANITIZE_STRING),
                'price'        => $postInfo->filter('price', null, FILTER_SANITIZE_NUMBER_FLOAT),
                'fk_publisher' => (int) $_SESSION['userid'],
            );

            $dateTime = new \DateTime($coverData['date']);
            $coverData['name'] = $dateTime->format('Ymd').date('His').'-'.$coverData['category'].'.pdf';
            $coverData['path'] = $dateTime->format('Y/m/d').'/';
            $path = INSTANCE_MEDIA_PATH. KIOSKO_DIR. $coverData['path'];

            try {
                // Create folder if it doesn't exist
                if (!file_exists($path)) {
                    \FilesManager::createDirectory($path);
                }
                $uploadStatus = false;

                foreach ($request->files as $file) {
                    // Move uploaded file
                    $uploadStatus = $file->isValid() && $file->move(realpath($path), $coverData['name']);
                }

                if (!$uploadStatus) {
                    throw new \Exception(
                        sprintf(
                            _(
                                'There was an error while uploading the file. '
                                .'Try to upload a file smaller than %d MB or contact with '
                                .'your administrator'
                            ),
                            (int) ini_get('upload_max_filesize')
                        )
                    );
                }

                $kiosko = new \Kiosko();
                // TODO: clean the post var
                if (!$kiosko->create($coverData)) {
                    throw new \Exception(_('There was a problem with the cover data. Try again'));
                }

                return $this->redirect(
                    $this->generateUrl('admin_covers', array('category' => $postInfo->getDigits('category')))
                );

            } catch (\Exception $e) {
                m::add($e->getMessage(), m::ERROR);

                return $this->redirect(
                    $this->generateUrl(
                        'admin_covers',
                        array(
                            'category' => $postInfo->getDigits('category'),
                        )
                    )
                );
            }
        } else {
            return $this->render('covers/read.tpl');
        }
    }

    /**
     * Updates the cover information provided by POST request
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function updateAction(Request $request)
    {
        $this->checkAclOrForward('KIOSKO_UPDATE');

        $id = $request->query->getDigits('id');
        $continue = $request->request->filter('continue', false, FILTER_SANITIZE_STRING);
        $cover = new \Kiosko($id);
        $_POST['fk_user_last_editor']=$_SESSION['userid'];

        if ($cover->id != null) {
            if (!\Acl::isAdmin()
                && !\Acl::check('CONTENT_OTHER_UPDATE')
                && $cover->pk_user != $_SESSION['userid']
            ) {
                m::add(_("You can't modify this cover because you don't have enought privileges."));
            } else {
                $cover->update($_POST);
                m::add(_("Cover updated successfully."), m::SUCCESS);
            }
            if ($continue) {
                return $this->redirect(
                    $this->generateUrl(
                        'admin_cover_show',
                        array('id' => $cover->id)
                    )
                );
            } else {
                $page = $this->request->request->getDigits('page', 1);

                return $this->redirect(
                    $this->generateUrl(
                        'admin_covers',
                        array(
                            'category' => $cover->category,
                            'page'     => $page,
                        )
                    )
                );
            }
        }
    }

    /**
     * Deletes a video given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function deleteAction(Request $request)
    {
        $this->checkAclOrForward('KIOSKO_DELETE');

        $request = $this->request;
        $id = $request->query->getDigits('id');
        $page = $request->query->getDigits('page', 1);

        if (!empty($id)) {
            $cover = new \Kiosko($id);
            // Delete relations
            $rel= new \RelatedContent();
            $rel->deleteAll($id);

            $cover->delete($id, $_SESSION['userid']);
            m::add(_("Cover '{$cover->title}' deleted successfully."), m::SUCCESS);
        } else {
            m::add(_('You must give an id for delete the cover.'), m::ERROR);
        }

        return $this->redirect(
            $this->generateUrl(
                'admin_covers',
                array(
                    'category' => $cover->category,
                    'page'     => $page
                )
            )
        );
    }

    /**
     * Change availability for one cover given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function toggleAvailableAction(Request $request)
    {
        $this->checkAclOrForward('KIOSKO_AVAILABLE');

        $request  = $this->get('request');
        $id       = $request->query->getDigits('id', 0);
        $status   = $request->query->getDigits('status', 0);
        $page     = $request->query->getDigits('page', 1);
        $category = $request->query->get('category', 'all');

        $cover = new \Kiosko($id);
        if (is_null($cover->id)) {
            m::add(sprintf(_('Unable to find a cover with id "%d"'), $id), m::ERROR);
        } else {
            $cover->toggleAvailable($cover->id);
            if ($status == 0) {
                $cover->set_favorite($status);
            }
            m::add(sprintf(_('Successfully changed availability for the cover "%s"'), $cover->title), m::SUCCESS);
        }

        return $this->redirect(
            $this->generateUrl(
                'admin_covers',
                array(
                    'category' => $category,
                    'page'     => $page
                )
            )
        );
    }

    /**
     * Change suggested flag for one cover given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function toggleFavoriteAction(Request $request)
    {
        $this->checkAclOrForward('KIOSKO_AVAILABLE');

        $request  = $this->get('request');
        $id       = $request->query->getDigits('id', 0);
        $status   = $request->query->getDigits('status', 0);
        $page     = $request->query->getDigits('page', 1);
        $category = $request->query->get('category', 'all');

        $cover = new \Kiosko($id);
        if (is_null($cover->id)) {
            m::add(sprintf(_('Unable to find cover with id "%d"'), $id), m::ERROR);
        } else {

            $cover->set_favorite($status);
            m::add(sprintf(_('Successfully changed suggested flag for cover "%s"'), $cover->title), m::SUCCESS);
        }

        return $this->redirect(
            $this->generateUrl(
                'admin_covers',
                array(
                    'category' => $category,
                    'page'     => $page
                )
            )
        );
    }

    /**
     * Change in_home flag for one cover given its id
     * Used for putting this content widgets in home
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function toggleInHomeAction(Request $request)
    {
        $this->checkAclOrForward('KIOSKO_AVAILABLE');

        $request  = $this->get('request');
        $id       = $request->query->getDigits('id', 0);
        $status   = $request->query->getDigits('status', 0);
        $page     = $request->query->getDigits('page', 1);
        $category = $request->query->get('category', 'all');

        $cover = new \Kiosko($id);
        if (is_null($cover->id)) {
            m::add(sprintf(_('Unable to find a cover with the id "%d"'), $id), m::ERROR);
        } else {
            $cover->set_inhome($status, $_SESSION['userid']);
            m::add(sprintf(_('Successfully changed suggested flag for cover "%s"'), $cover->title), m::SUCCESS);
        }

        return $this->redirect(
            $this->generateUrl(
                'admin_covers',
                array(
                    'category' => $category,
                    'page'     => $page
                )
            )
        );
    }

    /**
     * Deletes multiple covers at once given its ids
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function batchDeleteAction(Request $request)
    {
        $this->checkAclOrForward('KIOSKO_DELETE');

        $request       = $this->request;
        $category      = $request->query->filter('category', 'all', FILTER_SANITIZE_STRING);
        $page          = $request->query->getDigits('page', 1);
        $selectedItems = $request->query->get('selected_fld');

        if (is_array($selectedItems)
            && count($selectedItems) > 0
        ) {
            foreach ($selectedItems as $element) {
                $cover = new \Kiosko($element);

                $relations = array();
                $relations = \RelatedContent::getContentRelations($element);

                $cover->delete($element, $_SESSION['userid']);

                m::add(sprintf(_('Video "%s" deleted successfully.'), $cover->title), m::SUCCESS);
            }
        }

        return $this->redirect(
            $this->generateUrl(
                'admin_covers',
                array(
                    'categoy' => $category,
                    'page'    => $page,
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
        $this->checkAclOrForward('KIOSKO_AVAILABLE');

        $request  = $this->request;
        $status   = $request->query->getDigits('status', 0);
        $selected = $request->query->get('selected_fld', null);
        $category = $request->query->filter('category', 'all', FILTER_SANITIZE_STRING);
        $page     = $request->query->getDigits('page', 1);

        if (is_array($selected)
            && count($selected) > 0
        ) {
            foreach ($selected as $id) {
                $cover = new \Kiosko($id);
                $cover->set_available($status, $_SESSION['userid']);
                if ($status == 0) {
                    $cover->set_favorite($status, $_SESSION['userid']);
                }
            }
        }

        return $this->redirect(
            $this->generateUrl(
                'admin_covers',
                array(
                    'category' => $category,
                    'page'     => $page,
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

        $positions = $request->query->get('positions');

        $result = true;
        if (isset($positions)
            && is_array($positions)
            && count($positions) > 0
        ) {
            $pos = 1;

            foreach ($positions as $id) {
                $cover = new \Kiosko($id);
                $result = $result && $cover->setPosition($pos);
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
     * Handles the configuration of the covers module
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function configAction(Request $request)
    {
        if ('POST' != $request->getMethod()) {
            $configurationsKeys = array('kiosko_settings',);
            $configurations = s::get($configurationsKeys);

            return $this->render(
                'covers/config.tpl',
                array('configs'   => $configurations,)
            );
        } else {
            $settingsRAW = $request->request->get('kiosko_settings');
            $settings = array(
                'kiosko_settings' => array(
                    'orderFrontpage' => filter_var($settingsRAW['orderFrontpage'], FILTER_SANITIZE_STRING),
                )
            );

            foreach ($settings as $key => $value) {
                s::set($key, $value);
            }

            m::add(_('Settings saved successfully.'), m::SUCCESS);

            $httpParams = array(array('action'=>'list'),);

            return $this->redirect($this->generateUrl('admin_covers_config'));
        }
    }
}
