<?php

namespace Framework\Tests\FreshBooks\Repository;

use Framework\FreshBooks\Entity\Payment;
use Framework\FreshBooks\Persister\PaymentPersister;
use Freshbooks\FreshBooksApi;

class PaymentPersisterTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->api = $this->getMockBuilder('Freshbooks\FreshBooksApi')
            ->disableOriginalConstructor()
            ->getMock();

        $this->api->method('setMethod')->willReturn(true);
        $this->api->method('post')->willReturn(true);

        $this->persister = new PaymentPersister($this->api);

        $this->existingPayment = new Payment([
            'payment_id' => '123',
            'invoice_id' => '1',
            'amount'     => '20.5',
            'type'       => 'Check'
        ]);

        $this->unexistingPayment = new Payment([
            'invoice_id' => '1',
            'amount'     => '20.5',
            'type'       => 'Check'
        ]);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testCreateWithError()
    {
        $this->api->method('success')->willReturn(false);

        $this->api->expects($this->once())->method('setMethod')
            ->with('payment.create');

        $this->persister->create($this->existingPayment);
    }

    public function testCreateWithoutErrors()
    {
        $response = [
            '@attributes' => [ 'status' => 'ok' ],
            'payment_id'  => '123',
        ];

        $this->api->method('success')->willReturn(true);
        $this->api->method('getResponse')->willReturn($response);

        $this->api->expects($this->once())->method('setMethod')
            ->with('payment.create');

        $this->api->expects($this->once())->method('post');
        $this->api->expects($this->once())->method('success');
        $this->api->expects($this->once())->method('getResponse');

        $this->persister->create($this->unexistingPayment);
        $this->assertEquals(
            $response['payment_id'],
            $this->unexistingPayment->payment_id
        );
    }

    /**
     * @expectedException Framework\FreshBooks\Exception\PaymentNotFoundException
     */
    public function testRemoveWithError()
    {
        $this->api->method('success')->willReturn(false);

        $this->api->expects($this->once())->method('setMethod')
            ->with('payment.delete');

        $this->persister->remove($this->unexistingPayment);
    }

    public function testRemoveWithoutErrors()
    {
        $response = [ '@attributes' => [ 'status' => 'ok' ] ];

        $this->api->method('success')->willReturn(true);
        $this->api->method('getResponse')->willReturn($response);

        $this->api->expects($this->once())->method('setMethod')
            ->with('payment.delete');

        $this->api->expects($this->once())->method('post');
        $this->api->expects($this->once())->method('success');
        $this->api->expects($this->once())->method('getResponse');

        $r = $this->persister->remove($this->existingPayment);
        $this->assertEquals($response, $r);
    }

    /**
     * @expectedException Framework\FreshBooks\Exception\PaymentNotFoundException
     */
    public function testUpdateWithError()
    {
        $this->api->method('success')->willReturn(false);

        $this->api->expects($this->once())->method('setMethod')
            ->with('payment.update');

        $this->persister->update($this->unexistingPayment);
    }

    public function testUpdateWithoutErrors()
    {
        $response = [ '@attributes' => [ 'status' => 'ok' ] ];

        $this->api->method('success')->willReturn(true);
        $this->api->method('getResponse')->willReturn($response);

        $this->api->expects($this->once())->method('setMethod')
            ->with('payment.update');

        $this->api->expects($this->once())->method('post');
        $this->api->expects($this->once())->method('success');
        $this->api->expects($this->once())->method('getResponse');

        $r = $this->persister->update($this->existingPayment);
        $this->assertEquals($response, $r);
    }
}
