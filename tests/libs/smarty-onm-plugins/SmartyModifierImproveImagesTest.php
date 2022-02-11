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
    }

    /**
     * Tests improve images modifier.
     */
    public function testImproveImagesModifier()
    {
        $img = '<img src="glorp">';

        $this->assertEquals('<img data-src="glorp" class="lazyload">', smarty_modifier_improve_images($img));

        $img = '<img src="glorp" class="foo">';

        $this->assertEquals('<img data-src="glorp" class="foo lazyload">', smarty_modifier_improve_images($img));

        $figure = '<figure class="image image-inbody-left" data-random="foo">' .
            '<img width="340" height="340" src="baz">' .
            '</figure>';

        $result = '<figure class="image image-inbody-left ckeditor-image" style="width: 340px" data-random="foo">' .
            '<img width="340" height="340" data-src="baz" class="lazyload">' .
            '</figure>';

        $this->assertEquals($result, smarty_modifier_improve_images($figure));

        $figure = '<figure class="image">' .
            '<img width="340" height="340" src="baz">' .
            '</figure>';

        $result = '<figure class="image ckeditor-image" style="width: 340px">' .
            '<img width="340" height="340" data-src="baz" class="lazyload">' .
            '</figure>';

        $this->assertEquals($result, smarty_modifier_improve_images($figure));
    }
}
