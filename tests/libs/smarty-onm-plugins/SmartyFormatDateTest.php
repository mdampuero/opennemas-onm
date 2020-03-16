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
class SmartyFormatDateTests extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        include_once './libs/smarty-onm-plugins/function.format_date.php';

        $this->container = $this->getMockBuilder('Container')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->fm = $this->getMockBuilder('Common\Data\Filter\FilterManager')
            ->disableOriginalConstructor()
            ->setMethods([ 'filter', 'get', 'set' ])
            ->getMock();

        $this->locale = $this->getMockBuilder('Common\Core\Component\Locale\Locale')
            ->disableOriginalConstructor()
            ->setMethods([ 'getLocale', 'getTimeZone' ])
            ->getMock();

        $this->smarty = $this->getMockBuilder('Smarty')
            ->setMethods([ 'getContainer' ])
            ->getMock();

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));

        $this->smarty->expects($this->any())->method('getContainer')
            ->willReturn($this->container);
    }

    /**
     * Return a mock basing on the service name.
     *
     * @param string $name The service name.
     *
     * @return mixed The mocked service.
     */
    public function serviceContainerCallback($name)
    {
        switch ($name) {
            case 'core.locale':
                return $this->locale;

            case 'data.manager.filter':
                return $this->fm;

            default:
                return null;
        }
    }

    /**
     * Tests smarty_function_format_date when an expection is thrown by the
     * FilterManager.
     */
    public function testFormatDateWhenError()
    {
        $this->locale->expects($this->once())->method('getLocale')
            ->with('frontend')->willReturn('es_ES');
        $this->locale->expects($this->once())->method('getTimeZone')
            ->willReturn(new \DateTimeZone('Europe/Madrid'));

        $this->fm->expects($this->once())->method('set')
            ->willReturn($this->fm);
        $this->fm->expects($this->once())->method('filter')
            ->with('format_date', [
                'format'   => null,
                'locale'   => 'es_ES',
                'timezone' => new \DateTimeZone('Europe/Madrid'),
                'type'     => 'long|short'
            ])->will($this->throwException(new \Exception()));

        $this->assertEmpty(smarty_function_format_date([], $this->smarty));
    }

    /**
     * Tests smarty_function_format_date when the provided date is invalid and
     * the DateTime object could not be created.
     */
    public function testFormatDateWhenInvalidDate()
    {
        $this->locale->expects($this->once())->method('getLocale')
            ->with('frontend')->willReturn('es_ES');
        $this->locale->expects($this->once())->method('getTimeZone')
            ->willReturn(new \DateTimeZone('Europe/Madrid'));

        $this->assertEmpty(smarty_function_format_date([
            'date' => '2010-2010-10-10'
        ], $this->smarty));
    }

    /**
     * Tests smarty_function_format_date when no expection thrown by the
     * FilterManager.
     */
    public function testFormatDateWhenNoError()
    {
        $this->locale->expects($this->once())->method('getLocale')
            ->with('frontend')->willReturn('es_ES');
        $this->locale->expects($this->once())->method('getTimeZone')
            ->willReturn(new \DateTimeZone('Europe/Madrid'));

        $this->fm->expects($this->once())->method('set')
            ->willReturn($this->fm);
        $this->fm->expects($this->once())->method('filter')
            ->with('format_date', [
                'format'   => null,
                'locale'   => 'es_ES',
                'timezone' => new \DateTimeZone('Europe/Madrid'),
                'type'     => 'long|short'
            ])->willReturn($this->fm);
        $this->fm->expects($this->once())->method('get')
            ->willReturn('fred');

        $this->assertEquals(
            'fred',
            smarty_function_format_date([], $this->smarty)
        );
    }
}
