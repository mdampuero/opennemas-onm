<?php

namespace Framework\Tests\ORM\Braintree\Repository;

use Common\ORM\Entity\Client;
use Common\ORM\Braintree\Repository\ClientRepository;

class ClientRepositoryTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->clients = [
            new Client([
                'client_id'  => 1,
                'first_name' => 'John',
                'last_name'  => 'Doe'
            ]),
            new Client([
                'client_id'  => 2,
                'first_name' => 'Jane',
                'last_name'  => 'Doe'
            ])
        ];
    }

    /**
     * @expectedException \Common\ORM\Core\Exception\EntityNotFoundException
     */
    public function testFindWithError()
    {
        $bc = \Mockery::mock('Braintree_Customer_' . uniqid());
        $bc->shouldReceive('id')->once()->andThrow('\Braintree_Exception');

        $factory = $this
            ->getMockBuilder('CometCult\BraintreeBundle\Factory\BraintreeFactory')
            ->disableOriginalConstructor()
            ->getMock();

        $factory->method('get')->with('customer')->willReturn($bc);
        $factory->expects($this->once())->method('get')->with('customer');

        $repository = new ClientRepository($factory, 'Braintree');
        $repository->find('1');
    }

    public function testFindWithoutError()
    {
        $response = $this->getMock('\Braintree_Customer_' . uniqid());
        $response->id = '1';
        $response->firstName = 'John';
        $response->lastName = 'Doe';
        $response->email = 'johndoe@example.org';
        $response->company = 'John Doe, Inc.';
        $response->phone = '555-555-555';
        $response->addresses = [];

        $bc = \Mockery::mock('Braintree_Customer_' . uniqid());
        $bc->shouldReceive('find')->once()->andReturn($response);

        $factory = $this
            ->getMockBuilder('CometCult\BraintreeBundle\Factory\BraintreeFactory')
            ->disableOriginalConstructor()
            ->getMock();

        $factory->method('get')->with('customer')->willReturn($bc);
        $factory->expects($this->once())->method('get')->with('customer');

        $repository = new ClientRepository($factory, 'Braintree');
        $client = $repository->find('1');
        $this->assertEquals($response->id, $client->client_id);
        $this->assertEquals($response->firstName, $client->first_name);
        $this->assertEquals($response->lastName, $client->last_name);
    }

    /**
     * @expectedException \Common\ORM\Core\Exception\InvalidCriteriaException
     */
    public function testFindByWithError()
    {
        $bc = \Mockery::mock('Braintree_Customer_' . uniqid());
        $bc->shouldReceive('search')->once()->andThrow('\Braintree_Exception');

        $factory = $this
            ->getMockBuilder('CometCult\BraintreeBundle\Factory\BraintreeFactory')
            ->disableOriginalConstructor()
            ->getMock();

        $factory->method('get')->with('customer')->willReturn($bc);
        $factory->expects($this->once())->method('get')->with('customer');

        $repository = new ClientRepository($factory, 'Braintree');
        $repository->findBy();
    }

    public function testFindByWithoutError()
    {
        $fbresponse = $this->getMock('\Braintree_ResourceCollection_' . uniqid());
        $fbresponse->_ids = [ '1' ];

        $fresponse = $this->getMock('\Braintree_Customer_' . uniqid());
        $fresponse->id = '1';
        $fresponse->firstName = 'John';
        $fresponse->lastName = 'Doe';
        $fresponse->email = 'johndoe@example.org';
        $fresponse->company = 'John Doe, Inc.';
        $fresponse->phone = '555-555-555';
        $fresponse->addresses = [];

        $bc = \Mockery::mock('Braintree_Customer_' . uniqid());
        $bc->shouldReceive('search')->once()->andReturn($fbresponse);
        $bc->shouldReceive('find')->once()->andReturn($fresponse);

        $factory = $this
            ->getMockBuilder('CometCult\BraintreeBundle\Factory\BraintreeFactory')
            ->disableOriginalConstructor()
            ->getMock();

        $factory->method('get')->with('customer')->willReturn($bc);
        $factory->expects($this->any())->method('get')->with('customer');

        $repository = new ClientRepository($factory, 'Braintree');
        $clients = array_values($repository->findBy());

        $this->assertEquals(1, count($clients));
        $this->assertEquals($fresponse->id, $clients[0]->client_id);
    }

    public function testCriteriaToArrayWithEmptyCriteria()
    {
        $factory = $this
            ->getMockBuilder('CometCult\BraintreeBundle\Factory\BraintreeFactory')
            ->disableOriginalConstructor()
            ->getMock();

        $repository = new ClientRepository($factory, 'Braintree');
        $reflection = new \ReflectionClass(get_class($repository));
        $method = $reflection->getMethod('arrayToCriteria');
        $method->setAccessible(true);

        $criteria = $method->invokeArgs($repository, [ [] ]);
        $this->assertEmpty($criteria);
    }

    public function testCriteriaToArrayWithValidCriteria()
    {
        $factory = $this
            ->getMockBuilder('CometCult\BraintreeBundle\Factory\BraintreeFactory')
            ->disableOriginalConstructor()
            ->getMock();

        $repository = new ClientRepository($factory, 'Braintree');
        $reflection = new \ReflectionClass(get_class($repository));
        $method = $reflection->getMethod('arrayToCriteria');
        $method->setAccessible(true);

        $source = [ 'id' => 1 ];
        $criteria = $method->invokeArgs($repository, [ $source ]);
        $this->assertEquals(count($source), count($criteria));
    }

    public function testResponseToDataWithEmptyResponse()
    {
        $factory = $this
            ->getMockBuilder('CometCult\BraintreeBundle\Factory\BraintreeFactory')
            ->disableOriginalConstructor()
            ->getMock();

        $repository = new ClientRepository($factory, 'Braintree');
        $reflection = new \ReflectionClass(get_class($repository));
        $method = $reflection->getMethod('responseToData');
        $method->setAccessible(true);

        $data = $method->invokeArgs($repository, [ null ]);
        $this->assertEmpty($data);
    }

    public function testResponseToDataWithValidResponse()
    {
        $factory = $this
            ->getMockBuilder('CometCult\BraintreeBundle\Factory\BraintreeFactory')
            ->disableOriginalConstructor()
            ->getMock();

        $repository = new ClientRepository($factory, 'Braintree');
        $reflection = new \ReflectionClass(get_class($repository));
        $method = $reflection->getMethod('responseToData');
        $method->setAccessible(true);

        $address = $this->getMock('\Braintree_Address_' . uniqid());
        $address->streetAddress = 'Fake Street, 123';
        $address->extendedAddress = '';
        $address->locality = 'New York City';
        $address->region = 'New York';
        $address->countryName = 'United States';
        $address->postalCode = '00000';

        $response = $this->getMock('\Braintree_Customer_' . uniqid());
        $response->id = '1';
        $response->firstName = 'John';
        $response->lastName = 'Doe';
        $response->email = 'johndoe@example.org';
        $response->company = 'John Doe, Inc.';
        $response->phone = '555-555-555';
        $response->addresses = [ $address ];

        $data = $method->invokeArgs($repository, [ $response ]);

        $this->assertEquals($response->id, $data['client_id']);
        $this->assertEquals($response->firstName, $data['first_name']);
        $this->assertEquals($response->lastName, $data['last_name']);
        $this->assertEquals($response->email, $data['email']);
        $this->assertEquals($response->company, $data['organization']);
        $this->assertEquals($response->addresses[0]->streetAddress, $data['p_street1']);
        $this->assertEquals($response->addresses[0]->extendedAddress, $data['p_street2']);
        $this->assertEquals($response->addresses[0]->locality, $data['p_city']);
        $this->assertEquals($response->addresses[0]->region, $data['p_state']);
        $this->assertEquals($response->addresses[0]->countryName, $data['p_country']);
        $this->assertEquals($response->addresses[0]->postalCode, $data['p_code']);
    }
}
