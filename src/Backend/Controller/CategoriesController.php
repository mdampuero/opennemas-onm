<?php
/**
 * Handles the actions for categories
 *
 * @package Backend_Controllers
 */
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Backend\Controller;

use Common\Core\Annotation\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Common\Core\Controller\Controller;
use Onm\Settings as s;

/**
 * Handles the actions for categories
 *
 * @package Backend_Controllers
 */
class CategoriesController extends Controller
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

        $categories = $this->get('data.manager.filter')->set($categories)->filter('unlocalize', [
            'keys' => \ContentCategory::getL10nKeys(),
            'locale' => $languageData['default']
        ])->get();

        $cm->sortCategories($categories, $languageData);

        $contentsCount['articles'] =
            \ContentCategoryManager::countContentsByGroupType(1);

        return $this->render('category/list.tpl', [
            'categories'     => $categories,
            'contents_count' => $contentsCount,
            'language_data'  => $languageData
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
        $fm            = $this->get('data.manager.filter');

        // we adapt the category data and if the value returned and
        // multilanguage field is a string, create a new array with all values
        $allcategories = $fm->set($allcategories)->filter('localize', [
            'keys'   => \ContentCategory::getL10nKeys(),
            'locale' => $languageData['default']
        ])->get();

        if (empty($id)) {
            $category      = null;
            $subcategories = [];
        } else {
            $subcategories = $ccm->getSubcategories($id);
            $category      = new \ContentCategory($id);
            if ($category->pk_content_category != null) {
                $ccm      = \ContentCategoryManager::get_instance();
                $category = $fm->set($category)->filter('unlocalize', [
                    'keys'      => \ContentCategory::getL10nKeys()
                ])->get();
            }
        }

        return $this->render('category/new.tpl', [
            'categoryData' => [
                'categories'            => $this->categoryMapping($allcategories),
                'configurations'        => s::get('section_settings'),
                'category'              => $this->categoryMapping($category),
                'subcategories'         => $this->categoryMapping($subcategories),
                'internal_categories'   => $this->getInternalCategories(),
                'image_path'            => MEDIA_URL . MEDIA_DIR,
                'language_data'         => $languageData,
                'multilanguage_enable'  => $this->get('core.security')->hasExtension('es.openhost.module.multilanguage')
            ]
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
        $status = $request->query->getDigits('status', 0);

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
        if ('POST' == $request->getMethod()) {
            $sectionSettings = $request->request->get('section_settings');
            if ($sectionSettings['allowLogo'] == 1) {
                $path = MEDIA_PATH . '/sections';
                \Onm\FilesManager::createDirectory($path);
            }

            s::set('section_settings', $sectionSettings);

            $this->get('session')->getFlashBag()->add('success', _('Settings saved.'));

            return $this->redirect($this->generateUrl('admin_categories_config'));
        } else {
            $configurations = s::get(['section_settings']);

            return $this->render(
                'category/config.tpl',
                ['configs'   => $configurations]
            );
        }
    }

    /**
     *  Handles the list of internalCategories needed for the view
     *
     *  @return Response the list of internal categories and the permissions for them
     */
    private function getInternalCategories()
    {
        $internalCategories = [1,7,9,10,11,14,15];
        $allowedCategories  = [1];

        $security = $this->get('core.security');

        if ($security->hasExtension('ALBUM_MANAGER')) {
            $allowedCategories[] = 7;
        }

        if ($security->hasExtension('VIDEO_MANAGER')) {
            $allowedCategories[] = 9;
        }

        if ($security->hasExtension('POLL_MANAGER')) {
            $allowedCategories[] = 11;
        }

        if ($security->hasExtension('KIOSKO_MANAGER')) {
            $allowedCategories[] = 14;
        }

        if ($security->hasExtension('SPECIAL_MANAGER')) {
            $allowedCategories[] = 10;
        }

        if ($security->hasExtension('BOOK_MANAGER')) {
            $allowedCategories[] = 15;
        }

        if ($security->hasPermission('MASTER')) {
            $allowedCategories[] = 0;
        }

        $internalCategoriesList = [];
        foreach (\ContentManager::getContentTypes() as $internalCategory) {
            if (in_array($internalCategory['pk_content_type'], $internalCategories)) {
                $internalCategoriesList[$internalCategory['pk_content_type']] = $internalCategory;
            }
        }

        return [
            'internalCategories'    => $internalCategoriesList,
            'allowedCategories'     => $allowedCategories
        ];
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
            'subcategory'       => $category-> fk_content_category ,
            'logo_path'         => $category->logo_path,
            'color'             => $category->color,
            'params'            => $category->params
        ];
    }
}
