<?php

namespace Tests\Common\Model\Database\Data\Converter;

use Common\Model\Database\Data\Converter\ContentConverter;

/**
 * Defines test cases for ContentConverter class.
 */
class ContentConverterTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->metadata = $this->getMockBuilder('Opennemas\Orm\Core\Metadata')
            ->disableOriginalConstructor()
            ->getMock();

        $this->converter = new ContentConverter($this->metadata);
    }

    /**
     * Tests databasify related with multilanguage and without multilanguage.
     */
    public function testDatabasifyRelated()
    {
        $this->assertEquals([], $this->converter->databasifyRelated([]));

        $this->assertEquals([
            [
                'source_id' => 948,
                'target_id' => 734,
                'type'      => 'qux',
                'caption'   => 'Id diam impedit agam interesset enim.'
            ]
        ], $this->converter->databasifyRelated([
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
                'caption'   => 'a:2:{'
                    . 's:2:"en";s:37:"Id diam impedit agam interesset enim.";'
                    . 's:2:"es";s:32:"Gravida aliquet aliquet viderer.";}'
            ]
        ], $this->converter->databasifyRelated([
            [
                'source_id' => 948,
                'target_id' => 734,
                'type'      => 'qux',
                'caption'   => [
                    'en' => 'Id diam impedit agam interesset enim.',
                    'es' => 'Gravida aliquet aliquet viderer.'
                ]
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
            'related_contents' => [[
                'source_id' => 948,
                'target_id' => 734,
                'type'      => 'qux',
                'caption'   => 'Id diam impedit agam interesset enim.'
            ]]
        ], $method->invokeArgs($this->converter, [[
            'related_contents' => [[
                'source_id' => 948,
                'target_id' => 734,
                'type'      => 'qux',
                'caption'   => 'Id diam impedit agam interesset enim.'
            ]]
        ]]));

        $this->assertEquals([
            'related_contents' => [[
                'source_id' => 948,
                'target_id' => 734,
                'type'      => 'qux',
                'caption'   => [
                    'en' => 'Id diam impedit agam interesset enim.',
                    'es' => 'Gravida aliquet aliquet viderer.'
                ]
            ]]
        ], $method->invokeArgs($this->converter, [[
            'related_contents' => [[
                'source_id' => 948,
                'target_id' => 734,
                'type'      => 'qux',
                'caption'   => 'a:2:{'
                    . 's:2:"en";s:37:"Id diam impedit agam interesset enim.";'
                    . 's:2:"es";s:32:"Gravida aliquet aliquet viderer.";}'
            ] ]
        ]]));
    }
}
