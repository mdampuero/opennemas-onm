<?php

namespace Framework\Tests\ORM\Core\Exception;

use Framework\ORM\Core\Exception\EntityNotFoundException;

class EntityNotFoundExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testGetMessage()
    {
        $message = uniqid();

        $e = new EntityNotFoundException($message);

        $this->assertEquals($message, $e->getMessage());
    }
}
