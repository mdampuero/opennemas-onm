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

use Common\Migration\Component\Persister\ContentPersister;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Defines test cases for ContentPersister class.
 */
class ContentPersisterTest extends KernelTestCase
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

        $this->em = $this->getMockBuilder('Common\ORM\Core\EntityManager')
            ->disableOriginalConstructor()
            ->setMethods([ 'getConverter', 'persist' ])
            ->getMock();

        $this->em->expects($this->any())->method('getConverter')->willReturn($this->converter);

        $this->persister = new ContentPersister($this->em);
    }

    /**
     * Tests persist.
     */
    public function testPersist()
    {
        $data = [ 'foo' => 'bar' ];

        $this->converter->expects($this->once())->method('objectify')->with($data);
        $this->em->expects($this->once())->method('persist');

        $this->persister->persist($data);
    }
}
