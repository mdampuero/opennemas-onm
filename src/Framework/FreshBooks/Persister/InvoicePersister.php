<?php

namespace Framework\FreshBooks\Persister;

use Framework\FreshBooks\Entity\Entity;
use Framework\FreshBooks\Exception\InvoiceNotFoundException;

class InvoicePersister extends Persister
{
    /**
     * Saves a new invoice in FreshBooks.
     *
     * @param Entity $entity The invoice to save.
     *
     * @return mixed The response from FreshBooks.
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

            return $response;
        }

        throw new \RuntimeException($this->api->getError());
    }

    /**
     * Removes the invoice in FreshBooks.
     *
     * @param Entity $entity The invoice to update.
     *
     * @return mixed The response from FreshBooks.
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

            return $response;
        }

        throw new InvoiceNotFoundException($this->api->getError());
    }

    /**
     * Updates the invoice in FreshBooks.
     *
     * @param Entity $entity The invoice to update.
     *
     * @return mixed The response from FreshBooks.
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

            return $response;
        }

        throw new InvoiceNotFoundException($this->api->getError());
    }
}
