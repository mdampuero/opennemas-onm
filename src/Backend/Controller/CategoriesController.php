<?php
/**
 * Handles the actions for categories
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

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Onm\Framework\Controller\Controller;
use Onm\Message as m;
use Onm\Settings as s;

/**
 * Handles the actions for categories
 *
 * @package Backend_Controllers
 **/
class CategoriesController extends Controller
{
    /**
     * Common code for all the actions
     *
     * @return void
     **/
    public function init()
    {
        //Check if module is activated in this onm instance
        \Onm\Module\ModuleManager::checkActivatedOrForward('CATEGORY_MANAGER');

        $this->checkAclOrForward('CATEGORY_ADMIN');
    }

    /**
     * Lists all the available categories
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function listAction(Request $request)
    {
        $ccm = \ContentCategoryManager::get_instance();

        // Get contents by group
        $groups['articles']       = $ccm->countContentsByGroupType(1);
        $groups['photos']         = $ccm->countContentsByGroupType(8);
        $groups['advertisements'] = $ccm->countContentsByGroupType(2);

        $allcategorys  = $ccm->categories;
        $categorygorys = $subcategorys =array();
        $contentsCount =  $subContentsCount = array();

        $i = 0;
        foreach ($allcategorys as $category) {
            if ($category->internal_category !=0
                && $category->fk_content_category == 0
            ) {
                if (isset($groups['articles'][$category->pk_content_category])) {
                    $contentsCount[$i]['articles'] =
                        $groups['articles'][$category->pk_content_category];
                } else {
                    $contentsCount[$i]['articles'] = 0;
                }
                if (isset($groups['photos'][$category->pk_content_category])) {
                    $contentsCount[$i]['photos'] =
                        $groups['photos'][$category->pk_content_category];
                } else {
                    $contentsCount[$i]['photos'] = 0;
                }
                if (isset($groups['advertisements'][$category->pk_content_category])) {
                    $contentsCount[$i]['advertisements'] =
                        $groups['advertisements'][$category->pk_content_category];
                } else {
                    $contentsCount[$i]['advertisements'] = 0;
                }
                //Unserialize category param field
                $category->params = unserialize($category->params);
                $categorygorys[$i] = $category;

                $resul = $ccm->getSubcategories($category->pk_content_category);
                $j=0;
                foreach ($resul as $category) {
                    if (isset($groups['articles'][$category->pk_content_category])) {
                        $subContentsCount[$i][$j]['articles'] = $groups['articles'][$category->pk_content_category];
                    } else {
                        $subContentsCount[$i][$j]['articles'] = 0;
                    }
                    if (isset($groups['photos'][$category->pk_content_category])) {
                        $subContentsCount[$i][$j]['photos'] = $groups['photos'][$category->pk_content_category];
                    } else {
                        $subContentsCount[$i][$j]['photos'] = 0;
                    }
                    if (isset($groups['advertisements'][$category->pk_content_category])) {
                        $subContentsCount[$i][$j]['advertisements'] =
                            $groups['advertisements'][$category->pk_content_category];
                    } else {
                        $subContentsCount[$i][$j]['advertisements'] = 0;
                    }

                    //Unserialize subcategory param field
                    $category->params = unserialize($category->params);
                    $j++;
                }
                $subcategorys[$i]=$resul;
                 $i++;
            }
        }

        return $this->render(
            'category/list.tpl',
            array(
                'categorys'        => $categorygorys,
                'num_contents'     => $contentsCount,
                'num_sub_contents' => $subContentsCount,
                'subcategorys'     => $subcategorys,
                'ordercategorys'   => $allcategorys,
                'allcategorys'     => $allcategorys
            )
        );
    }

    /**
     * Handles the creation of new categories
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function createAction(Request $request)
    {
        $this->checkAclOrForward('CATEGORY_CREATE');

        $configurations = s::get('section_settings');
        $ccm = \ContentCategoryManager::get_instance();

        if ('POST' == $request->getMethod()) {

            $logoPath = '';
            if (!empty($_FILES) && isset($_FILES['logo_path'])) {
                $nameFile = $_FILES['logo_path']['name'];
                $uploaddir= MEDIA_PATH.'/sections/'.$nameFile;

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
                $user = new \User();
                $user->addCategoryToUser($_SESSION['userid'], $category->pk_content_category);
                $_SESSION['accesscategories'] = $user->getAccessCategoryIds($_SESSION['userid']);

                getService('cache')->delete(CACHE_PREFIX.'_content_categories');
                m::add(_("Category created successfully."), m::SUCCESS);
            }

            $continue = $request->request->getDigits('continue', 0);
            if ($continue) {
                return $this->redirect(
                    $this->generateUrl(
                        'admin_category_show',
                        array('id' => $category->pk_content_category)
                    )
                );
            } else {
                return $this->redirect($this->generateUrl('admin_categories'));
            }
        } else {
            $allcategorys  = $ccm->categories;
            $categories = array();
            foreach ($allcategorys as $category) {
                if ($category->internal_category != 0
                    && $category->fk_content_category == 0
                ) {
                    $categories[] = $category;
                }
            }

            return $this->render(
                'category/new.tpl',
                array(
                    'configurations' => $configurations,
                    'allcategorys'   => $categories,
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
     **/
    public function showAction(Request $request)
    {
        $this->checkAclOrForward('CATEGORY_UPDATE');

        $id = $request->query->getDigits('id');

        $ccm = \ContentCategoryManager::get_instance();

        $category = new \ContentCategory($id);
        if ($category->pk_content_category != null) {
            $allcategorys = $ccm->categories;
            $subcategorys = $ccm->getSubcategories($id);

            $categories = array();
            foreach ($allcategorys as $categoryItem) {
                if ($categoryItem->pk_content_category != $id &&
                   ($categoryItem->internal_category != 0 && $categoryItem->fk_content_category == 0)
                ) {
                    $categories[] = $categoryItem;
                }
            }

            return $this->render(
                'category/new.tpl',
                array(
                    'allcategorys'   => $categories,
                    'configurations' => s::get('section_settings'),
                    'category'       => $category,
                    'subcategorys'   => $subcategorys
                )
            );
        } else {
            m::add(_('Unable to find a category for the give id.'), m::ERROR);

            return $this->redirect($this->generateUrl('admin_categories'));
        }
    }

    /**
     * Updates the category information send by POST
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function updateAction(Request $request)
    {
        $this->checkAclOrForward('CATEGORY_UPDATE');

        $id = $request->query->getDigits('id');
        $params = $request->request->get('params');
        $inrss = (array_key_exists('inrss', $params) && $params['inrss'] == true);

        // Check empty data
        if (count($request->request) < 1) {
            m::add(_("Category data sent not valid."), m::ERROR);

            return $this->redirect($this->generateUrl('admin_category_show', array('id' => $id)));
        }

        $data = array(
            'id'                  => $id,
            'name'                => $request->request->filter('name', '', FILTER_SANITIZE_STRING),
            'title'               => $request->request->filter('title', '', FILTER_SANITIZE_STRING),
            'inmenu'              => $request->request->getDigits('inmenu', 0),
            'subcategory'         => $request->request->getDigits('subcategory', 0),
            'internal_category'   => $request->request->getDigits('internal_category'),
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
            getService('cache')->delete(CACHE_PREFIX.'_content_categories');

            m::add(sprintf(_('Category "%s" updated successfully.'), $data['title']), m::SUCCESS);
        }

        /* Limpiar la cache de portada de todas las categorias */
        if ($data['inmenu'] == 1) {
            \Content::refreshFrontpageForAllCategories();
        }

        $continue = $request->request->getDigits('continue', 0);
        if ($continue) {
            return $this->redirect($this->generateUrl('admin_category_show', array('id' => $id)));
        } else {
            return $this->redirect($this->generateUrl('admin_categories'));
        }
    }

    /**
     * Deletes an category given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function deleteAction(Request $request)
    {
        $this->checkAclOrForward('CATEGORY_DELETE');

        $id = $request->query->getDigits('id');
        $category = new \ContentCategory($id);

        if ($category->pk_content_category != null) {

            if ($category->delete($id)) {
                $user = new \User();
                $user->delCategoryToUser($_SESSION['userid'], $id);

                $_SESSION['accesscategories'] =
                    $user->getAccessCategoryIds($_SESSION['userid']);

                getService('cache')->delete(CACHE_PREFIX.'_content_categories');
                m::add(_("Category deleted successfully."), m::SUCCESS);
            } else {
                m::add(_("To delete a category previously you have to empty it."), m::ERROR);
            }

        } else {
            m::add(_('You must give a valid id for delete the category.'), m::ERROR);
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
     **/
    public function emptyAction(Request $request)
    {
        $this->checkAclOrForward('CATEGORY_DELETE');

        $id = $request->query->getDigits('id');
        $category = new \ContentCategory($id);

        if ($category->pk_content_category != null) {

            if ($category->deleteContents()) {
                m::add(_("Category emptied successfully."), m::SUCCESS);
            } else {
                m::add(
                    sprintf(
                        _("Unable to delete all the contents in the category '%s'"),
                        $category->title
                    ),
                    m::ERROR
                );
            }

        } else {
            m::add(_('You must give a valid id for delete contents in a category.'), m::ERROR);
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
     **/
    public function toggleAvailableAction(Request $request)
    {
        $this->checkAclOrForward('CATEGORY_AVAILABLE');

        $request  = $this->get('request');
        $id       = $request->query->getDigits('id', 0);
        $status   = $request->query->getDigits('status', 0);

        $category = new \ContentCategory($id);

        if (is_null($category->pk_content_category)) {
            m::add(sprintf(_('Unable to find a category with the id "%d"'), $id), m::ERROR);
        } else {
            $category->setInMenu($status);

            getService('cache')->delete(CACHE_PREFIX.'_content_categories');

            // Limpiar la cache de portada de todas las categorias
            // $refresh = Content::refreshFrontpageForAllCategories();

            m::add(sprintf(_('Successfully changed availability for category with id "%d"'), $id), m::SUCCESS);
        }

        return $this->redirect($this->generateUrl('admin_categories'));
    }

    /**
     * Toggle the availability status for a category given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function toggleRssAction(Request $request)
    {
        $this->checkAclOrForward('CATEGORY_AVAILABLE');

        $request  = $this->get('request');
        $id       = $request->query->getDigits('id', 0);
        $status   = $request->query->getDigits('status', 0);

        $category = new \ContentCategory($id);

        if (is_null($category->pk_content_category)) {
            m::add(sprintf(_('Unable to find a category with the id "%d"'), $id), m::ERROR);
        } else {
            $category->setInRss($status, $id);

            getService('cache')->delete(CACHE_PREFIX.'_content_categories');

            // Limpiar la cache de portada de todas las categorias
            // $refresh = Content::refreshFrontpageForAllCategories();

            m::add(sprintf(_('Successfully changed availability for category with id "%d"'), $id), m::SUCCESS);
        }

        return $this->redirect($this->generateUrl('admin_categories'));
    }

    /**
     * Handles the configuration for the categories manager
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function configAction(Request $request)
    {
        $this->checkAclOrForward('CATEGORY_SETTINGS');

        if ('POST' == $request->getMethod()) {
            $sectionSettings = $request->request->get('section_settings');
            if ($sectionSettings['allowLogo'] == 1) {
                $path = MEDIA_PATH.'/sections';
                \FilesManager::createDirectory($path);
            }

            s::set('section_settings', $sectionSettings);

            m::add(_('Settings saved.'), m::SUCCESS);

            return $this->redirect($this->generateUrl('admin_categories_config'));
        } else {
            $configurations = s::get(array('section_settings',));

            return $this->render(
                'category/config.tpl',
                array('configs'   => $configurations,)
            );
        }
    }
}
