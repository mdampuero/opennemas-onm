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
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Common\Core\Controller\Controller;

/**
 * Handles the actions for handling the pdf covers
 *
 * @package Backend_Controllers
 */
class NewsstandsController extends Controller
{
    /**
     * Shows the list of the
     *
     * @return Response the response object
     *
     * @Security("hasExtension('KIOSKO_MANAGER')
     *     and hasPermission('KIOSKO_ADMIN')")
     */
    public function listAction()
    {
        return $this->render('newsstand/list.tpl', array_merge($this->getExtraData(), [
            'KIOSKO_IMG_URL' => INSTANCE_MEDIA . KIOSKO_DIR,
        ]));
    }

    /**
     * Show the list of the kiosko with favorite flag enabled
     *
     * @return Response the response object
     *
     * @Security("hasExtension('KIOSKO_MANAGER')
     *     and hasPermission('KIOSKO_ADMIN')")
     */
    public function widgetAction()
    {
        return $this->render('newsstand/list.tpl', [
            'KIOSKO_IMG_URL' => INSTANCE_MEDIA . KIOSKO_DIR,
        ]);
    }

    /**
     * List the form for create or load contents in a newsletter.
     *
     * @return Response The response object.
     *
     * @Security("hasExtension('KIOSKO_MANAGER')
     *     and hasPermission('KIOSKO_CREATE')")
     */
    public function createAction()
    {
        return $this->render('newsstand/item.tpl');
    }

    /**
     * Shows the newsletter template information given its id
     *
     * @param integer $id The user id.
     *
     * @return Response The response object.
     *
     * @Security("hasExtension('KIOSKO_MANAGER')
     *     and hasPermission('KIOSKO_UPDATE')")
     */
    public function showAction($id)
    {
        return $this->render('newsstand/item.tpl', [ 'id' => $id ]);
    }

    /**
     * Handles the configuration of the kiosko module
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("hasExtension('KIOSKO_MANAGER')
     *     and hasPermission('KIOSKO_ADMIN')")
     */
    public function configAction(Request $request)
    {
        $ds = $this->get('orm.manager')->getDataSet('Settings', 'instance');

        if ('POST' != $request->getMethod()) {
            return $this->render('newsstand/config.tpl', [
                'configs' => $ds->get([ 'kiosko_settings' ])
            ]);
        }

        $settingsRAW = $request->request->get('kiosko_settings');
        $settings    = [ 'kiosko_settings' => [
            'orderFrontpage' => filter_var($settingsRAW['orderFrontpage'], FILTER_SANITIZE_STRING),
        ] ];

        try {
            $ds->set($settings);

            $this->get('session')->getFlashBag()
                ->add('success', _('Settings saved successfully.'));
        } catch (\Exception $e) {
            $this->get('session')->getFlashBag()
                ->add('success', _('Unable to save the settings.'));
        }


        return $this->redirect($this->generateUrl('backend_newsstands_config'));
    }

    /**
     * Returns a list of extra data to use in  the create/edit item form
     *
     * @return array
     **/
    private function getExtraData()
    {
        $extra = [];

        $security   = $this->get('core.security');
        $converter  = $this->get('orm.manager')->getConverter('Category');
        $categories = $this->get('orm.manager')
            ->getRepository('Category')
            ->findBy('internal_category = 1'); // review this filter to search for commen and specific for kiosko

        $categories = array_filter($categories, function ($a) use ($security) {
            return $security->hasCategory($a->pk_content_category);
        });

        $extra['categories'] = $converter->responsify($categories);
        array_unshift($extra['categories'], [
            'pk_content_category' => null,
            'title'               => _('Select a category...')
        ]);

        return $extra;
    }
}
