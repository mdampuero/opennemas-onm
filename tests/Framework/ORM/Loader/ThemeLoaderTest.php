<?php

namespace Framework\Tests\ORM\Configuration;

use Framework\ORM\Exception\InvalidThemeException;
use Framework\ORM\Loader\ThemeLoader;
use Framework\Fixture\FixtureLoader;
use Framework\ORM\Validator\Validator;

class ThemeLoaderTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->fixtureLoader = new FixtureLoader($this);

        $this->validator = new Validator(__DIR__ . '/../../../../src/Framework/Resources/config/orm/validation');
        $this->loader    = new ThemeLoader($this->validator);
    }

    /**
     * @expectedException Framework\ORM\Exception\InvalidEntityException
     */
    public function testInvalidTheme()
    {
        $this->loader->load([]);
    }

    public function testValidTheme()
    {
        $data = $this->fixtureLoader
            ->loadData('Framework/Resources/fixtures/theme/valid.yml');

        $theme = $this->loader->load($data);

        $this->assertEquals($data, $theme->getData());
    }
}
