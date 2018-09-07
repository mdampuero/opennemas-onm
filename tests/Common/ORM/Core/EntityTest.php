<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <onm-devs@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\ORM\Core;

use Common\ORM\Core\Entity;
use Framework\Fixture\FixtureLoader;

class EntityTest extends \PHPUnit\Framework\TestCase
{
    public function testExists()
    {
        $entity = new Entity([ 'bar' => 'glork' ]);
        $this->assertFalse($entity->exists());

        $entity->refresh();
        $this->assertTrue($entity->exists());
    }

    public function testGetCachedId()
    {
        $entity = new Entity([ 'id' => 1 ]);
        $this->assertEquals('entity-1', $entity->getCachedId());
    }

    public function testGetClassName()
    {
        $entity = new Entity();
        $this->assertEquals('Entity', $entity->getClassName());
    }

    public function testGetOrigin()
    {
        $entity = new Entity();
        $entity->setOrigin('thud');

        $this->assertEquals('thud', $entity->getOrigin());
    }

    public function testMerge()
    {
        $data   = [ 'foo' => 'bar' ];
        $entity = new Entity($data);

        $this->assertFalse($entity->merge(null));
        $this->assertFalse($entity->merge(1));
        $this->assertFalse($entity->merge('foo'));

        $entity->merge([ 'foo' => 'norf', 'baz' => 'qux' ]);
        $this->assertEquals('norf', $entity->foo);
        $this->assertEquals('qux', $entity->baz);
    }
}
