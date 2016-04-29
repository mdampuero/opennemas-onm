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

use Framework\ORM\Entity\Client;
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
        if ($this->get('instance')->getClient() !== $id) {
            return JsonResponse('', 400);
        }

        $client = $this->get('orm.manager')
            ->getRepository('manager.Client', 'Database')
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
        $client = new Client($request->request->all());

        $this->get('orm.manager')->persist($client, 'FreshBooks');
        $this->get('orm.manager')->persist($client, 'Braintree');
        $this->get('orm.manager')->persist($client, 'Database');

        $instance = $this->get('instance');
        $instance->metas['client'] = $client->id;
        $this->get('instance_manager')->persist($instance);

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
        $client = $this->get('orm.manager')
            ->getRepository('manager.client', 'Database')
            ->find($id);

        $client->merge($request->request->all());

        $this->get('orm.manager')->persist($client, 'FreshBooks');
        $this->get('orm.manager')->persist($client, 'Braintree');
        $this->get('orm.manager')->persist($client, 'Database');

        return new JsonResponse();
    }
}
