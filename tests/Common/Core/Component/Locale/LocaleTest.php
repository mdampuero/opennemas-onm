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
        $this->locale = new Locale([ 'en_US' ], '/foo/bar');
    }

    /**
     * Tests getLocale with default and custom values.
     */
    public function testGetLocale()
    {
        $this->assertEquals('en_US', $this->locale->getLocale());
    }

    /**
     * Tests getLocales.
     */
    public function testGetLocales()
    {
        $this->assertEquals(
            [ 'en_US' => ucfirst(\Locale::getDisplayLanguage('en_US')) ],
            $this->locale->getLocales()
        );
    }

    /**
     * Tests getLocaleName.
     */
    public function testGetLocaleName()
    {
        $this->assertEquals(
            ucfirst(\Locale::getDisplayLanguage('en_US')),
            $this->locale->getLocaleName()
        );
    }

    /**
     * Tests getLocaleShort.
     */
    public function testGetLocaleShort()
    {
        $this->assertEquals('en', $this->locale->getLocaleShort());

        $property = new \ReflectionProperty($this->locale, 'locale');
        $property->setAccessible(true);
        $property->setValue($this->locale, 'es');

        $this->assertEquals('es', $this->locale->getLocale());
    }

    /**
     * Tests getTimeZone.
     */
    public function testGetTimeZone()
    {
        $this->assertEquals('UTC', $this->locale->getTimeZone()->getName());
    }

    /**
     * Tests setLocale.
     */
    public function testSetLocale()
    {
        $this->locale->setLocale('foo');
        $this->assertNotEquals('foo', $this->locale->getLocale());

        $this->locale->setLocale('en');
        $this->assertEquals('en_US', $this->locale->getLocale());
    }

    /**
     * Tests setTimeZone.
     */
    public function testSetTimeZone()
    {
        $id = array_flip(\DateTimeZone::listIdentifiers())['Europe/Lisbon'];

        $this->locale->setTimeZone('foo');
        $this->assertEquals('UTC', $this->locale->getTimeZone()->getName());

        $this->locale->setTimeZone(9999);
        $this->assertEquals('UTC', $this->locale->getTimeZone()->getName());

        $this->locale->setTimeZone($id);
        $this->assertEquals('Europe/Lisbon', $this->locale->getTimeZone()->getName());

        $this->locale->setTimeZone('Europe/Lisbon');
        $this->assertEquals('Europe/Lisbon', $this->locale->getTimeZone()->getName());
    }
}
