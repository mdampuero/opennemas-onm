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
use Common\ORM\Entity\Payment;
use Common\ORM\Braintree\Persister\PaymentPersister;

/**
 * Defines test cases for PaymentPersister class.
 */
class PaymentPersisterTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the test environment.
     */
    public function setUp()
    {
        $this->metadata = new Metadata([
            'properties' => [
                'amount'     => 'float',
                'client_id'  => 'integer',
                'date'       => 'datetime',
                'invoice_id' => 'integer',
                'nonce'      => 'string',
                'notes'      => 'string',
                'type'       => 'string',
            ],
            'mapping' => [
                'braintree' => [
                    'client_id' => [ 'name' => 'customerId', 'type' => 'string' ],
                    'amount'    => [ 'name' => 'amount', 'type' => 'string' ],
                    'nonce'     => [ 'name' => 'paymentMethodNonce', 'type' => 'string' ],
                ]
            ],
        ]);

        $this->existingPayment = new Payment([
            'id'     => 1,
            'amount' => 123.12,
            'nonce'  => 'fooflobglork'
        ]);

        $this->unexistingPayment = new Payment([
            'amount' => 123.12,
            'nonce'  => 'fooflobglork'
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

        $response->success = true;

        $response->transaction = $this->getMockBuilder('\Braintree_Transaction')
            ->disableOriginalConstructor()
            ->getMock();

        $response->transaction->id = '1';

        $bc = \Mockery::mock('Braintree_Transaction_' . uniqid());
        $bc->shouldReceive('sale')->once()->andReturn($response);

        $factory = $this
            ->getMockBuilder('CometCult\BraintreeBundle\Factory\BraintreeFactory')
            ->disableOriginalConstructor()
            ->getMock();

        $factory->expects($this->once())->method('get')->with('transaction')->willReturn($bc);

        $persister = new PaymentPersister($factory, $this->metadata);
        $persister->create($this->unexistingPayment);

        $this->assertEquals(
            $response->transaction->id,
            $this->unexistingPayment->id
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
        $response->message = 'Unable to save';

        $bc = \Mockery::mock('Braintree_Transaction_' . uniqid());
        $bc->shouldReceive('sale')->once()->andReturn($response);

        $factory = $this
            ->getMockBuilder('CometCult\BraintreeBundle\Factory\BraintreeFactory')
            ->disableOriginalConstructor()
            ->getMock();

        $factory->expects($this->once())->method('get')->with('transaction')->willReturn($bc);

        $persister = new PaymentPersister($factory, $this->metadata);
        $persister->create($this->existingPayment);
    }

    /**
     * Tests create when API call fails.
     *
     * @expectedException Braintree_Exception
     */
    public function testCreateWhenAPIFails()
    {
        $bc = \Mockery::mock('Braintree_Transaction_' . uniqid());
        $bc->shouldReceive('sale')->once()->andThrow('Braintree_Exception');

        $factory = $this
            ->getMockBuilder('CometCult\BraintreeBundle\Factory\BraintreeFactory')
            ->disableOriginalConstructor()
            ->getMock();

        $factory->expects($this->once())->method('get')->with('transaction')->willReturn($bc);

        $persister = new PaymentPersister($factory, $this->metadata);
        $persister->create($this->existingPayment);
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

        $bc = \Mockery::mock('Braintree_Transaction_' . uniqid());
        $bc->shouldReceive('void')->once()->with(1)->andReturn($response);

        $factory = $this
            ->getMockBuilder('CometCult\BraintreeBundle\Factory\BraintreeFactory')
            ->disableOriginalConstructor()
            ->getMock();

        $factory->method('get')->with('transaction')->willReturn($bc);
        $factory->expects($this->once())->method('get')->with('transaction');

        $persister = new PaymentPersister($factory, $this->metadata);
        $persister->remove($this->existingPayment);
    }

    /**
     * Tests remove when API returns a false.
     *
     * @expectedException \RuntimeException
     */
    public function testRemoveWithErrors()
    {
        $response = $this->getMockBuilder('\Braintree_Response')
            ->disableOriginalConstructor()
            ->getMock();

        $response->success = false;
        $response->message = 'Unable to remove';

        $bc = \Mockery::mock('Braintree_Payment' . uniqid());
        $bc->shouldReceive('void')->once()->andReturn($response);

        $factory = $this
            ->getMockBuilder('CometCult\BraintreeBundle\Factory\BraintreeFactory')
            ->disableOriginalConstructor()
            ->getMock();

        $factory->expects($this->once())->method('get')->with('transaction')->willReturn($bc);

        $persister = new PaymentPersister($factory, $this->metadata);
        $persister->remove($this->unexistingPayment);
    }

    /**
     * Tests remove when API call fails.
     *
     * @expectedException Braintree_Exception
     */
    public function testRemoveWhenAPIFails()
    {
        $bc = \Mockery::mock('Braintree_Transaction_' . uniqid());
        $bc->shouldReceive('void')->once()->andThrow('Braintree_Exception');

        $factory = $this
            ->getMockBuilder('CometCult\BraintreeBundle\Factory\BraintreeFactory')
            ->disableOriginalConstructor()
            ->getMock();

        $factory->expects($this->once())->method('get')->with('transaction')->willReturn($bc);

        $persister = new PaymentPersister($factory, $this->metadata);
        $persister->remove($this->existingPayment);
    }
}
