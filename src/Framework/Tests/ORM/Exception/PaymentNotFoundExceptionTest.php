<?php

namespace Framework\Tests\ORM\Exception;

use Framework\ORM\Exception\PaymentNotFoundException;

class PaymentNotFoundExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testGetMessageWithPreviousMessage()
    {
        $id     = (int) uniqid();
        $source = uniqid();
        $error  = uniqid();

        $e = new PaymentNotFoundException($id, $source, $error);

        $this->assertRegexp('/' . $id . '/', $e->getMessage());
        $this->assertRegexp('/' . $source . '/', $e->getMessage());
        $this->assertRegexp('/' . $error . '$/', $e->getMessage());
    }

    public function testGetMessageWithoutPreviousMessage()
    {
        $id     = (int) uniqid();
        $source = uniqid();

        $e = new PaymentNotFoundException($id, $source);

        $this->assertRegexp('/' . $id . '/', $e->getMessage());
        $this->assertRegexp('/' . $source . '$/', $e->getMessage());
    }
}
