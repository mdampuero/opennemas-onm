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
     * @expectedException Framework\ORM\Core\Exception\InvalidRepositoryException
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
     * @expectedException Framework\ORM\Core\Exception\InvalidPersisterException
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

    public function testPersistWithExistingEntity()
    {
        $p = $this->getMockBuilder('Framework\ORM\Braintree\PersisterBraintreePersister')
            ->disableOriginalConstructor()
            ->setMethods([ 'update', 'create' ])
            ->getMock();

        $p->expects($this->once())->method('update')->willReturn(true);

        $bm = $this
            ->getMockBuilder('Framework\ORM\Braintree\BraintreeManager')
            ->disableOriginalConstructor()
            ->setMethods([ 'getPersister' ])
            ->getMock();

        $bm->expects($this->once())->method('getPersister')->willReturn($p);

        $bm->persist(new Client([ 'client_id' => 1]));
    }

    public function testPersistWithUnexistingEntity()
    {
        $p = $this->getMockBuilder('Framework\ORM\Braintree\PersisterBraintreePersister')
            ->disableOriginalConstructor()
            ->setMethods([ 'update', 'create' ])
            ->getMock();

        $p->expects($this->once())->method('create')->willReturn(true);

        $bm = $this
            ->getMockBuilder('Framework\ORM\Braintree\BraintreeManager')
            ->disableOriginalConstructor()
            ->setMethods([ 'getPersister' ])
            ->getMock();

        $bm->expects($this->once())->method('getPersister')->willReturn($p);

        $bm->persist(new Client());
    }

    public function testRemoveWithExistingEntity()
    {
        $p = $this->getMockBuilder('Framework\ORM\Braintree\PersisterBraintreePersister')
            ->disableOriginalConstructor()
            ->setMethods([ 'remove' ])
            ->getMock();

        $p->method('remove')->willReturn(true);

        $bm = $this
            ->getMockBuilder('Framework\ORM\Braintree\BraintreeManager')
            ->disableOriginalConstructor()
            ->setMethods([ 'getPersister' ])
            ->getMock();

        $bm->expects($this->once())->method('getPersister')->willReturn($p);

        $bm->remove(new Client());
    }

    /**
     * @expectedException \Framework\ORM\Core\Exception\EntityNotFoundException
     */
    public function testRemoveWithUnexistingEntity()
    {
        $p = $this->getMockBuilder('Framework\ORM\Braintree\PersisterBraintreePersister')
            ->disableOriginalConstructor()
            ->setMethods([ 'remove' ])
            ->getMock();

        $p->method('remove')->will(
            $this->throwException(new \Framework\ORM\Core\Exception\EntityNotFoundException('Client', 1, ''))
        );

        $bm = $this
            ->getMockBuilder('Framework\ORM\Braintree\BraintreeManager')
            ->disableOriginalConstructor()
            ->setMethods([ 'getPersister' ])
            ->getMock();

        $bm->expects($this->once())->method('getPersister')->willReturn($p);

        $bm->remove(new Client());
    }
}
