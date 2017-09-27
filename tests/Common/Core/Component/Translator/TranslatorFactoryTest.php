<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\Core\Component\Translator;

use Common\Core\Component\Translator\TranslatorFactory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Defines test cases for TranslatorFactory class.
 */
class TranslatorFactoryTest extends KernelTestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->foo = $this->getMockBuilder('Common\Core\Component\Translator\Foo\FooTranslator')
            ->setMethods([ 'getRequiredParameters' ])
            ->disableOriginalConstructor()
            ->getMock();

        $this->foo->expects($this->any())->method('getRequiredParameters')->willReturn([ 'xyzzy' => 'mumble' ]);

        $file = $this->getMockBuilder('Symfony\Component\Finder\SplFileInfo')
            ->disableOriginalConstructor()
            ->setMethods([ 'getFilename', 'getRelativePathName' ])
            ->getMock();

        $file->expects($this->any())->method('getFilename')->willReturn('FooTranslator.php');
        $file->expects($this->any())->method('getRelativePathName')->willReturn('Foo/FooTranslator.php');

        $this->finder = $this->getMockBuilder('Symfony\Component\Finder\Finder')
            ->setMethods([ 'files', 'in', 'name' ])
            ->getMock();

        $this->finder->expects($this->any())->method('name')->willReturn($this->finder);
        $this->finder->expects($this->any())->method('files')->willReturn([ $file ]);

        $this->factory = $this->getMockBuilder('Common\Core\Component\Translator\TranslatorFactory')
            ->setMethods([ 'getFinder' ])
            ->getMock();

        $this->factory->expects($this->any())->method('getFinder')->willReturn($this->finder);
    }

    /**
     * Tests get.
     */
    public function testGet()
    {
        $this->assertInstanceOf(
            'Common\Core\Component\Translator\Foo\FooTranslator',
            $this->factory->get('foo', 'norf', 'flob', [])
        );
    }

    /**
     * Tests get with an invalid translator name.
     *
     * @expectedException \InvalidArgumentException
     */
    public function testGetWithInvalidTranslator()
    {
        $this->factory->get('gorp', 'norf', 'flob', []);
    }

    /**
     * Tests getAvailableTranslators.
     */
    public function testGetAvailableTranslators()
    {
        $this->assertEquals([ 'foo' ], $this->factory->getAvailableTranslators());
    }

    /**
     * Tests getFinder.
     */
    public function testGetFinder()
    {
        $factory = new TranslatorFactory();

        $method = new \ReflectionMethod($factory, 'getFinder');
        $method->setAccessible(true);

        $this->assertInstanceOf('Symfony\Component\Finder\Finder', $method->invokeArgs($factory, []));
    }

    /**
     * Tests getTranslators.
     */
    public function testGetTranslators()
    {
        $method = new \ReflectionMethod($this->factory, 'getTranslators');
        $method->setAccessible(true);

        $this->assertEquals([ 'foo' => 'Foo\FooTranslator' ], $method->invokeArgs($this->factory, []));
    }

    /**
     * Tests getTranslatorsData.
     */
    public function testGetTranslatorsData()
    {
        $factory = $this->getMockBuilder('Common\Core\Component\Translator\TranslatorFactory')
            ->setMethods([ 'get', 'getFinder' ])
            ->getMock();

        $factory->expects($this->any())->method('get')->willReturn($this->foo);
        $factory->expects($this->any())->method('getFinder')->willReturn($this->finder);

        $this->assertEquals([
            [
                'translator' => 'foo',
                'parameters' => [ 'xyzzy' => 'mumble' ]
            ]
        ], $factory->getTranslatorsData());

    }
}
