<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Onm\Utils;

class BotDetectorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var StringUtils
     */
    protected $object;

    /**
     * @covers Onm\Utils\BotDetector::isBot
     */
    public function testIsBot()
    {
        // Test
        $this->assertFalse(BotDetector::isBot('Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2228.0 Safari/537.36'));

        // Test
        $this->assertTrue(BotDetector::isBot('Mozilla/5.0 (compatible; bingbot/2.0; +http://www.bing.com/bingbot.htm)'));
    }

    /**
     * @covers Onm\Utils\BotDetector::isSpecificBot
     */
    public function testIsSpecificBot()
    {
        // Test
        $this->assertFalse(BotDetector::isSpecificBot('Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2228.0 Safari/537.36', 'bingbot'));

        // Test
        $this->assertTrue(BotDetector::isSpecificBot('Mozilla/5.0 (compatible; bingbot/2.0; +http://www.bing.com/bingbot.htm)', 'bingbot'));
    }
}
