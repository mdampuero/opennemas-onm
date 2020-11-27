<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <onm-devs@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\Model\Entity;

use Common\Model\Entity\Content;

class ContentTest extends \PHPUnit\Framework\TestCase
{
    public function testConstructor()
    {
        $data   = [ 'foo' => 'bar' ];
        $entity = new Content($data);

        $this->assertEquals($entity->getData(), $data);

        foreach ($data as $key => $value) {
            $this->assertEquals($value, $entity->{$key});
        }
    }

    public function testPkcontenToId()
    {
        $data   = [ 'pk_content' => 1 ];
        $entity = new Content($data);

        $this->assertEquals($entity->id, 1);
    }

    public function testIsset()
    {
        $data   = [ 'pk_content' => 1 ];
        $entity = new Content($data);

        $this->assertTrue($entity->__isset('id'));
    }

    public function testGetMedia()
    {
        $data   = [ 'related_contents' => [] ];
        $entity = new Content($data);

        $this->assertEmpty($entity->getMedia('photo'));

        $data   = [ 'related_contents' => [
            [
                'target_id' => 2,
                'type'      => 'photo',
            ]
        ] ];
        $entity = new Content($data);

        $this->assertEquals(2, $entity->getMedia('photo'));
    }

    public function testGetRelated()
    {
        $data   = [ 'related_contents' => [] ];
        $entity = new Content($data);

        $this->assertEmpty($entity->getRelated('photo'));

        $data   = [ 'related_contents' => [
            [
                'target_id' => 2,
                'type'      => 'photo',
            ]
        ] ];
        $entity = new Content($data);

        $this->assertEquals([2], $entity->getRelated('photo'));

        $data   = [ 'related_contents' => [
            [
                'target_id' => 2,
                'type'      => 'photo',
            ],
            [
                'target_id' => 3,
                'type'      => 'photo',
            ]
        ] ];
        $entity = new Content($data);

        $this->assertEquals($entity->getRelated('photo'), [ 2, 3 ]);
    }

    public function testHasRelated()
    {
        $data   = [ 'related_contents' => [
            [
                'target_id' => 2,
                'type'      => 'photo',
            ],
            [
                'target_id' => 3,
                'type'      => 'photo',
            ]
        ] ];
        $entity = new Content($data);

        $this->assertTrue($entity->hasRelated('photo'));
    }
}
