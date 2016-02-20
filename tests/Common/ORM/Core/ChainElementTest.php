<?php

namespace Framework\Tests\ORM\Core;

use Common\ORM\Core\ChainElement;

class ClientTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->first  = new ChainElement();
        $this->second = new ChainElement();
        $this->last   = new ChainElement();

        $this->first->add($this->second);
        $this->first->add($this->last);
    }

    public function testAdd()
    {
        $this->assertTrue($this->first->hasNext());
        $this->assertEquals($this->second, $this->first->next());
        $this->assertEquals($this->last, $this->second->next());
    }

    public function testHasNextOnFirstElement()
    {
        $this->assertTrue($this->first->hasNext());
    }

    public function testHasNextOnLastElement()
    {
        $this->assertFalse($this->last->hasNext());
    }

    public function testNextOnFirstElement()
    {
        $this->assertEquals($this->second, $this->first->next());
    }

    public function testNextOLastElement()
    {
        $this->assertEquals(null, $this->last->next());
    }

    public function testSetNext()
    {
        $element = new ChainElement();

        $this->first->setNext($element);
        $this->assertEquals($element, $this->first->next());
    }
}
