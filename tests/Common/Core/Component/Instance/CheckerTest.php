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
use Common\Model\Entity\Instance;

/**
 * Defines test cases for Geo class.
 */
class CheckerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->repository = $this->getMockBuilder('Opennemas\Orm\Database\Repository\BaseRepository')
            ->disableOriginalConstructor()
            ->setMethods([ 'countBy', 'findBy' ])
            ->getMock();

        $this->em = $this->getMockBuilder('Opennemas\Orm\Core\EntityManager')
            ->disableOriginalConstructor()
            ->setMethods([ 'getRepository' ])
            ->getMock();

        $this->locale = $this->getMockBuilder('Common\Core\Component\Locale\Locale')
            ->disableOriginalConstructor()
            ->setMethods([ 'getSlugs' ])
            ->getMock();

        $this->locale->expects($this->any())->method('getSlugs')
            ->with('frontend')
            ->willReturn([ 'ca', 'gl', 'es' ]);

        $this->em->expects($this->any())->method('getRepository')->willReturn($this->repository);

        $this->checker = new Checker($this->em, $this->locale);
    }

    /**
     * Checks if the instance is valid
     */
    public function testCheck()
    {
        $instance                = new \Common\Model\Entity\Instance();
        $instance->internal_name = 'test';
        $instance->domains       = [ 'test.domain.com' ];
        $instance->contact_mail  = 'test@opennemas.com';
        $this->checker->check($instance);

        $this->addToAssertionCount(1);
    }

    /**
     * Checks if the subdirectory of the instance is valid
     *
     * @expectedException Onm\Exception\InvalidSubdirectoryException
     */
    public function testValidateSubdirectory()
    {
        $instance = new Instance([ 'subdirectory' => '/es' ]);

        $this->checker->validateSubdirectory($instance);
    }
}
