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

use Common\ORM\Entity\Invoice;
use Common\ORM\Core\Exception\EntityNotFoundException;
use Common\ORM\Core\Exception\InvalidCriteriaException;

/**
 * The InvoiceRepository class defines actions to search Invoices in FreshBooks.
 */
class InvoiceRepository extends BaseRepository
{
    /**
     * {@inheritdoc}
     */
    public function countBy($oql = '')
    {
        throw new \Exception();
    }

    /**
     * Find a invoice by id.
     *
     * @param integer $id The invoice id.
     *
     * @return array The invoice.
     *
     * @throws EntityNotFoundException
     */
    public function find($id)
    {
        $this->api->setMethod('invoice.get');
        $this->api->post([ 'invoice_id' => $id ]);
        $this->api->request();

        if ($this->api->success()) {
            $response = $this->api->getResponse();

            return new Invoice($this->converter->objectify($response['invoice']));
        }

        throw new EntityNotFoundException($this->metadata->name, $id);
    }

    /**
     * Finds a list of invoices basing on a criteria.
     *
     * @param array $criteria The criteria.
     *
     * @return array The list of invoices.
     *
     * @throws InvalidCriteriaException
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
                    $invoices[] = new Invoice($this->converter->objectify($response['invoices']['invoice']));
                } else {
                    $response = $response['invoices']['invoice'];

                    foreach ($response as $data) {
                        $invoices[] = new Invoice($this->converter->objectify($data));
                    }
                }
            }

            return $invoices;
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
     * Return the PDF content for an invoice by id.
     *
     * @param integer $id The invoice id.
     *
     * @return string The PDF content as string.
     *
     * @throws EntityNotFoundException
     */
    public function getPdf($id)
    {
        $this->api->setMethod('invoice.getPDF');
        $this->api->post([ 'invoice_id' => $id ]);
        $this->api->request();

        if ($this->api->success()) {
            return $this->api->getResponse();
        }

        throw new EntityNotFoundException($this->metadata->name, $id);
    }
}
