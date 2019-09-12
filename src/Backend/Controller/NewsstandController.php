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

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class NewsstandController extends BackendController
{
    /**
     * {@inheritdoc}
     */
    protected $extension = 'KIOSKO_MANAGER';

    /**
     * {@inheritdoc}
     */
    protected $permissions = [
        'create' => 'KIOSKO_CREATE',
        'update' => 'KIOSKO_UPDATE',
        'list'   => 'KIOSKO_ADMIN',
        'show'   => 'KIOSKO_UPDATE',
    ];

    /**
     * {@inheritdoc}
     */
    protected $groups = [
        'preview' => 'album_inner'
    ];

    /**
     * {@inheritdoc}
     */
    protected $resource = 'newsstand';

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
}
