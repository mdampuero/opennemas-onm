<?php

namespace Framework\Tests\ORM\Core\Exception;

use Common\ORM\Core\Exception\InvalidPersisterException;

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
