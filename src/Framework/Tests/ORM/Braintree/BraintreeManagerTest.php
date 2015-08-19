<?php

namespace Framework\Tests\ORM\Braintree;

use CometCult\BraintreeBundle\Factory\BraintreeFactory;
use Framework\ORM\Braintree\BraintreeManager;
use Framework\ORM\Entity\Client;
use Framework\ORM\Entity\Payment;

class BraintreeManagerTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $factory = $this
            ->getMockBuilder('CometCult\BraintreeBundle\Factory\BraintreeFactory')
            ->disableOriginalConstructor()
            ->getMock();

        $this->manager = new BraintreeManager($factory);
    }

    public function testContructor()
    {
        $this->assertInstanceOf(
            'CometCult\BraintreeBundle\Factory\BraintreeFactory',
            $this->manager->getFactory()
        );
    }

    /**
     * @expectedException Framework\ORM\Exception\InvalidRepositoryException
     */
    public function tesGetRepositoryInvalid()
    {
        $this->manager->getRepository('invalid_repository');
    }

    public function testGetRepositoryValid()
    {
        $cp = $this->manager->getRepository('client');
        $this->assertInstanceOf('Framework\ORM\Braintree\Repository\BraintreeRepository', $cp);
    }

    /**
     * @expectedException Framework\ORM\Exception\InvalidPersisterException
     */
    public function testGetPersisterInvalid()
    {
        $entity = new Payment();
        $cp = $this->manager->getPersister($entity);
        $this->assertInstanceOf('Framework\ORM\Braintree\Persister\BraintreePersister', $cp);
    }

    public function testGetPersisterValid()
    {
        $entity = new Client();
        $cp = $this->manager->getPersister($entity);
        $this->assertInstanceOf('Framework\ORM\Braintree\Persister\BraintreePersister', $cp);
    }
}
