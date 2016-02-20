<?php

namespace Framework\Tests\ORM\FreshBooks\Repository;

use Common\ORM\Entity\Client;
use Common\ORM\FreshBooks\Repository\ClientRepository;
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

        $this->repository = new ClientRepository($this->api, 'FreshBooks');
    }

    public function testContructor()
    {
        $this->assertEquals($this->repository->getApi(), $this->api);
    }

    /**
     * @expectedException Common\ORM\Core\Exception\EntityNotFoundException
     */
    public function testFindWithInvalidId()
    {
        $this->api->method('success')->willReturn(false);

        $this->api->expects($this->once())->method('setMethod')
            ->with('client.get');

        $this->repository->find('1');
    }

    public function testFindWithValidId()
    {
        $client = [
            'client_id'  => '1',
            'first_name' => 'John',
            'last_name'  => 'Doe'
        ];

        $response = [
            '@attributes' => [ 'status' => 'ok' ],
            'client'      => $client,
        ];

        $this->api->method('success')->willReturn(true);
        $this->api->method('getResponse')->willReturn($response);

        $this->api->expects($this->once())->method('setMethod')
            ->with('client.get');

        $this->api->expects($this->once())->method('post');
        $this->api->expects($this->once())->method('success');
        $this->api->expects($this->once())->method('getResponse');

        $this->assertEquals($client, $this->repository->find('1')->getData());
    }

    public function testFindInSecondRepository()
    {
        $client = [
            'client_id'  => '1',
            'first_name' => 'John',
            'last_name'  => 'Doe'
        ];

        $response = [
            '@attributes' => [ 'status' => 'ok' ],
            'client'      => $client,
        ];

        $this->api->method('success')->willReturn(true);
        $this->api->method('getResponse')->willReturn($response);

        $next = $this->getMockBuilder('Common\ORM\Braintree\Repository\ClientRepository')
            ->disableOriginalConstructor()
            ->getMock();

        $this->repository->setNext($next);

        $this->api->expects($this->once())->method('setMethod')->with('client.get');
        $this->api->expects($this->once())->method('post');
        $this->api->expects($this->once())->method('success');
        $this->api->expects($this->once())->method('getResponse');

        $next->expects($this->once())->method('find')
            ->with('1', new Client($client));

        $this->repository->find('1');
    }

    public function testFindWithClient()
    {
        $client = [
            'client_id'  => '1',
            'first_name' => 'John',
            'last_name'  => 'Doe'
        ];

        $previous = [ 'foo' => 'bar' ];

        $response = [
            '@attributes' => [ 'status' => 'ok' ],
            'client'      => $client,
        ];

        $this->api->method('success')->willReturn(true);
        $this->api->method('getResponse')->willReturn($response);

        $this->api->expects($this->once())->method('setMethod')->with('client.get');
        $this->api->expects($this->once())->method('post');
        $this->api->expects($this->once())->method('success');
        $this->api->expects($this->once())->method('getResponse');

        $response = $this->repository->find('1', new Client($previous));

        $this->assertEquals(
            array_merge($client, $previous),
            $response->getData()
        );
    }

    /**
     * @expectedException Common\ORM\Core\Exception\InvalidCriteriaException
     */
    public function testFindByWithInvalidCriteria()
    {
        $criteria = [ 'invalid_field' => 'johndoe@example.org' ];

        $this->api->method('success')->willReturn(false);

        $this->api->expects($this->once())->method('setMethod')
            ->with('client.list');

        $this->api->expects($this->once())->method('post');
        $this->api->expects($this->once())->method('success');

        $this->repository->findBy($criteria);
    }

    public function testFindByWithValidCriteriaMultipleResults()
    {
        $criteria = [ 'email' => 'johndoe@example.org' ];

        $clients = [
            [
                'client_id'  => '1',
                'first_name' => 'John',
                'last_name'  => 'Doe',
                'email'      => 'johndoe@example.org'
            ],
            [
                'client_id'  => '2',
                'first_name' => 'Jane',
                'last_name'  => 'Doe',
                'email'      => 'janedoe@example.org'
            ]

        ];

        $response = [
            '@attributes' => [ 'status' => 'ok' ],
            'clients'     => [
                '@attributes' => [ 'page' => 1, 'total' => 2 ],
                'client'      => $clients
            ]
        ];

        $this->api->method('success')->willReturn(true);
        $this->api->method('getResponse')->willReturn($response);

        $this->api->expects($this->once())->method('setMethod')
            ->with('client.list');

        $this->api->expects($this->once())->method('post');
        $this->api->expects($this->once())->method('success');
        $this->api->expects($this->once())->method('getResponse');

        $response = $this->repository->findBy($criteria);

        $this->assertEquals(count($clients), count($response));

        $response = array_values($response);
        for ($i = 0; $i < count($response); $i++) {
            $this->assertEquals($clients[$i], $response[$i]->getData());
        }
    }

    public function testFindByWithValidMultipleResultsInSecondRepository()
    {
        $criteria = [ 'email' => 'johndoe@example.org' ];

        $clients = [
            [
                'client_id'  => '1',
                'first_name' => 'John',
                'last_name'  => 'Doe',
                'email'      => 'johndoe@example.org'
            ],
            [
                'client_id'  => '2',
                'first_name' => 'Jane',
                'last_name'  => 'Doe',
                'email'      => 'janedoe@example.org'
            ]
        ];

        $response = [
            '@attributes' => [ 'status' => 'ok' ],
            'clients'     => [
                '@attributes' => [ 'page' => 1, 'total' => 2 ],
                'client'      => $clients
            ]
        ];

        $this->api->method('success')->willReturn(true);
        $this->api->method('getResponse')->willReturn($response);

        $next = $this->getMockBuilder('Common\ORM\Braintree\Repository\ClientRepository')
            ->disableOriginalConstructor()
            ->getMock();

        $this->repository->setNext($next);

        $this->api->expects($this->once())->method('setMethod')->with('client.list');

        $this->api->expects($this->once())->method('post');
        $this->api->expects($this->once())->method('success');
        $this->api->expects($this->once())->method('getResponse');

        $next->expects($this->once())->method('findBy')
            ->with(
                $criteria,
                [ '1' => new Client($clients[0]), '2' => new Client($clients[1]) ]
            );

        $this->repository->findBy($criteria);
    }

    public function testFindByWithValidCriteriaMultipleResultsWithClients()
    {
        $criteria = [ 'email' => 'johndoe@example.org' ];

        $clients = [
            [
                'client_id'  => '1',
                'first_name' => 'John',
                'last_name'  => 'Doe',
                'email'      => 'johndoe@example.org'
            ],
            [
                'client_id'  => '2',
                'first_name' => 'Jane',
                'last_name'  => 'Doe',
                'email'      => 'janedoe@example.org'
            ]
        ];

        $response = [
            '@attributes' => [ 'status' => 'ok' ],
            'clients'     => [
                '@attributes' => [ 'page' => 1, 'total' => 2 ],
                'client'      => $clients
            ]
        ];

        $this->api->method('success')->willReturn(true);
        $this->api->method('getResponse')->willReturn($response);

        $this->api->expects($this->once())->method('setMethod')
            ->with('client.list');

        $this->api->expects($this->once())->method('post');
        $this->api->expects($this->once())->method('success');
        $this->api->expects($this->once())->method('getResponse');

        $previous = [];
        foreach ($clients as $values) {
            $previous[$values['client_id']] = new Client($values);
        }

        $response = $this->repository->findBy($criteria, $previous);

        $this->assertEquals(count($clients), count($response));

        $response = array_values($response);
        for ($i = 0; $i < count($response); $i++) {
            $this->assertEquals($clients[$i], $response[$i]->getData());
        }
    }

    public function testFindByWithValidCriteriaOneResult()
    {
        $criteria = [ 'email' => 'johndoe@example.org' ];

        $clients = [
            'client_id'  => '1',
            'first_name' => 'John',
            'last_name'  => 'Doe',
            'email'      => 'johndoe@example.org'
        ];

        $response = [
            '@attributes' => [ 'status' => 'ok' ],
            'clients'     => [
                '@attributes' => [ 'page' => 1, 'total' => 1 ],
                'client'      => $clients
            ]
        ];

        $this->api->method('success')->willReturn(true);
        $this->api->method('getResponse')->willReturn($response);

        $this->api->expects($this->once())->method('setMethod')
            ->with('client.list');

        $this->api->expects($this->once())->method('post');
        $this->api->expects($this->once())->method('success');
        $this->api->expects($this->once())->method('getResponse');

        $response = $this->repository->findBy($criteria);

        $this->assertEquals(1, count($response));
        $response = array_pop($response);
        $this->assertEquals($clients, $response->getData());
    }

    public function testFindByWithValidCriteriaOneResultInSecondRepository()
    {
        $criteria = [ 'email' => 'johndoe@example.org' ];

        $clients = [
            'client_id'  => '1',
            'first_name' => 'John',
            'last_name'  => 'Doe',
            'email'      => 'johndoe@example.org'
        ];

        $response = [
            '@attributes' => [ 'status' => 'ok' ],
            'clients'     => [
                '@attributes' => [ 'page' => 1, 'total' => 1 ],
                'client'      => $clients
            ]
        ];

        $this->api->method('success')->willReturn(true);
        $this->api->method('getResponse')->willReturn($response);

        $next = $this->getMockBuilder('Common\ORM\Braintree\Repository\ClientRepository')
            ->disableOriginalConstructor()
            ->getMock();

        $this->repository->setNext($next);

        $this->api->expects($this->once())->method('setMethod')
            ->with('client.list');

        $this->api->expects($this->once())->method('post');
        $this->api->expects($this->once())->method('success');
        $this->api->expects($this->once())->method('getResponse');

        $next->expects($this->once())->method('findBy')
            ->with($criteria, [ '1' => new Client($clients) ]);

        $this->repository->findBy($criteria);
    }

    public function testFindByWithValidCriteriaOneResultWithClients()
    {
        $criteria = [ 'email' => 'johndoe@example.org' ];

        $clients = [
            'client_id'  => '1',
            'first_name' => 'John',
            'last_name'  => 'Doe',
            'email'      => 'johndoe@example.org'
        ];

        $response = [
            '@attributes' => [ 'status' => 'ok' ],
            'clients'     => [
                '@attributes' => [ 'page' => 1, 'total' => 1 ],
                'client'      => $clients
            ]
        ];

        $this->api->method('success')->willReturn(true);
        $this->api->method('getResponse')->willReturn($response);

        $this->api->expects($this->once())->method('setMethod')
            ->with('client.list');

        $this->api->expects($this->once())->method('post');
        $this->api->expects($this->once())->method('success');
        $this->api->expects($this->once())->method('getResponse');

        $response = $this->repository->findBy(
            $criteria,
            [ '1' => new Client($clients) ]
        );

        $this->assertEquals(1, count($response));
        $response = array_pop($response);
        $this->assertEquals($clients, $response->getData());
    }
}
