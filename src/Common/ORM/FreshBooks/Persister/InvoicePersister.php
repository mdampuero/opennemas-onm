<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\ORM\FreshBooks\Persister;

use Common\ORM\Core\Entity;
use Common\ORM\Core\Exception\EntityNotFoundException;

/**
 * The InvoicePersister class defines actions to create, update and remove
 * Invoices from FreshBooks.
 */
class InvoicePersister extends BasePersister
{
    /**
     * Saves a new invoice in FreshBooks.
     *
     * @param Entity $entity The invoice to save.
     *
     * @return InvoicePersister
     *
     * @throws \RuntimeException If the the invoice can not be saved.
     */
    public function create(Entity &$entity)
    {
        $this->api->setMethod('invoice.create');
        $this->api->post([
            'invoice' => $this->converter->freshbooksfy($entity)
        ]);

        $this->api->request();

        if ($this->api->success()) {
            $response = $this->api->getResponse();

            $entity->id = (int) $response['invoice_id'];

            return $this;
        }

        throw new \RuntimeException($this->api->getError());
    }

    /**
     * Removes the invoice in FreshBooks.
     *
     * @param Entity $entity The invoice to update.
     *
     * @return InvoicePersister
     *
     * @throws EntityNotFoundException If the invoice does not exist.
     */
    public function remove(Entity $entity)
    {
        $this->api->setMethod('invoice.delete');
        $this->api->post([ 'invoice_id' => $entity->invoice_id ]);
        $this->api->request();

        if ($this->api->success()) {
            return $this;
        }

        throw new EntityNotFoundException(
            $this->metadata->name,
            $entity->invoice_id,
            $this->api->getError()
        );
    }

    /**
     * Updates the invoice in FreshBooks.
     *
     * @param Entity $entity The invoice to update.
     *
     * @return InvoicePersister
     *
     * @throws EntityNotFoundException If the invoice does not exist.
     */
    public function update(Entity $entity)
    {
        $this->api->setMethod('invoice.update');
        $this->api->post([
            'invoice' => $this->converter->freshbooksfy($entity)
        ]);

        $this->api->request();

        if ($this->api->success()) {
            return $this;
        }

        throw new EntityNotFoundException(
            $this->metadata->name,
            $entity->invoice_id,
            $this->api->getError()
        );
    }
}
