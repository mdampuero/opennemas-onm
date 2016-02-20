<?php

namespace Common\ORM\FreshBooks\Repository;

use Common\ORM\Entity\Invoice;
use Common\ORM\Core\Exception\EntityNotFoundException;
use Common\ORM\Core\Exception\InvalidCriteriaException;

class InvoiceRepository extends FreshBooksRepository
{
    /**
     * Find a invoice by id.
     *
     * @param integer $id The invoice id.
     *
     * @return array The invoice.
     *
     * @throws InvoiceNotFoundException When the invoice id is invalid.
     */
    public function find($id)
    {
        $this->api->setMethod('invoice.get');
        $this->api->post([ 'invoice_id' => $id ]);
        $this->api->request();

        if ($this->api->success()) {
            $response = $this->api->getResponse();

            return new Invoice($response['invoice']);
        }

        throw new EntityNotFoundException('Invoice', $id, $this->api->getError());
    }

    /**
     * Finds a list of invoices basing on a criteria.
     *
     * @param array $criteria The criteria.
     *
     * @return array The list of invoices.
     */
    public function findBy($criteria = null)
    {
        $this->api->setMethod('invoice.list');
        $this->api->post($criteria);
        $this->api->request();

        $response = [];

        if ($this->api->success()) {
            $invoices = [];

            $response = $this->api->getResponse();

            if (array_key_exists('invoices', $response)
                && array_key_exists('invoice', $response['invoices'])
            ) {
                if ($response['invoices']['@attributes']['total'] == 1) {
                    $invoices[] = new Invoice($response['invoices']['invoice']);
                } else {
                    $response = $response['invoices']['invoice'];

                    foreach ($response as $data) {
                        $invoices[] = new Invoice($data);
                    }
                }
            }

            return $invoices;
        }

        throw new InvalidCriteriaException($criteria, $this->source, $this->api->getError());
    }

    /**
     * Return the PDF content for an invoice by id.
     *
     * @param integer $id The invoice id.
     *
     * @return string The PDF content as string.
     */
    public function getPDF($id)
    {
        $this->api->setMethod('invoice.getPDF');
        $this->api->post([ 'invoice_id' => $id ]);
        $this->api->request();

        if ($this->api->success()) {
            return $this->api->getResponse();
        }

        throw new EntityNotFoundException('Invoice', $id, $this->source, $this->api->getError());
    }
}
