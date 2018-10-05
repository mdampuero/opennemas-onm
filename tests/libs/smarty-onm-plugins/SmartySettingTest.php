<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Libs\Smarty;

use Symfony\Component\Routing\Exception\RouteNotFoundException;

/**
 * Defines test cases for SmartyUrl class.
 */
class SmartySettingTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        include_once './libs/smarty-onm-plugins/function.setting.php';

        $this->container = $this->getMockBuilder('Container')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->ds = $this->getMockBuilder('DataSet' . uniqid())
            ->setMethods([ 'get' ])
            ->getMock();

        $this->em = $this->getMockBuilder('EntityManager')
            ->setMethods([ 'getDataSet' ])
            ->getMock();

        $this->request = $this->getMockBuilder('Request')
            ->setMethods([ 'getRequestUri' ])
            ->getMock();

        $this->rs = $this->getMockBuilder('RequestStack')
            ->setMethods([ 'getCurrentRequest' ])
            ->getMock();

        $this->smarty = $this->getMockBuilder('Smarty')
            ->setMethods([ 'getContainer' ])
            ->getMock();

        $this->em->expects($this->any())->method('getDataSet')
            ->with('Settings', 'instance')->willReturn($this->ds);

        $this->smarty->expects($this->any())->method('getContainer')
            ->willReturn($this->container);

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));
    }

    /**
     * Return a mock basing on the service name.
     *
     * @param string $name The service name.
     */
    public function serviceContainerCallback($name)
    {
        switch ($name) {
            case 'orm.manager':
                return $this->em;

            case 'request_stack':
                return $this->rs;
        }

        return null;
    }

    /**
     * Tests smarty_function_setting when no setting name provided.
     */
    public function testSettingWhenNoSettingName()
    {
        $this->assertEmpty(smarty_function_setting([], $this->smarty));
    }

    /**
     * Tests smarty_function_setting when refresh_interval setting provided for
     * a backend request.
     */
    public function testSettingWhenRefreshIntervalForBackendRequest()
    {
        $this->request->expects($this->once())->method('getRequestUri')
            ->willReturn('/admin/frontpages/preview');

        $this->rs->expects($this->once())->method('getCurrentRequest')
            ->willReturn($this->request);

        $this->assertEquals(-1, smarty_function_setting([
            'name' => 'refresh_interval'
        ], $this->smarty));
    }

    /**
     * Tests smarty_function_setting when refresh_interval setting provided for
     * a frontend request.
     */
    public function testSettingWhenRefreshIntervalForFrontendRequest()
    {
        $this->ds->expects($this->once())->method('get')
            ->with('refresh_interval')->willReturn(300);

        $this->request->expects($this->once())->method('getRequestUri')
            ->willReturn('/xyzzy');

        $this->rs->expects($this->once())->method('getCurrentRequest')
            ->willReturn($this->request);

        $this->assertEquals(300, smarty_function_setting([
            'name' => 'refresh_interval'
        ], $this->smarty));
    }

    /**
     * Tests smarty_function_setting when a setting name provided.
     */
    public function testSettingWhenSettingProvided()
    {
        $this->ds->expects($this->once())->method('get')
            ->with('norf')->willReturn('garply');

        $this->assertEquals('garply', smarty_function_setting([
            'name' => 'norf'
        ], $this->smarty));
    }

    /**
     * Tests smarty_function_setting when a setting name provided and the key
     * to return if the value is an array and it has a value for the key.
     */
    public function testSettingWhenSettingAndExistingSubfieldProvided()
    {
        $this->ds->expects($this->once())->method('get')
            ->with('norf')->willReturn([ 'frog' => 'garply', 'fred' => 'glork' ]);

        $this->assertEquals('glork', smarty_function_setting([
            'name'  => 'norf',
            'field' => 'fred'
        ], $this->smarty));
    }

    /**
     * Tests smarty_function_setting when a setting name provided and the key
     * to return if the value is an array and it has not a value for the key.
     */
    public function testSettingWhenSettingAndUnexistingSubfieldProvided()
    {
        $this->ds->expects($this->once())->method('get')
            ->with('norf')->willReturn([ 'frog' => 'garply', 'fred' => 'glork' ]);

        $this->assertEmpty(smarty_function_setting([
            'name'  => 'norf',
            'field' => 'flob'
        ], $this->smarty));
    }
}
