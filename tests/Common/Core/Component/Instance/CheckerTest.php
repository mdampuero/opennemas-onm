<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Test\Common\Core\Component\Instance;

use Common\Core\Component\Instance\Checker;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Defines test cases for Geo class.
 */
class CheckerTest extends KernelTestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->repository = $this->getMockBuilder('\Common\ORM\Database\Repository\BaseRepository')
            ->disableOriginalConstructor()
            ->setMethods([ 'countBy', 'findBy' ])
            ->getMock();

        $this->em = $this->getMockBuilder('\Common\ORM\Core\EntityManager')
            ->disableOriginalConstructor()
            ->setMethods([ 'getRepository' ])
            ->getMock();

        $this->em->expects($this->any())->method('getRepository')->willReturn($this->repository);

        $this->checker = new Checker($this->em);
    }

    /**
     * undocumented function
     **/
    public function testCheck()
    {
        $instance = new \Common\ORM\Entity\Instance();
        $instance->internal_name = 'test';
        $instance->domains = [
            'test.domain.com'
        ];
        $instance->contact_mail = 'test@opennemas.com';
        $this->checker->check($instance);
    }
}
