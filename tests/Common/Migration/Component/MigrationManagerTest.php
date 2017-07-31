<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\Migration\Component;

use Common\Migration\Component\MigrationManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Defines test cases for MigrationManager class.
 */
class MigrationManagerTest extends KernelTestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->config = [
            'type'    => 'bar',
            'tracker' => [
                'type'   => 'simple_id',
                'fields' => [ 'id' ]
            ],
            'source'  => [
                'persister'  => 'database',
                'repository' => 'database',
                'database'   => 'flob'
            ],
            'filter'  => [
                'wubble' => [
                    'type'   => [ 'literal' ],
                    'params' => [ 'literal' => [ 'value' => 'bar' ] ]
                ],
                'foobar' => [ 'type' => [ 'html' ] ]
            ]
        ];

        $this->container = $this->getMockBuilder('Container')->getMock();

        $this->em = $this->getMockBuilder('Common\ORM\Core\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->mm = new MigrationManager($this->container, $this->em, [ 'connection' => [] ]);
    }

    /**
     * Tests configure.
     */
    public function testConfigure()
    {
        $property  = new \ReflectionProperty($this->mm, 'config');
        $property->setAccessible(true);

        $this->mm->configure($this->config);

        $this->assertEquals($this->config, $property->getValue($this->mm));
    }

    /**
     * Tests filter.
     */
    public function testFilter()
    {
        $fm = $this->getMockBuilder('Common\Data\Core\FilterManager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->mm->configure($this->config);

        $property  = new \ReflectionProperty($this->mm, 'fm');
        $property->setAccessible(true);

        $property->setValue($this->mm, $fm);

        $fm->expects($this->at(0))->method('filter')
            ->with('literal', null, [ 'value' => 'bar' ]);
        $fm->expects($this->at(1))->method('filter')
            ->with('html', 'flob', []);

        $this->mm->filter([ 'foobar' => 'flob' ]);
    }

    /**
     * Tests getPersister.
     */
    public function testGetPersister()
    {
        $this->mm->configure($this->config);
        $persister = $this->mm->getPersister();

        $this->assertInstanceOf(
            'Common\Migration\Component\Persister\DatabasePersister',
            $persister
        );
    }

    /**
     * Tests getPersister when a Persister was previously created.
     */
    public function testGetPersisterWhenCreated()
    {
        $tracker = $this->getMockBuilder('Common\Migration\Component\Persister\ContentPersister')
            ->disableOriginalConstructor()
            ->getMock();

        $property  = new \ReflectionProperty($this->mm, 'persister');
        $property->setAccessible(true);
        $property->setValue($this->mm, $tracker);

        $this->assertEquals($tracker, $this->mm->getPersister());
    }

    /**
     * Tests getPersister when the persister configuration is invalid.
     *
     * @expectedException Common\Migration\Component\Exception\InvalidPersisterException
     */
    public function testGetPersisterWhenInvalidConfiguration()
    {
        $this->config['source']['persister'] = 'garply';
        $this->mm->configure($this->config);
        $this->mm->getPersister();
    }

    /**
     * Tests getPersister when no persister configured.
     *
     * @expectedException Common\Migration\Component\Exception\InvalidPersisterException
     */
    public function testGetPersisterWhenNoConfiguration()
    {
        unset($this->config['source']['persister']);
        $this->mm->configure($this->config);
        $this->mm->getPersister();
    }

    /**
     * Tests getRepository.
     */
    public function testGetRepository()
    {
        $tracker = $this->getMockBuilder('Common\Migration\Component\Tracker\SimpleIdTracker')
            ->disableOriginalConstructor()
            ->getMock();

        $property  = new \ReflectionProperty($this->mm, 'tracker');
        $property->setAccessible(true);
        $property->setValue($this->mm, $tracker);

        $this->mm->configure($this->config);
        $persister = $this->mm->getRepository();

        $this->assertInstanceOf(
            'Common\Migration\Component\Repository\DatabaseRepository',
            $persister
        );
    }

    /**
     * Tests getRepository when a Repository was previously created.
     */
    public function testGetRepositoryWhenCreated()
    {
        $repository = $this->getMockBuilder('Common\Migration\Component\Repository\DatabaseRepository')
            ->disableOriginalConstructor()
            ->getMock();

        $property  = new \ReflectionProperty($this->mm, 'repository');
        $property->setAccessible(true);
        $property->setValue($this->mm, $repository);

        $this->assertEquals($repository, $this->mm->getRepository());
    }

    /**
     * Tests getRepository when the repository configuration is invalid.
     *
     * @expectedException Common\Migration\Component\Exception\InvalidRepositoryException
     */
    public function testGetRepositoryWhenInvalidConfiguration()
    {
        $this->config['source']['repository'] = 'garply';
        $this->mm->configure($this->config);
        $this->mm->getRepository();
    }

    /**
     * Tests getRepository when no repository configured.
     *
     * @expectedException Common\Migration\Component\Exception\InvalidRepositoryException
     */
    public function testGetRepositoryWhenNoConfiguration()
    {
        unset($this->config['source']['repository']);
        $this->mm->configure($this->config);
        $this->mm->getRepository();
    }

    /**
     * Tests getTracker.
     */
    public function testGetTracker()
    {
        $this->mm->configure($this->config);

        $this->assertInstanceOf(
            'Common\Migration\Component\Tracker\Tracker',
            $this->mm->getTracker()
        );
    }

    /**
     * Tests getTracker when a Tracker is already created.
     */
    public function testGetTrackerWhenCreated()
    {
        $tracker = $this->getMockBuilder('Common\Migration\Component\Tracker\Tracker')
            ->disableOriginalConstructor()
            ->getMock();

        $property  = new \ReflectionProperty($this->mm, 'tracker');
        $property->setAccessible(true);
        $property->setValue($this->mm, $tracker);

        $this->assertEquals($tracker, $this->mm->getTracker());
    }

    /**
     * Tests getRepository when the tracker configuration is invalid.
     *
     * @expectedException Common\Migration\Component\Exception\InvalidTrackerException
     */
    public function testGetRepositoryWhenNoTracker()
    {
        $this->config['tracker'] = 'garply';
        $this->mm->configure($this->config);
        $this->mm->getTracker();
    }

    /**
     * Tests persist.
     */
    public function testPersist()
    {
        $item      = [ 'wibble' => 'foobar' ];
        $persister = $this->getMockBuilder('Common\Migration\Component\Persister\Persister')
            ->disableOriginalConstructor()
            ->setMethods([ 'persist' ])
            ->getMock();

        $property  = new \ReflectionProperty($this->mm, 'persister');
        $property->setAccessible(true);
        $property->setValue($this->mm, $persister);

        $persister->expects($this->once())->method('persist')->with($item);

        $this->mm->persist($item);
    }
}
