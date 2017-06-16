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

use Common\Data\Adapter\GoogleAnalyticsAdapter;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Defines test cases for GoogleAnalyticsAdapter class.
 */
class GoogleAnalyticsAdapterTest extends KernelTestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $container     = $this->getMockBuilder('ServiceContaienr')->getMock();
        $this->adapter = new GoogleAnalyticsAdapter($container);
    }

    /**
     * Tests adapt with
     */
    public function testAdapt()
    {
        $this->assertEquals([], $this->adapter->adapt(null));
        $this->assertEquals([], $this->adapter->adapt(''));
        $this->assertEquals([], $this->adapter->adapt([]));

        $ga = [
            'api_key'     => 'wibble',
            'base_domain' => 'corge',
            'custom_var'  => 'foobar'
        ];

        $this->assertEquals([ $ga ], $this->adapter->adapt($ga));
    }
}
