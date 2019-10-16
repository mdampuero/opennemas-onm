<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\NewsAgency\Component\Factory;

use Common\NewsAgency\Component\Factory\ServerFactory;

/**
 * Defines test cases for ServerFactory class.
 */
class ServerFactoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->tpl = $this->getMockBuilder('Common\Core\Component\Template\Template')
            ->disableOriginalConstructor()
            ->getMock();

        $this->factory = new ServerFactory($this->tpl);
    }

    public function testConstructor()
    {
        $property = new \ReflectionProperty($this->factory, 'finder');
        $property->setAccessible(true);

        $this->assertInstanceOf(
            'Symfony\Component\Finder\Finder',
            $property->getValue($this->factory)
        );
    }

    /**
     * Tests get when the provided parameters are invalid.
     *
     * @expectedException \Exception
     */
    public function testGetForInvalidServer()
    {
        $this->factory->get([]);
    }

    /**
     * Tests get when the provided parameters are valid.
     */
    public function testGetForValidServer()
    {
        $this->assertInstanceOf(
            'Common\NewsAgency\Component\Server\Http\HttpOpennemas',
            $this->factory->get([ 'url' => 'http://norf.opennemas.com/ws/agency' ])
        );
    }
}
