<?php

namespace Framework\Tests\FreshBooks\Repository;

use Framework\FreshBooks\Repository\PaymentRepository;
use Freshbooks\FreshBooksApi;

class PaymentRepositoryTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->api = $this->getMockBuilder('Freshbooks\FreshBooksApi')
            ->disableOriginalConstructor()
            ->getMock();

        $this->api->method('setMethod')->willReturn(true);
        $this->api->method('post')->willReturn(true);

        $this->repository = new PaymentRepository($this->api);
    }

    /**
     * @expectedException Framework\FreshBooks\Exception\PaymentNotFoundException
     */
    public function testFindWithInvalidId()
    {
        $this->api->method('success')->willReturn(false);

        $this->api->expects($this->once())->method('setMethod')
            ->with('payment.get');

        $this->repository->find('1');
    }

    public function testFindWithValidId()
    {
        $payment = [
            'payment_id' => '1',
            'invoice_id'  => '1',
            'client_id'  => '1',
            'Organization' => 'John Doe, Inc.',
        ];

        $response = [
            '@attributes' => [ 'status' => 'ok' ],
            'payment'     => $payment,
        ];

        $this->api->method('success')->willReturn(true);
        $this->api->method('getResponse')->willReturn($response);

        $this->api->expects($this->once())->method('setMethod')
            ->with('payment.get');

        $this->api->expects($this->once())->method('post');
        $this->api->expects($this->once())->method('success');
        $this->api->expects($this->once())->method('getResponse');

        $this->assertEquals($payment, $this->repository->find('1')->getData());
    }

    /**
     * @expectedException Framework\FreshBooks\Exception\InvalidCriteriaException
     */
    public function testFindByWithInvalidCriteria()
    {
        $criteria = [ 'invalid_field' => 'johndoe@example.org' ];

        $this->api->method('success')->willReturn(false);

        $this->api->expects($this->once())->method('setMethod')
            ->with('payment.list');

        $this->api->expects($this->once())->method('post');
        $this->api->expects($this->once())->method('success');

        $this->repository->findBy($criteria);
    }

    public function testFindByWithValidCriteriaMultipleResults()
    {
        $criteria = [ 'client_id' => '1' ];

        $payments = [
            [
                'payment_id' => '1',
                'invoice_id'  => '1',
                'amount'  => '13.25'
            ],
            [
                'payment_id' => '2',
                'invoice_id'  => '1',
                'amount'  => '20.95'
            ]
        ];

        $response = [
            '@attributes' => [ 'status' => 'ok' ],
            'payments'    => [
                '@attributes' => [ 'page' => 1, 'total' => 2 ],
                'payment'     => $payments
            ]

        ];

        $this->api->method('success')->willReturn(true);
        $this->api->method('getResponse')->willReturn($response);


        $this->api->expects($this->once())->method('setMethod')
            ->with('payment.list');

        $this->api->expects($this->once())->method('post');
        $this->api->expects($this->once())->method('success');
        $this->api->expects($this->once())->method('getResponse');

        $response = $this->repository->findBy($criteria);

        $this->assertEquals(count($payments), count($response));

        for ($i = 0; $i < count($response); $i++) {
            $this->assertEquals($payments[$i], $response[$i]->getData());
        }
    }

    public function testFindByWithValidCriteriaOneResult()
    {
        $criteria = [ 'email' => 'johndoe@example.org' ];

        $payments = [
            'payment_id' => '1',
            'invoice_id'  => '1',
            'amount'  => '13.25'
        ];

        $response = [
            '@attributes' => [ 'status' => 'ok' ],
            'payments'    => [
                '@attributes' => [ 'page' => 1, 'total' => 1 ],
                'payment'     => $payments
            ]
        ];

        $this->api->method('success')->willReturn(true);
        $this->api->method('getResponse')->willReturn($response);

        $this->api->expects($this->once())->method('setMethod')
            ->with('payment.list');

        $this->api->expects($this->once())->method('post');
        $this->api->expects($this->once())->method('success');
        $this->api->expects($this->once())->method('getResponse');

        $response = $this->repository->findBy($criteria);

        $this->assertEquals(1, count($response));
        $this->assertEquals($payments, $response[0]->getData());
    }
}
