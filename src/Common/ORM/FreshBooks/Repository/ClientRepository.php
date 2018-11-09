<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\ORM\FreshBooks\Repository;

use Common\ORM\Entity\Client;
use Common\ORM\Core\Exception\EntityNotFoundException;
use Common\ORM\Core\Exception\InvalidCriteriaException;

/**
 * The ClientRepository class defines actions to search Clients in FreshBooks.
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
     * @param integer $id The client id.
     *
     * @return Client The client.
     *
     * @throws EntityNotFoundException
     */
    public function find($id)
    {
        $this->api->setMethod('client.get');
        $this->api->post([ 'client_id' => $id ]);
        $this->api->request();

        if ($this->api->success()) {
            $response = $this->api->getResponse();

            return new Client($this->converter->objectify($response['client']));
        }

        throw new EntityNotFoundException($this->metadata->name, $id);
    }

    /**
     * Finds a list of clients basing on a criteria.
     *
     * @param array $criteria The criteria.
     *
     * @return array The list of clients.
     *
     * @throws InvalidCriteriaException
     */
    public function findBy($criteria = null)
    {
        $this->api->setMethod('client.list');
        $this->api->post($criteria);
        $this->api->request();

        if (!$this->api->success()) {
            throw new InvalidCriteriaException($criteria);
        }

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

                $clients[] = new Client($this->converter->objectify($data));
            }
        }

        return $clients;
    }

    /**
     * {@inheritdoc}
     */
    public function findOneBy($oql = '')
    {
        throw new \Exception();
    }
}
