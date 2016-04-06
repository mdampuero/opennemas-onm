<?php

namespace Framework\Tests\ORM\FreshBooks\Repository;

use Framework\ORM\Entity\Invoice;
use Framework\ORM\FreshBooks\Persister\InvoicePersister;
use Freshbooks\FreshBooksApi;

class InvoicePersisterTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->api = $this->getMockBuilder('Freshbooks\FreshBooksApi')
            ->disableOriginalConstructor()
            ->getMock();

        $this->api->method('setMethod')->willReturn(true);
        $this->api->method('post')->willReturn(true);

        $this->persister = new InvoicePersister($this->api, 'FreshBooks');

        $this->existingInvoice = new Invoice([
            'invoice_id' => '123',
            'client_id'  => '1',
            'lines' => [
                [
                    'order'    => 1,
                    'name'     => 'test',
                    'cost'     => 2,
                    'quantity' => 3
                ]
            ],
        ]);

        $this->unexistingInvoice = new Invoice([
            'client_id'  => '1',
            'lines' => [
                [
                    'order'    => 1,
                    'name'     => 'test',
                    'cost'     => 2,
                    'quantity' => 3
                ]
            ],
        ]);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testCreateWithError()
    {
        $this->api->method('success')->willReturn(false);

        $this->api->expects($this->once())->method('setMethod')
            ->with('invoice.create');

        $this->persister->create($this->existingInvoice);
    }

    public function testCreateWithoutErrors()
    {
        $response = [
            '@attributes' => [ 'status' => 'ok' ],
            'invoice_id'  => '123',
        ];

        $this->api->method('success')->willReturn(true);
        $this->api->method('getResponse')->willReturn($response);

        $this->api->expects($this->once())->method('setMethod')
            ->with('invoice.create');

        $this->api->expects($this->once())->method('post');
        $this->api->expects($this->once())->method('success');
        $this->api->expects($this->once())->method('getResponse');

        $this->persister->create($this->unexistingInvoice);
        $this->assertEquals(
            $response['invoice_id'],
            $this->unexistingInvoice->invoice_id
        );
    }

    /**
     * @expectedException Framework\ORM\Exception\InvoiceNotFoundException
     */
    public function testRemoveWithError()
    {
        $this->api->method('success')->willReturn(false);

        $this->api->expects($this->once())->method('setMethod')
            ->with('invoice.delete');

        $this->persister->remove($this->unexistingInvoice);
    }

    public function testRemoveWithoutErrors()
    {
        $response = [ '@attributes' => [ 'status' => 'ok' ] ];

        $this->api->method('success')->willReturn(true);
        $this->api->method('getResponse')->willReturn($response);

        $this->api->expects($this->once())->method('setMethod')
            ->with('invoice.delete');

        $this->api->expects($this->once())->method('post');
        $this->api->expects($this->once())->method('success');

        $r = $this->persister->remove($this->existingInvoice);
        $this->assertEquals($this->persister, $r);
    }

    /**
     * @expectedException Framework\ORM\Exception\InvoiceNotFoundException
     */
    public function testUpdateWithError()
    {
        $this->api->method('success')->willReturn(false);

        $this->api->expects($this->once())->method('setMethod')
            ->with('invoice.update');

        $this->persister->update($this->unexistingInvoice);
    }

    public function testUpdateWithoutErrors()
    {
        $response = [ '@attributes' => [ 'status' => 'ok' ] ];

        $this->api->method('success')->willReturn(true);
        $this->api->method('getResponse')->willReturn($response);

        $this->api->expects($this->once())->method('setMethod')
            ->with('invoice.update');

        $this->api->expects($this->once())->method('post');
        $this->api->expects($this->once())->method('success');

        $r = $this->persister->update($this->existingInvoice);
        $this->assertEquals($this->persister, $r);
    }

    public function testCleanWithEmptyData()
    {
        $entity = new Invoice();

        $this->assertEquals([], $this->persister->clean($entity));
    }

    public function testCleanWithData()
    {
        $data = [
            'foo'   => 'bar',
            'lines' => [
                [
                    'order'    => 1,
                    'name'     => 'test',
                    'cost'     => 2,
                    'quantity' => 3
                ]
            ],
            'url'   => 'http://example.org'
        ];

        $entity = new Invoice($data);

        $this->assertEquals(
            [
                'foo'   => 'bar',
                'lines' => [
                    'line' => [
                        [
                            [
                                'name' => 'test',
                                'cost' => 2,
                                'quantity' => 3
                            ]
                        ]
                    ]
                ]
            ],
            $this->persister->clean($entity)
        );
    }
}
