<?php

namespace Framework\Tests\ORM\Exception;

use Framework\ORM\Exception\InvalidPersisterException;

class InvalidPersisterExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testGetMessageWithPreviousMessage()
    {
        $class  = uniqid();
        $source = uniqid();

        $e = new InvalidPersisterException($class, $source);

        $this->assertRegexp('/' . $class . '/', $e->getMessage());
        $this->assertRegexp('/' . $source . '$/', $e->getMessage());
    }
}
