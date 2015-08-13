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

    public function testCleanWithEmptyData()
    {
        $entity = new Invoice();

        $this->assertEquals([], $entity->clean());
    }

    public function testCleanWithData()
    {
        $data = [
            'foo'   => 'bar',
            'lines' => [
                'line' => [
                    [
                        'order'    => 1,
                        'name'     => 'test',
                        'cost'     => 2,
                        'quantity' => 3
                    ]
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
                            'name' => 'test',
                            'cost' => 2,
                            'quantity' => 3
                        ]
                    ]
                ]
            ],
            $entity->clean()
        );
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
