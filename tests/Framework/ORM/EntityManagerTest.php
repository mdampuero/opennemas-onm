<?php

namespace Framework\Tests\ORM;

use Framework\ORM\Braintree\BraintreeManager;
use Framework\ORM\Core\ChainElement;
use Framework\ORM\Database\DatabaseManager;
use Framework\ORM\Entity\Client;
use Framework\ORM\Core\Entity;
use Framework\ORM\Core\Validation;
use Framework\ORM\Entity\Payment;
use Framework\ORM\EntityManager;
use Framework\ORM\FreshBooks\FreshBooksManager;
use Framework\ORM\Exception\InvalidPersisterException;

class EntityManagerTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->container = $this->getMockBuilder('ServiceContainer')
            ->disableOriginalConstructor()
            ->setMethods([ 'get', 'getParameter' ])
            ->getMock();

        $this->loader = $this->getMockBuilder('Loader')
            ->disableOriginalConstructor()
            ->setMethods([ 'load' ])
            ->getMock();

        $this->persister = $this->getMockBuilder('Persister')
            ->disableOriginalConstructor()
            ->setMethods([ 'create', 'remove', 'update' ])
            ->getMock();

        $this->repository = $this->getMockBuilder('Repository')
            ->disableOriginalConstructor()
            ->getMock();

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));

        $this->container->expects($this->any())->method('getParameter')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));


        $this->loader->expects($this->any())->method('load')
            ->willReturn([ 'validation' => [] ]);

        $this->em = new EntityManager($this->container);

        $this->em->config['connection']['foo'] = true;
        $this->em->config['validation']['Entity'] = new Validation([
            'mapping' => [
                'persisters' => [
                    'Entity' => [ 'class' => 'Persister', 'arguments'  => [] ],
                ],
                'repositories' => [
                    'Entity' => [ 'class' => 'Repository', 'arguments'  => [] ]
                ]
            ]
        ]);
    }

    public function serviceContainerCallback()
    {
        $args = func_get_args();

        switch ($args[0]) {
            case '@foo':
                return 'foo';
            case 'foo':
                return 'foo';
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
        $entity = new Payment();

        $this->em->getPersister($entity);
    }

    public function testGetPersisterValid()
    {
        $entity = new Entity();

        $this->assertNotEmpty($this->em->getPersister($entity));
        $this->assertEquals(1, count($this->em->getPersister($entity, 'Entity')));
    }

    /**
     * @expectedException \Framework\ORM\Exception\InvalidRepositoryException
     */
    public function testGetRepositoryInvalid()
    {
        $this->em->getRepository('payment');
    }

    public function testGetRepositoryValid()
    {
        $this->assertNotEmpty($this->em->getRepository('entity'));

        $persisters = $this->em->getRepository('entity', 'Entity');
        $this->assertEquals(1, count($persisters));
    }

    public function testPersistWithExistingEntity()
    {
        $entity     = new Entity([ 'id' => 1 ]);
        $reflection = new \ReflectionClass($entity);
        $property   = $reflection->getProperty('in_db');

        $property->setAccessible(true);
        $property->setValue($entity, [ true ]);

        $em = $this->getMockBuilder('\Framework\ORM\EntityManager')
            ->setConstructorArgs([ $this->container ])
            ->setMethods([ 'getPersister' ])
            ->getMock();

        $em->method('getPersister')->willReturn($this->persister);
        $this->persister->expects($this->once())->method('update');

        $em->persist($entity);
    }

    public function testPersistWithUnexistingEntity()
    {
        $entity = new Entity([ 'id' => 1 ]);

        $em = $this->getMockBuilder('\Framework\ORM\EntityManager')
            ->setConstructorArgs([ $this->container ])
            ->setMethods([ 'getPersister' ])
            ->getMock();

        $em->method('getPersister')->willReturn($this->persister);
        $this->persister->expects($this->once())->method('create');

        $em->persist($entity);
    }

    public function testRemoveWithExistingEntity()
    {
        $entity = new Entity([ 'id' => 1 ]);

        $em = $this->getMockBuilder('\Framework\ORM\EntityManager')
            ->setConstructorArgs([ $this->container ])
            ->setMethods([ 'getPersister' ])
            ->getMock();

        $em->method('getPersister')->willReturn($this->persister);
        $this->persister->expects($this->once())->method('remove');

        $em->remove($entity);
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

    public function testParseArgs()
    {
        $reflection = new \ReflectionClass($this->em);
        $method = $reflection->getMethod('parseArgs');
        $method->setAccessible(true);

        $this->assertEmpty($method->invokeArgs($this->em, [ [] ]));
        $this->assertNotEmpty($method->invokeArgs($this->em, [ [ 'foo' => 'bar' ] ]));
    }

    public function testParseArg()
    {
        $reflection = new \ReflectionClass($this->em);
        $method = $reflection->getMethod('parseArg');
        $method->setAccessible(true);

        $this->assertEquals(123, $method->invokeArgs($this->em, [ 123 ]));
        $this->assertEquals($this->container, $method->invokeArgs($this->em, [ '@service_container' ]));
        $this->assertNotEmpty($method->invokeArgs($this->em, [ '@orm.connection.foo' ]));
        $this->assertNotEmpty($method->invokeArgs($this->em, [ '@foo' ]));
        $this->assertNotEmpty($method->invokeArgs($this->em, [ '%foo%' ]));
    }
}
