<?php

namespace Framework\Tests\ORM\FreshBooks\Repository;

use Common\ORM\Entity\Client;
use Common\ORM\FreshBooks\Persister\ClientPersister;
use Freshbooks\FreshBooksApi;

class ClientPersisterTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->api = $this->getMockBuilder('Freshbooks\FreshBooksApi')
            ->disableOriginalConstructor()
            ->getMock();

        $this->api->method('setMethod')->willReturn(true);
        $this->api->method('post')->willReturn(true);

        $this->persister = new ClientPersister($this->api, 'FreshBooks');

        $this->existingClient = new Client([
            'client_id'  => 1,
            'first_name' => 'John',
            'last_name'  => 'Doe'
        ]);

        $this->unexistingClient = new Client([
            'first_name' => 'John',
            'last_name'  => 'Doe'
        ]);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testCreateWithError()
    {
        $this->api->method('success')->willReturn(false);

        $this->api->expects($this->once())->method('setMethod')
            ->with('client.create');

        $this->persister->create($this->existingClient);
    }

    public function testCreateWithoutErrors()
    {
        $response = [
            '@attributes' => [ 'status' => 'ok' ],
            'client_id'      => '123',
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
            $this->unexistingClient->client_id
        );
    }

    /**
     * @expectedException Common\ORM\Core\Exception\EntityNotFoundException
     */
    public function testRemoveWithError()
    {
        $this->api->method('success')->willReturn(false);

        $this->api->expects($this->once())->method('setMethod')
            ->with('client.delete');

        $this->persister->remove($this->unexistingClient);
    }

    public function testRemoveWithoutErrors()
    {
        $response = [ '@attributes' => [ 'status' => 'ok' ] ];

        $this->api->method('success')->willReturn(true);
        $this->api->method('getResponse')->willReturn($response);

        $this->api->expects($this->once())->method('setMethod')
            ->with('client.delete');

        $this->api->expects($this->once())->method('post');
        $this->api->expects($this->once())->method('success');

        $r = $this->persister->remove($this->existingClient);
        $this->assertEquals($this->persister, $r);
    }

    /**
     * @expectedException Common\ORM\Core\Exception\EntityNotFoundException
     */
    public function testUpdateWithError()
    {
        $this->api->method('success')->willReturn(false);

        $this->api->expects($this->once())->method('setMethod')
            ->with('client.update');

        $this->persister->update($this->unexistingClient);
    }

    public function testUpdateWithoutErrors()
    {
        $response = [ '@attributes' => [ 'status' => 'ok' ] ];

        $this->api->method('success')->willReturn(true);
        $this->api->method('getResponse')->willReturn($response);

        $this->api->expects($this->once())->method('setMethod')
            ->with('client.update');

        $this->api->expects($this->once())->method('post');
        $this->api->expects($this->once())->method('success');

        $r = $this->persister->update($this->existingClient);
        $this->assertEquals($this->persister, $r);
    }
}
