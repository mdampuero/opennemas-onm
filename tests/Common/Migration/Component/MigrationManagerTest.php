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
            'type'   => 'bar',
            'source' => [ 'repository' => 'database', 'database' => 'wobble' ],
            'target' => [ 'persister' => 'content' , 'database' => 'flob']
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
     * Tests getMigrationTracker.
     */
    public function testGetMigrationTracker()
    {
        $this->mm->configure($this->migration);
        $tracker = $this->mm->getMigrationTracker();

        $this->assertInstanceOf(
            'Common\Migration\Component\Tracker\MigrationTracker',
            $tracker
        );
    }

    /**
     * Tests getMigrationTracker when a MigrationTracker was previously created.
     */
    public function testGetMigrationTrackerWhenCreated()
    {
        $tracker = $this->getMockBuilder('Common\Migration\Component\Tracker\MigrationTracker')
            ->disableOriginalConstructor()
            ->getMock();

        $property  = new \ReflectionProperty($this->mm, 'tracker');
        $property->setAccessible(true);
        $property->setValue($this->mm, $tracker);

        $this->assertEquals($tracker, $this->mm->getMigrationTracker());
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
     * Tests getRepository.
     */
    public function testGetRepository()
    {
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
}
