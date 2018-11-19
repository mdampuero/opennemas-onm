<?php

namespace Tests\Common\ORM\Core\Data\Mapper;

use Common\ORM\Core\Data\Mapper\DatetimeDataMapper;

class DatetimeDataMapperTest extends \PHPUnit\Framework\TestCase
{
    public function setUp()
    {
        $this->mapper = new DatetimeDataMapper();
    }

    public function testFromDate()
    {
        $this->assertEquals(
            '2000-01-01',
            $this->mapper->fromDate('2000-01-01')->format('Y-m-d')
        );

        $this->assertEmpty($this->mapper->fromDate(null));
        $this->assertEmpty($this->mapper->fromDate(''));
        $this->assertEmpty($this->mapper->fromDate('bar'));
    }

    public function testFromDatetime()
    {
        $this->assertEquals(
            new \DateTime('2000-01-01 10:00:10'),
            $this->mapper->fromDatetime(new \DateTime('2000-01-01 10:00:10'))
        );

        $this->assertEquals(
            new \DateTime('2000-01-01 10:00:10'),
            $this->mapper->fromDatetime('2000-01-01 10:00:10')
        );

        $this->assertEmpty($this->mapper->fromDatetime(null));
        $this->assertEmpty($this->mapper->fromDatetime(''));
        $this->assertEmpty($this->mapper->fromDatetime('bar'));
    }

    public function testFromDatetimetz()
    {
        $expected = new \Datetime('2000-01-01 10:00:10', new \DateTimeZone('UTC'));
        $expected->setTimeZone(new \DateTimeZone(date_default_timezone_get()));

        $this->assertEquals($expected, $this->mapper->fromDatetimetz('2000-01-01 10:00:10'));

        $this->assertEmpty($this->mapper->fromDatetimetz(null));
        $this->assertEmpty($this->mapper->fromDatetimetz(''));
        $this->assertEmpty($this->mapper->fromDatetimetz('bar'));
    }

    public function testFromString()
    {
        $expected = new \DateTime('2000-01-01 10:00:10', new \DateTimeZone('UTC'));
        $expected->setTimeZone(new \DateTimeZone(date_default_timezone_get()));

        $this->assertEquals($expected, $this->mapper->fromString('2000-01-01 10:00:10'));

        $this->assertEmpty($this->mapper->fromString(null));
        $this->assertEmpty($this->mapper->fromString(''));
        $this->assertEmpty($this->mapper->fromString('bar'));
    }

    public function testFromTime()
    {
        $this->assertEquals(
            '10:00:10',
            $this->mapper->fromTime('10:00:10')->format('H:i:s')
        );

        $this->assertEmpty($this->mapper->fromTime(null));
        $this->assertEmpty($this->mapper->fromTime(''));
        $this->assertEmpty($this->mapper->fromTime('bar'));
    }

    public function testToDate()
    {
        $this->assertEquals(
            '2000-01-01',
            $this->mapper->toDate(new \Datetime('2000-01-01'))
        );

        $this->assertEmpty($this->mapper->toDate(null));
        $this->assertEmpty($this->mapper->toDate(''));
    }

    public function testToDatetime()
    {
        $this->assertEquals(
            '2000-01-01 10:00:10',
            $this->mapper->toDatetime(new \DateTime('2000-01-01 10:00:10'))
        );

        $this->assertEmpty($this->mapper->toDatetime(null));
        $this->assertEmpty($this->mapper->toDatetime(''));
    }

    public function testToDatetimetz()
    {
        $expected = new \Datetime('2000-01-01 10:00:10');
        $expected->setTimeZone(new \DateTimeZone('UTC'));

        $this->assertEquals(
            $expected->format('Y-m-d H:i:s'),
            $this->mapper->toDatetimetz(new \DateTime('2000-01-01 10:00:10'))
        );

        $this->assertEmpty($this->mapper->toDatetimetz(null));
        $this->assertEmpty($this->mapper->toDatetimetz(''));
    }

    public function testToTime()
    {
        $this->assertEquals(
            '10:00:10',
            $this->mapper->toTime(new \DateTime('10:00:10'))
        );

        $this->assertEmpty($this->mapper->toTime(null));
        $this->assertEmpty($this->mapper->toTime(''));
    }

    public function testToString()
    {
        $expected = new \Datetime('2000-01-01 10:00:10');
        $expected->setTimeZone(new \DateTimeZone('UTC'));

        $this->assertEquals(
            $expected->format('Y-m-d H:i:s'),
            $this->mapper->toString(new \DateTime('2000-01-01 10:00:10'))
        );
    }
}
