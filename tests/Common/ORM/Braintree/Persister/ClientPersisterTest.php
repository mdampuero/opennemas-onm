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
use Common\ORM\Braintree\Persister\ClientPersister;

/**
 * Defines test cases for ClientPersister class.
 */
class ClientPersisterTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the test environment.
     */
    public function setUp()
    {
        $this->metadata = new Metadata([
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
     * Tests create when API call returns a success.
     */
    public function testCreate()
    {
        $response = $this->getMockBuilder('\Braintree_Response')
            ->disableOriginalConstructor()
            ->getMock();

        $response->success  = true;
        $response->customer = $this->getMockBuilder('\Braintree_Customer')
            ->disableOriginalConstructor()
            ->getMock();
        $response->customer->id = '1';

        $bc = \Mockery::mock('Braintree_Customer_' . uniqid());
        $bc->shouldReceive('create')->once()->andReturn($response);

        $factory = $this
            ->getMockBuilder('CometCult\BraintreeBundle\Factory\BraintreeFactory')
            ->disableOriginalConstructor()
            ->getMock();

        $factory->expects($this->once())->method('get')->with('customer')->willReturn($bc);

        $persister = new ClientPersister($factory, $this->metadata);
        $persister->create($this->unexistingClient);

        $this->assertEquals(
            $response->customer->id,
            $this->unexistingClient->id
        );
    }

    /**
     * Tests create when API returns a false.
     *
     * @expectedException \RuntimeException
     */
    public function testCreateWithRuntimeError()
    {
        $response = $this->getMockBuilder('\Braintree_Response')
            ->disableOriginalConstructor()
            ->getMock();

        $response->success = false;
        $response->message = 'Unable to create';

        $bc = \Mockery::mock('Braintree_Customer_' . uniqid());
        $bc->shouldReceive('create')->once()->andReturn($response);

        $factory = $this
            ->getMockBuilder('CometCult\BraintreeBundle\Factory\BraintreeFactory')
            ->disableOriginalConstructor()
            ->getMock();

        $factory->expects($this->once())->method('get')->with('customer')->willReturn($bc);

        $persister = new ClientPersister($factory, $this->metadata);
        $persister->create($this->existingClient);
    }

    /**
     * Tests create when API call fails.
     *
     * @expectedException Braintree_Exception
     */
    public function testCreateWhenAPIFails()
    {
        $bc = \Mockery::mock('Braintree_Customer_' . uniqid());
        $bc->shouldReceive('create')->once()->andThrow('Braintree_Exception');

        $factory = $this
            ->getMockBuilder('CometCult\BraintreeBundle\Factory\BraintreeFactory')
            ->disableOriginalConstructor()
            ->getMock();

        $factory->expects($this->once())->method('get')->with('customer')->willReturn($bc);

        $persister = new ClientPersister($factory, $this->metadata);
        $persister->create($this->existingClient);
    }

    /**
     * Tests remove when API call returns a success.
     */
    public function testRemove()
    {
        $response = $this->getMockBuilder('\Braintree_Response')
            ->disableOriginalConstructor()
            ->getMock();

        $response->success = true;

        $bc = \Mockery::mock('Braintree_Customer_' . uniqid());
        $bc->shouldReceive('delete')->once()->andReturn($response);

        $factory = $this
            ->getMockBuilder('CometCult\BraintreeBundle\Factory\BraintreeFactory')
            ->disableOriginalConstructor()
            ->getMock();

        $factory->method('get')->with('customer')->willReturn($bc);
        $factory->expects($this->once())->method('get')->with('customer');

        $persister = new ClientPersister($factory, $this->metadata);
        $persister->remove($this->unexistingClient);
    }

    /**
     * Tests remove when API returns a false.
     *
     * @expectedException \RuntimeException
     */
    public function testRemoveWhenEntityNotFound()
    {
        $response = $this->getMockBuilder('\Braintree_Response')
            ->disableOriginalConstructor()
            ->getMock();

        $response->success = false;
        $response->message = 'UnableToRemove';

        $bc = \Mockery::mock('Braintree_Customer_' . uniqid());
        $bc->shouldReceive('delete')->once()->andReturn($response);

        $factory = $this
            ->getMockBuilder('CometCult\BraintreeBundle\Factory\BraintreeFactory')
            ->disableOriginalConstructor()
            ->getMock();

        $factory->expects($this->once())->method('get')->with('customer')->willReturn($bc);

        $persister = new ClientPersister($factory, $this->metadata);
        $persister->remove($this->unexistingClient);
    }

    /**
     * Tests remove when API call fails.
     *
     * @expectedException Braintree_Exception
     */
    public function testRemoveWhenAPIFails()
    {
        $bc = \Mockery::mock('Braintree_Customer_' . uniqid());
        $bc->shouldReceive('delete')->once()->andThrow('Braintree_Exception');

        $factory = $this
            ->getMockBuilder('CometCult\BraintreeBundle\Factory\BraintreeFactory')
            ->disableOriginalConstructor()
            ->getMock();

        $factory->expects($this->once())->method('get')->with('customer')->willReturn($bc);

        $persister = new ClientPersister($factory, $this->metadata);
        $persister->remove($this->existingClient);
    }

    /**
     * Tests update when API returns a success.
     */
    public function testUpdate()
    {
        $response = $this->getMockBuilder('\Braintree_Response')
            ->disableOriginalConstructor()
            ->getMock();

        $response->success = true;

        $bc = \Mockery::mock('Braintree_Customer_' . uniqid());
        $bc->shouldReceive('update')->once()->andReturn($response);

        $factory = $this
            ->getMockBuilder('CometCult\BraintreeBundle\Factory\BraintreeFactory')
            ->disableOriginalConstructor()
            ->getMock();

        $factory->method('get')->with('customer')->willReturn($bc);
        $factory->expects($this->once())->method('get')->with('customer');

        $persister = new ClientPersister($factory, $this->metadata);
        $persister->update($this->existingClient);
    }

    /**
     * Tests update when API returns a false.
     *
     * @expectedException \RuntimeException
     */
    public function testUpdateWithErrors()
    {
        $response = $this->getMockBuilder('\Braintree_Response')
            ->disableOriginalConstructor()
            ->getMock();

        $response->success = false;
        $response->message = 'Unable to update';

        $bc = \Mockery::mock('Braintree_Customer_' . uniqid());
        $bc->shouldReceive('update')->once()->andReturn($response);

        $factory = $this
            ->getMockBuilder('CometCult\BraintreeBundle\Factory\BraintreeFactory')
            ->disableOriginalConstructor()
            ->getMock();

        $factory->method('get')->with('customer')->willReturn($bc);

        $factory->expects($this->once())->method('get')
            ->with('customer');

        $persister = new ClientPersister($factory, $this->metadata);
        $persister->update($this->existingClient);
    }

    /**
     * Tests update when API call fails.
     *
     * @expectedException Braintree_Exception
     */
    public function testUpdateWhenAPIFails()
    {
        $bc = \Mockery::mock('Braintree_Customer_' . uniqid());
        $bc->shouldReceive('update')->once()->andThrow('Braintree_Exception');

        $factory = $this
            ->getMockBuilder('CometCult\BraintreeBundle\Factory\BraintreeFactory')
            ->disableOriginalConstructor()
            ->getMock();

        $factory->method('get')->with('customer')->willReturn($bc);

        $factory->expects($this->once())->method('get')->with('customer');

        $persister = new ClientPersister($factory, $this->metadata);
        $persister->update($this->existingClient);
    }
}
