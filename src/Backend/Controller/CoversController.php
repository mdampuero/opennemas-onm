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
namespace Backend\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Onm\Security\Acl;
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

        if (!defined('KIOSKO_DIR')) {
            define('KIOSKO_DIR', "kiosko".SS);
        }

        $contentType = \ContentManager::getContentTypeIdFromName('kiosko');

        $category = $this->get('request')->query->getDigits('category', 'all');

        $ccm = \ContentCategoryManager::get_instance();
        list($parentCategories, $subcat, $categoryData) =
            $ccm->getArraysMenu($category, $contentType);

        $timezones = \DateTimeZone::listIdentifiers();
        $timezone  = new \DateTimeZone($timezones[s::get('time_zone', 'UTC')]);

        $this->view->assign(
            array(
                'category'     => $category,
                'subcat'       => $subcat,
                'allcategorys' => $parentCategories,
                'datos_cat'    => $categoryData,
                'timezone'     => $timezone->getName()
            )
        );
    }

    /**
     * Shows the list of the
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("has_role('KIOSKO_ADMIN')")
     **/
    public function listAction(Request $request)
    {
        return $this->render(
            'covers/list.tpl',
            array('KIOSKO_IMG_URL' => INSTANCE_MEDIA.KIOSKO_DIR,)
        );
    }

    /**
     * Show the list of the covers with favorite flag enabled
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("has_role('KIOSKO_ADMIN')")
     **/
    public function widgetAction(Request $request)
    {
        $category = 'widget';

        return $this->render(
            'covers/list.tpl',
            array(
                'KIOSKO_IMG_URL' => INSTANCE_MEDIA.KIOSKO_DIR,
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
     *
     * @Security("has_role('KIOSKO_UPDATE')")
     **/
    public function showAction(Request $request)
    {
        $id = $request->query->getDigits('id', null);

        $cover = new \Kiosko($id);

        if (is_null($cover->id)) {
            $this->get('session')->getFlashBag()->add(
                'error',
                sprintf(_('Unable to find the cover with the id "%d"'), $id)
            );

            return $this->redirect($this->generateUrl('admin_covers'));
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
     *
     * @Security("has_role('KIOSKO_CREATE')")
     **/
    public function createAction(Request $request)
    {
        if ('POST' !== $request->getMethod()) {
            return $this->render('covers/read.tpl');
        }

        $postInfo = $request->request;

        $coverData = array(
            'title'          => $postInfo->filter('title', null, FILTER_SANITIZE_STRING),
            'metadata'       => $postInfo->filter('metadata', null, FILTER_SANITIZE_STRING),
            'type'           => (int) $postInfo->getDigits('type', 0),
            'category'       => (int) $postInfo->getDigits('category'),
            'content_status' => (int) $postInfo->getDigits('content_status', 1),
            'favorite'       => (int) $postInfo->getDigits('favorite', 1),
            'date'           => $postInfo->filter('date', null, FILTER_SANITIZE_STRING),
            'price'          => $postInfo->filter('price', null, FILTER_SANITIZE_NUMBER_FLOAT),
            'fk_publisher'   => (int) $_SESSION['userid'],
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
            $this->get('session')->getFlashBag()->add('error', $e->getMessage());

            return $this->redirect(
                $this->generateUrl(
                    'admin_covers',
                    array(
                        'category' => $postInfo->getDigits('category'),
                    )
                )
            );
        }
    }

    /**
     * Updates the cover information provided by POST request
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("has_role('KIOSKO_UPDATE')")
     **/
    public function updateAction(Request $request)
    {
        $id = $request->query->getDigits('id');

        $cover = new \Kiosko($id);
        if ($cover->id == null) {
            $this->get('session')->getFlashBag()->add('error', _('Cover id not valid.'));

            return $this->redirect(
                $this->generateUrl(
                    'admin_covers',
                    array(
                        'category' => $cover->category,
                    )
                )
            );
        }
        $_POST['fk_user_last_editor'] = $_SESSION['userid'];

        if (!Acl::isAdmin()
            && !Acl::check('CONTENT_OTHER_UPDATE')
            && !$cover->isOwner($_SESSION['userid'])
        ) {
            $this->get('session')->getFlashBag()->add(
                'error',
                _("You can't modify this cover because you don't have enough privileges.")
            );
        } else {
            $cover->update($_POST);
            $this->get('session')->getFlashBag()->add(
                'success',
                _("Cover updated successfully.")
            );
        }

        return $this->redirect(
            $this->generateUrl(
                'admin_cover_show',
                array('id' => $cover->id)
            )
        );
    }

    /**
     * Deletes a video given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("has_role('KIOSKO_DELETE')")
     **/
    public function deleteAction(Request $request)
    {
        $id   = $request->query->getDigits('id');
        $page = $request->query->getDigits('page', 1);

        if (!empty($id)) {
            $cover = new \Kiosko($id);
            // Delete relations
            $rel= new \RelatedContent();
            $rel->deleteAll($id);

            $cover->delete($id, $_SESSION['userid']);
            $this->get('session')->getFlashBag()->add('successs', sprintf(_("Cover %s deleted successfully."), $cover->title));
        } else {
            $this->get('session')->getFlashBag()->add('error', _('You must give an id for delete the cover.'));
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
     * Save positions for widget
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("has_role('KIOSKO_ADMIN')")
     **/
    public function savePositionsAction(Request $request)
    {
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
                $pos++;
            }
        }

        if ($result) {
            $msg = _("Positions saved successfully.");
        } else {
            $msg = _("Unable to save the new positions. Please contact with your system administrator.");
        }

        return new Response($msg);
    }

    /**
     * Handles the configuration of the covers module
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("has_role('KIOSKO_ADMIN')")
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
        }

        $settingsRAW = $request->request->get('kiosko_settings');
        $settings = array(
            'kiosko_settings' => array(
                'orderFrontpage' => filter_var($settingsRAW['orderFrontpage'], FILTER_SANITIZE_STRING),
            )
        );

        foreach ($settings as $key => $value) {
            s::set($key, $value);
        }

        $this->get('session')->getFlashBag()->add(
            'success',
            _('Settings saved successfully.')
        );

        return $this->redirect($this->generateUrl('admin_covers_config'));
    }
}
