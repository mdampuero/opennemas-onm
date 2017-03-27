<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\Core\Component\Filter;

use Common\Core\Component\Filter\TagsFilter;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Defines tests cases for SlugFilter class.
 */
class TagsFilterTest extends KernelTestCase
{
    /**
     * Tests filter.
     */
    public function testFilterWithParameters()
    {
        $container = $this->getMockBuilder('Container')->getMock();

        $str = '通訳・翻訳キャリアガイド・オンライン 2017 y otras Cosas mas en Español متوقعًا إفلاسه، النظام ينتهج استراتيجية القمع الوقائي Đ Ó 3';
        $params = [ 'separator' => ',', 'lowercase' => false ];

        $filter = new TagsFilter($params);
        $tags = $filter->filter($str);
        $this->assertEquals(
            '通訳・翻訳キャリアガイド・オンライン, 2017, otras, Cosas, mas, Español, متوقعًا, إفلاسه،, النظام, ينتهج, استراتيجية, القمع, الوقائي, Đ, Ó, 3',
            $tags
        );


        $params = [ 'separator' => ',', 'lowercase' => true ];
        $filter = new TagsFilter($container, $params);
        $tags = $filter->filter($str);
        $this->assertEquals(
            '通訳・翻訳キャリアガイド・オンライン, 2017, otras, cosas, mas, español, متوقعًا, إفلاسه،, النظام, ينتهج, استراتيجية, القمع, الوقائي, đ, ó, 3',
            $tags
        );
    }
}
