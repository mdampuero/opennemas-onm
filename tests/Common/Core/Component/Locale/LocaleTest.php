<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\Core\Component\Locale;

use Common\Core\Component\Locale\Locale;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Defines test cases for Locale class.
 */
class LocaleTest extends KernelTestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->locale = $this->getMockBuilder('Common\Core\Component\Locale\Locale')
            ->setMethods([ 'addTextDomain', 'changeLocale', 'changeTimeZone'])
            ->setConstructorArgs([ [ 'en_US', 'es_ES' ], '/foo/bar' ])
            ->getMock();
    }

    /**
     * Tests the constructor.
     */
    public function testConstructor()
    {
        $path   = new \ReflectionProperty($this->locale, 'path');
        $config = new \ReflectionProperty($this->locale, 'config');

        $path->setAccessible(true);
        $config->setAccessible(true);

        $this->assertEquals('/foo/bar', $path->getValue($this->locale));
        $this->assertEquals(
            [ 'en_US', 'es_ES' ],
            $config->getValue($this->locale)['backend']['language']['available']
        );
    }

    /**
     * Tests apply.
     */
    public function testApply()
    {
        $this->locale->expects($this->once())->method('changeLocale');
        $this->locale->expects($this->once())->method('changeTimeZone');

        $this->locale->apply();
    }

    /**
     * Tests configure with empty and non-empty values.
     */
    public function testConfigure()
    {
        $config = new \ReflectionProperty($this->locale, 'config');
        $config->setAccessible(true);
        $original = $config->getValue($this->locale);

        $this->locale->configure([]);

        $this->assertEquals($original, $config->getValue($this->locale));

        $this->locale->configure([ 'backend' => [
            'language' => [ 'selected' => 'fr' ],
            'timezone' => 'Europe/Madrid'
        ] ]);

        $this->assertNotEquals($original, $config->getValue($this->locale));
    }

    /**
     * Tests getAvailableLocales for backend and frontend contexts.
     */
    public function testGetAvailableLocales()
    {
        $this->assertNotEmpty($this->locale->getAvailableLocales());
        $this->assertTrue(array_key_exists('en_US', $this->locale->getAvailableLocales()));
        $this->assertTrue(array_key_exists('en_US', $this->locale->setContext('admin')->getAvailableLocales()));
        $this->assertEmpty($this->locale->setContext('frontend')->getAvailableLocales());
    }

    /**
     * Tests getContext and setContext methods.
     */
    public function testGetAndSetContext()
    {
        $this->locale->setContext('');
        $this->assertEquals('frontend', $this->locale->getContext());
        $this->assertEquals('frontend', $this->locale->setContext('frontend')->getContext());
        $this->assertEquals('frontend', $this->locale->setContext('grault')->getContext());
        $this->assertEquals('backend', $this->locale->setContext('backend')->getContext());

        $config  = new \ReflectionProperty($this->locale, 'config');
        $default = new \ReflectionProperty($this->locale, 'default');

        $config->setAccessible(true);
        $default->setAccessible(true);

        $value = $config->getValue($this->locale);
        unset($value['frontend']);
        $config->setValue($this->locale, $value);

        $this->assertEquals('frontend', $this->locale->setContext('frontend')->getContext());
        $this->assertEquals(
            $default->getValue($this->locale),
            $config->getValue($this->locale)['frontend']
        );
    }

    /**
     * Tests getLocale and setLocale methods.
     */
    public function testGetAndSetLocale()
    {
        $this->locale->setLocale(null);
        $this->assertEquals('en_US', $this->locale->getLocale());

        $this->locale->setLocale('foo');
        $this->assertNotEquals('foo', $this->locale->getLocale());

        $this->locale->setLocale('en');
        $this->assertEquals('en_US', $this->locale->getLocale());
    }

    /**
     * Tests getRequestLocale and setRequestLocale methods.
     */
    public function testGetAndSetRequestLocale()
    {
        $this->locale->setRequestLocale(null);
        $this->assertNotEmpty($this->locale->getRequestLocale());
        $this->assertEquals($this->locale->getLocale(), $this->locale->getRequestLocale());

        $this->locale->setRequestLocale('waldo');
        $this->assertEquals('waldo', $this->locale->getRequestLocale());

        $this->locale->setRequestLocale('es_ES');
        $this->assertEquals('es_ES', $this->locale->getRequestLocale());
    }

    /**
     * Tests getTimeZone and setTimeZone methods.
     */
    public function testGetAndSetTimeZone()
    {
        $id = array_flip(\DateTimeZone::listIdentifiers())['Europe/Lisbon'];

        $this->assertEquals('UTC', $this->locale->getTimeZone()->getName());

        $this->locale->setTimeZone('foo');
        $this->assertEquals('UTC', $this->locale->getTimeZone()->getName());

        $this->locale->setTimeZone(9999);
        $this->assertEquals('UTC', $this->locale->getTimeZone()->getName());

        $this->locale->setTimeZone($id);
        $this->assertEquals('Europe/Lisbon', $this->locale->getTimeZone()->getName());

        $this->locale->setTimeZone('Europe/Lisbon');
        $this->assertEquals('Europe/Lisbon', $this->locale->getTimeZone()->getName());
    }

    /**
     * Tests getLocaleName.
     */
    public function testGetLocaleName()
    {
        $this->assertEquals(
            ucfirst(\Locale::getDisplayName('en_US')),
            $this->locale->getLocaleName()
        );
    }

    /**
     * Tests getLocaleShort.
     */
    public function testGetLocaleShort()
    {
        $this->assertEquals('en', $this->locale->getLocaleShort());

        $property = new \ReflectionProperty($this->locale, 'config');
        $property->setAccessible(true);
        $property->setValue($this->locale, [ 'backend' => [
            'language' => [ 'selected' => 'es_ES' ]
        ]]);

        $this->assertEquals('es', $this->locale->getLocaleShort());
    }

    /**
     * Tests getSlugs for backend and frontend contexts.
     */
    public function testGetSlugs()
    {
        $config = new \ReflectionProperty($this->locale, 'config');
        $config->setAccessible(true);
        $value = $config->getValue($this->locale);

        $value['frontend'] = [
            'language' => [ 'available' => [ 'en_US' ], 'slug' => [ 'en_US' => 'en' ]],
            'timezone' => 'UTC'
        ];
        $config->setValue($this->locale, $value);

        $this->assertEmpty($this->locale->getSlugs());
        $this->assertTrue(array_key_exists('en_US', $this->locale->setContext('frontend')->getSlugs()));
        $this->assertEmpty($this->locale->setContext('backend')->getSlugs());
    }

    /**
     * Tests getSupportedLocales.
     */
    public function testGetSupportedLocales()
    {
        $this->assertEquals(
            [
                'en_US' => ucfirst(\Locale::getDisplayName('en_US')),
                'es_ES' => ucfirst(\Locale::getDisplayName('es_ES'))
            ],
            $this->locale->getSupportedLocales()
        );

        $this->assertNotEquals(
            count($this->locale->getSupportedLocales()),
            count($this->locale->setContext('frontend')->getSupportedLocales())
        );
    }

    /**
     * Tests getTimeZoneName with multiple valid and invalid values.
     */
    public function testGetTimeZoneName()
    {
        $method = new \ReflectionMethod($this->locale, 'getTimeZoneName');
        $method->setAccessible(true);

        $timezones = \DateTimeZone::listIdentifiers();

        $this->assertEmpty($method->invokeArgs($this->locale, [ null ]));
        $this->assertEmpty($method->invokeArgs($this->locale, [ 'mumble' ]));
        $this->assertEquals('Europe/Madrid', $method->invokeArgs($this->locale, [ 'Europe/Madrid' ]));

        $index = array_rand(array_keys($timezones));

        $this->assertEquals($timezones[$index], $method->invokeArgs($this->locale, [ $index ]));
    }
}
