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
        $this->migration = [
            'type'    => 'bar',
            'tracker' => 'simple_id',
            'source'  => [ 'repository' => 'database', 'database' => 'wobble' ],
            'target'  => [
                'persister' => 'content' ,
                'database'  => 'flob',
                'filter'    => [
                    'wubble' => [
                        'type'   => [ 'literal' ],
                        'params' => [ 'literal' => [ 'value' => 'bar' ] ]
                    ],
                    'foobar' => [ 'type' => [ 'html' ] ]
                ]
            ]
        ];

        $this->em = $this->getMockBuilder('Common\ORM\Core\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->mm = new MigrationManager($this->em, [ 'connection' => [] ]);
    }

    /**
     * Tests configure.
     */
    public function testConfigure()
    {
        $property  = new \ReflectionProperty($this->mm, 'migration');
        $property->setAccessible(true);

        $this->mm->configure($this->migration);

        $this->assertEquals($this->migration, $property->getValue($this->mm));
    }

    /**
     * Tests filter.
     */
    public function testFilter()
    {
        $fm = $this->getMockBuilder('Common\Core\Component\Filter\FilterManager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->mm->configure($this->migration);

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
        $this->mm->configure($this->migration);
        $persister = $this->mm->getPersister();

        $this->assertInstanceOf(
            'Common\Migration\Component\Persister\ContentPersister',
            $persister
        );
    }

    /**
     * Tests getPersister when the given entity is invalid.
     *
     * @expectedException Common\Migration\Component\Exception\InvalidPersisterException
     */
    public function testGetPersisterWhenNoPersister()
    {
        $this->migration['target']['persister'] = 'garply';
        $this->mm->configure($this->migration);
        $this->mm->getPersister();
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

        $this->mm->configure($this->migration);
        $persister = $this->mm->getRepository();

        $this->assertInstanceOf(
            'Common\Migration\Component\Repository\DatabaseRepository',
            $persister
        );
    }

    /**
     * Tests getRepository when the given entity is invalid.
     *
     * @expectedException Common\Migration\Component\Exception\InvalidRepositoryException
     */
    public function testGetRepositoryWhenNoRepository()
    {
        $this->migration['source']['repository'] = 'garply';
        $this->mm->configure($this->migration);
        $this->mm->getRepository();
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
     * Tests getTracker.
     */
    public function testGetTracker()
    {
        $this->mm->configure($this->migration);

        $this->assertInstanceOf(
            'Common\Migration\Component\Tracker\SimpleIdTracker',
            $this->mm->getTracker()
        );
    }

    /**
     * Tests getRepository when the given entity is invalid.
     *
     * @expectedException Common\Migration\Component\Exception\InvalidTrackerException
     */
    public function testGetRepositoryWhenNoTracker()
    {
        $this->migration['tracker'] = 'garply';
        $this->mm->configure($this->migration);
        $this->mm->getTracker();
    }

    /**
     * Tests getTracker when a Tracker was previously created.
     */
    public function testGetTrackerWhenCreated()
    {
        $tracker = $this->getMockBuilder('Common\Migration\Component\Tracker\SimpleIdTracker')
            ->disableOriginalConstructor()
            ->getMock();

        $property  = new \ReflectionProperty($this->mm, 'tracker');
        $property->setAccessible(true);
        $property->setValue($this->mm, $tracker);

        $this->assertEquals($tracker, $this->mm->getTracker());
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
