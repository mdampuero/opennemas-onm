<?php

namespace Tests\Common\Core\Component\Filter;

use Common\Core\Component\Filter\AmpFilter;

/**
 * Defines test cases for HtmlFilter class.
 */
class AmpFilterTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp() : void
    {
        $container = $this->getMockBuilder('Container')->getMock();

        $this->filter = new AmpFilter($container);
    }

    /**
     * Tests filter when value contains HTML.
     */
    public function testFilterWithValidHtml()
    {
        $str      = '';
        $expected = '';

        $this->assertEquals($expected, $this->filter->filter($str));

        $str      = [];
        $expected = '';

        $this->assertEquals($expected, $this->filter->filter($str));

        $str      = '<p>The string</p><p>to</p><p>parse</p>';
        $expected = '<p>The string</p><p>to</p><p>parse</p>';

        $this->assertEquals($expected, $this->filter->filter($str));
    }

    /**
     * Tests filter when value contains invalid properties.
     */
    public function testFilterWithInvalidProperties()
    {
        // String with align property
        $str      = '<p align="center">The string</p><p>to</p><p>parse</p>';
        $expected = '<p>The string</p><p>to</p><p>parse</p>';

        $this->assertEquals($expected, $this->filter->filter($str));

        // String with <a></a> with onclick
        $str      = '<p onclick="mumble()">The string</p><p>to</p><p>parse</p>';
        $expected = '<p>The string</p><p>to</p><p>parse</p>';

        $this->assertEquals($expected, $this->filter->filter($str));

        // String with a <a></a> with target
        $str      = '<a href="/mumble" target="_blank>The string</a><p>to</p><p>parse</p>';
        $expected = '<a href="/mumble">The string</a><p>to</p><p>parse</p>';

        $this->assertEquals($expected, $this->filter->filter($str));

        // String with not allowed properties in links and a <font></font>
        $str      = '<a href="/mumble" other="thing" target="_blank><font>The string</font></a><p>to</p><p>parse</p>';
        $expected = '<a href="/mumble">The string</a><p>to</p><p>parse</p>';

        $this->assertEquals($expected, $this->filter->filter($str));
    }

    /**
     * Tests filter when value contains entities to transform.
     */
    public function testFilterWithTransformedEntities()
    {
        // String with img
        $str      = '<p>The string</p><img src="/mumble/" /><p>to</p><p>parse</p>';
        $expected = '<p>The string</p><div class="fixed-container"><amp-img class="cover" layout="fill"'
            . ' src="/mumble/"></amp-img></div><p>to</p><p>parse</p>';

        $this->assertEquals($expected, $this->filter->filter($str));

        // Div with class and other attributes
        $str      = '<div style="width:100%;" class="thisClassShouldBeKept">Some div content</div>';
        $expected = '<div class="thisClassShouldBeKept">Some div content</div>';

        $this->assertEquals($expected, $this->filter->filter($str));

        // String with video
        $str      = '<p>The string</p><video src="/mumble/"></video><p>to</p><p>parse</p>';
        $expected = '<p>The string</p><amp-video layout="responsive" width="518" '
            . 'height="291" controls><div><p>This browser does not support the video '
            . 'element.</p></div></amp-video><p>to</p><p>parse</p>';

        $this->assertEquals($expected, $this->filter->filter($str));

        // String with iframe
        $str      = '<iframe src="http://whatever.com"></iframe>';
        $expected = '<amp-iframe width=518 height=291 sandbox="allow-scripts '
            . 'allow-same-origin allow-popups allow-popups-to-escape-sandbox allow-forms" '
            . 'layout="responsive" frameborder="0" src="https://whatever.com"> '
            . '<amp-img layout="fill" src="/assets/images/lazy-bg.png" placeholder></amp-img></amp-iframe>';

        $this->assertEquals($expected, $this->filter->filter($str));

        // String with iframe with withespaces between open/close
        $str      = '<iframe src="http://whatever.com">  </iframe>';
        $expected = '<amp-iframe width=518 height=291 sandbox="allow-scripts '
                . 'allow-same-origin allow-popups allow-popups-to-escape-sandbox allow-forms" '
                . 'layout="responsive" frameborder="0" src="https://whatever.com"> '
                . '<amp-img layout="fill" src="/assets/images/lazy-bg.png" placeholder></amp-img></amp-iframe>';

        $this->assertEquals($expected, $this->filter->filter($str));

        // String with iframe with newlines between open/close
        $str      = '<iframe src="http://whatever.com">

            </iframe>';
        $expected = '<amp-iframe width=518 height=291 sandbox="allow-scripts '
                    . 'allow-same-origin allow-popups allow-popups-to-escape-sandbox allow-forms" '
                    . 'layout="responsive" frameborder="0" src="https://whatever.com"> '
                    . '<amp-img layout="fill" src="/assets/images/lazy-bg.png" placeholder></amp-img></amp-iframe>';

            $this->assertEquals($expected, $this->filter->filter($str));
        // String with iframe without src
        $str      = '<iframe id="foo/bar/baz"></iframe>';
        $expected = '';

        $this->assertEquals($expected, $this->filter->filter($str));

        // String with facebook
        $str      = '<div class="fb-post" data-href="{your-post-url}"></div>';
        $expected = '<amp-facebook width=486 height=657 layout="responsive" '
            . 'data-embed-as="post" data-href="{your-post-url}"></amp-facebook>';

        $this->assertEquals($expected, $this->filter->filter($str));

        // String with instagram
        $str      = '<blockquote class="instagram-media" data-instgrm-captioned '
            . 'data-instgrm-permalink="https://www.instagram.com/p/tsxp1hhQTG/?utm_source=ig_embed&amp;'
            . 'utm_medium=loading" data-instgrm-version="12" style="">whatever</blockquote>';
        $expected = '<blockquote class="instagram-media">whatever</blockquote>';

        $this->assertEquals($expected, $this->filter->filter($str));

        // String with twitter
        $str      = '<blockquote class="twitter-tweet" data-lang="en"><p lang="en" '
            . 'dir="ltr">Sunsets don&#39;t get much better than this one over <a href="https://twitter.com/Grand'
            . 'TetonNPS?ref_src=twsrc%5Etfw">@GrandTetonNPS</a>. <a href="https://twitter.com/hashtag/nature?src='
            . 'hash&amp;ref_src=twsrc%5Etfw">#nature</a> <a href="https://twitter.com/hashtag/sunset?src=hash&amp;'
            . 'ref_src=twsrc%5Etfw">#sunset</a> <a href="http://t.co/YuKy2rcjyU">pic.twitter.com/YuKy2rcjyU</a></p>'
            . '&mdash; US Department of the Interior (@Interior) <a href="https://twitter.com/Interior/status/46344'
            . '0424141459456?ref_src=twsrc%5Etfw">May 5, 2014</a></blockquote>';
        $expected = '<amp-twitter width=486 height=657 layout="responsive" '
            . 'data-tweetid="463440424141459456"></amp-twitter>';

        $this->assertEquals($expected, $this->filter->filter($str));
    }

    /**
     * Tests filter when value contains invalid tags.
     */
    public function testFilterWithInvalidTags()
    {
        // String with meta and script
        $str      = '<script>document.write("hello");</script><meta link="whatever">to<p>parse</p>';
        $expected = 'to<p>parse</p>';

        $this->assertEquals($expected, $this->filter->filter($str));
    }
}
