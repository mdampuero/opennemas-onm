<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\Core\Component\Helper;

use Common\Core\Component\Helper\CsvHelper;

/**
 * Defines test cases for CsvHelper class.
 */
class CsvHelperTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->helper = new CsvHelper();
    }

    /**
     * Tests getWriter when it is called multiple times.
     */
    public function testGetWriter()
    {
        $method = new \ReflectionMethod($this->helper, 'getWriter');
        $method->setAccessible(true);

        $this->assertInstanceOf(
            'League\Csv\Writer',
            $method->invokeArgs($this->helper, [])
        );
    }
}
