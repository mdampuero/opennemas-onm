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

use Common\Core\Annotation\Security;
use Common\Model\Entity\Ads;
use League\Csv\Writer;
use Common\Core\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Opennemas\Task\Component\Queue\Queue;
use Opennemas\Task\Component\Task\ServiceTask;

class AdsController extends Controller
{
    /**
     * Returns the list of users as JSON.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     *
     * @Security("hasPermission('ADS_LIST')")
     */
    public function listAction(Request $request)
    {
        $oql = $request->query->get('oql', '');

        $repository = $this->get('orm.manager')->getRepository('Ads');
        $converter  = $this->get('orm.manager')->getConverter('Ads');

        $ids   = [];
        $total = $repository->countBy($oql);
        $ads   = $repository->findBy($oql);

        $ads = array_map(function ($a) use ($converter, &$ids) {
            $ids[] = $a->id;
            return $converter->responsify($a);
        }, $ads);

        return new JsonResponse([
            'results' => $ads,
            'items'   => $ads,
            'total'   => $total,
        ]);
    }

    /**
     * Creates a new ads.txt container from the request.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     *
     * @Security("hasPermission('ADS_CREATE')")
     */
    public function saveAction(Request $request)
    {
        $em   = $this->get('orm.manager');
        $msg  = $this->get('core.messenger');
        $data = $em->getConverter('Ads')
            ->objectify($request->request->all());

        $adContainer = new Ads($data);
        $em->persist($adContainer);
        $msg->add(_('Ads.txt container saved successfully'), 'success', 201);

        $this->banAdsTxtOnInstances($adContainer->instances);

        $response = new JsonResponse($msg->getMessages(), $msg->getCode());
        $response->headers->set(
            'Location',
            $this->generateUrl(
                'manager_ws_ads_show',
                [ 'id' => $adContainer->id ]
            )
        );

        return $response;
    }

    /**
     * Returns an instance as JSON.
     *
     * @param integer  $id The instance id.
     *
     * @return Response The response object.
     *
     * @Security("hasPermission('ADS_UPDATE')")
     */
    public function showAction($id)
    {
        $em          = $this->get('orm.manager');
        $converter   = $em->getConverter('Ads');
        $adContainer = $em->getRepository('Ads')->find($id);

        $extra       = $this->getExtraData();
        $adContainer = $converter->responsify($adContainer->getData());

        return new JsonResponse([
            'extra' => $extra,
            'item'  => $adContainer
        ]);
    }

    /**
     * Deletes a ads container.
     *
     * @param integer $id The ads id.
     *
     * @return JsonResponse The response object.
     *
     * @Security("hasPermission('ADS_DELETE')")
     */
    public function deleteAction($id)
    {
        $em  = $this->get('orm.manager');
        $msg = $this->get('core.messenger');

        $adContainer = $em->getRepository('Ads')->find($id);

        $em->remove($adContainer);
        $msg->add(_('Ads.txt container deleted successfully.'), 'success');

        $this->banAdsTxtOnInstances($adContainer->instances);

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    public function banAdsTxtOnInstances($instances = [])
    {
        foreach ($instances as $instanceName) {
            $this->get('task.service.queue')->push(
                new ServiceTask('core.varnish', 'ban', [
                    sprintf('obj.http.x-tags ~ ^instance-%s.*ads,txt.*', $instanceName)
                ])
            );
        }
    }

    /**
     * Deletes the selected ads.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     *
     * @Security("hasPermission('ADS_DELETE')")
     */
    public function deleteSelectedAction(Request $request)
    {
        $ids = $request->request->get('ids', []);
        $msg = $this->get('core.messenger');

        if (!is_array($ids) || empty($ids)) {
            $msg->add(_('Bad request'), 'error', 400);
            return new JsonResponse($msg->getMessages(), $msg->getCode());
        }

        $em  = $this->get('orm.manager');
        $oql = sprintf('id in [%s]', implode(',', $ids));

        $adsContainers = $em->getRepository('Ads')->findBy($oql);

        $instancesToBan = [];

        $deleted = 0;
        foreach ($adsContainers as $container) {
            try {
                $em->remove($container);
                $deleted++;
            } catch (\Exception $e) {
                $msg->add($e->getMessage(), 'error');
            }
            foreach ($container->instances as $instanceName) {
                $instancesToBan[] = $instanceName;
            }
        }

        $this->banAdsTxtOnInstances(array_unique($instancesToBan));
        if ($deleted > 0) {
            $msg->add(
                sprintf(_('%s ads containers deleted successfully'), $deleted),
                'success'
            );
        }

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * Updates the instance information gives its id
     *
     * @param  Request  $request The request object.
     * @param  integer  $id      The instance id.
     *
     * @return Response          The response object.
     *
     * @Security("hasPermission('ADS_UPDATE')")
     */
    public function updateAction(Request $request, $id)
    {
        $em   = $this->get('orm.manager');
        $msg  = $this->get('core.messenger');
        $data = $em->getConverter('Ads')
            ->objectify($request->request->all());

        $adContainer = $em->getRepository('Ads')->find($id);
        $adContainer->merge($data);

        $em->persist($adContainer);
        $msg->add(_('Ads.txt container saved successfully'), 'success');

        $this->banAdsTxtOnInstances($adContainer->instances);

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * Returns the data to create a new ads.txt container.
     *
     * @return JsonResponse The response object.
     *
     * @Security("hasPermission('ADS_CREATE')")
     */
    public function newAction()
    {
        return new JsonResponse([
            'extra'  => $this->getExtraData()
        ]);
    }

    /**
     * Returns a list of parameters for the template.
     *
     * @return array Array of template parameters.
     */
    private function getExtraData()
    {
        return [];
    }

    /**
     * Returns a list of targets basing on the request.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     *
     * @Security("hasPermission('ADS_CREATE')")
     */
    public function autocompleteAction(Request $request)
    {
        $target   = [];
        $query    = strtolower($request->query->get('query'));
        $security = $this->get('core.security');

        if ($security->hasPermission('MASTER')
            && (empty($query)
                || strpos(strtolower(_('All')), strtolower($query)) !== false)
        ) {
            $target[] = [ 'id' => 'all', 'name' => _('All') ];
        }

        $oql = '';
        if (!$security->hasPermission('MASTER')
            && $security->hasPermission('PARTNER')
        ) {
            $oql = sprintf('owner_id = "%s" ', $this->get('core.user')->id);
        }

        if (!empty($query)) {
            if (!empty($oql)) {
                $oql .= 'and ';
            }

            $oql .= '(internal_name ~ "%s" or name ~ "%s" or domains ~ "%s") ';
            $oql  = sprintf($oql, $query, $query, $query);
        }

        $oql .= 'order by internal_name asc limit 10';

        $instances = $this->get('orm.manager')->getRepository('instance')
            ->findBy($oql);

        foreach ($instances as $instance) {
            $target[] = [
                'id'      => $instance->internal_name,
                'name'    => $instance->internal_name,
            ];
        }

        return new JsonResponse([ 'target' => $target ]);
    }
}
