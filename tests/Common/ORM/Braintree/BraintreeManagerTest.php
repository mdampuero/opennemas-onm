<?php

namespace Framework\Tests\ORM\Braintree;

use CometCult\BraintreeBundle\Factory\BraintreeFactory;
use Common\ORM\Braintree\BraintreeManager;
use Common\ORM\Entity\Client;
use Common\ORM\Entity\Payment;

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
     * @expectedException Common\ORM\Core\Exception\InvalidRepositoryException
     */
    public function tesGetRepositoryInvalid()
    {
        $this->manager->getRepository('invalid_repository');
    }

    public function testGetRepositoryValid()
    {
        $cp = $this->manager->getRepository('client');
        $this->assertInstanceOf('Common\ORM\Braintree\Repository\BraintreeRepository', $cp);
    }

    /**
     * @expectedException Common\ORM\Core\Exception\InvalidPersisterException
     */
    public function testGetPersisterInvalid()
    {
        $entity = new Payment();
        $cp = $this->manager->getPersister($entity);
        $this->assertInstanceOf('Common\ORM\Braintree\Persister\BraintreePersister', $cp);
    }

    public function testGetPersisterValid()
    {
        $entity = new Client();
        $cp = $this->manager->getPersister($entity);
        $this->assertInstanceOf('Common\ORM\Braintree\Persister\BraintreePersister', $cp);
    }

    public function testPersistWithExistingEntity()
    {
        $p = $this->getMockBuilder('Common\ORM\Braintree\PersisterBraintreePersister')
            ->disableOriginalConstructor()
            ->setMethods([ 'update', 'create' ])
            ->getMock();

        $p->expects($this->once())->method('update')->willReturn(true);

        $bm = $this
            ->getMockBuilder('Common\ORM\Braintree\BraintreeManager')
            ->disableOriginalConstructor()
            ->setMethods([ 'getPersister' ])
            ->getMock();

        $bm->expects($this->once())->method('getPersister')->willReturn($p);

        $bm->persist(new Client([ 'client_id' => 1]));
    }

    public function testPersistWithUnexistingEntity()
    {
        $p = $this->getMockBuilder('Common\ORM\Braintree\PersisterBraintreePersister')
            ->disableOriginalConstructor()
            ->setMethods([ 'update', 'create' ])
            ->getMock();

        $p->expects($this->once())->method('create')->willReturn(true);

        $bm = $this
            ->getMockBuilder('Common\ORM\Braintree\BraintreeManager')
            ->disableOriginalConstructor()
            ->setMethods([ 'getPersister' ])
            ->getMock();

        $bm->expects($this->once())->method('getPersister')->willReturn($p);

        $bm->persist(new Client());
    }

    public function testRemoveWithExistingEntity()
    {
        $p = $this->getMockBuilder('Common\ORM\Braintree\PersisterBraintreePersister')
            ->disableOriginalConstructor()
            ->setMethods([ 'remove' ])
            ->getMock();

        $p->method('remove')->willReturn(true);

        $bm = $this
            ->getMockBuilder('Common\ORM\Braintree\BraintreeManager')
            ->disableOriginalConstructor()
            ->setMethods([ 'getPersister' ])
            ->getMock();

        $bm->expects($this->once())->method('getPersister')->willReturn($p);

        $bm->remove(new Client());
    }

    /**
     * @expectedException \Common\ORM\Core\Exception\EntityNotFoundException
     */
    public function testRemoveWithUnexistingEntity()
    {
        $p = $this->getMockBuilder('Common\ORM\Braintree\PersisterBraintreePersister')
            ->disableOriginalConstructor()
            ->setMethods([ 'remove' ])
            ->getMock();

        $p->method('remove')->will(
            $this->throwException(new \Common\ORM\Core\Exception\EntityNotFoundException('Client', 1, ''))
        );

        $bm = $this
            ->getMockBuilder('Common\ORM\Braintree\BraintreeManager')
            ->disableOriginalConstructor()
            ->setMethods([ 'getPersister' ])
            ->getMock();

        $bm->expects($this->once())->method('getPersister')->willReturn($p);

        $bm->remove(new Client());
    }
}
