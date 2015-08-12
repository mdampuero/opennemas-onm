<?php

namespace Framework\Tests\FreshBooks\Entity;

use Framework\FreshBooks\Entity\Client;
use Freshbooks\FreshBooksApi;

class ClientTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $data   = [ 'foo' => 'bar' ];
        $entity = new Client($data);

        $this->assertEquals($entity->getData(), $data);

        foreach ($data as $key => $value) {
           $this->assertEquals($value, $entity->{$key});
        }
    }

    public function testExistsWithUnexistingClient()
    {
        $data   = [ 'foo' => 'bar' ];
        $entity = new Client($data);

        $this->assertFalse($entity->exists());
    }

    public function testExistsWithExistingClient()
    {
        $data   = [ 'client_id' => '1' ];
        $entity = new Client($data);

        $this->assertTrue($entity->exists());
    }
}
