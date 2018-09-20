<?php

namespace Framework\Tests\ORM\Core\Exception;

use Common\ORM\Core\Exception\InvalidCriteriaException;

class InvalidCriteriaExceptionTest extends \PHPUnit\Framework\TestCase
{
    public function testGetMessageWithPreviousMessage()
    {
        $criteria = uniqid();
        $error    = uniqid();

        $e = new InvalidCriteriaException($criteria, $error);

        $this->assertRegexp('/' . @serialize($criteria) . '/', $e->getMessage());
        $this->assertRegexp('/' . $error . '$/', $e->getMessage());
    }

    public function testGetMessageWithoutPreviousMessage()
    {
        $criteria = uniqid();

        $e = new InvalidCriteriaException($criteria);

        $this->assertRegexp('/' . serialize($criteria) . '/', $e->getMessage());
    }
}
