<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\ORM\Braintree\Repository;

use Common\ORM\Core\Metadata;
use Common\ORM\Entity\Client;
use Common\ORM\Braintree\Repository\ClientRepository;

/**
 * Defines test cases for ClientRepository class.
 */
class ClientRepositoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the test environment.
     */
    public function setUp()
    {
        $this->metadata = new Metadata([
            'name'       => 'Client',
            'properties' => [
                'id'         => 'integer',
                'first_name' => 'string',
                'last_name'  => 'string',
                'email'      => 'string',
                'company'    => 'string',
                'phone'      => 'string',
            ],
            'mapping' => [
                'braintree' => [
                    'id'         => [ 'name' => 'id', 'type' => 'string' ],
                    'first_name' => [ 'name' => 'firstName', 'type' => 'string' ],
                    'last_name'  => [ 'name' => 'lastName', 'type' => 'string' ],
                    'email'      => [ 'name' => 'email', 'type' => 'string' ],
                    'company'    => [ 'name' => 'company', 'type' => 'string' ],
                    'phone'      => [ 'name' => 'phone', 'type' => 'string' ],
                ]
            ],
        ]);

        $this->clients = [
            new Client([
                'id'         => 1,
                'first_name' => 'John',
                'last_name'  => 'Doe'
            ]),
            new Client([
                'id'         => 2,
                'first_name' => 'Jane',
                'last_name'  => 'Doe'
            ])
        ];
    }

    /**
     * Tests countBy.
     *
     * @expectedException \Exception
     */
    public function testCountBy()
    {
        $factory = $this
            ->getMockBuilder('CometCult\BraintreeBundle\Factory\BraintreeFactory')
            ->disableOriginalConstructor()
            ->getMock();

        $repository = new ClientRepository('foo', $factory, $this->metadata);
        $repository->countBy();
    }

    /**
     * Tests find when API call fails.
     *
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

        $repository = new ClientRepository('foo', $factory, $this->metadata);
        $repository->find('1');
    }

    /**
     * Tests find when API returns a valid result.
     */
    public function testFindWithoutError()
    {
        $response = $this->getMockBuilder('\Braintree_Customer')
            ->disableOriginalConstructor()
            ->getMock();

        $response->id        = '1';
        $response->firstName = 'John';
        $response->lastName  = 'Doe';
        $response->email     = 'johndoe@example.org';
        $response->company   = 'John Doe, Inc.';
        $response->phone     = '555-555-555';
        $response->addresses = [];

        $bc = \Mockery::mock('Braintree_Customer_' . uniqid());
        $bc->shouldReceive('find')->once()->andReturn($response);

        $factory = $this
            ->getMockBuilder('CometCult\BraintreeBundle\Factory\BraintreeFactory')
            ->disableOriginalConstructor()
            ->getMock();

        $factory->method('get')->with('customer')->willReturn($bc);
        $factory->expects($this->once())->method('get')->with('customer');

        $repository = new ClientRepository('foo', $factory, $this->metadata);
        $client     = $repository->find('1');

        $this->assertEquals($response->id, $client->id);
        $this->assertEquals($response->firstName, $client->first_name);
        $this->assertEquals($response->lastName, $client->last_name);
    }

    /**
     * Tests findBy when the search criteria is invalid.
     *
     * @expectedException Common\ORM\Core\Exception\InvalidCriteriaException
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

        $repository = new ClientRepository('foo', $factory, $this->metadata);
        $repository->findBy();
    }

    /*
     * Tests findBy when API returns valid results.
     */
    public function testFindByWithoutError()
    {
        $fbresponse = $this->getMockBuilder('\Braintree_ResourceCollection')
            ->disableOriginalConstructor()
            ->getMock();

        $fbresponse->_ids = [ '1' ];

        $fresponse = $this->getMockBuilder('\Braintree_Customer')
            ->disableOriginalConstructor()
            ->getMock();

        $fresponse->id        = '1';
        $fresponse->firstName = 'John';
        $fresponse->lastName  = 'Doe';
        $fresponse->email     = 'johndoe@example.org';
        $fresponse->company   = 'John Doe, Inc.';
        $fresponse->phone     = '555-555-555';
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

        $repository = new ClientRepository('foo', $factory, $this->metadata);
        $clients    = array_values($repository->findBy());

        $this->assertEquals(1, count($clients));
        $this->assertEquals($fresponse->id, $clients[0]->id);
    }

    /**
     * Tests findOneBy.
     *
     * @expectedException \Exception
     */
    public function testFindOneBy()
    {
        $factory = $this
            ->getMockBuilder('CometCult\BraintreeBundle\Factory\BraintreeFactory')
            ->disableOriginalConstructor()
            ->getMock();

        $repository = new ClientRepository('foo', $factory, $this->metadata);
        $repository->findOneBy();
    }

    /**
     * Tests criteriaToArray with an empty criteria.
     */
    public function testCriteriaToArrayWithEmptyCriteria()
    {
        $factory = $this
            ->getMockBuilder('CometCult\BraintreeBundle\Factory\BraintreeFactory')
            ->disableOriginalConstructor()
            ->getMock();

        $repository = new ClientRepository('foo', $factory, $this->metadata);
        $reflection = new \ReflectionClass(get_class($repository));

        $method = $reflection->getMethod('arrayToCriteria');
        $method->setAccessible(true);

        $criteria = $method->invokeArgs($repository, [ [] ]);
        $this->assertEmpty($criteria);
    }

    /**
     * Tests criteriaToArray with a valid criteria.
     */
    public function testCriteriaToArrayWithValidCriteria()
    {
        $factory = $this
            ->getMockBuilder('CometCult\BraintreeBundle\Factory\BraintreeFactory')
            ->disableOriginalConstructor()
            ->getMock();

        $repository = new ClientRepository('foo', $factory, $this->metadata);
        $reflection = new \ReflectionClass(get_class($repository));

        $method = $reflection->getMethod('arrayToCriteria');
        $method->setAccessible(true);

        $source   = [ 'id' => 1 ];
        $criteria = $method->invokeArgs($repository, [ $source ]);

        $this->assertEquals(count($source), count($criteria));
    }
}
