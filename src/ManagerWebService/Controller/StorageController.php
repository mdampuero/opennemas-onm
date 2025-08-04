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
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class StorageController extends Controller
{
    /**
     * Returns the config storage as JSON.
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
     * Save storage settings
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

    /**
     * Returns the list of instances as JSON.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     */
    public function instancesAction(Request $request)
    {
        $oql = $request->query->get('oql', '');

        if (!$this->get('core.security')->hasPermission('MASTER')) {
            $condition = sprintf('owner_id = %s ', $this->get('core.user')->id);

            $oql = $this->get('orm.oql.fixer')->fix($oql)
                ->addCondition($condition)->getOql();
        }

        $repository = $this->get('orm.manager')->getRepository('Instance');
        $converter  = $this->get('orm.manager')->getConverter('Instance');
        $helper     = $this->get('core.helper.instance');

        $instances = $repository->findBy($oql);
        $total     = $repository->countBy($oql);

        $instances = array_map(function ($a) use ($converter, $helper) {
            $data                     = $converter->responsify($a->getData());
            $data['storage_settings'] = $helper->getStorageSettings($a);
            return $data;
        }, $instances);

        return new JsonResponse([
            'total'   => $total,
            'results' => $instances,
            'extra'   => [
                'service' => 'storage'
            ],
            'oql' => $oql,
        ]);
    }

    /**
     * Save instance storage settings
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     */
    public function instancesSaveAction(Request $request)
    {
        $request = $request->query->all();
        $msg     = $this->get('core.messenger');

        $id               = $request['id'] ?? 0;
        $storage_settings = $request['storage_settings'] ?? [];
        $em               = $this->get('orm.manager');
        $instance         = $em->getRepository('Instance')->find($id);

        if (!$this->get('core.security')->hasInstance($instance->internal_name)) {
            throw new AccessDeniedException();
        }

        $this->get('core.loader')->configureInstance($instance);
        $em->getDataSet('Settings', 'instance')->set('storage_settings', $storage_settings);

        $msg->add(_('Settings saved successfully'), 'success');

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * Returns the list tasks as JSON.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     *
     */

    public function tasksAction(Request $request)
    {
        $oql          = $request->query->get('oql', '');
        $repository   = $this->get('orm.manager')->getRepository('Task');
        $converter    = $this->get('orm.manager')->getConverter('Task');
        $helperLocale = $this->get('core.helper.locale');

        $ids   = [];
        $total = $repository->countBy($oql);
        $items = $repository->findBy($oql);

        $items = array_map(function ($a) use ($converter, &$ids) {
            $ids[] = $a->id;
            return $converter->responsify($a);
        }, $items);

        return new JsonResponse([
            'results' => $helperLocale->translateAttributes($items, ['mode', 'field']),
            'items'   => $items,
            'extra'   => [],
            'total'   => $total,
        ]);
    }

    /**
     * Deletes a task container.
     *
     * @param integer $id The task id.
     *
     * @return JsonResponse The response object.
     *
     */
    public function deleteAction($id)
    {
        $em  = $this->get('orm.manager');
        $msg = $this->get('core.messenger');

        $em->remove($em->getRepository('Task')->find($id));
        $msg->add(_('Item deleted successfully'), 'success');

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }
}
