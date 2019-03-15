<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <onm-devs@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\ORM\Entity;

use Common\ORM\Entity\Content;

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

    public function testGetRelated()
    {
        $data   = [ 'related_contents' => [] ];
        $entity = new Content($data);

        $this->assertEmpty($entity->getRelated('photo'));

        $data   = [ 'related_contents' => [
            [
                'pk_content2'  => 2,
                'relationship' => 'photo',
            ]
        ] ];
        $entity = new Content($data);

        $this->assertEquals($entity->getRelated('photo'), 2);

        $data   = [ 'related_contents' => [
            [
                'pk_content2'  => 2,
                'relationship' => 'photo',
            ],
            [
                'pk_content2'  => 3,
                'relationship' => 'photo',
            ]
        ] ];
        $entity = new Content($data);

        $this->assertEquals($entity->getRelated('photo'), [ 2, 3 ]);
    }

    public function testHasRelated()
    {
        $data   = [ 'related_contents' => [
            [
                'pk_content2'  => 2,
                'relationship' => 'photo',
            ],
            [
                'pk_content2'  => 3,
                'relationship' => 'photo',
            ]
        ] ];
        $entity = new Content($data);

        $this->assertTrue($entity->hasRelated('photo'));
    }

    public function testIsReadyForPublish()
    {
        // Entity with all valid
        $data   = [
            'starttime'      => (new \DateTime())->sub(new \DateInterval('P7D')),
            'endtime'        => (new \DateTime()),
            'in_litter'      => 0,
            'content_status' => 1,
         ];
        $entity = new Content($data);

        $this->assertTrue($entity->isReadyForPublish($entity));
    }

    public function testIsReadyForPublishWithContentStatusZero()
    {
        // Entity with content_status = 0
        $data   = [
            'starttime'      => (new \DateTime())->sub(new \DateInterval('P7D')),
            'endtime'        => (new \DateTime()),
            'in_litter'      => 0,
            'content_status' => 0,
         ];
        $entity = new Content($data);

        $this->assertFalse($entity->isReadyForPublish($entity));
    }

    public function testIsReadyForPublishWithInLitter()
    {
        // Entity with in_litter = 1
        $data   = [
            'starttime'      => (new \DateTime())->sub(new \DateInterval('P7D')),
            'endtime'        => (new \DateTime()),
            'in_litter'      => 1,
            'content_status' => 1,
         ];
        $entity = new Content($data);

        $this->assertFalse($entity->isReadyForPublish($entity));
    }

    public function testIsReadyForPublishWithContentDued()
    {
        // Entity dued
        $data   = [
            'starttime'      => (new \DateTime())->sub(new \DateInterval('P8D')),
            'endtime'        => (new \DateTime())->sub(new \DateInterval('P7D')),
            'in_litter'      => 0,
            'content_status' => 1,
         ];
        $entity = new Content($data);

        $this->assertFalse($entity->isReadyForPublish($entity));
    }

    public function testIsReadyForPublishWithContentPostponed()
    {
        // Entity postponed
        $data   = [
            'starttime'      => (new \DateTime())->add(new \DateInterval('P8D')),
            'endtime'        => (new \DateTime())->add(new \DateInterval('P9D')),
            'in_litter'      => 0,
            'content_status' => 1,
         ];
        $entity = new Content($data);

        $this->assertFalse($entity->isReadyForPublish($entity));
    }
}
