<?php

namespace Framework\Tests\ORM\Core\Exception;

use Common\ORM\Core\Exception\EntityNotFoundException;

class EntityNotFoundExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testGetMessage()
    {
        $message = uniqid();

        $e = new EntityNotFoundException('Entity', 1, 'error');

        $this->assertTrue(strpos($e->getMessage(), 'Entity') !== false);
        $this->assertTrue(strpos($e->getMessage(), '1') !== false);
        $this->assertTrue(strpos($e->getMessage(), 'error') !== false);
    }
}
