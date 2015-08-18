<?php

namespace Framework\ORM\FreshBooks\Persister;

use Framework\ORM\Entity\Entity;
use Framework\ORM\Exception\InvoiceNotFoundException;

class InvoicePersister extends FreshBooksPersister
{
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
        $this->api->post([ 'invoice' => $entity->getData() ]);

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
            $response = $this->api->getResponse();

            if ($this->hasNext()) {
                $this->next()->remove($entity);
            }

            return $this;
        }

        throw new InvoiceNotFoundException($this->api->getError());
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
        $data = $entity->getData();

        $this->api->setMethod('invoice.update');
        $this->api->post([ 'invoice' => $data ]);
        $this->api->request();

        if ($this->api->success()) {
            $response = $this->api->getResponse();

            if ($this->hasNext()) {
                $this->next()->update($entity);
            }

            return $this;
        }

        throw new InvoiceNotFoundException($this->api->getError());
    }
}
