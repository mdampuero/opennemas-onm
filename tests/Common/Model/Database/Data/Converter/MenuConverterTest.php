<?php

namespace Tests\Common\Model\Database\Data\Converter;

use Common\Model\Database\Data\Converter\MenuConverter;

/**
 * Defines test cases for ContentConverter class.
 */
class MenuConverterTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->metadata = $this->getMockBuilder('Opennemas\Orm\Core\Metadata')
            ->disableOriginalConstructor()
            ->getMock();

        $this->converter = new MenuConverter($this->metadata);
    }

    /**
     * Tests databasify related with multilanguage and without multilanguage.
     */
    public function testdatabasifyMenuItems()
    {
        $this->assertEquals([], $this->converter->databasifyMenuItems([]));

        $this->assertEquals([
            [
                'source_id' => 948,
                'target_id' => 734,
                'type'      => 'qux',
                'caption'   => 'Id diam impedit agam interesset enim.'
            ]
        ], $this->converter->databasifyMenuItems([
            [
                'source_id' => 948,
                'target_id' => 734,
                'type'      => 'qux',
                'caption'   => 'Id diam impedit agam interesset enim.'
            ]
        ]));

        $this->assertEquals([
            [
                'source_id' => 948,
                'target_id' => 734,
                'type'      => 'qux',
                'title'   => 'a:2:{'
                    . 's:2:"en";s:37:"Id diam impedit agam interesset enim.";'
                    . 's:2:"es";s:32:"Gravida aliquet aliquet viderer.";}',
                'link_name'   => 'a:2:{'
                    . 's:2:"en";s:37:"Id diam impedit agam interesset enim.";'
                    . 's:2:"es";s:32:"Gravida aliquet aliquet viderer.";}'
            ]
        ], $this->converter->databasifyMenuItems([
            [
                'source_id' => 948,
                'target_id' => 734,
                'type'      => 'qux',
                'title'   => [
                    'en' => 'Id diam impedit agam interesset enim.',
                    'es' => 'Gravida aliquet aliquet viderer.'
                ],
                'link_name'   => [
                    'en' => 'Id diam impedit agam interesset enim.',
                    'es' => 'Gravida aliquet aliquet viderer.'
                ],
            ]
        ]));
    }

    /**
     * Tests sObjectifyStrict with multilanguage and without multilanguage.
     */
    public function testSObjectifyStrict()
    {
        $method = new \ReflectionMethod($this->converter, 'sObjectifyStrict');
        $method->setAccessible(true);

        $this->assertEquals([], $method->invokeArgs($this->converter, [[]]));

        $this->assertEquals([
            'menu_items' => [[
                'source_id' => 948,
                'target_id' => 734,
                'type'      => 'qux',
                'title'   => 'Id diam impedit agam interesset enim.',
                'link_name'   => 'Id diam impedit agam interesset enim.'
            ]]
        ], $method->invokeArgs($this->converter, [[
            'menu_items' => [[
                'source_id' => 948,
                'target_id' => 734,
                'type'      => 'qux',
                'title'   => 'Id diam impedit agam interesset enim.',
                'link_name'   => 'Id diam impedit agam interesset enim.'
            ]]
        ]]));

        $this->assertEquals([
            'menu_items' => [[
                'source_id' => 948,
                'target_id' => 734,
                'type'      => 'qux',
                'title'   => [
                    'en' => 'Id diam impedit agam interesset enim.',
                    'es' => 'Gravida aliquet aliquet viderer.'
                ],
                'link_name'   => [
                    'en' => 'Id diam impedit agam interesset enim.',
                    'es' => 'Gravida aliquet aliquet viderer.'
                ]
            ]]
        ], $method->invokeArgs($this->converter, [[
            'menu_items' => [[
                'source_id' => 948,
                'target_id' => 734,
                'type'      => 'qux',
                'title'   => 'a:2:{'
                    . 's:2:"en";s:37:"Id diam impedit agam interesset enim.";'
                    . 's:2:"es";s:32:"Gravida aliquet aliquet viderer.";}',
                'link_name'   => 'a:2:{'
                    . 's:2:"en";s:37:"Id diam impedit agam interesset enim.";'
                    . 's:2:"es";s:32:"Gravida aliquet aliquet viderer.";}'
            ] ]
        ]]));
    }
}
