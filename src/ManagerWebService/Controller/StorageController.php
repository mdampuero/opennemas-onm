<?php

/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ManagerWebService\Controller;

use Common\Core\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class StorageController extends Controller
{
    /**
     * Returns the list of prompts as JSON.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     *
     */
    public function configAction()
    {
        $storage_settings = $this->container->get('orm.manager')
            ->getDataSet('Settings', 'manager')
            ->get('storage_settings', []);
        return new JsonResponse([
            'storage_settings'  => $storage_settings
        ]);
    }

    /**
     * Save prompt settings
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     *
     */
    public function configSaveAction(Request $request)
    {
        $request = $request->request->all();
        $msg     = $this->get('core.messenger');

        $this->get('orm.manager')
            ->getDataSet('Settings', 'manager')
            ->set($request);

        $msg->add(_('Prompt saved successfully'), 'success');

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }
}
