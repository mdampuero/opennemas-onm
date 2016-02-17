<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <onm-devs@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Framework\ORM\Braintree\Repository;

use Framework\ORM\Entity\Client;
use Framework\ORM\Braintree\Persister\ClientPersister;

class ClientPersisterTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
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
    public function testCreateWithRuntimeError()
    {
        $response = $this->getMock('\Braintree_Response_' . uniqid());
        $response->success = false;

        $bc = \Mockery::mock('Braintree_Customer_' . uniqid());
        $bc->shouldReceive('create')->once()->andReturn($response);

        $factory = $this
            ->getMockBuilder('CometCult\BraintreeBundle\Factory\BraintreeFactory')
            ->disableOriginalConstructor()
            ->getMock();

        $factory->method('get')->with('customer')->willReturn($bc);

        $factory->expects($this->once())->method('get')
            ->with('customer');

        $persister = new ClientPersister($factory, 'Braintree');
        $persister->create($this->existingClient);
    }

    /**
     * @expectedException Braintree_Exception
     */
    public function testCreateWithBraintreeError()
    {
        $bc = \Mockery::mock('Braintree_Customer_' . uniqid());
        $bc->shouldReceive('create')->once()->andThrow('Braintree_Exception');

        $factory = $this
            ->getMockBuilder('CometCult\BraintreeBundle\Factory\BraintreeFactory')
            ->disableOriginalConstructor()
            ->getMock();

        $factory->method('get')->with('customer')->willReturn($bc);

        $factory->expects($this->once())->method('get')->with('customer');

        $persister = new ClientPersister($factory, 'Braintree');
        $persister->create($this->existingClient);
    }

    public function testCreateWithoutErrors()
    {
        $response = $this->getMock('\Braintree_Response_' . uniqid());
        $response->success = true;
        $response->customer = $this->getMock('\Braintree_Customer_' . uniqid());
        $response->customer->id = '1';

        $bc = \Mockery::mock('Braintree_Customer_' . uniqid());
        $bc->shouldReceive('create')->once()->andReturn($response);

        $factory = $this
            ->getMockBuilder('CometCult\BraintreeBundle\Factory\BraintreeFactory')
            ->disableOriginalConstructor()
            ->getMock();

        $factory->method('get')->with('customer')->willReturn($bc);
        $factory->expects($this->once())->method('get')->with('customer');

        $persister = new ClientPersister($factory, 'Braintree');
        $persister->create($this->unexistingClient);

        $this->assertEquals(
            $response->customer->id,
            $this->unexistingClient->client_id
        );
    }

    /**
     * @expectedException \Framework\ORM\Exception\ClientNotFoundException
     */
    public function testRemoveWithRuntimeError()
    {
        $response = $this->getMock('\Braintree_Response_' . uniqid());
        $response->success = false;

        $bc = \Mockery::mock('Braintree_Customer_' . uniqid());
        $bc->shouldReceive('delete')->once()->andReturn($response);

        $factory = $this
            ->getMockBuilder('CometCult\BraintreeBundle\Factory\BraintreeFactory')
            ->disableOriginalConstructor()
            ->getMock();

        $factory->method('get')->with('customer')->willReturn($bc);

        $factory->expects($this->once())->method('get')
            ->with('customer');

        $persister = new ClientPersister($factory, 'Braintree');
        $persister->remove($this->unexistingClient);
    }

    /**
     * @expectedException Braintree_Exception
     */
    public function testRemoveWithBraintreeError()
    {
        $bc = \Mockery::mock('Braintree_Customer_' . uniqid());
        $bc->shouldReceive('delete')->once()->andThrow('Braintree_Exception');

        $factory = $this
            ->getMockBuilder('CometCult\BraintreeBundle\Factory\BraintreeFactory')
            ->disableOriginalConstructor()
            ->getMock();

        $factory->method('get')->with('customer')->willReturn($bc);

        $factory->expects($this->once())->method('get')->with('customer');

        $persister = new ClientPersister($factory, 'Braintree');
        $persister->remove($this->existingClient);
    }

    public function testRemoveWithoutErrors()
    {
        $response = $this->getMock('\Braintree_Response_' . uniqid());
        $response->success = true;

        $bc = \Mockery::mock('Braintree_Customer_' . uniqid());
        $bc->shouldReceive('delete')->once()->andReturn($response);

        $factory = $this
            ->getMockBuilder('CometCult\BraintreeBundle\Factory\BraintreeFactory')
            ->disableOriginalConstructor()
            ->getMock();

        $factory->method('get')->with('customer')->willReturn($bc);
        $factory->expects($this->once())->method('get')->with('customer');

        $persister = new ClientPersister($factory, 'Braintree');
        $persister->remove($this->unexistingClient);
    }

    /**
     * @expectedException \Framework\ORM\Exception\ClientNotFoundException
     */
    public function testUpdateWithRuntimeError()
    {
        $response = $this->getMock('\Braintree_Response_' . uniqid());
        $response->success = false;

        $bc = \Mockery::mock('Braintree_Customer_' . uniqid());
        $bc->shouldReceive('update')->once()->andReturn($response);

        $factory = $this
            ->getMockBuilder('CometCult\BraintreeBundle\Factory\BraintreeFactory')
            ->disableOriginalConstructor()
            ->getMock();

        $factory->method('get')->with('customer')->willReturn($bc);

        $factory->expects($this->once())->method('get')
            ->with('customer');

        $persister = new ClientPersister($factory, 'Braintree');
        $persister->update($this->existingClient);
    }

    /**
     * @expectedException Braintree_Exception
     */
    public function testUpdateWithBraintreeError()
    {
        $bc = \Mockery::mock('Braintree_Customer_' . uniqid());
        $bc->shouldReceive('update')->once()->andThrow('Braintree_Exception');

        $factory = $this
            ->getMockBuilder('CometCult\BraintreeBundle\Factory\BraintreeFactory')
            ->disableOriginalConstructor()
            ->getMock();

        $factory->method('get')->with('customer')->willReturn($bc);

        $factory->expects($this->once())->method('get')->with('customer');

        $persister = new ClientPersister($factory, 'Braintree');
        $persister->update($this->existingClient);
    }

    public function testUpdateWithoutErrors()
    {
        $response = $this->getMock('\Braintree_Response_' . uniqid());
        $response->success = true;

        $bc = \Mockery::mock('Braintree_Customer_' . uniqid());
        $bc->shouldReceive('update')->once()->andReturn($response);

        $factory = $this
            ->getMockBuilder('CometCult\BraintreeBundle\Factory\BraintreeFactory')
            ->disableOriginalConstructor()
            ->getMock();

        $factory->method('get')->with('customer')->willReturn($bc);
        $factory->expects($this->once())->method('get')->with('customer');

        $persister = new ClientPersister($factory, 'Braintree');
        $persister->update($this->existingClient);
    }

    public function testClean()
    {
        $factory = $this
            ->getMockBuilder('CometCult\BraintreeBundle\Factory\BraintreeFactory')
            ->disableOriginalConstructor()
            ->getMock();

        $persister  = new ClientPersister($factory, 'Braintree');
        $reflection = new \ReflectionClass(get_class($persister));

        $method = $reflection->getMethod('clean');
        $method->setAccessible(true);

        $client = new Client([
            'client_id'    => '1',
            'first_name'   => 'John',
            'last_name'    => 'Doe',
            'email'        => 'johndoe@example.org',
            'organization' => 'Foobar, Inc.',
            'phone'        => '123456789',
            'extra'        => 'foo'
        ]);

        $this->assertEquals(
            [
                'id'        => '1',
                'firstName' => 'John',
                'lastName'  => 'Doe',
                'email'     => 'johndoe@example.org',
                'company'   => 'Foobar, Inc.',
                'phone'     => '123456789'
            ],
            $method->invokeArgs($persister, [ $client ])
        );
    }
}
