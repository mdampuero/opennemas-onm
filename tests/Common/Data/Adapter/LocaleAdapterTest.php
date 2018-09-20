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

use Common\Data\Adapter\LocaleAdapter;

/**
 * Defines test cases for LocaleAdapter class.
 */
class LocaleAdapterTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->dataset = $this->getMockBuilder('SettingDataset')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->manager = $this->getMockBuilder('EntityManager')
            ->setMethods([ 'getDataSet' ])
            ->getMock();

        $this->manager->expects($this->any())->method('getDataSet')
            ->willReturn($this->dataset);

        $this->container = $this->getMockBuilder('ServiceContainer')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->container->expects($this->any())->method('get')
            ->willReturn($this->manager);

        $this->adapter = new LocaleAdapter($this->container);
    }

    /**
     * Tests adapt with multiple deprecated and up-to-date value formats.
     */
    public function testAdapt()
    {
        $this->assertEquals(
            [
                'backend' => 'en',
                'frontend' => [ 'en', 'es' ],
                'main' => 'en' ,
                'timezone' => 'UTC'
            ],
            $this->adapter->adapt([
                'backend'  => 'en',
                'frontend' => [ 'en', 'es' ],
                'main'     => 'en' ,
                'timezone' => 'UTC'
            ])
        );

        $this->dataset->expects($this->at(0))->method('get')
            ->willReturn([ 'site_language' => 'es', 'time_zone' => 'wibble/corge' ]);
        $this->assertEquals(
            [ 'backend' => 'es', 'timezone' => 'wibble/corge' ],
            $this->adapter->adapt([])
        );

        $this->dataset->expects($this->at(0))->method('get')
            ->willReturn([ 'site_language' => 'es', 'time_zone' => 21 ]);
        $this->assertEquals(
            [
                'backend' => 'es',
                'timezone' => \DateTimeZone::listIdentifiers()[21]
            ],
            $this->adapter->adapt(null)
        );
    }
}
