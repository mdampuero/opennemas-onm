<?php

namespace Common\ORM\FreshBooks\Persister;

use Common\ORM\Core\Entity;
use Common\ORM\Core\Exception\EntityNotFoundException;

class InvoicePersister extends FreshBooksPersister
{
    /**
     * Array of invalid fields to send in requests
     *
     * @var array
     */
    protected $invalid = [
        'auth_url',
        'amount_outstanding',
        'estimate_id',
        'folder',
        'gateways',
        'links',
        'order',
        'paid',
        'po_number',
        'recurring_id',
        'staff_id',
        'updated',
        'url'
    ];

    /**
     * Saves a new invoice in FreshBooks.
     *
     * @param Entity $entity The invoice to save.
     *
     * @throws RuntimeException If the the invoice can not be saved.
     */
    public function create(Entity &$entity)
    {
        $this->api->setMethod('invoice.create');
        $this->api->post([ 'invoice' => $this->clean($entity) ]);

        $this->api->request();

        if ($this->api->success()) {
            $response = $this->api->getResponse();

            $entity->invoice_id = $response['invoice_id'];

            if ($this->hasNext()) {
                $this->next()->create($entity);
            }

            return $this;
        }

        throw new \RuntimeException($this->api->getError());
    }

    /**
     * Removes the invoice in FreshBooks.
     *
     * @param Entity $entity The invoice to update.
     *
     * @throws InvoiceNotFoundException If the invoice does not exist.
     */
    public function remove(Entity $entity)
    {
        $this->api->setMethod('invoice.delete');
        $this->api->post([ 'invoice_id' => $entity->invoice_id ]);
        $this->api->request();

        if ($this->api->success()) {
            if ($this->hasNext()) {
                $this->next()->remove($entity);
            }

            return $this;
        }

        throw new EntityNotFoundException(
            'Invoice',
            $entity->invoice_id,
            $this->api->getError()
        );
    }

    /**
     * Updates the invoice in FreshBooks.
     *
     * @param Entity $entity The invoice to update.
     *
     * @throws InvoiceNotFoundException If the invoice does not exist.
     */
    public function update(Entity $entity)
    {
        $this->api->setMethod('invoice.update');
        $this->api->post([ 'invoice' => $this->clean($entity) ]);
        $this->api->request();

        if ($this->api->success()) {
            if ($this->hasNext()) {
                $this->next()->update($entity);
            }

            return $this;
        }

        throw new EntityNotFoundException(
            'Invoice',
            $entity->invoice_id,
            $this->api->getError()
        );
    }

    /**
     * Cleans invalid fields from the invoice.
     *
     * @param Entity $data The data to clean.
     *
     * @return array The cleaned RAW data array.
     */
    public function clean(Entity $entity)
    {
        $cleaned =
            array_diff_key($entity->getData(), array_flip($this->invalid));

        if (array_key_exists('lines', $cleaned)
            && array_key_exists('line', $cleaned['lines'])
        ) {
            foreach ($cleaned['lines']['line'] as &$line) {
                unset($line['order']);
            }
        }

        return $cleaned;
    }
}
