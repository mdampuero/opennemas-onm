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

    public function testCompositeKeywordsReplacement()
    {
        $keywords = [
            new Keyword(['id' => '1', 'keyword' => 'Lorem', 'type' => 'url', 'value' => 'glorp']),
            new Keyword(['id' => '2', 'keyword' => 'Lorem ipsum', 'type' => 'email', 'value' => 'link_lorem_ipsum']),
            new Keyword(['id' => '3', 'keyword' => 'dolor sit amet', 'type' => 'intsearch', 'value' => 'foo']),
            new Keyword(['id' => '4', 'keyword' => 'sit amet', 'type'   => 'url', 'value' => 'link_sit_amet']),
            new Keyword(['id' => '5', 'keyword' => 'sit', 'type'   => 'email', 'value' => 'link_sit'])
        ];
        $newText = $this->text . ' Lorem dolor sit, consectetur adipiscing sit';

        $expected = '<a href="mailto:link_lorem_ipsum" target="_blank">Lorem ipsum</a> ' .
            '<a href="/tag/foo" target="_blank">dolor sit amet</a>, consectetur adipiscing elit. ' .
            '<a href="glorp" target="_blank">Lorem</a> dolor ' .
            '<a href="mailto:link_sit" target="_blank">sit</a>, consectetur adipiscing ' .
            '<a href="mailto:link_sit" target="_blank">sit</a>';

        $actual = $this->service->replaceTerms($newText, $keywords);

        $this->assertEquals($expected, $actual);
    }

    public function testReplaceTermsRepeat()
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
        $textreplace_1 = $this->service->replaceTerms($this->text, $keywords);

        $textreplace_2 = $this->service->replaceTerms($textreplace_1, $keywords);

        $actual = $this->service->replaceTerms($textreplace_2, $keywords);

        $this->assertEquals($expected, $actual);
    }
}
