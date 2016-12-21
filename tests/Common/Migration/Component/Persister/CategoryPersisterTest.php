<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\Migration\Component\Persister;

use Common\ORM\Entity\Category;
use Common\Migration\Component\Persister\CategoryPersister;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Defines test cases for CategoryPersister class.
 */
class CategoryPersisterTest extends KernelTestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->converter = $this->getMockBuilder('Common\ORM\Database\Data\Converter\BaseConverter')
            ->disableOriginalConstructor()
            ->setMethods([ 'objectify' ])
            ->getMock();

        $this->repository = $this->getMockBuilder('Common\ORM\Database\Repository\BaseRepository')
            ->disableOriginalConstructor()
            ->setMethods([ 'findOneBy' ])
            ->getMock();

        $this->em = $this->getMockBuilder('Common\ORM\Core\EntityManager')
            ->disableOriginalConstructor()
            ->setMethods([ 'getConverter', 'getRepository', 'persist' ])
            ->getMock();

        $this->em->expects($this->any())->method('getConverter')->willReturn($this->converter);
        $this->em->expects($this->any())->method('getRepository')->willReturn($this->repository);

        $this->persister = new CategoryPersister($this->em);
    }

    /**
     * Tests persist and find when category was not previously migrated.
     */
    public function testPersist()
    {
        $data = [ 'foo' => 'bar' ];

        $this->converter->expects($this->once())->method('objectify')->with($data);
        $this->repository->expects($this->once())->method('findOneBy')
            ->will($this->throwException(new \Exception()));

        $this->em->expects($this->once())->method('persist');

        $this->persister->persist($data);
    }

    /**
     * Tests persist and find when the category was previously migrated.
     */
    public function testPersistWhenAlreadyMigrated()
    {
        $data = [ 'foo' => 'bar' ];

        $this->converter->expects($this->once())->method('objectify')->with($data);
        $this->repository->expects($this->once())->method('findOneBy')
            ->willReturn(new Category([ 'pk_content_category' => 1 ]));

        $this->assertEquals(1, $this->persister->persist($data));
    }
}
