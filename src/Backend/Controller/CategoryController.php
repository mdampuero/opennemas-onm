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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Common\Core\Controller\Controller;

/**
 * Handles the actions for categories
 *
 * @package Backend_Controllers
 */
class CategoryController extends Controller
{
    /**
     * Lists all the available categories
     *
     * @return Response the response object
     *
     * @Security("hasExtension('CATEGORY_MANAGER')
     *     and hasPermission('CATEGORY_ADMIN')")
     *
     */
    public function listAction()
    {
        $cm           = $this->get('category_repository');
        $categories   = $cm->findBy(null, 'name ASC');
        $languageData = $this->getLocaleData('frontend', null, true);

        $cm->sortCategories($categories);

        $map = [];
        foreach ($categories as $category) {
            $category->subcategories = array_map(function ($a) {
                return $a->pk_content_category;
            }, array_filter($categories, function ($a) use ($category) {
                return $a->fk_content_category ===
                    $category->pk_content_category;
            }));

            $map[$category->pk_content_category] = $category;
        }

        $contentsCount['articles'] =
            \ContentCategoryManager::countContentsByGroupType(1);

        return $this->render('category/list.tpl', [
            'categories'           => $map,
            'contents_count'       => $contentsCount,
            'language_data'        => $languageData,
            'multilanguage_enable' => in_array(
                'es.openhost.module.multilanguage',
                getService('core.instance')->activated_modules
            )
        ]);
    }

    /**
     * Shows the category information in a form
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("hasExtension('CATEGORY_MANAGER')
     *     and hasPermission('CATEGORY_UPDATE')")
     */
    public function showAction(Request $request)
    {
        $id = $request->query->getDigits('id', null);

        $ccm           = \ContentCategoryManager::get_instance();
        $allcategories = $ccm->categories;
        $languageData  = $this->getLocaleData('frontend', $request, true);

        if (empty($id)) {
            $category      = [];
            $subcategories = [];
        } else {
            $subcategories = $ccm->getSubcategories($id);
            $category      = new \ContentCategory($id);
        }

        $settings = $this->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('section_settings');

        return $this->render('category/new.tpl', [
            'category' => $this->categoryMapping($category),
            'extra_data' => [
                'categories'     => $this->categoryMapping($allcategories),
                'subcategories'  => $this->categoryMapping($subcategories),
                'modules'        => $this->getModules(),
                'configurations' => $settings,
                'image_path' => MEDIA_URL . MEDIA_DIR,
            ],
            'language_data'         => $languageData,
            'multilanguage_enable'  => in_array(
                'es.openhost.module.multilanguage',
                getService('core.instance')->activated_modules
            )
        ]);
    }

    /**
     * Deletes an category given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("hasExtension('CATEGORY_MANAGER')
     *     and hasPermission('CATEGORY_DELETE')")
     */
    public function deleteAction(Request $request)
    {
        $id       = $request->query->getDigits('id');
        $category = new \ContentCategory($id);

        if ($category->pk_content_category != null) {
            if ($category->delete($id)) {
                $user = $this->get('core.user');

                if ($user->getOrigin() != 'manager'
                    && in_array($id, $user->categories)
                    && ($key = array_search($id, $user->categories)) !== false
                ) {
                    unset($user->categories[$key]);
                    $this->get('orm.manager')->persist($user, 'instance');
                }

                dispatchEventWithParams('category.delete', ['category' => $category]);

                $this->get('session')->getFlashBag()->add(
                    'success',
                    _("Category deleted successfully.")
                );
            } else {
                $this->get('session')->getFlashBag()->add(
                    'error',
                    _("To delete a category previously you have to empty it.")
                );
            }
        } else {
            $this->get('session')->getFlashBag()->add(
                'error',
                _('You must give a valid id for delete the category.')
            );
        }

        if (!$request->isXmlHttpRequest()) {
            return $this->redirect($this->generateUrl('admin_categories'));
        } else {
            return new Response('ok');
        }
    }

    /**
     * Deletes all the category contents given the category id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("hasExtension('CATEGORY_MANAGER')
     *     and hasPermission('CATEGORY_DELETE')")
     */
    public function emptyAction(Request $request)
    {
        $id       = $request->query->getDigits('id');
        $category = new \ContentCategory($id);

        if ($category->pk_content_category != null) {
            if ($category->deleteContents()) {
                $this->get('session')->getFlashBag()->add(
                    'success',
                    _("Category emptied successfully.")
                );
            } else {
                $this->get('session')->getFlashBag()->add(
                    'error',
                    sprintf(
                        _("Unable to delete all the contents in the category '%s'"),
                        $category->title
                    )
                );
            }
        } else {
            $this->get('session')->getFlashBag()->add(
                'error',
                _('You must give a valid id for delete contents in a category.')
            );
        }

        if (!$request->isXmlHttpRequest()) {
            return $this->redirect($this->generateUrl('admin_categories'));
        } else {
            return new Response('ok');
        }
    }

    /**
     * Toggle the availability status for a category given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("hasExtension('CATEGORY_MANAGER')
     *     and hasPermission('CATEGORY_AVAILABLE')")
     */
    public function toggleAvailableAction(Request $request)
    {
        $id     = $request->query->getDigits('id', 0);
        $status = $request->query->getDigits('status', 0);

        $category = new \ContentCategory($id);

        if (is_null($category->pk_content_category)) {
            $this->get('session')->getFlashBag()->add(
                'error',
                sprintf(_('Unable to find a category with the id "%d"'), $id)
            );
        } else {
            $category->setAvailable($status);

            dispatchEventWithParams('category.update', ['category' => $category]);

            $this->get('session')->getFlashBag()->add(
                'success',
                sprintf(_('Successfully changed availability for category with id "%d"'), $id)
            );
        }

        return $this->redirect($this->generateUrl('admin_categories'));
    }

    /**
     * Toggle the availability status for a category given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("hasExtension('CATEGORY_MANAGER')
     *     and hasPermission('CATEGORY_AVAILABLE')")
     */
    public function toggleRssAction(Request $request)
    {
        $id     = $request->query->getDigits('id', 0);
        $status = $request->query->getInt('status', 0);

        $category = new \ContentCategory($id);

        if (is_null($category->pk_content_category)) {
            $this->get('session')->getFlashBag()->add(
                'error',
                sprintf(_('Unable to find a category with the id "%d"'), $id)
            );
        } else {
            $category->setInRss($status, $id);

            dispatchEventWithParams('category.update', ['category' => $category]);

            $this->get('session')->getFlashBag()->add(
                'success',
                sprintf(_('Successfully changed availability for category with id "%d"'), $id)
            );
        }

        return $this->redirect($this->generateUrl('admin_categories'));
    }

    /**
     * Handles the configuration for the categories manager
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("hasExtension('CATEGORY_MANAGER')
     *     and hasPermission('CATEGORY_SETTINGS')")
     */
    public function configAction(Request $request)
    {
        $ds = $this->get('orm.manager')->getDataSet('Settings', 'instance');

        if ('POST' == $request->getMethod()) {
            $sectionSettings = $request->request->get('section_settings');
            if ($sectionSettings['allowLogo'] == 1) {
                $path = MEDIA_PATH . '/sections';
                \Onm\FilesManager::createDirectory($path);
            }

            $ds->set('section_settings', $sectionSettings);

            $this->get('session')->getFlashBag()->add('success', _('Settings saved.'));

            return $this->redirect($this->generateUrl('admin_categories_config'));
        } else {
            $configurations = $ds->get(['section_settings']);

            return $this->render(
                'category/config.tpl',
                ['configs'   => $configurations]
            );
        }
    }

    /**
     *  Handles the list of modules needed for the view
     *
     *  @return Response the list of modules and the permissions for them
     */
    private function getModules()
    {
        $security = $this->get('core.security');

        $modules[1] = _('All contents');

        if ($security->hasExtension('ALBUM_MANAGER')) {
            $modules[7] = _('Album');
        }

        if ($security->hasExtension('VIDEO_MANAGER')) {
            $modules[9] = _('Video');
        }

        if ($security->hasExtension('POLL_MANAGER')) {
            $modules[11] = _('Poll');
        }

        if ($security->hasExtension('KIOSKO_MANAGER')) {
            $modules[14] = _('Kiosko');
        }

        if ($security->hasExtension('SPECIAL_MANAGER')) {
            $modules[10] = _('Special');
        }

        if ($security->hasExtension('BOOK_MANAGER')) {
            $modules[15] = _('Book');
        }

        if ($security->hasPermission('MASTER')) {
            $modules[0] = _('Internal');
        }

        return $modules;
    }

    /**
     *  Mapping to transform the category object for hidde the name fields too
     * mutch caractetistic of the DB.
     *
     *  @param mixed $category category/categories to transform
     *
     *  @return mixed Return category/categories tranformed
     */
    private function categoryMapping($category)
    {
        if (is_array($category)) {
            return array_map(function ($category) {
                return $this->categoryMapping($category);
            }, $category);
        }

        if (!is_object($category)) {
            return $category;
        }

        return [
            'id'                => $category->id,
            'title'             => $category->title,
            'name'              => $category->name,
            'inmenu'            => $category->inmenu,
            'posmenu'           => $category->posmenu,
            'internal_category' => $category->internal_category,
            'subcategory'       => $category->fk_content_category,
            'logo_path'         => $category->logo_path,
            'color'             => $category->color,
            'params'            => $category->params
        ];
    }
}
