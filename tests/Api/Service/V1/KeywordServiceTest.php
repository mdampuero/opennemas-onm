<?php

/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Api\Service\V1;

use Api\Service\V1\ContentService;
use Api\Service\V1\KeywordService;
use Common\Model\Entity\Content;
use Common\Model\Entity\Keyword;
use Opennemas\Orm\Core\Entity;

/**
 * Defines test cases for CategoryService class.
 */
class KeywordServiceTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->container = $this->getMockBuilder('ServiceContainer')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->text = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.';

        $this->service = $this->getMockBuilder('Api\Service\V1\KeywordService')
            ->disableOriginalConstructor()
            ->setMethods([ 'get' ])
            ->getMock();
    }

    public function testReplaceTerms()
    {
        $keywords = [
            new Keyword(['id' => '1', 'keyword' => 'Lorem', 'type' => 'url', 'value'       => 'glorp']),
            new Keyword(['id' => '2', 'keyword' => 'ipsum', 'type' => 'email', 'value'     => 'baz']),
            new Keyword(['id' => '3', 'keyword' => 'dolor', 'type' => 'intsearch', 'value' => 'foo']),
            new Keyword(['id' => '4', 'keyword' => 'sit', 'type'   => '', 'value'          => 'default'])
        ];

        $expected = '<a href="glorp" target="_blank">Lorem</a> ' .
            '<a href="mailto:baz" target="_blank">ipsum</a> ' .
            '<a href="/tag/foo" target="_blank">dolor</a> ' .
            'sit amet, consectetur adipiscing elit.';

        $this->assertEquals($expected, $this->service->replaceTerms($this->text, $keywords));
    }
}
