<?php

namespace Framework\Tests\FreshBooks\Entity;

use Framework\FreshBooks\Entity\Entity;
use Freshbooks\FreshBooksApi;

class EntityTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $data   = [ 'foo' => 'bar' ];
        $entity = new Entity($data);

        $this->assertEquals($entity->getData(), $data);

        foreach ($data as $key => $value) {
           $this->assertEquals($value, $entity->{$key});
        }

    }
}
