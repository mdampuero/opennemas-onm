<?php

namespace Framework\Tests\ORM\Entity;

use Framework\ORM\Entity\Client;

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

    public function testMergeWithInvalidData()
    {
        $data   = [ 'foo' => 'bar' ];
        $entity = new Client($data);

        $this->assertFalse($entity->merge(null));
        $this->assertFalse($entity->merge(1));
        $this->assertFalse($entity->merge('foo'));
    }

    public function testMergewithValidData()
    {
        $data   = [ 'foo' => 'bar' ];
        $entity = new Client($data);

        $entity->merge([ 'foo' => 'xyz' ]);
        $this->assertEquals('xyz', $entity->foo);
    }

    public function testExistsWithExistingClient()
    {
        $data   = [ 'id' => '1' ];
        $entity = new Client($data);

        $entity->refresh();

        $this->assertTrue($entity->exists());
    }

    public function testExistsWithUnexistingClient()
    {
        $data   = [ 'foo' => 'bar' ];
        $entity = new Client($data);

        $this->assertFalse($entity->exists());
    }
}
