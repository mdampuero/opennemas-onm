<?php

namespace Framework\Tests\ORM\Exception;

use Framework\ORM\Exception\EntityNotFoundException;

class EntityNotFoundExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testGetMessage()
    {
        $message = uniqid();

        $e = new EntityNotFoundException($message);

        $this->assertEquals($message, $e->getMessage());
    }
}
