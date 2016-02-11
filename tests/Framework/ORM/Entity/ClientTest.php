<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <onm-devs@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Framework\ORM\Entity;

use Framework\ORM\Entity\Client;

class ClientTest extends \PHPUnit_Framework_TestCase
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
        $data   = [ 'client_id' => '1' ];
        $entity = new Client($data);

        $this->assertTrue($entity->exists());
    }

    public function testExistsWithUnexistingClient()
    {
        $data   = [ 'foo' => 'bar' ];
        $entity = new Client($data);

        $this->assertFalse($entity->exists());
    }
}
