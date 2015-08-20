<?php

namespace Framework\Tests\ORM\Entity;

use Framework\ORM\Entity\Payment;

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
