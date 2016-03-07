<?php

namespace Framework\Tests\ORM\FreshBooks\Repository;

use Common\ORM\FreshBooks\Repository\InvoiceRepository;
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

        $this->repository = new InvoiceRepository('foo', 'bar');

        $property = new \ReflectionProperty($this->repository, 'api');
        $property->setAccessible(true);
        $property->setValue($this->repository, $this->api);
    }

    /**
     * @expectedException Common\ORM\Core\Exception\EntityNotFoundException
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
            'lines' => [
                'line' => []
            ]
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
     * @expectedException Common\ORM\Core\Exception\InvalidCriteriaException
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

    public function testFindByWithValidCriteriaMultipleResults()
    {
        $criteria = [ 'email' => 'johndoe@example.org' ];

        $invoices = [
            [
                'invoice_id' => '1',
                'number'     => '1',
                'client_id'  => '1',
                'lines' => [
                    'line' => []
                ]
            ],
            [
                'invoice_id' => '2',
                'number'     => '2',
                'client_id'  => '1',
                'lines' => [
                    'line' => []
                ]
            ]
        ];

        $response = [
            '@attributes' => [ 'status' => 'ok' ],
            'invoices'    => [
                '@attributes' => [ 'page' => 1, 'total' => 2 ],
                'invoice'     => $invoices
            ]

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

    public function testFindByWithValidCriteriaOneResult()
    {
        $criteria = [ 'email' => 'johndoe@example.org' ];

        $invoices = [
            'invoice_id' => '1',
            'number'     => '1',
            'client_id'  => '1',
            'lines' => [
                'line' => []
            ]
        ];

        $response = [
            '@attributes' => [ 'status' => 'ok' ],
            'invoices'    => [
                '@attributes' => [ 'page' => 1, 'total' => 1 ],
                'invoice'     => $invoices
            ]
        ];

        $this->api->method('success')->willReturn(true);
        $this->api->method('getResponse')->willReturn($response);

        $this->api->expects($this->once())->method('setMethod')
            ->with('invoice.list');

        $this->api->expects($this->once())->method('post');
        $this->api->expects($this->once())->method('success');
        $this->api->expects($this->once())->method('getResponse');

        $response = $this->repository->findBy($criteria);

        $this->assertEquals(1, count($response));
        $this->assertEquals($invoices, $response[0]->getData());
    }

    /**
     * @expectedException Common\ORM\Core\Exception\EntityNotFoundException
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
