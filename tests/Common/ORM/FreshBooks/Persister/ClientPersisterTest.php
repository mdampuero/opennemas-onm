<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Framework\ORM\FreshBooks\Repository;

use Common\ORM\Core\Metadata;
use Common\ORM\Entity\Client;
use Common\ORM\FreshBooks\Persister\ClientPersister;

/**
 * Defines test cases for ClientPersister class.
 */
class ClientPersisterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Configures the test environment.
     */
    public function setUp()
    {
        $this->api = $this->getMockBuilder('Freshbooks\FreshBooksApi')
            ->disableOriginalConstructor()
            ->getMock();

        $this->api->method('setMethod')->willReturn(true);
        $this->api->method('post')->willReturn(true);

        $this->metadata = new Metadata([
            'properties' => [
                'id'          => 'integer',
                'first_name'  => 'string',
                'last_name'   => 'string',
                'email'       => 'string',
                'company'     => 'string',
                'phone'       => 'string',
                'address'     => 'string',
                'postal_code' => 'string',
                'city'        => 'string',
                'state'       => 'string',
                'country'     => 'string',
            ],
            'mapping' => [
                'freshbooks' => [
                    'id'          => [ 'name' => 'client_id', 'type' => 'string' ],
                    'first_name'  => [ 'name' => 'first_name', 'type' => 'string' ],
                    'last_name'   => [ 'name' => 'last_name', 'type' => 'string' ],
                    'email'       => [ 'name' => 'email', 'type' => 'string' ],
                    'company'     => [ 'name' => 'organization', 'type' => 'string' ],
                    'phone'       => [ 'name' => 'work_phone', 'type' => 'string' ],
                    'address'     => [ 'name' => 'p_street1', 'type' => 'string' ],
                    'postal_code' => [ 'name' => 'p_code', 'type' => 'string' ],
                    'city'        => [ 'name' => 'p_city', 'type' => 'string' ],
                    'state'       => [ 'name' => 'p_state', 'type' => 'string' ],
                    'country'     => [ 'name' => 'p_country', 'type' => 'string' ],
                ]
            ],
        ]);

        $this->persister = new ClientPersister('foo', 'bar', $this->metadata);

        $property = new \ReflectionProperty($this->persister, 'api');
        $property->setAccessible(true);
        $property->setValue($this->persister, $this->api);

        $this->existingClient = new Client([
            'id'         => 1,
            'first_name' => 'John',
            'last_name'  => 'Doe'
        ]);

        $this->unexistingClient = new Client([
            'first_name' => 'John',
            'last_name'  => 'Doe'
        ]);
    }

    /**
     * Tests create when API returns false.
     *
     * @expectedException \RuntimeException
     */
    public function testCreateWithError()
    {
        $this->api->method('success')->willReturn(false);

        $this->api->expects($this->once())->method('setMethod')
            ->with('client.create');

        $this->persister->create($this->existingClient);
    }

    /**
     * Tests create.
     */
    public function testCreateWithoutErrors()
    {
        $response = [
            '@attributes' => [ 'status' => 'ok' ],
            'client_id'   => '123',
        ];

        $this->api->method('success')->willReturn(true);
        $this->api->method('getResponse')->willReturn($response);

        $this->api->expects($this->once())->method('setMethod')
            ->with('client.create');

        $this->api->expects($this->once())->method('post');
        $this->api->expects($this->once())->method('success');
        $this->api->expects($this->once())->method('getResponse');

        $this->persister->create($this->unexistingClient);
        $this->assertEquals(
            $response['client_id'],
            $this->unexistingClient->id
        );
    }

    /**
     * Tests remove when API returns false.
     *
     * @expectedException \Common\ORM\Core\Exception\EntityNotFoundException
     */
    public function testRemoveWithError()
    {
        $this->api->method('success')->willReturn(false);

        $this->api->expects($this->once())->method('setMethod')
            ->with('client.delete');

        $this->persister->remove($this->unexistingClient);
    }

    /**
     * Tests remove.
     */
    public function testRemoveWithoutErrors()
    {
        $response = [ '@attributes' => [ 'status' => 'ok' ] ];

        $this->api->method('success')->willReturn(true);
        $this->api->method('getResponse')->willReturn($response);

        $this->api->expects($this->once())->method('setMethod')
            ->with('client.delete');

        $this->api->expects($this->once())->method('post');
        $this->api->expects($this->once())->method('success');

        $this->persister->remove($this->existingClient);
    }

    /**
     * Tests update when API returns false.
     *
     * @expectedException \Common\ORM\Core\Exception\EntityNotFoundException
     */
    public function testUpdateWithError()
    {
        $this->api->method('success')->willReturn(false);

        $this->api->expects($this->once())->method('setMethod')
            ->with('client.update');

        $this->persister->update($this->unexistingClient);
    }

    /**
     * Tests update.
     */
    public function testUpdateWithoutErrors()
    {
        $response = [ '@attributes' => [ 'status' => 'ok' ] ];

        $this->api->method('success')->willReturn(true);
        $this->api->method('getResponse')->willReturn($response);

        $this->api->expects($this->once())->method('setMethod')
            ->with('client.update');

        $this->api->expects($this->once())->method('post');
        $this->api->expects($this->once())->method('success');

        $this->persister->update($this->existingClient);
    }
}
