<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Backend\Controller;

use Common\Core\Annotation\Security;
use Common\Core\Controller\Controller;
use Onm\Settings as s;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SpecialsController extends Controller
{
    /**
     * Common code for all the actions
     *
     * @return void
     */
    public function init()
    {
        $this->contentType = \ContentManager::getContentTypeIdFromName('special');

        $this->category = $this->get('request_stack')->getCurrentRequest()
            ->query->getDigits('category', null);
        $this->ccm      = \ContentCategoryManager::get_instance();

        list($this->parentCategories, $this->subcat, $this->categoryData) =
                $this->ccm->getArraysMenu($this->category, $this->contentType);

        $this->view->assign([
            'category'     => $this->category,
            'subcat'       => $this->subcat,
            'allcategorys' => $this->parentCategories,
            'datos_cat'    => $this->categoryData,
        ]);
    }

    /**
     * List all the specials in a category
     *
     * @return void
     *
     * @Security("hasExtension('SPECIAL_MANAGER')
     *     and hasPermission('SPECIAL_ADMIN')")
     */
    public function listAction()
    {
        $categories = [ [ 'name' => _('All'), 'value' => null ] ];
        foreach ($this->parentCategories as $key => $category) {
            $categories[] = [
                'name'  => $category->title,
                'value' => $category->pk_content_category
            ];

            foreach ($this->subcat[$key] as $subcategory) {
                $categories[] = [
                    'name' => '&rarr; ' . $subcategory->title,
                    'value' => $subcategory->name
                ];
            }
        }

        return $this->render(
            'special/list.tpl',
            [ 'categories' => $categories ]
        );
    }

    /**
     * List all the specials selected for the widget
     *
     * @return void
     *
     * @Security("hasExtension('SPECIAL_MANAGER')
     *     and hasPermission('SPECIAL_ADMIN')")
     */
    public function widgetAction()
    {
        $numFavorites = 1;
        $settings     = $this->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get([ 'special_settings' ]);

        if (isset($settings['total_widget'])
            && !empty($settings['total_widget'])
        ) {
            $numFavorites = $settings['total_widget'];
        }

        return $this->render('special/list.tpl', [
            'total_elements_widget' => $numFavorites,
            'category'              => 'widget',
        ]);
    }

    /**
     * Handles the form for create new specials
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("hasExtension('SPECIAL_MANAGER')
     *     and hasPermission('SPECIAL_CREATE')")
     */
    public function createAction(Request $request)
    {
        if ('POST' !== $request->getMethod()) {
            return $this->render('special/new.tpl');
        }

        $special = new \Special();

        $data = [
            'title'          => $request->request
                ->filter('title', '', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES),
            'subtitle'       => $request->request
                ->filter('subtitle', '', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES),
            'description'    => $request->request->get('description', ''),
            'metadata'       => \Onm\StringUtils::normalizeMetadata(
                $request->request->filter('metadata', '', FILTER_SANITIZE_STRING)
            ),
            'slug'           => $request->request->filter('slug', '', FILTER_SANITIZE_STRING),
            'category'       => $request->request->filter('category', '', FILTER_SANITIZE_STRING),
            'content_status' => $request->request->filter('content_status', 0, FILTER_SANITIZE_STRING),
            'img1'           => $request->request->filter('img1', '', FILTER_SANITIZE_STRING),
            'category_imag'  => $request->request->filter('category_imag', '', FILTER_SANITIZE_STRING),
            'noticias_right' => json_decode($request->request->get('noticias_right_input')),
            'noticias_left'  => json_decode($request->request->get('noticias_left_input')),
        ];

        if ($special->create($data)) {
            $this->get('session')->getFlashBag()->add(
                'success',
                _('Special successfully created.')
            );

            return $this->redirect(
                $this->generateUrl('admin_special_show', ['id' => $special->id])
            );
        } else {
            $this->get('session')->getFlashBag()->add('error', _('Unable to create the new special.'));

            return $this->redirect($this->generateUrl('admin_special_create'));
        }
    }

    /**
     * Shows the special information form
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("hasExtension('SPECIAL_MANAGER')
     *     and hasPermission('SPECIAL_UPDATE')")
     */
    public function showAction(Request $request)
    {
        $id = $request->query->getDigits('id', null);

        $special = new \Special($id);
        if (is_null($special->id)) {
            $this->get('session')->getFlashBag()->add(
                'error',
                sprintf(_('Unable to find the special with the id "%d"'), $id)
            );

            return $this->redirect($this->generateUrl('admin_specials'));
        }

        $contents = $special->getContents($id);

        if (!empty($special->img1)) {
            $photo1 = new \Photo($special->img1);
            $this->view->assign('photo1', $photo1);
        }

        $contentsLeft  = [];
        $contentsRight = [];

        if (!empty($contents)) {
            foreach ($contents as $content) {
                if (($content['position'] % 2) == 0) {
                    $contentsRight[] = new \Content($content['fk_content']);
                } else {
                    $contentsLeft[] = new \Content($content['fk_content']);
                }
            }

            $this->view->assign([
                'contentsRight' => $contentsRight,
                'contentsLeft'  => $contentsLeft,
            ]);
        }

        return $this->render('special/new.tpl', [
            'special'  => $special,
            'category' => $special->category,
        ]);
    }

    /**
     * Updates the special information
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("hasExtension('SPECIAL_MANAGER')
     *     and hasPermission('SPECIAL_UPDATE')")
     */
    public function updateAction(Request $request)
    {
        $id = $request->query->getDigits('id');

        $special = new \Special($id);

        if ($special->id != null) {
            // Check empty data
            if (count($request->request) < 1) {
                $this->get('session')->getFlashBag()->add(
                    'error',
                    _("Special data sent not valid.")
                );

                return $this->redirect($this->generateUrl('admin_special_show', [ 'id' => $id ]));
            }

            $data = [
                'id'             => $id,
                'title'          => $request->request
                    ->filter('title', '', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES),
                'subtitle'       => $request->request
                    ->filter('subtitle', '', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES),
                'description'    => $request->request->get('description', ''),
                'metadata'       => \Onm\StringUtils::normalizeMetadata(
                    $request->request->filter('metadata', '', FILTER_SANITIZE_STRING)
                ),
                'slug'           => $request->request->filter('slug', '', FILTER_SANITIZE_STRING),
                'category'       => $request->request->filter('category', '', FILTER_SANITIZE_STRING),
                'content_status' => $request->request->filter('content_status', 0, FILTER_SANITIZE_STRING),
                'img1'           => $request->request->filter('img1', '', FILTER_SANITIZE_STRING),
                'category_imag'  => $request->request->filter('category_imag', '', FILTER_SANITIZE_STRING),
                'noticias_left'  => json_decode($request->request->get('noticias_left_input')),
                'noticias_right' => json_decode($request->request->get('noticias_right_input')),
            ];

            if ($special->update($data)) {
                $this->get('session')->getFlashBag()->add(
                    'success',
                    _('Special successfully updated.')
                );
            } else {
                $this->get('session')->getFlashBag()->add(
                    'error',
                    _('Unable to update the special.')
                );
            }

            return $this->redirect($this->generateUrl('admin_special_show', [
                'id' => $special->id
            ]));
        }
    }

    /**
     * Delete a special given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("hasExtension('SPECIAL_MANAGER')
     *     and hasPermission('SPECIAL_DELETE')")
     */
    public function deleteAction(Request $request)
    {
        $id       = $request->query->getDigits('id');
        $category = $request->query->filter('category', 'all', FILTER_SANITIZE_STRING);
        $page     = $request->query->getDigits('page', 1);

        if (!empty($id)) {
            $special = new \Special($id);

            $special->delete($id, $this->getUser()->id);
            $this->get('session')->getFlashBag()->add(
                'success',
                _("Special deleted successfully.")
            );
        } else {
            $this->get('session')->getFlashBag()->add(
                'error',
                _('You must give an id for delete a special.')
            );
        }

        if (!$request->isXmlHttpRequest()) {
            return $this->redirect($this->generateUrl('admin_specials', [
                'category' => $category,
                'page'     => $page
            ]));
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
     *
     * @Security("hasExtension('SPECIAL_MANAGER')
     *     and hasPermission('SPECIAL_ADMIN')")
     */
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
                $result  = $result && $special->setPosition($pos);

                $pos++;
            }

            // TODO: remove cache cleaning actions
            $cacheManager = $this->get('template_cache_manager');
            $cacheManager->setSmarty($this->get('core.template'));
            $cacheManager->delete('home|0');
        }

        if (!empty($result) && $result == true) {
            $output = _("Positions saved successfully.");
        } else {
            $output = _("Unable to save positions for the specials widget.");
        }

        return new Response($output);
    }

    /**
     * Handles the configuration for the specials module
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("hasExtension('SPECIAL_MANAGER')
     *     and hasPermission('SPECIAL_SETTINGS')")
     */
    public function configAction(Request $request)
    {
        $sm = $this->get('orm.manager')->getDataSet('Settings', 'instance');

        if ('POST' == $request->getMethod()) {
            $settingsRAW = $request->request->get('special_settings');
            $data        = [
                'special_settings' => [
                    'total_widget' => $settingsRAW['total_widget'] ?: 0,
                    'time_last'    => $settingsRAW['time_last'] ?: 0,
                ]
            ];

            foreach ($data as $key => $value) {
                $sm->set($key, $value);
            }

            $this->get('session')->getFlashBag()->add(
                'success',
                _('Settings saved successfully.')
            );

            return $this->redirect($this->generateUrl('admin_specials_config'));
        }

        $settings = $sm->get([ 'special_settings' ]);

        return $this->render('special/config.tpl', [ 'configs' => $settings ]);
    }
}
