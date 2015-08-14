<?php

namespace Framework\Tests\FreshBooks\Entity;

use Framework\FreshBooks\Entity\Payment;
use Freshbooks\FreshBooksApi;

class PaymentTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $data   = [ 'foo' => 'bar' ];
        $entity = new Payment($data);

        $this->assertEquals($entity->getData(), $data);

        foreach ($data as $key => $value) {
            $this->assertEquals($value, $entity->{$key});
        }
    }

    public function testCleanWithEmptyData()
    {
        $entity = new Payment();

        $this->assertEquals([], $entity->clean());
    }

    public function testCleanWithData()
    {
        $data = [
            'invoice_id'  => 1,
            'from_credit' => 0,
            'amount'      => '20.00',
            'updated'     => '2015-08-10'
        ];

        $entity = new Payment($data);

        $this->assertEquals(
            [
                'invoice_id' => 1,
                'amount'     => '20.00',
            ],
            $entity->clean()
        );
    }

    public function testExistsWithUnexistingPayment()
    {
        $data   = [ 'foo' => 'bar' ];
        $entity = new Payment($data);

        $this->assertFalse($entity->exists());
    }

    public function testExistsWithExistingPayment()
    {
        $data   = [ 'payment_id' => '1' ];
        $entity = new Payment($data);

        $this->assertTrue($entity->exists());
    }

}
