<?php

namespace Tests\Libs\Smarty;

/**
 * Defines test cases for SmartyUrl class.
 */
class SmartyModifierImproveImages extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        include_once './libs/smarty-onm-plugins/modifier.improve_images.php';

        $this->container = $this->getMockBuilder('Container')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->helper = $this->getMockBuilder('PhotoHelper')
            ->setMethods([ 'getSrcSetFromImagePath' ])
            ->getMock();

        $this->kernel = $this->getMockBuilder('Kernel')
            ->setMethods([ 'getContainer' ])
            ->getMock();

        $this->kernel->expects($this->any())->method('getContainer')
            ->willReturn($this->container);

        $this->container->expects($this->any())->method('get')
            ->with('core.helper.photo')
            ->willReturn($this->helper);

        $GLOBALS['kernel'] = $this->kernel;
    }

    /**
     * Tests improve images modifier when width and src provided.
     */
    public function testImproveImageSrcWidth()
    {
        $this->helper->expects($this->any())->method('getSrcSetFromImagePath')
            ->with('mercury', 10)
            ->willReturn(
                '/asset/thumbnail,480,270,center,center//media/opennemas/images/2018/10/02/2018100212424091861.jpg 480w'
            );

        $img = '<img width="10" src="mercury" >';

        $this->assertEquals(
            '<img width="10" data-src="mercury"  class="lazyload" data-srcset="' .
            '/asset/thumbnail,480,270,center,center//media/opennemas/images/2018/10/02/2018100212424091861.jpg 480w" ' .
            'data-sizes="auto">',
            smarty_modifier_improve_images($img)
        );
    }

    /**
     * Tests improve images modifier when no width provided.
     */
    public function testImproveImageNoWidth()
    {
        $this->helper->expects($this->any())->method('getSrcSetFromImagePath')
            ->with('mercury', 9999)
            ->willReturn(
                '/asset/thumbnail,480,270,center,center//media/opennemas/images/2018/10/02/2018100216271084045.jpg ' .
                '480w, /asset/thumbnail,768,432,center,center//media/opennemas/images/2018/10/02/' .
                '2018100216271084045.jpg 768w, /asset/thumbnail,992,558,center,center//media/opennemas/images' .
                '/2018/10/02/2018100216271084045.jpg 992w, /asset/thumbnail,1280,720,center,center//media/opennemas' .
                '/images/2018/10/02/2018100216271084045.jpg 1280w'
            );

        $img = '<img src="mercury" >';

        $this->assertEquals(
            '<img data-src="mercury"  class="lazyload" data-srcset="' .
            '/asset/thumbnail,480,270,center,center//media/opennemas/images/2018/10/02/2018100216271084045.jpg ' .
            '480w, /asset/thumbnail,768,432,center,center//media/opennemas/images/2018/10/02/' .
            '2018100216271084045.jpg 768w, /asset/thumbnail,992,558,center,center//media/opennemas/images' .
            '/2018/10/02/2018100216271084045.jpg 992w, /asset/thumbnail,1280,720,center,center//media/opennemas' .
            '/images/2018/10/02/2018100216271084045.jpg 1280w" ' .
            'data-sizes="auto">',
            smarty_modifier_improve_images($img)
        );
    }

    /**
     * Tests improve images modifier when class but no width provided.
     */
    public function testImproveImageClassNoWidth()
    {
        $this->helper->expects($this->any())->method('getSrcSetFromImagePath')
            ->with('mercury', 9999)
            ->willReturn(
                '/asset/thumbnail,480,270,center,center//media/opennemas/images/2018/10/02/2018100216271084045.jpg ' .
                '480w, /asset/thumbnail,768,432,center,center//media/opennemas/images/2018/10/02/' .
                '2018100216271084045.jpg 768w, /asset/thumbnail,992,558,center,center//media/opennemas/images' .
                '/2018/10/02/2018100216271084045.jpg 992w, /asset/thumbnail,1280,720,center,center//media/opennemas' .
                '/images/2018/10/02/2018100216271084045.jpg 1280w'
            );

        $img = '<img src="mercury" class="camazot">';

        $this->assertEquals(
            '<img data-src="mercury" class="camazot lazyload" data-srcset="' .
            '/asset/thumbnail,480,270,center,center//media/opennemas/images/2018/10/02/2018100216271084045.jpg ' .
            '480w, /asset/thumbnail,768,432,center,center//media/opennemas/images/2018/10/02/' .
            '2018100216271084045.jpg 768w, /asset/thumbnail,992,558,center,center//media/opennemas/images' .
            '/2018/10/02/2018100216271084045.jpg 992w, /asset/thumbnail,1280,720,center,center//media/opennemas' .
            '/images/2018/10/02/2018100216271084045.jpg 1280w" ' .
            'data-sizes="auto">',
            smarty_modifier_improve_images($img)
        );
    }

    /**
     * Tests improve images modifier when figure.
     */
    public function testImproveImageFigure()
    {
        $this->helper->expects($this->any())->method('getSrcSetFromImagePath')
            ->with('baz', 340)
            ->willReturn(
                '/asset/thumbnail,480,270,center,center//media/opennemas/images/2018/10/02/2018100216271084045.jpg ' .
                '480w'
            );

        $figure = '<figure class="image image-inbody-left" data-random="foo">' .
            '<img width="340" height="340" src="baz">' .
            '</figure>';

        $result = '<figure class="image image-inbody-left ckeditor-image" style="width: 340px" data-random="foo">' .
            '<img width="340" height="340" data-src="baz" ' .
            'class="lazyload" data-srcset="/asset/thumbnail,480,270,center,center' .
            '//media/opennemas/images/2018/10/02/2018100216271084045.jpg 480w" data-sizes="auto">' .
            '</figure>';

        $this->assertEquals($result, smarty_modifier_improve_images($figure));
    }
}
