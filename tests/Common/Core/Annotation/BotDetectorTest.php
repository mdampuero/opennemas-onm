<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\Core\Annotation;

use Common\Core\Annotation\BotDetector;

/**
 * Defines test cases for BotDetector class.
 */
class BotDetectorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tests the annotation creation and getter methors.
     */
    public function testBotDetector()
    {
        $annotation = new BotDetector([ 'bot' => 'frog', 'route' => 'grault' ]);

        $this->assertEquals('frog', $annotation->getBot());
        $this->assertEquals('grault', $annotation->getRoute());
    }
}
