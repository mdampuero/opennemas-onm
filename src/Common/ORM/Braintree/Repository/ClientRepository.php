<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\ORM\Braintree\Repository;

use Common\ORM\Entity\Client;
use Common\ORM\Core\Exception\EntityNotFoundException;
use Common\ORM\Core\Exception\InvalidCriteriaException;

/**
 * The ClientRepository class defines actions to search Clients in Braintree.
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
     * @throws EntityNotFoundException When the client id is invalid.
     */
    public function find($id)
    {
        try {
            $cr = $this->factory->get('customer');
            $response = $cr::find($id);

            return new Client($this->converter->objectify($response));
        } catch (\Exception $e) {
            throw new EntityNotFoundException($this->metadata->name, $id, $e->getMessage());
        }
    }

    /**
     * Finds a list of clients basing on a criteria.
     *
     * @param array   $criteria The criteria.
     * @param mixed   $clients  The clients from the previous repository.
     *
     * @return array The list of clients.
     *
     * @throws InvalidCriteriaException
     */
    public function findBy($criteria = null)
    {
        $bcriteria = $this->arrayToCriteria($criteria);

        try {
            $cr = $this->factory->get('customer');
            $response = $cr::search($bcriteria);

            $clients = [];
            foreach ($response->_ids as $id) {
                $clients[$id] = $this->find($id);
            }

            return $clients;
        } catch (\Exception $e) {
        }

        throw new InvalidCriteriaException($criteria);
    }

    /**
     * {@inheritdoc}
     */
    public function findOneBy($oql = '')
    {
        throw new \Exception();
    }

    /**
     * Transform a criteria as array to a Braintree criteria.
     *
     * @param array $array The criteria.
     *
     * @return Braintree_CustomerSearch The Braintree criteria.
     */
    protected function arrayToCriteria($array)
    {
        $criteria = [];

        if (!is_array($array)) {
            return $criteria;
        }

        foreach ($array as $key => $value) {
            $key        = \classify($key);
            $criteria[] = \Braintree_CustomerSearch::{$key}()->is($value);
        }

        return $criteria;
    }
}
