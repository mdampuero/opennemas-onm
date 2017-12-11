<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\Data\Filter;

use Common\Data\Filter\TagsFilter;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Defines tests cases for TagsFilter class.
 */
class TagsFilterTest extends KernelTestCase
{
    public function setUp()
    {
        $this->container = $this->getMockBuilder('ServiceContainer')
            ->setMethods([ 'hasParameter' ])
            ->getMock();

        $this->container->expects($this->any())->method('hasParameter')
            ->willReturn(false);

        $this->filter = new TagsFilter($this->container);
    }

    /**
     * Tests filter.
     */
    public function testFilterWithParameters()
    {
        $str = '通訳・翻訳キャリアガイド・オンライン 2017 y otras Cosas mas en '
            . 'Español متوقعًا إفلاسه، النظام ينتهج استراتيجية القمع الوقائي Đ Ó 3';

        $params = [ 'separator' => ',', 'lowercase' => false ];
        $filter = new TagsFilter($this->container, $params);

        $this->assertEquals(
            '通訳・翻訳キャリアガイド・オンライン,2017,otras,Cosas,mas,Español,م'
            . 'توقعًا,إفلاسه،,النظام,ينتهج,استراتيجية,القمع,الوقائي,Đ,Ó,3',
            $filter->filter($str)
        );

        $params = [ 'separator' => '-', 'lowercase' => true ];
        $filter = new TagsFilter($this->container, $params);

        $this->assertEquals(
            '通訳・翻訳キャリアガイド・オンライン-2017-otras-cosas-mas-español-م'
            . 'توقعًا-إفلاسه،-النظام-ينتهج-استراتيجية-القمع-الوقائي-đ-ó-3',
            $filter->filter($str)
        );
    }

    /**
     * Tests filter with shorts strings that should be removed in Spanish
     * language.
     */
    public function testFilterWithShortStrings()
    {
        $this->assertEquals(
            'Sánchez,obligará,españa,español,letras,você,tildes',
            $this->filter->filter("Sánchez obligará españa español con letras você tildes")
        );
    }
}
