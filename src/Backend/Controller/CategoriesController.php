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
use Common\Data\Adapter\MultiOptionAdapter;

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
     */
    public function listAction(Request $request)
    {
        $categories   = $this->get('category_repository')->findBy(null, 'name ASC');
        $languageData = $this->getLocaleData('frontend');

        $categories = array_map(
            function ($category) use ($languageData) {
                return $this->get('data.manager.adapter')->adapt('multi_option', $category, $languageData);
            },
            $categories
        );
        $contentsCount['articles'] = \ContentCategoryManager::countContentsByGroupType(1);

        return $this->render(
            'category/list.tpl',
            array(
                'categories'        => $categories,
                'contents_count'    => $contentsCount,
                'language_data'     => $languageData
            )
        );
    }

    /**
     * Handles the creation of new categories
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("hasExtension('CATEGORY_MANAGER')
     *     and hasPermission('CATEGORY_CREATE')")
     */
    public function createAction(Request $request)
    {
        $configurations = s::get('section_settings');
        $ccm = \ContentCategoryManager::get_instance();

        if ('POST' == $request->getMethod()) {
            $logoPath = '';
            if (!empty($_FILES) && isset($_FILES['logo_path'])) {
                $nameFile   = $_FILES['logo_path']['name'];
                $uploaddir  = MEDIA_PATH.'/sections/'.$nameFile;

                if (move_uploaded_file($_FILES["logo_path"]["tmp_name"], $uploaddir)) {
                    $logoPath = $nameFile;
                }
            }

            $params = $request->request->get('params');
            $data = array(
                'title'             => $request->request->filter('title', '', FILTER_SANITIZE_STRING),
                'inmenu'            => $request->request->getDigits('inmenu', 0),
                'subcategory'       => $request->request->getDigits('subcategory'),
                'internal_category' => $request->request->getDigits('internal_category'),
                'logo_path'         => $logoPath,
                'color'             => $request->request->filter('color', '', FILTER_SANITIZE_STRING),
                'params'            => array(
                    'inrss' => $params['inrss'],
                ),
            );

            $category = new \ContentCategory();

            if ($category->create($data)) {
                $user = $this->get('core.user');

                if ($user->getOrigin() != 'manager') {
                    $user->categories[] = $category->pk_content_category;
                    $this->get('orm.manager')->persist($user, 'instance');
                }

                dispatchEventWithParams('category.create', ['category' => $category]);

                $this->get('session')->getFlashBag()->add(
                    'success',
                    _("Category created successfully.")
                );
            }

            return $this->redirect(
                $this->generateUrl(
                    'admin_category_show',
                    array('id' => $category->pk_content_category)
                )
            );
        } else {
            $allcategories = $ccm->categories;
            $categories    = array();
            foreach ($allcategories as $category) {
                if ($category->internal_category != 0
                    && $category->fk_content_category == 0
                ) {
                    $categories[] = $category;
                }
            }

            $languageData = $this->getLocaleData('frontend', $request);
            $jsonData     = json_encode(
                array(
                    'categories'            => $allcategories,
                    'configurations'        => s::get('section_settings'),
                    'subcategories'         => [],
                    'internal_categories'   => $this->getInternalCategories(),
                    'image_path'            => MEDIA_URL . MEDIA_DIR,
                    'language_data'         => $languageData,
                )
            );

            return $this->render(
                'category/new.tpl',
                array(
                    'categoryData'  => $jsonData
                )
            );
        }
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
        $id = $request->query->getDigits('id');

        $category = new \ContentCategory($id);
        if ($category->pk_content_category != null) {
            $ccm           = \ContentCategoryManager::get_instance();
            $allcategorys  = $ccm->categories;
            $subcategories = $ccm->getSubcategories($id);
            $languageData  = $this->getLocaleData('frontend', $request);
            // we adapt the category data and if the value returned y multilanguage field is a string, create a new
            //array with all values
            $category = $this->get('data.manager.adapter')->adapt('multi_option', $category, [
                    MultiOptionAdapter::PARAM_DEFAULT_KEY_VALUE => $languageData['default'],
                    MultiOptionAdapter::PARAM_MULTIVALUED_FIELDS => ['title', 'name']
            ]);

            $jsonData = json_encode(
                array(
                    'categories'            => $allcategorys,
                    'configurations'        => s::get('section_settings'),
                    'category'              => $category,
                    'subcategories'         => $subcategories,
                    'internal_categories'   => $this->getInternalCategories(),
                    'image_path'            => MEDIA_URL . MEDIA_DIR,
                    'language_data'         => $languageData,
                )
            );

            return $this->render(
                'category/new.tpl',
                array(
                    'categoryData'   => $jsonData
                )
            );
        }

        $this->get('session')->getFlashBag()->add(
            'error',
            _('Unable to find a category for the given id.')
        );

        return $this->redirect($this->generateUrl('admin_categories'));
    }

    /**
     * Updates the category information send by POST
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("hasExtension('CATEGORY_MANAGER')
     *     and hasPermission('CATEGORY_UPDATE')")
     */
    public function updateAction(Request $request)
    {
        $id     = $request->query->getDigits('id');
        $params = $request->request->get('params');
        $inrss  = ($params && array_key_exists('inrss', $params) && $params['inrss'] == true);

        // Check empty data
        if (count($request->request) < 1) {
            $this->get('session')->getFlashBag()->add(
                'error',
                _('Category data sent not valid.')
            );

            return $this->redirect($this->generateUrl('admin_category_show', array('id' => $id)));
        }

        $data = array(
            'id'                  => $id,
            'name'                => $request->request->filter('name', '', FILTER_SANITIZE_STRING),
            'title'               => $request->request->filter('title', '', FILTER_SANITIZE_STRING),
            'inmenu'              => $request->request->getDigits('inmenu', 0),
            'subcategory'         => $request->request->getDigits('subcategory', 0),
            'internal_category'   => $request->request->getDigits('internal_category'),
            'logo_path'           => $request->request->filter('logo_path', '', FILTER_SANITIZE_STRING),
            'color'               => $request->request->filter('color', '', FILTER_SANITIZE_STRING),
            'params'  => array(
                'inrss' => $inrss,
            ),
        );

        // If file was attached, handle it
        if (!empty($_FILES) && isset($_FILES['logo_path'])) {
            $nameFile = $_FILES['logo_path']['name'];
            $uploaddir= MEDIA_PATH.'/sections/'.$nameFile;

            if (move_uploaded_file($_FILES["logo_path"]["tmp_name"], $uploaddir)) {
                $data['logo_path'] = $nameFile;
            }
        }

        $category = new \ContentCategory($id);
        if ($category->update($data)) {
            dispatchEventWithParams('category.update', ['category' => $category]);

            $this->get('session')->getFlashBag()->add(
                'success',
                sprintf(_('Category "%s" updated successfully.'), $data['title'])
            );
        }

        return $this->redirect($this->generateUrl('admin_category_show', array('id' => $id)));
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
        $id = $request->query->getDigits('id');
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
        $id = $request->query->getDigits('id');
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
        $id       = $request->query->getDigits('id', 0);
        $status   = $request->query->getDigits('status', 0);

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
        $id       = $request->query->getDigits('id', 0);
        $status   = $request->query->getDigits('status', 0);

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
                $path = MEDIA_PATH.'/sections';
                \Onm\FilesManager::createDirectory($path);
            }

            s::set('section_settings', $sectionSettings);

            $this->get('session')->getFlashBag()->add('success', _('Settings saved.'));

            return $this->redirect($this->generateUrl('admin_categories_config'));
        } else {
            $configurations = s::get(array('section_settings',));

            return $this->render(
                'category/config.tpl',
                array('configs'   => $configurations,)
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
}
