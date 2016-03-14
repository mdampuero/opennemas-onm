<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <onm-devs@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\ORM\FreshBooks\Repository;

use Common\ORM\Entity\Client;
use Common\ORM\Core\Exception\EntityNotFoundException;
use Common\ORM\Core\Exception\InvalidCriteriaException;

/**
 * The ClientRepository class searches Clients in FreshBooks.
 */
class ClientRepository extends BaseRepository
{
    /**
     * {@inheritdoc}
     */
    public function countBy($oql = '')
    {
        throw new \Exception();
    }

    /**
     * Find a client by id.
     *
     * @param integer $id     The client id.
     * @param Client  $client The client.
     * @param boolean $next   Whether to continue to the next repository.
     *
     * @return Client The client.
     *
     * @throws EntityNotFoundException When the client id is invalid.
     */
    public function find($id, $client = null, $next = true)
    {
        $this->api->setMethod('client.get');
        $this->api->post([ 'client_id' => $id ]);
        $this->api->request();

        if ($this->api->success()) {
            $response = $this->api->getResponse();

            if (empty($client)) {
                $client = new Client($response['client']);
            } else {
                $client->merge($response['client']);
            }

            if ($next && $this->hasNext()) {
                return $this->next()->find($id, $client);
            }

            return $client;
        }

        throw new EntityNotFoundException('Client', $id, $this->api->getError());
    }

    /**
     * Finds a list of clients basing on a criteria.
     *
     * @param array   $criteria The criteria.
     * @param mixed   $clients  The clients from the previous repository.
     * @param boolean $next     Whether to continue to the next repository.
     *
     * @return array The list of clients.
     */
    public function findBy($criteria = null, $clients = null, $next = true)
    {
        $this->api->setMethod('client.list');
        $this->api->post($criteria);
        $this->api->request();

        $response = [];

        if ($this->api->success()) {
            $response = $this->api->getResponse();

            if (empty($clients)) {
                $clients = [];
            }

            if (array_key_exists('clients', $response)
                && array_key_exists('client', $response['clients'])
            ) {
                if ($response['clients']['@attributes']['total'] == 1) {
                    $id = $response['clients']['client']['client_id'];


                    $response['clients']['client' ] = [
                        $response['clients']['client' ]
                    ];
                }

                $response = $response['clients']['client'];

                foreach ($response as $data) {
                    $id = $data['client_id'];

                    if (empty($clients[$id])) {
                        $clients[$id] = new Client($data);
                    } else {
                        $clients[$id]->merge($data);
                    }
                }
            }

            if ($this->hasNext()) {
                return $this->next()->findBy($criteria, $clients);
            }

            return $clients;
        }

        throw new InvalidCriteriaException($criteria, 'Braintree', $this->api->getError());
    }

    /**
     * {@inheritdoc}
     */
    public function findOneBy($oql = '')
    {
        throw new \Exception();
    }
}
