<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\Migration\Component\Translator;

use Common\Migration\Component\Translator\Translator;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Defines test cases for Translator class.
 */
class TranslatorTest extends KernelTestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->translator = $this->getMockBuilder('Common\Migration\Component\Translator\Translator')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
    }

    public function testsAddTranslation()
    {
        //$this->translator->addTranslation('foobar', 'thud');
        //$this->assertTrue($this->translator->isTranslated('foobar'));
        //$this->assertFalse($this->translator->isTranslated('foobar', 'frog'));

        //$this->translator->addTranslation('foobar', 'thud', 'frog');
        //$this->assertTrue($this->translator->isTranslated('foobar', 'frog'));
    }

    /**
     * Tests isTranslated.
     */
    public function testIsTranslated()
    {
        $property = new \ReflectionProperty($this->translator, 'translations');
        $property->setAccessible(true);

        $property->setValue($this->translator, [
            [ 'source_id' => 'waldo', 'type' => null, 'slug' => null, 'target_id' => 'fred' ],
            [ 'source_id' => 'xyzzy', 'type' => 'plugh', 'slug' => 'quux', 'target_id' => 'corge' ]
        ]);

        $this->assertFalse($this->translator->isTranslated('corge'));
        $this->assertFalse($this->translator->isTranslated('waldo', null, 'gorp'));
        $this->assertTrue($this->translator->isTranslated('waldo'));
        $this->assertTrue($this->translator->isTranslated('waldo', null, null));
        $this->assertFalse($this->translator->isTranslated('xyzzy', 'foobar'));
        $this->assertFalse($this->translator->isTranslated('xyzzy', 'foobar', 'quux'));
        $this->assertFalse($this->translator->isTranslated('xyzzy', 'plugh', 'gorp'));
        $this->assertTrue($this->translator->isTranslated('xyzzy'));
        $this->assertTrue($this->translator->isTranslated('xyzzy', null, 'quux'));
        $this->assertTrue($this->translator->isTranslated('xyzzy', 'plugh'));
    }

    /**
     * Tests getSourceId when the content was translated previously.
     */
    public function testGetSourceId()
    {
        $property = new \ReflectionProperty($this->translator, 'translations');
        $property->setAccessible(true);

        $property->setValue($this->translator, [
            [ 'source_id' => 'waldo', 'type' => null, 'slug' => null, 'target_id' => 'fred' ],
            [ 'source_id' => 'xyzzy', 'type' => 'plugh', 'slug' => 'quux', 'target_id' => 'corge' ],
            [ 'source_id' => 'xyzzy', 'type' => 'plugh', 'slug' => 'fubar', 'target_id' => 'thud' ]
        ]);

        $this->assertEquals('waldo', $this->translator->getSourceId('fred'));
        $this->assertEquals('xyzzy', $this->translator->getSourceId('corge', null, 'quux'));
        $this->assertEquals('xyzzy', $this->translator->getSourceId('corge', 'plugh', 'quux'));
        $this->assertEquals('xyzzy', $this->translator->getSourceId('thud'));
    }

    /**
     * Tests getSourceId when the list of translated contents of the type is
     * empty.
     *
     * @expectedException \Common\Migration\Component\Exception\EntityNotTranslatedException
     */
    public function testGetSourceIdWhenNoOtherContentOfTypeTranslated()
    {
        $this->translator->getSourceId('fred');
    }

    /**
     * Tests getSourceId when the content was not translated previously.
     *
     * @expectedException \Common\Migration\Component\Exception\EntityNotTranslatedException
     */
    public function testGetSourceIdWhenNoContentTranslated()
    {
        $property = new \ReflectionProperty($this->translator, 'translations');
        $property->setAccessible(true);

        $property->setValue($this->translator, [
            [ 'source_id' => 'xyzzy', 'type' => 'plugh', 'slug' => 'quux', 'target_id' => 'corge' ],
        ]);

        $this->translator->getSourceId('fred');
    }

    /**
     * Tests getTargetId when the content was translated previously.
     */
    public function testGetTargetId()
    {
        $property = new \ReflectionProperty($this->translator, 'translations');
        $property->setAccessible(true);

        $property->setValue($this->translator, [
            [ 'source_id' => 'xyzzy', 'type' => 'plugh', 'slug' => 'quux', 'target_id' => 'corge' ],
            [ 'source_id' => 'xyzzy', 'type' => 'plugh', 'slug' => 'fubar', 'target_id' => 'thud' ]
        ]);

        $this->assertEquals('corge', $this->translator->getTargetId('xyzzy'));
        $this->assertEquals('corge', $this->translator->getTargetId('xyzzy', 'plugh'));
        $this->assertEquals('thud', $this->translator->getTargetId('xyzzy', 'plugh', 'fubar'));
    }

    /**
     * Tests getTargetId when the list of translated contents of the type is
     * empty.
     *
     * @expectedException \Common\Migration\Component\Exception\EntityNotTranslatedException
     */
    public function testGetTargetIdWhenNoOtherContentOfTypeTranslated()
    {
        $this->translator->getTargetId('fred');
    }

    /**
     * Tests getTargetId when the content was not translated previously.
     *
     * @expectedException \Common\Migration\Component\Exception\EntityNotTranslatedException
     */
    public function testGetTargetIdWhenNoContentTranslated()
    {
        $this->translator->getTargetId('fred');
    }
}
