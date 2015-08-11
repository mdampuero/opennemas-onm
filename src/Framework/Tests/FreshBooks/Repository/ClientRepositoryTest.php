<?php

namespace Framework\Tests\FreshBooks;

use Framework\FreshBooks\Repository\ClientRepository;
use Freshbooks\FreshBooksApi;

class ClientRepositoryTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->api = $this->getMockBuilder('Freshbooks\FreshBooksApi')
            ->disableOriginalConstructor()
            ->getMock();

        $this->api->method('setMethod')->willReturn(true);
        $this->api->method('post')->willReturn(true);

        $this->repository = new ClientRepository($this->api);
    }

    /**
     * @expectedException Framework\FreshBooks\Exception\ClientNotFoundException
     */
    public function testFindClientWithInvalidId()
    {
        // Configure stub for unexisting client
        $this->api->method('success')->willReturn(false);

        $this->api->expects($this->once())->method('setMethod')
            ->with('client.get');

        $this->repository->findClient('1');
    }

    public function testFindClientWithValidId()
    {
        $client = [
            'client_id'  => '1',
            'first_name' => 'John',
            'last_name'  => 'Doe'
        ];

        $response = [
            '@attributes' => [ 'status' => 'ok' ],
            'client' => $client,
        ];

        $this->api->method('success')->willReturn(true);
        $this->api->method('getResponse')->willReturn($response);

        $this->api->expects($this->once())->method('setMethod')
            ->with('client.get');

        $this->api->expects($this->once())->method('post');
        $this->api->expects($this->once())->method('success');
        $this->api->expects($this->once())->method('getResponse');

        $this->assertEquals($client, $this->repository->findClient('1'));
    }

    /**
     * @expectedException Framework\FreshBooks\Exception\InvalidCriteriaException
     */
    public function testFindClientsWithInvalidCriteria()
    {
        $criteria = [ 'invalid_field' => 'johndoe@example.org' ];

        $this->api->method('success')->willReturn(false);

        $this->api->expects($this->once())->method('setMethod')
            ->with('client.list');

        $this->api->expects($this->once())->method('post');
        $this->api->expects($this->once())->method('success');

        $this->repository->findClients($criteria);
    }

    public function testFindClientsWithValidCriteria()
    {
        $criteria = [ 'email' => 'johndoe@example.org' ];

        $clients = [
            [
                'client_id'  => '1',
                'first_name' => 'John',
                'last_name'  => 'Doe',
                'email'      => 'johndoe@example.org'
            ]
        ];

        $response = [
            '@attributes' => [ 'status' => 'ok' ],
            'clients' => $clients
        ];

        $this->api->method('success')->willReturn(true);
        $this->api->method('getResponse')->willReturn($response);


        $this->api->expects($this->once())->method('setMethod')
            ->with('client.list');

        $this->api->expects($this->once())->method('post');
        $this->api->expects($this->once())->method('success');
        $this->api->expects($this->once())->method('getResponse');

        $this->assertEquals($clients, $this->repository->findClients($criteria));
    }

}
