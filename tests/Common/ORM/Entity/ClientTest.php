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

use Common\ORM\Entity\Client;

class ClientTest extends \PHPUnit\Framework\TestCase
{
    public function testConstructor()
    {
        $data   = [ 'foo' => 'bar' ];
        $entity = new Client($data);

        $this->assertEquals($entity->getData(), $data);

        foreach ($data as $key => $value) {
            $this->assertEquals($value, $entity->{$key});
        }
    }

    public function testExistsWithExistingClient()
    {
        $data   = [ 'id' => '1' ];
        $entity = new Client($data);

        $entity->refresh();

        $this->assertTrue($entity->exists());
    }

    public function testExistsWithUnexistingClient()
    {
        $data   = [ 'foo' => 'bar' ];
        $entity = new Client($data);

        $this->assertFalse($entity->exists());
    }
}
