<?php

namespace Framework\Tests\ORM\Core\Loader;

use Framework\ORM\Core\Entity;
use Framework\ORM\Core\Loader\Loader;
use Framework\Fixture\FixtureLoader;

class LoaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException InvalidArgumentException
     */
    public function testInvalidPaths()
    {
        new Loader(null, []);
    }

    public function testValidPaths()
    {
        $loader = new Loader(__DIR__ . '/../../../../../app', [ 'config/orm' ]);
        $this->assertNotEmpty($loader->load());
    }
}
