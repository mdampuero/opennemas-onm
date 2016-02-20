<?php

namespace Framework\Tests\ORM\Core\Exception;

use Common\ORM\Core\Exception\InvalidCriteriaException;

class InvalidCriteriaExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testGetMessageWithPreviousMessage()
    {
        $criteria = uniqid();
        $source   = uniqid();
        $error    = uniqid();

        $e = new InvalidCriteriaException($criteria, $source, $error);

        $this->assertRegexp('/' . @serialize($criteria) . '/', $e->getMessage());
        $this->assertRegexp('/' . $source . '/', $e->getMessage());
        $this->assertRegexp('/' . $error . '$/', $e->getMessage());
    }

    public function testGetMessageWithoutPreviousMessage()
    {
        $criteria = uniqid();
        $source   = uniqid();

        $e = new InvalidCriteriaException($criteria, $source);

        $this->assertRegexp('/' . serialize($criteria) . '/', $e->getMessage());
        $this->assertRegexp('/' . $source . '$/', $e->getMessage());
    }
}
