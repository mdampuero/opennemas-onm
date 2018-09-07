<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\Tests\ORM\FreshBooks\Repository;

use Common\ORM\Core\Metadata;
use Common\ORM\Entity\Invoice;
use Common\ORM\FreshBooks\Persister\InvoicePersister;
use Freshbooks\FreshBooksApi;

/**
 * Defines test cases for InvoicePersister class.
 */
class InvoicePersisterTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the test environment.
     */
    public function setUp()
    {
        $this->api = $this->getMockBuilder('Freshbooks\FreshBooksApi')
            ->disableOriginalConstructor()
            ->getMock();

        $this->api->method('setMethod')->willReturn(true);
        $this->api->method('post')->willReturn(true);

        $this->metadata = new Metadata([
            'properties' => [
                'client_id' => 'integer',
                'date'      => 'datetime',
                'status'    => 'string',
                'lines'     => 'array'
            ],
            'mapping' => [
                'freshbooks' => [
                    'client_id' => [ 'name' => 'client_id', 'type' => 'string' ],
                    'date'      => [ 'name' => 'date', 'type' => 'datetime' ],
                    'status'    => [ 'name' => 'status', 'type' => 'string' ],
                    'lines'     => [ 'name' => 'lines', 'type' => 'array' ],
                ]
            ],
        ]);

        $this->persister = new InvoicePersister('foo', 'bar', $this->metadata);

        $property = new \ReflectionProperty($this->persister, 'api');
        $property->setAccessible(true);
        $property->setValue($this->persister, $this->api);

        $this->existingInvoice = new Invoice([
            'id'        => '123',
            'client_id' => '1',
            'lines'     => [
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
     * Tests create when API returns false.
     *
     * @expectedException \RuntimeException
     */
    public function testCreateWithError()
    {
        $this->api->method('success')->willReturn(false);

        $this->api->expects($this->once())->method('setMethod')
            ->with('invoice.create');

        $this->persister->create($this->existingInvoice);
    }

    /**
     * Test create.
     */
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
            $this->unexistingInvoice->id
        );
    }

    /**
     * Tests remove when API returns false.
     *
     * @expectedException Common\ORM\Core\Exception\EntityNotFoundException
     */
    public function testRemoveWithError()
    {
        $this->api->method('success')->willReturn(false);

        $this->api->expects($this->once())->method('setMethod')
            ->with('invoice.delete');

        $this->persister->remove($this->unexistingInvoice);
    }

    /**
     * Tests remove.
     */
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
     * Test update when API returns false.
     *
     * @expectedException Common\ORM\Core\Exception\EntityNotFoundException
     */
    public function testUpdateWithError()
    {
        $this->api->method('success')->willReturn(false);

        $this->api->expects($this->once())->method('setMethod')
            ->with('invoice.update');

        $this->persister->update($this->unexistingInvoice);
    }

    /**
     * Test update.
     */
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
}
