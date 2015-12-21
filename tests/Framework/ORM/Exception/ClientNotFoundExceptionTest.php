<?php

namespace Framework\Tests\ORM\Exception;

use Framework\ORM\Exception\ClientNotFoundException;

class ClientNotFoundExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testGetMessageWithPreviousMessage()
    {
        $id     = (int) uniqid();
        $source = uniqid();
        $error  = uniqid();

        $e = new ClientNotFoundException($id, $source, $error);

        $this->assertRegexp('/' . $id . '/', $e->getMessage());
        $this->assertRegexp('/' . $source . '/', $e->getMessage());
        $this->assertRegexp('/' . $error . '$/', $e->getMessage());
    }

    public function testGetMessageWithoutPreviousMessage()
    {
        $id     = (int) uniqid();
        $source = uniqid();

        $e = new ClientNotFoundException($id, $source);

        $this->assertRegexp('/' . $id . '/', $e->getMessage());
        $this->assertRegexp('/' . $source . '$/', $e->getMessage());
    }
}
