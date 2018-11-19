<?php

namespace Framework\Tests\ORM\Entity;

use Common\ORM\Entity\Invoice;

class InvoiceTest extends \PHPUnit\Framework\TestCase
{
    public function testConstructor()
    {
        $data   = [ 'foo' => 'bar' ];
        $entity = new Invoice($data);

        $this->assertEquals($entity->getData(), $data);

        foreach ($data as $key => $value) {
            $this->assertEquals($value, $entity->{$key});
        }
    }

    public function testExistsWithUnexistingInvoice()
    {
        $data   = [ 'foo' => 'bar' ];
        $entity = new Invoice($data);

        $this->assertFalse($entity->exists());
    }

    public function testExistsWithExistingInvoice()
    {
        $data   = [ 'invoice_id' => '1' ];
        $entity = new Invoice($data);

        $this->assertTrue($entity->exists());
    }
}
