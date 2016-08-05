<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <onm-devs@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace BackendWebService\Controller;

use Common\ORM\Entity\Client;
use Onm\Framework\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * The ClientController class creates and updates Clients.
 */
class ClientController extends Controller
{
    /**
     * Shows the client.
     *
     * @param integer $id The client id.
     *
     * @return JsonResponse The response object.
     */
    public function showAction($id)
    {
        if ($this->get('core.instance')->getClient() !== $id) {
            return JsonResponse('', 400);
        }

        $client = $this->get('orm.manager')
            ->getRepository('Client', 'manager')
            ->find($id);

        return new JsonResponse($client->getData());
    }

    /**
     * Creates the client from the client data.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     */
    public function saveAction(Request $request)
    {
        $em     = $this->get('orm.manager');
        $client = new Client($request->request->all());

        $em->persist($client, 'freshbooks');
        $em->persist($client, 'braintree');
        $em->persist($client, 'manager');

        $instance = $this->get('core.instance');
        $instance->client = $client->id;
        $em->persist($instance);

        $this->get('core.dispatcher')
            ->dispatch('instance.client.update', [ 'instance' => $instance ]);

        return new JsonResponse($client->id);
    }

    /**
     * Creates the client from the client data.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     */
    public function updateAction(Request $request, $id)
    {
        $em     = $this->get('orm.manager');
        $client = $em->getRepository('Client', 'manager')->find($id);

        $client->merge($request->request->all());

        $em->persist($client, 'freshbooks');
        $em->persist($client, 'braintree');
        $em->persist($client, 'manager');

        return new JsonResponse();
    }
}
