<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <onm-devs@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\ORM\FreshBooks\Persister;

use Common\ORM\Core\Entity;
use Common\ORM\Core\Exception\EntityNotFoundException;
use Symfony\Component\Intl\Intl;

/**
 * The ClientPersister class persists Clients to FreshBooks.
 */
class ClientPersister extends BasePersister
{
    /**
     * Saves a new client in FreshBooks.
     *
     * @param Entity $entity The client to save.
     *
     * @throws RuntimeException If the the client can not be saved.
     */
    public function create(Entity &$entity)
    {
        $data = $this->clean($entity);

        $this->api->setMethod('client.create');
        $this->api->post([ 'client' => $data ]);
        $this->api->request();

        if ($this->api->success()) {
            $response = $this->api->getResponse();

            $entity->id = (int) $response['client_id'];

            return;
        }

        throw new \RuntimeException($this->api->getError());
    }

    /**
     * Removes the client in FreshBooks.
     *
     * @param Entity $entity The client to update.
     *
     * @throws EntityNotFoundException If the client does not exist.
     */
    public function remove(Entity $entity)
    {
        $this->api->setMethod('client.delete');
        $this->api->post([ 'client_id' => $entity->id ]);
        $this->api->request();

        if ($this->api->success()) {
            return;
        }

        throw new EntityNotFoundException(
            $entity->getClassName(),
            $entity->client_id,
            $this->api->getError()
        );
    }

    /**
     * Updates the client in FreshBooks.
     *
     * @param Entity $entity The client to update.
     *
     * @throws EntityNotFoundException If the client does not exist.
     */
    public function update(Entity $entity)
    {
        $data = $this->clean($entity);

        $this->api->setMethod('client.update');
        $this->api->post([ 'client' => $data ]);
        $this->api->request();

        if ($this->api->success()) {
            return;
        }

        throw new EntityNotFoundException(
            $entity->getClassName(),
            $entity->client_id,
            $this->api->getError()
        );
    }

    /**
     * Cleans the data for Freshbooks.
     *
     * @param Entity $entity The entity data.
     *
     * @return array The cleaned data.
     */
    protected function clean($entity)
    {
        $countries = Intl::getRegionBundle()->getCountryNames('en');

        $map = [
            'id'          => 'client_id',
            'address'     => 'p_street1',
            'city'        => 'p_city',
            'company'     => 'organization',
            'country'     => 'p_country',
            'phone'       => 'work_phone',
            'postal_code' => 'p_code',
            'state'       => 'p_state',
        ];

        $data = [];
        foreach ($entity->getData() as $property => $value) {
            $key = $property;

            if (array_key_exists($property, $map)) {
                $key = $map[$property];
            }

            if ($property === 'country' && !empty($value)) {
                $value = $countries[$value];
            }

            $data[$key] = $value;
        }

        return $data;
    }
}
