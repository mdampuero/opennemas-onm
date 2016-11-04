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
        $this->em = $this->getMockBuilder('Common\ORM\Core\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->mm = new MigrationManager($this->em);
    }

    /**
     * Tests getPersister.
     */
    public function testGetPersister()
    {
        $saver = $this->mm->getPersister('content');

        $this->assertInstanceOf('Common\Migration\Component\Persister\ContentPersister', $saver);
    }

    /**
     * Tests getPersister when the given entity is invalid.
     *
     * @expectedException Common\Migration\Component\Exception\InvalidPersisterException
     */
    public function testGetPersisterWhenNoPersister()
    {
        $this->mm->getPersister('garply');
    }
}
