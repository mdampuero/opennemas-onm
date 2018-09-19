<?php

namespace Tests\Common\ORM\Core\Data\Mapper;

use Common\ORM\Core\Data\Mapper\EnumDataMapper;

class EnumDataMapperTest extends \PHPUnit\Framework\TestCase
{
    public function setUp()
    {
        $this->mapper = new EnumDataMapper();
    }

    public function testFromEnum()
    {
        $this->assertEquals('1', $this->mapper->fromEnum(1));
        $this->assertEquals('1.21', $this->mapper->fromEnum(1.21));
        $this->assertEquals('foo', $this->mapper->fromEnum('foo'));
        $this->assertEquals(null, $this->mapper->fromEnum(null));
        $this->assertEquals(null, $this->mapper->fromEnum([]));
    }
}
