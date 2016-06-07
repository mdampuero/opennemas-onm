<?php

namespace Common\ORM\Braintree\Repository;

use Common\ORM\Entity\Client;
use Common\ORM\Core\Exception\EntityNotFoundException;
use Common\ORM\Core\Exception\InvalidCriteriaException;

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

            $data = $this->responseToData($response);

            return new Client($data);
        } catch (\Exception $e) {
            throw new EntityNotFoundException($id, $this->source, $e->getMessage());
        }

        throw new EntityNotFoundException($id, $this->source);
    }

    /**
     * Finds a list of clients basing on a criteria.
     *
     * @param array   $criteria The criteria.
     * @param mixed   $clients  The clients from the previous repository.
     *
     * @return array The list of clients.
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
            throw new InvalidCriteriaException($criteria, $this->source, $e->getMessage());
        }

        throw new InvalidCriteriaException($criteria, $this->source);
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
    private function arrayToCriteria($array)
    {
        $criteria = [];

        if (is_array($array)) {
            foreach ($array as $key => $value) {
                $key = \classify($key);
                $criteria[] = \Braintree_CustomerSearch::{$key}()->is($value);
            }
        }

        return $criteria;
    }

    /**
     * Transform the Braintree response to array.
     *
     * @param Braintree_Customer $response The response to convert.
     *
     * @return array The array.
     */
    private function responseToData($response)
    {
        if (empty($response)) {
            return null;
        }

        $data = [
            'id'         => $response->id,
            'first_name' => $response->firstName,
            'last_name'  => $response->lastName,
            'email'      => $response->email,
            'company'    => $response->company,
            'phone'      => $response->phone,
        ];

        if (!empty($response->addresses)) {
            $address = $response->addresses[0];

            $data['address']     = $address->streetAddress;
            $data['city']        = $address->locality;
            $data['state']       = $address->region;
            $data['country']     = $address->countryName;
            $data['postal_code'] = $address->postalCode;
        }

        return $data;
    }
}
