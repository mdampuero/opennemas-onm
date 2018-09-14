<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Framework\ORM\FreshBooks\Repository;

use Common\ORM\Core\Metadata;
use Common\ORM\Entity\Payment;
use Common\ORM\FreshBooks\Persister\PaymentPersister;

/**
 * Defines test cases for PaymentPersister class.
 */
class PaymentPersisterTest extends \PHPUnit_Framework_TestCase
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
                'amount'     => 'float',
                'client_id'  => 'integer',
                'date'       => 'datetime',
                'invoice_id' => 'integer',
                'nonce'      => 'string',
                'notes'      => 'string',
                'type'       => 'string',
            ],
            'mapping' => [
                'freshbooks' => [
                    'client_id' => [ 'name' => 'customerId', 'type' => 'string' ],
                    'amount'    => [ 'name' => 'amount', 'type' => 'string' ],
                    'date'      => [ 'name' => 'date', 'type' => 'string' ],
                    'notes'     => [ 'name' => 'notes', 'type' => 'string' ],
                    'type'      => [ 'name' => 'type', 'type' => 'string' ],
                ]
            ],
        ]);

        $this->persister = new PaymentPersister('foo', 'bar', $this->metadata);

        $property = new \ReflectionProperty($this->persister, 'api');
        $property->setAccessible(true);
        $property->setValue($this->persister, $this->api);

        $this->existingPayment = new Payment([
            'id'        => 1,
            'amount'    => 123.12,
            'client_id' => 1,
            'date'      => '2013-02-01 10:00:10',
            'type'      => 'Check',
        ]);

        $this->unexistingPayment = new Payment([
            'amount'    => 123.12,
            'client_id' => 1,
            'date'      => '2013-02-01 10:00:10',
            'type'      => 'Check'
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
            ->with('payment.create');

        $this->persister->create($this->existingPayment);
    }

    /**
     * Tests create.
     */
    public function testCreateWithoutErrors()
    {
        $this->api->method('success')->willReturn(true);

        $this->api->expects($this->once())->method('setMethod')
            ->with('payment.create');

        $this->api->expects($this->once())->method('post');
        $this->api->expects($this->once())->method('success');

        $this->persister->create($this->unexistingPayment);
    }

    /**
     * Tests remove when API returns false.
     *
     * @expectedException \Common\ORM\Core\Exception\EntityNotFoundException
     */
    public function testRemoveWithError()
    {
        $this->api->method('success')->willReturn(false);

        $this->api->expects($this->once())->method('setMethod')
            ->with('payment.delete');

        $this->persister->remove($this->unexistingPayment);
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
            ->with('payment.delete');

        $this->api->expects($this->once())->method('post')->with([ 'payment_id' => 1 ]);
        $this->api->expects($this->once())->method('success');

        $this->persister->remove($this->existingPayment);
    }

    /**
     * Tests update when API returns false.
     *
     * @expectedException \Common\ORM\Core\Exception\EntityNotFoundException
     */
    public function testUpdateWithError()
    {
        $this->api->method('success')->willReturn(false);

        $this->api->expects($this->once())->method('setMethod')
            ->with('payment.update');

        $this->persister->update($this->unexistingPayment);
    }

    /**
     * Tests update.
     */
    public function testUpdateWithoutErrors()
    {
        $response = [ '@attributes' => [ 'status' => 'ok' ] ];

        $this->api->method('success')->willReturn(true);
        $this->api->method('getResponse')->willReturn($response);

        $this->api->expects($this->once())->method('setMethod')
            ->with('payment.update');

        $this->api->expects($this->once())->method('post');
        $this->api->expects($this->once())->method('success');

        $this->persister->update($this->existingPayment);
    }
}
