<?php

namespace Tests\Common\ORM\Core;

use Common\ORM\Braintree\BraintreeManager;
use Common\ORM\Database\DatabaseManager;
use Common\ORM\Entity\Client;
use Common\ORM\Core\Entity;
use Common\ORM\Core\EntityManager;
use Common\ORM\Core\Metadata;
use Common\ORM\Entity\Payment;
use Common\ORM\FreshBooks\FreshBooksManager;
use Common\ORM\Core\Exception\InvalidPersisterException;

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

        $config = [
            'connection' => [ 'foo' => true ],
            'metadata'   => [
                'Entity' => new Metadata([
                    'name'       => 'Entity',
                    'properties' => [ 'foo' => 'string', 'bar' => 'integer' ],
                    'mapping'    => [
                        'persisters' => [
                            'Entity' => [ 'class' => 'Persister', 'arguments'  => [] ],
                        ],
                        'repositories' => [
                            'Entity' => [ 'class' => 'Repository', 'arguments'  => [] ]
                        ]
                    ]
                ])
            ],
            'schema' => []
        ];

        $this->loader->expects($this->any())->method('load')->willReturn($config);

        $this->persister = $this->getMockBuilder('MockPersister')
            ->setMockClassName('Persister')
            ->disableOriginalConstructor()
            ->setMethods([ '__construct', 'create', 'remove', 'update' ])
            ->getMock();

        $this->repository = $this->getMockBuilder('MockRepository')
            ->setMockClassName('Repository')
            ->disableOriginalConstructor()
            ->setMethods([ '__construct' ])
            ->getMock();

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));

        $this->container->expects($this->any())->method('getParameter')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));

        $this->em = new EntityManager($this->container);
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
     * @expectedException \Common\ORM\Core\Exception\InvalidConnectionException
     */
    public function testGetConnectionInvalid()
    {
        $this->em->getConnection('Foobar');
    }

    public function testGetConnectionValid()
    {
        $this->assertNotEmpty($this->em->getConnection('foo'));
    }

    /**
     * @expectedException \Common\ORM\Core\Exception\InvalidConverterException
     */
    public function testGetConverterInvalid()
    {
        $this->em->getConverter('Foobar');
    }

    public function testGetConverterValid()
    {
        $this->assertNotEmpty($this->em->getConverter('Entity'));
    }

    public function testGetDumper()
    {
        $this->assertNotEmpty($this->em->getDumper());
    }

    /**
     * @expectedException \Common\ORM\Core\Exception\InvalidPersisterException
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
     * @expectedException \Common\ORM\Core\Exception\InvalidRepositoryException
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

        $this->em->persist($entity);
    }

    public function testPersistWithUnexistingEntity()
    {
        $entity = new Entity([ 'id' => 1 ]);

        $this->em->persist($entity);
    }

    public function testRemoveWithExistingEntity()
    {
        $entity = new Entity([ 'id' => 1 ]);

        $this->em->remove($entity);
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
