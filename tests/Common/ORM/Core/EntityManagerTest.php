<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\ORM\Core;

use Common\ORM\Braintree\BraintreeManager;
use Common\ORM\Database\DatabaseManager;
use Common\ORM\Entity\Client;
use Common\ORM\Core\Connection;
use Common\ORM\Core\Entity;
use Common\ORM\Core\EntityManager;
use Common\ORM\Core\Metadata;
use Common\ORM\Entity\Payment;
use Common\ORM\FreshBooks\FreshBooksManager;
use Common\ORM\Core\Exception\InvalidPersisterException;

/**
 * Defines test cases for EntityManager class.
 */
class EntityManagerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the test environment.
     */
    public function setUp()
    {
        $this->container = $this->getMockBuilder('ServiceContainer')
            ->disableOriginalConstructor()
            ->setMethods([ 'get', 'getParameter' ])
            ->getMock();

        $this->defaults = [
            'connection' => [
                'driver'   => 'mysqli',
                'dbname'   => 'onm-instances',
                'host'     => '172.17.0.6',
                'port'     => '3306',
                'user'     => 'root',
                'password' => 'root',
                'charset'  => 'UTF-8',
            ]
        ];

        $this->config = [
            'connection' => [
                'foo' => [ 'name' => 'foo', 'dbname' => 'glorp' ]
            ],
            'metadata'   => [
                'Entity' => [
                    'name'       => 'Entity',
                    'properties' => [ 'foo' => 'string', 'bar' => 'integer' ],
                    'converters' => [
                        'default' => [ 'class' => 'Converter', 'arguments'  => [] ],
                    ],
                    'datasets' => [
                        'default' => [ 'class' => 'DataSet', 'arguments'  => [] ],
                    ],
                    'persisters' => [
                        'Entity' => [ 'class' => 'Persister', 'arguments'  => [] ],
                    ],
                    'repositories' => [
                        'Entity' => [ 'class' => 'Repository', 'arguments'  => [] ]
                    ],
                    'mapping'    => [ ]
                ],
                'Client' => [
                    'name'       => 'Client',
                    'properties' => [],
                    'mapping'    => []
                ]
            ],
            'schema' => [
                'flob' => [ 'name' => 'flob', 'entities' => [ 'Entity' ] ],
            ]
        ];

        $this->converter = $this->getMockBuilder('MockConverter')
            ->setMockClassName('Converter')
            ->disableOriginalConstructor()
            ->setMethods([ '__construct' ])
            ->getMock();

        $this->dataset = $this->getMockBuilder('Dataset')
            ->disableOriginalConstructor()
            ->setMethods([ '__construct' ])
            ->getMock();

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

    /**
     * Returns mocks basing on arguments when calling get method of
     * ServiceContainer mock.
     */
    public function serviceContainerCallback()
    {
        $args = func_get_args();

        switch ($args[0]) {
            case '@foo':
                return 'foo';
            case 'foo':
                return 'foo';
            case 'orm':
                return $this->config;
            case 'orm.default':
                return $this->defaults;
            case 'orm.manager.database':
                return $this->dm;
            default:
                throw new \Exception();
        }
    }

    /**
     * Tests getConnection for an invalid connection name.
     *
     * @expectedException \Common\ORM\Core\Exception\InvalidConnectionException
     */
    public function testGetConnectionInvalid()
    {
        $this->em->getConnection('Foobar');
    }

    /**
     * Tests getConnection for a valid connection name.
     */
    public function testGetConnectionValid()
    {
        $this->assertEquals(
            new Connection([
                'charset'  => 'UTF-8',
                'dbname'   => 'glorp',
                'driver'   => 'mysqli',
                'host'     => '172.17.0.6',
                'name'     => 'foo',
                'password' => 'root',
                'port'     => '3306',
                'user'     => 'root'
            ]),
            $this->em->getConnection('foo')
        );
    }

    /**
     * Tests getContainer.
     */
    public function testGetContainer()
    {
        $this->assertEquals($this->container, $this->em->getContainer());
    }

    /**
     * Tests getConverter when the requested converter is not defined.
     *
     * @expectedException \Common\ORM\Core\Exception\InvalidConverterException
     */
    public function testGetConverterInvalidName()
    {
        $this->em->getConverter('Entity', 'foo');
    }

    /**
     * Tests getConverter when no converters defined.
     *
     * @expectedException \Common\ORM\Core\Exception\InvalidConverterException
     */
    public function testGetConverterNoConverters()
    {
        $this->em->getConverter('Client');
    }

    /**
     * Tests getConverter for a valid entity and converter.
     */
    public function testGetConverterValid()
    {
        $this->assertNotEmpty($this->em->getConverter('Entity'));
        $this->assertNotEmpty($this->em->getConverter('Entity', 'default'));
    }

    /**
     * Tests getDataSet when the requested dataset is not defined.
     *
     * @expectedException \Common\ORM\Core\Exception\InvalidDataSetException
     */
    public function testGetDataSetInvalidName()
    {
        $this->em->getDataSet('Entity', 'foo');

        $this->addToAssertionCount(1);
    }

    /**
     * Tests getDataSet when no datasets defined.
     *
     * @expectedException \Common\ORM\Core\Exception\InvalidDataSetException
     */
    public function testGetDataSetNoDataSets()
    {
        $this->em->getDataSet('Client');
    }

    /**
     * Tests getDataSet for a valid entity and dataset.
     */
    public function testGetDataSetValid()
    {
        $this->assertNotEmpty($this->em->getDataSet('Entity'));
        $this->assertNotEmpty($this->em->getDataSet('Entity', 'default'));
    }

    /**
     * Tests getDumper.
     */
    public function testGetDumper()
    {
        $this->assertNotEmpty($this->em->getDumper());
    }

    /**
     * Tests getMetadata for an undefined Entity.
     *
     * @expectedException \Common\ORM\Core\Exception\InvalidMetadataException
     */
    public function testGetMetadataInvalid()
    {
        $this->em->getMetadata('Foo');
    }

    /**
     * Tests getMetadata for Entities and entity names.
     */
    public function testGetMetadataValid()
    {
        $m1 = $this->em->getMetadata(new Entity());
        $m2 = $this->em->getMetadata('Entity');

        $this->assertNotEmpty($m1);
        $this->assertNotEmpty($m2);
        $this->assertEquals($m1, $m2);
    }

    /**
     * Tests getPersister when the requested persister is not defined.
     *
     * @expectedException \Common\ORM\Core\Exception\InvalidPersisterException
     */
    public function testGetPersisterInvalidName()
    {
        $entity = new Entity();

        $this->em->getPersister($entity, 'foo');
    }

    /**
     * Tests getPersister when no persisters defined.
     *
     * @expectedException \Common\ORM\Core\Exception\InvalidPersisterException
     */
    public function testGetPersisterNoPersisters()
    {
        $this->em->getPersister(new Client());
    }

    /**
     * Tests getPersister for a valid entity and persister.
     */
    public function testGetPersisterValid()
    {
        $entity = new Entity();

        $this->assertNotEmpty($this->em->getPersister($entity));
        $this->assertNotEmpty($this->em->getPersister($entity, 'Entity'));
        $this->assertEquals(
            $this->em->getPersister($entity),
            $this->em->getPersister($entity, 'Entity')
        );
    }

    /**
     * Tests getRepository when the requested repository is not defined.
     *
     * @expectedException \Common\ORM\Core\Exception\InvalidRepositoryException
     */
    public function testGetRepositoryInvalidName()
    {
        $this->em->getRepository('Entity', 'foo');
    }

    /**
     * Tests getRepository when no repositories defined.
     *
     * @expectedException \Common\ORM\Core\Exception\InvalidRepositoryException
     */
    public function testGetRepositoryNoRepositories()
    {
        $this->em->getRepository('Client');
    }

    /**
     * Tests getRepository for a valid entity and repository
     */
    public function testGetRepositoryValid()
    {
        $this->assertNotEmpty($this->em->getRepository('entity'));
        $this->assertNotEmpty($this->em->getRepository('entity', 'Entity'));
    }

    /**
     * Tests persist with an Entity from database.
     */
    public function testPersistWithExistingEntity()
    {
        $entity     = new Entity([ 'id' => 1 ]);
        $reflection = new \ReflectionClass($entity);
        $property   = $reflection->getProperty('stored');

        $property->setAccessible(true);
        $property->setValue($entity, [ true ]);

        $this->em->persist($entity);

        $this->addToAssertionCount(1);
    }

    /**
     * Tests persist with a new Entity.
     */
    public function testPersistWithUnexistingEntity()
    {
        $entity = new Entity([ 'id' => 1 ]);

        $this->em->persist($entity);

        $this->addToAssertionCount(1);
    }

    /**
     * Tests remove with an Entity from database.
     */
    public function testRemoveWithExistingEntity()
    {
        $entity = new Entity([ 'id' => 1 ]);

        $this->em->remove($entity);

        $this->addToAssertionCount(1);
    }

    /**
     * Tests parseArgs.
     */
    public function testParseArgs()
    {
        $reflection = new \ReflectionClass($this->em);
        $method     = $reflection->getMethod('parseArgs');

        $method->setAccessible(true);

        $this->assertEmpty($method->invokeArgs($this->em, [ [] ]));
        $this->assertNotEmpty($method->invokeArgs($this->em, [ [ 'foo' => 'bar' ] ]));
    }

    /**
     * Tests parseArg with ORM services, main services and parameters.
     */
    public function testParseArg()
    {
        $reflection = new \ReflectionClass($this->em);
        $method     = $reflection->getMethod('parseArg');

        $method->setAccessible(true);

        $this->assertEquals(123, $method->invokeArgs($this->em, [ 123 ]));
        $this->assertEquals($this->container, $method->invokeArgs($this->em, [ '@service_container' ]));
        $this->assertNotEmpty($method->invokeArgs($this->em, [ '@orm.connection.foo' ]));
        $this->assertNotEmpty($method->invokeArgs($this->em, [ '@orm.metadata.entity' ]));
        $this->assertNotEmpty($method->invokeArgs($this->em, [ '@foo' ]));
        $this->assertNotEmpty($method->invokeArgs($this->em, [ '%foo%' ]));
    }
}
