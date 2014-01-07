<?php
/**
 * Handles the actions for the specials
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

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Onm\Framework\Controller\Controller;
use Onm\Message as m;
use Onm\Settings as s;

/**
 * Handles the actions for the specials
 *
 * @package Backend_Controllers
 **/
class SpecialsController extends Controller
{
    /**
     * Common code for all the actions
     *
     * @return void
     **/
    public function init()
    {
        //Check if module is activated in this onm instance
        \Onm\Module\ModuleManager::checkActivatedOrForward('SPECIAL_MANAGER');

         // Check if the user can admin specials
        $this->checkAclOrForward('SPECIAL_ADMIN');

        $this->contentType = \ContentManager::getContentTypeIdFromName('special');

        $this->category = $this->get('request')->query->getDigits('category', null);

        $this->ccm = \ContentCategoryManager::get_instance();
        list($this->parentCategories, $this->subcat, $this->categoryData) =
                $this->ccm->getArraysMenu($this->category, $this->contentType);

        $this->view->assign(
            array(
                'category'     => $this->category,
                'subcat'       => $this->subcat,
                'allcategorys' => $this->parentCategories,
                'datos_cat'    => $this->categoryData
            )
        );
    }

    /**
     * List all the specials in a category
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function listAction(Request $request)
    {
        $category = $request->query->getDigits('category', null);
        $page = $request->query->getDigits('page', 1);
        $itemsPerPage = s::get('items_per_page') ?: 20;

        if (empty($category)) {
            $category = 'all';
            $categoryFilter = null;
        } else {
            $categoryFilter = $category;
        }

        $cm = new \ContentManager();
        list($countSpecials, $specials) = $cm->getCountAndSlice(
            'Special',
            $categoryFilter,
            '',
            'ORDER BY created DESC ',
            $page,
            $itemsPerPage
        );

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
                'totalItems'  => $countSpecials,
                'fileName'    => $this->generateUrl('admin_specials', array('category' => $category)).'&page=%d',
            )
        );

        return $this->render(
            'special/list.tpl',
            array(
                'pagination' => $pagination,
                'specials'   => $specials,
                'category'   => $category,
                'page'       => $page
            )
        );
    }

    /**
     * List all the specials selected for the widget
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function widgetAction(Request $request)
    {
        $page = $request->query->getDigits('page', 1);
        if (isset($configurations['total_widget'])
            && !empty($configurations['total_widget'])
        ) {
            $numFavorites = $configurations['total_widget'];
        } else {
            $numFavorites = 1;
        }
        $itemsPerPage = s::get('items_per_page') ?: 20;

        $cm = new \ContentManager();
        list($countSpecials, $specials) = $cm->getCountAndSlice(
            'Special',
            null,
            'in_home=1',
            'ORDER BY position, created DESC ',
            $page,
            $itemsPerPage
        );

        foreach ($specials as &$special) {
            $special->category_name  = $this->ccm->get_name($special->category);
            $special->category_title = $this->ccm->get_title($special->category_name);
        }

        if (count($specials) != $numFavorites) {
            m::add(sprintf(_("You must put %d specials in the HOME widget"), $numFavorites));
        }

        $pagination = \Pager::factory(
            array(
                'mode'        => 'Sliding',
                'perPage'     => $itemsPerPage,
                'append'      => false,
                'path'        => '',
                'delta'       => 4,
                'clearIfVoid' => true,
                'urlVar'      => 'page',
                'totalItems'  => $countSpecials,
                'fileName'    => $this->generateUrl('admin_specials_widget').'?page=%d',
            )
        );

        return $this->render(
            'special/list.tpl',
            array(
                'pagination' => $pagination,
                'specials'   => $specials,
                'category'   => 'widget',
                'page'       => $page
            )
        );
    }

    /**
     * Handles the form for create new specials
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function createAction(Request $request)
    {
        $this->checkAclOrForward('SPECIAL_CREATE');

        if ('POST' == $request->getMethod()) {
            $special = new \Special();

            $data = array(
                'title'          => $request->request->filter('title', '', FILTER_SANITIZE_STRING),
                'subtitle'       => $request->request->filter('subtitle', '', FILTER_SANITIZE_STRING),
                'description'    => $request->request->filter('description', '', FILTER_SANITIZE_STRING),
                'metadata'       => $request->request->filter('metadata', '', FILTER_SANITIZE_STRING),
                'slug'           => $request->request->filter('slug', '', FILTER_SANITIZE_STRING),
                'category'       => $request->request->filter('category', '', FILTER_SANITIZE_STRING),
                'available'      => $request->request->filter('available', 0, FILTER_SANITIZE_STRING),
                'img1'           => $request->request->filter('img1', '', FILTER_SANITIZE_STRING),
                'category_imag'  => $request->request->filter('category_imag', '', FILTER_SANITIZE_STRING),
                'noticias_right' => json_decode($request->request->get('noticias_right')),
                'noticias_left'  => json_decode($request->request->get('noticias_left')),
            );

            if ($special->create($data)) {
                m::add(_('Special successfully created.'), m::SUCCESS);
            } else {
                m::add(_('Unable to create the new special.'), m::ERROR);
            }

            return $this->redirect(
                $this->generateUrl(
                    'admin_special_show',
                    array('id' => $special->id)
                )
            );
        } else {
            return $this->render('special/new.tpl');
        }
    }

    /**
     * Shows the special information form
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function showAction(Request $request)
    {
        $this->checkAclOrForward('SPECIAL_UPDATE');

        $id = $request->query->getDigits('id', null);

        $special = new \Special($id);
        if (is_null($special->id)) {
            m::add(sprintf(_('Unable to find the special with the id "%d"'), $id));

            return $this->redirect($this->generateUrl('admin_specials'));
        }

        $contents = $special->getContents($id);
        if (!empty($special->img1)) {
            $photo1 = new \Photo($special->img1);
            $this->view->assign('photo1', $photo1);
        }

        $contentsLeft = array();
        $contentsRight = array();

        if (!empty($contents)) {
            foreach ($contents as $content) {
                if (($content['position']%2) == 0) {
                    $contentsRight[] = new \Content($content['fk_content']);
                } else {
                    $contentsLeft[] = new \Content($content['fk_content']);
                }
            }
            $this->view->assign(
                array(
                    'contentsRight' => $contentsRight,
                    'contentsLeft'  => $contentsLeft,
                )
            );
        }

        return $this->render(
            'special/new.tpl',
            array(
                'special'  => $special,
                'category' => $special->category,
            )
        );
    }

    /**
     * Updates the special information
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function updateAction(Request $request)
    {
        $this->checkAclOrForward('SPECIAL_UPDATE');

        $id = $request->query->getDigits('id');

        $special = new \Special($id);

        if ($special->id != null) {
            // Check empty data
            if (count($request->request) < 1) {
                m::add(_("Special data sent not valid."), m::ERROR);

                return $this->redirect($this->generateUrl('admin_special_show', array('id' => $id)));
            }
            $data = array(
                'id'             => $id,
                'title'          => $request->request->filter('title', '', FILTER_SANITIZE_STRING),
                'subtitle'       => $request->request->filter('subtitle', '', FILTER_SANITIZE_STRING),
                'description'    => $request->request->filter('description', '', FILTER_SANITIZE_STRING),
                'metadata'       => $request->request->filter('metadata', '', FILTER_SANITIZE_STRING),
                'slug'           => $request->request->filter('slug', '', FILTER_SANITIZE_STRING),
                'category'       => $request->request->filter('category', '', FILTER_SANITIZE_STRING),
                'available'      => $request->request->filter('available', 0, FILTER_SANITIZE_STRING),
                'img1'           => $request->request->filter('img1', '', FILTER_SANITIZE_STRING),
                'category_imag'  => $request->request->filter('category_imag', '', FILTER_SANITIZE_STRING),
                'noticias_right' => json_decode($request->request->get('noticias_right_input')),
                'noticias_left'  => json_decode($request->request->get('noticias_left_input')),
            );

            if ($special->update($data)) {
                m::add(_('Special successfully updated.'), m::SUCCESS);
            } else {
                m::add(_('Unable to update the special.'), m::ERROR);
            }

            return $this->redirect(
                $this->generateUrl(
                    'admin_special_show',
                    array('id' => $special->id)
                )
            );
        }
    }

    /**
     * Delete a special given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function deleteAction(Request $request)
    {
        $this->checkAclOrForward('SPECIAL_DELETE');

        $id       = $request->query->getDigits('id');
        $category = $request->query->filter('category', 'all', FILTER_SANITIZE_STRING);
        $page     = $request->query->getDigits('page', 1);

        if (!empty($id)) {
            $special = new \Special($id);

            $special->delete($id, $_SESSION['userid']);
            m::add(_("Special deleted successfully."), m::SUCCESS);
        } else {
            m::add(_('You must give an id for delete a special.'), m::ERROR);
        }

        if (!$request->isXmlHttpRequest()) {
            return $this->redirect(
                $this->generateUrl(
                    'admin_specials',
                    array(
                        'category' => $category,
                        'page'     => $page
                    )
                )
            );
        } else {
            return new Response('Ok', 200);
        }
    }

    /**
     * Saves the widget specials content positions
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function savePositionsAction(Request $request)
    {
        $positions = $request->get('positions');

        $result = true;
        if (isset($positions)
            && is_array($positions)
            && count($positions) > 0
        ) {
            $pos = 1;
            foreach ($positions as $id) {
                $special = new \Special($id);
                $result = $result && $special->setPosition($pos);

                $pos++;
            }

            // FIXME: buscar otra forma de hacerlo
            /* Eliminar caché portada cuando actualizan orden opiniones {{{ */
            $tplManager = new \TemplateCacheManager(TEMPLATE_USER_PATH);
            $tplManager->delete('home|0');
        }

        if (!empty($result) && $result == true) {
            $output = _("Positions saved successfully.");
        } else {
            $output = _("Unable to save positions for the specials widget.");
        }

        return new Response($output);
    }

    /**
     * Deletes multiple specials at once given their ids
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function batchDeleteAction(Request $request)
    {
        $this->checkAclOrForward('SPECIAL_DELETE');

        $selected = $request->query->get('selected_fld', null);
        $category = $request->query->getDigits('category', 'all');
        $page     = $request->query->getDigits('page', 1);

        if (is_array($selected)
            && count($selected) > 0
        ) {
            $changes = 0;
            foreach ($selected as $id) {
                $special = new \Special((int) $id);
                if (!is_null($special->id)) {
                    $special->delete($id, $_SESSION['userid']);
                    $changes++;
                } else {
                    m::add(sprintf(_('Unable to find a special with the id "%d"'), $id));
                }
            }
        }
        if ($changes > 0) {
            m::add(sprintf(_('Successfully deleted %d specials'), $changes));
        }

        if (!$request->isXmlHttpRequest()) {
            return $this->redirect(
                $this->generateUrl(
                    'admin_specials',
                    array(
                        'category' => $category,
                        'page'     => $page,
                    )
                )
            );
        } else {
            return new Response('Ok', 200);
        }

    }

    /**
     * Changes the available status for specials given their ids
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function batchPublishAction(Request $request)
    {
        $this->checkAclOrForward('SPECIAL_AVAILABLE');

        $status   = $request->query->getDigits('status', 0);
        $selected = $request->query->get('selected_fld', null);
        $category = $request->query->getDigits('category', 'all');
        $page     = $request->query->getDigits('page', 1);

        if (is_array($selected)
            && count($selected) > 0
        ) {
            $changes = 0;
            foreach ($selected as $id) {
                $special = new \Special((int) $id);
                if (!is_null($special->id)) {
                    $special->set_available($status, $_SESSION['userid']);
                    if ($status == 0) {
                        $special->set_favorite($status);
                    }
                    $changes++;
                } else {
                    m::add(sprintf(_('Unable to find a special with the id "%d"'), $id), m::ERROR);
                }
            }
        }
        if ($changes > 0) {
            m::add(sprintf(_('Successfully changed the available status of %d specials'), $changes), m::SUCCESS);
        }

        return $this->redirect(
            $this->generateUrl(
                'admin_specials',
                array(
                    'category' => $category,
                    'page' => $page,
                )
            )
        );
    }

    /**
     * Change available status for one special given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function toggleAvailableAction(Request $request)
    {
        $this->checkAclOrForward('SPECIAL_AVAILABLE');

        $id       = $request->query->getDigits('id', 0);
        $status   = $request->query->getDigits('status', 0);
        $category = $request->query->filter('category', 'all', FILTER_SANITIZE_STRING);
        $page     = $request->query->getDigits('page', 1);

        $special = new \Special($id);

        if (is_null($special->id)) {
            m::add(sprintf(_('Unable to find a special with the id "%d"'), $id), m::ERROR);
        } else {
            $special->set_available($status, $_SESSION['userid']);
            if ($status == 0) {
                $special->set_favorite($status);
            }
            m::add(sprintf(_('Successfully changed availability for the special "%s"'), $special->title), m::SUCCESS);
        }

        return $this->redirect(
            $this->generateUrl(
                'admin_specials',
                array(
                    'category' => $category,
                    'page'     => $page
                )
            )
        );
    }

    /**
     * Change available status for one special given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function toggleFavoriteAction(Request $request)
    {
        $this->checkAclOrForward('SPECIAL_FAVORITE');

        $id       = $request->query->getDigits('id', 0);
        $status   = $request->query->getDigits('status', 0);
        $category = $request->query->filter('category', 'all', FILTER_SANITIZE_STRING);
        $page     = $request->query->getDigits('page', 1);

        $special = new \Special($id);

        if (is_null($special->id)) {
            m::add(sprintf(_('Unable to find a special with the id "%d"'), $id), m::ERROR);
        } else {
            $special->set_favorite($status);
            m::add(sprintf(_('Successfully changed suggested flag for the special "%s"'), $special->title), m::SUCCESS);
        }

        return $this->redirect(
            $this->generateUrl(
                'admin_specials',
                array(
                    'category' => $category,
                    'page'     => $page
                )
            )
        );
    }

    /**
     * Change available status for one special given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function toggleInHomeAction(Request $request)
    {
        $this->checkAclOrForward('SPECIAL_AVAILABLE');

        $id       = $request->query->getDigits('id', 0);
        $status   = $request->query->getDigits('status', 0);
        $category = $request->query->filter('category', 'all', FILTER_SANITIZE_STRING);
        $page     = $request->query->getDigits('page', 1);

        $special = new \Special($id);

        if (is_null($special->id)) {
            m::add(sprintf(_('Unable to find a special with the id "%d"'), $id), m::ERROR);
        } else {
            $special->set_inhome($status);
            m::add(sprintf(_('Successfully changed suggested flag for the special "%s"'), $special->title), m::SUCCESS);
        }

        return $this->redirect(
            $this->generateUrl(
                'admin_specials',
                array(
                    'category' => $category,
                    'page'     => $page
                )
            )
        );
    }

    /**
     * Handles the configuration for the specials module
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function configAction(Request $request)
    {
        $this->checkAclOrForward('SPECIAL_SETTINGS');

        if ('POST' == $request->getMethod()) {
            $settingsRAW = $request->request->get('special_settings');
            $data = array(
                'special_settings' => array(
                    'total_widget' => $settingsRAW['total_widget'] ?: 0,
                    'time_last' => $settingsRAW['time_last'] ?: 0,
                )
            );

            foreach ($data as $key => $value) {
                s::set($key, $value);
            }
            m::add(_('Settings saved successfully.'), m::SUCCESS);

            return $this->redirect($this->generateUrl('admin_specials_config'));
        } else {
            $configurations = s::get(array('special_settings',));

            return $this->render(
                'special/config.tpl',
                array('configs'   => $configurations,)
            );
        }
    }
}
