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
     * Creates the client from the client data.
     *
     * @param array $data The client data.
     *
     * @return Client The client.
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

        return new JsonResponse();
    }
}
