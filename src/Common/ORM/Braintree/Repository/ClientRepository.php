<?php

namespace Framework\ORM\Braintree\Repository;

use Framework\ORM\Entity\Client;
use Framework\ORM\Core\Exception\EntityNotFoundException;
use Framework\ORM\Core\Exception\InvalidCriteriaException;

class ClientRepository extends BraintreeRepository
{
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
        try {
            $cr = $this->factory->get('customer');
            $response = $cr::find($id);

            $data = $this->responseToData($response);

            if (empty($client)) {
                $client = new Client($data);
            } else {
                $client->merge($data);
            }

            if ($next && $this->hasNext()) {
                return $this->next()->find($id, $client);
            }

            return $client;
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
     * @param boolean $next     Whether to continue to the next repository.
     *
     * @return array The list of clients.
     */
    public function findBy($criteria = null, $clients = null, $next = true)
    {
        $bcriteria = $this->arrayToCriteria($criteria);

        try {
            $cr = $this->factory->get('customer');
            $response = $cr::search($bcriteria);

            if (empty($clients)) {
                $clients = [];
            }

            foreach ($response->_ids as $id) {
                if (empty($clients[$id])) {
                    $clients[$id] = $this->find($id, null, false);
                } else {
                    $this->find($id, $clients[$id], false);
                }
            }

            if ($next && $this->hasNext()) {
                return $this->next()->findBy($criteria, $clients);
            }

            return $clients;
        } catch (\Exception $e) {
            throw new InvalidCriteriaException($criteria, $this->source, $e->getMessage());
        }

        throw new InvalidCriteriaException($criteria, $this->source);
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
            'client_id'    => $response->id,
            'first_name'   => $response->firstName,
            'last_name'    => $response->lastName,
            'email'        => $response->email,
            'organization' => $response->company,
            'phone'        => $response->phone,
        ];

        if (!empty($response->addresses)) {
            $address = $response->addresses[0];

            $data['p_street1'] = $address->streetAddress;
            $data['p_street2'] = $address->extendedAddress;
            $data['p_city']    = $address->locality;
            $data['p_state']   = $address->region;
            $data['p_country'] = $address->countryName;
            $data['p_code']    = $address->postalCode;
        }

        return $data;
    }
}
