<?php

namespace Framework\Tests\FreshBooks\Entity;

use Framework\FreshBooks\Entity\Invoice;
use Freshbooks\FreshBooksApi;

class InvoiceTest extends \PHPUnit_Framework_TestCase
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
}
