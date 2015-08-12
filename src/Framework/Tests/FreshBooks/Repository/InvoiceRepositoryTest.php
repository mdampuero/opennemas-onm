<?php

namespace Framework\Tests\FreshBooks\Repository;

use Framework\FreshBooks\Repository\InvoiceRepository;
use Freshbooks\FreshBooksApi;

class InvoiceRepositoryTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->api = $this->getMockBuilder('Freshbooks\FreshBooksApi')
            ->disableOriginalConstructor()
            ->getMock();

        $this->api->method('setMethod')->willReturn(true);
        $this->api->method('post')->willReturn(true);

        $this->repository = new InvoiceRepository($this->api);
    }

    /**
     * @expectedException Framework\FreshBooks\Exception\InvoiceNotFoundException
     */
    public function testFindWithInvalidId()
    {
        $this->api->method('success')->willReturn(false);

        $this->api->expects($this->once())->method('setMethod')
            ->with('invoice.get');

        $this->repository->find('1');
    }

    public function testFindWithValidId()
    {
        $invoice = [
            'invoice_id' => '1',
            'number'     => '1',
            'client_id'  => '1',
            'first_name' => 'John',
            'last_name'  => 'Doe'
        ];

        $response = [
            '@attributes' => [ 'status' => 'ok' ],
            'invoice'     => $invoice,
        ];

        $this->api->method('success')->willReturn(true);
        $this->api->method('getResponse')->willReturn($response);

        $this->api->expects($this->once())->method('setMethod')
            ->with('invoice.get');

        $this->api->expects($this->once())->method('post');
        $this->api->expects($this->once())->method('success');
        $this->api->expects($this->once())->method('getResponse');

        $this->assertEquals($invoice, $this->repository->find('1')->getData());
    }

    /**
     * @expectedException Framework\FreshBooks\Exception\InvalidCriteriaException
     */
    public function testFindByWithInvalidCriteria()
    {
        $criteria = [ 'invalid_field' => 'johndoe@example.org' ];

        $this->api->method('success')->willReturn(false);

        $this->api->expects($this->once())->method('setMethod')
            ->with('invoice.list');

        $this->api->expects($this->once())->method('post');
        $this->api->expects($this->once())->method('success');

        $this->repository->findBy($criteria);
    }

    public function testFindByWithValidCriteria()
    {
        $criteria = [ 'email' => 'johndoe@example.org' ];

        $invoices = [
            [
                'invoice_id' => '1',
                'number'     => '1',
                'client_id'  => '1',
                'first_name' => 'John',
                'last_name'  => 'Doe'
            ]
        ];

        $response = [
            '@attributes' => [ 'status' => 'ok' ],
            'invoices'    => $invoices
        ];

        $this->api->method('success')->willReturn(true);
        $this->api->method('getResponse')->willReturn($response);


        $this->api->expects($this->once())->method('setMethod')
            ->with('invoice.list');

        $this->api->expects($this->once())->method('post');
        $this->api->expects($this->once())->method('success');
        $this->api->expects($this->once())->method('getResponse');

        $response = $this->repository->findBy($criteria);

        $this->assertEquals(count($invoices), count($response));

        for ($i = 0; $i < count($response); $i++) {
            $this->assertEquals($invoices[$i], $response[$i]->getData());
        }
    }

    /**
     * @expectedException Framework\FreshBooks\Exception\InvoiceNotFoundException
     */
    public function testGetPDFWithInvalidId()
    {
        $this->api->method('success')->willReturn(false);

        $this->api->expects($this->once())->method('setMethod')
            ->with('invoice.getPDF');

        $this->repository->getPDF('1');
    }

    public function testGetPDFWithValidId()
    {
        $response = '%PDF-1.4....';

        $this->api->method('success')->willReturn(true);
        $this->api->method('getResponse')->willReturn($response);

        $this->api->expects($this->once())->method('setMethod')
            ->with('invoice.getPDF');

        $this->api->expects($this->once())->method('post');
        $this->api->expects($this->once())->method('success');
        $this->api->expects($this->once())->method('getResponse');

        $this->assertEquals(strpos($this->repository->getPDF('1'), '%PDF-1.4'), 0);
    }
}
