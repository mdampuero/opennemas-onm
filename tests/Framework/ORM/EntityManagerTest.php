<?php

namespace Framework\Tests\ORM;

use Framework\ORM\Braintree\BraintreeManager;
use Framework\ORM\Core\ChainElement;
use Framework\ORM\Database\DatabaseManager;
use Framework\ORM\Entity\Client;
use Framework\ORM\Core\Entity;
use Framework\ORM\Entity\Payment;
use Framework\ORM\EntityManager;
use Framework\ORM\FreshBooks\FreshBooksManager;
use Framework\ORM\Exception\InvalidPersisterException;

class EntityManagerTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $container = $this->getMockBuilder('ServiceContainer')
            ->disableOriginalConstructor()
            ->setMethods([ 'get' ])
            ->getMock();

        $this->loader = $this->getMockBuilder('Loader')
            ->disableOriginalConstructor()
            ->setMethods([ 'load' ])
            ->getMock();

        $this->dm = $this->getMockBuilder('DatabaseManager')
            ->disableOriginalConstructor()
            ->setMethods([ 'getPersister', 'getRepository' ])
            ->getMock();

        $this->persister = $this->getMockBuilder('Persister')
            ->disableOriginalConstructor()
            ->setMethods([ 'create', 'remove', 'update' ])
            ->getMock();

        $container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));

        $this->loader->expects($this->once())->method('load')
            ->willReturn([ 'validation' => [] ]);

        $this->em = new EntityManager($container);
    }

    public function serviceContainerCallback()
    {
        $args = func_get_args();

        switch ($args[0]) {
            case 'orm.loader':
                return $this->loader;
            case 'orm.manager.database':
                return $this->dm;
            default:
                throw new \Exception();
        }
    }

    /**
     * @expectedException \Framework\ORM\Exception\InvalidPersisterException
     */
    public function testGetPersisterInvalid()
    {
        $this->dm->method('getPersister')->will($this->throwException(new \Exception()));

        $entity = new Payment();

        $this->em->getPersister($entity);
    }

    public function testGetPersisterValid()
    {
        $this->dm->method('getPersister')->willReturn($this->persister);

        $entity     = new Client();
        $persisters = $this->em->getPersister($entity);

        $this->assertTrue(0 < count($persisters));
    }

    /**
     * @expectedException \Framework\ORM\Exception\InvalidRepositoryException
     */
    public function testGetRepositoryInvalid()
    {
        $this->dm->method('getRepository')->will($this->throwException(new \Exception));
        $this->em->getRepository('payment');
    }

    public function testGetRepositoryValid()
    {
        $this->dm->method('getRepository')->willReturn($this->getMock('Repository'));
        $persisters = $this->em->getRepository('client');

        $this->assertTrue(0 < count($persisters));
    }

    public function testPersistWithExistingEntity()
    {
        $entity     = new Entity([ 'id' => 1 ]);
        $reflection = new \ReflectionClass($entity);
        $property   = $reflection->getProperty('in_db');

        $property->setAccessible(true);
        $property->setValue($entity, [ true ]);

        $this->dm->method('getPersister')->willReturn($this->persister);
        $this->persister->expects($this->once())->method('update');

        $this->em->persist($entity);
    }

    public function testPersistWithUnexistingEntity()
    {
        $entity = new Entity([ 'id' => 1 ]);

        $this->dm->method('getPersister')->willReturn($this->persister);
        $this->persister->expects($this->once())->method('create');

        $this->em->persist($entity);
    }

    public function testRemoveWithExistingEntity()
    {
        $entity = new Entity([ 'id' => 1 ]);

        $this->dm->method('getPersister')->willReturn($this->persister);
        $this->persister->expects($this->once())->method('remove');

        $this->em->remove($entity);
    }

    public function testBuildChainWithoutElements()
    {
        $reflection = new \ReflectionClass(get_class($this->em));
        $method = $reflection->getMethod('buildChain');
        $method->setAccessible(true);

        $this->assertEmpty($method->invokeArgs($this->em, [ [] ]));
    }

    public function testBuildChainWithElements()
    {
        $a = new ChainElement();
        $b = new ChainElement();

        $elements = [ $a, $b ];

        $reflection = new \ReflectionClass(get_class($this->em));
        $method = $reflection->getMethod('buildChain');
        $method->setAccessible(true);

        $chain = $method->invokeArgs($this->em, [ $elements ]);
        $this->assertEquals($a, $chain);
        $this->assertEquals($b, $a->next());
        $this->assertFalse($b->hasNext());
    }
}
