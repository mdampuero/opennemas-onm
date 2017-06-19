<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\Data\Adapter;

use Common\Data\Adapter\LogoEnabledAdapter;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Defines test cases for LogoEnabledAdapter class.
 */
class LogoEnabledAdapterTest extends KernelTestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->repository = $this->getMockBuilder('SettingRepository')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->manager = $this->getMockBuilder('EntityManager')
            ->setMethods([ 'getDataSet' ])
            ->getMock();

        $this->manager->expects($this->any())->method('getDataSet')
            ->willReturn($this->repository);

        $this->container = $this->getMockBuilder('ServiceContainer')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->container->expects($this->any())->method('get')
            ->willReturn($this->manager);

        $this->adapter = new LogoEnabledAdapter($this->container);
    }

    /**
     * Tests adapt with values in logo_enabled and settings['allowLogo'].
     */
    public function testAdapt()
    {
        $this->assertEquals(0, $this->adapter->adapt(0));
        $this->assertEquals(1, $this->adapter->adapt('1'));

        $this->repository->expects($this->at(0))->method('get')->willReturn([]);
        $this->assertEquals(0, $this->adapter->adapt(null));

        $this->repository->expects($this->at(0))->method('get')->willReturn([ 'allowLogo' => 1 ]);
        $this->assertEquals(1, $this->adapter->adapt(null));
    }
}
