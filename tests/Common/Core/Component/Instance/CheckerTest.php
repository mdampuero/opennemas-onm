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
use Common\ORM\Entity\Instance;
use Onm\Exception\InstanceAlreadyExistsException;
use Common\ORM\Core\Exception\EntityNotFoundException;

/**
 * Defines test cases for Geo class.
 */
class CheckerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->repository = $this->getMockBuilder('\Common\ORM\Database\Repository\BaseRepository')
            ->disableOriginalConstructor()
            ->setMethods([ 'countBy', 'findOneBy' ])
            ->getMock();

        $this->em = $this->getMockBuilder('\Common\ORM\Core\EntityManager')
            ->disableOriginalConstructor()
            ->setMethods([ 'getRepository' ])
            ->getMock();

        $this->em->expects($this->any())->method('getRepository')->willReturn($this->repository);

        $this->instance = new Instance([
            'id'            => 1,
            'contact_mail'  => 'test@opennemas.com',
            'domains'       => [ 'test.domain.com', 'test.com' ],
            'internal_name' => 'test'
        ]);

        $this->checker = new Checker($this->em);
    }

    /**
     * Tests if check calls other actions in service.
     */
    public function testCheck()
    {
        $checker = $this->getMockBuilder('Common\Core\Component\Instance\Checker')
            ->setConstructorArgs([ $this->em ])
            ->setMethods([ 'fixInternalName', 'validateDomains' ])
            ->getMock();

        $checker->expects($this->once())->method('fixInternalName')
            ->with($this->instance);
        $checker->expects($this->once())->method('validateDomains')
            ->with($this->instance);

        $checker->check($this->instance);
    }

    /**
     * Tests fixInternalName when internal_name is empty.
     */
    public function testFixInternalNameWhenEmptyInternalName()
    {
        $this->instance->internal_name = '';

        $this->repository->expects($this->once())->method('countBy')
            ->with('internal_name regexp "^test[0-9]*$" and id != "1"')
            ->willReturn(2);

        $this->checker->fixInternalName($this->instance);

        $this->assertEquals('test2', $this->instance->internal_name);
    }

    /**
     * Tests fixInternalName when internal_name and domains are empty.
     */
    public function testFixInternalNameWhenEmptyInternalNameAndDomains()
    {
        $this->instance->internal_name = '';
        $this->instance->domains = [];

        $this->repository->expects($this->once())->method('countBy')
            ->willReturn(0);

        $this->checker->fixInternalName($this->instance);

        $this->assertRegexp('/[a-z0-9]+/', $this->instance->internal_name);
    }

    /**
     * Tests validateDomains when domains are empty.
     *
     * @expectedException \InvalidArgumentException
     */
    public function testValidateDomainsWhenEmptyDomains()
    {
        $this->instance->domains = [];

        $this->checker->validateDomains($this->instance);
    }

    /**
     * Tests validateDomains when there is another instance with the same
     * domains.
     *
     * @expectedException Onm\Exception\InstanceAlreadyExistsException
     */
    public function testValidateDomainsWhenDomainsInvalid()
    {
        $this->instance->id = 123;

        $this->repository->expects($this->once())->method('findOneBy')
            ->with(
                'domains regexp "^test.domain.com|,\s*test.domain.com\s*,|,\s*test.domain.com$"'
                . ' or domains regexp "^test.com|,\s*test.com\s*,|,\s*test.com$"'
            )
            ->willReturn(new Instance([ 'id' => 1 ]));

        $this->checker->validateDomains($this->instance);
    }

    /**
     * Tests validateDomains when there is no instance with the same domains in
     * database.
     */
    public function testValidateDomainsWhenDomainsValid()
    {
        $this->repository->expects($this->once())->method('findOneBy')
            ->with(
                'domains regexp "^test.domain.com|,\s*test.domain.com\s*,|,\s*test.domain.com$"'
                . ' or domains regexp "^test.com|,\s*test.com\s*,|,\s*test.com$"'
            )
            ->will($this->throwException(new EntityNotFoundException('Instance')));

        $this->checker->validateDomains($this->instance);
    }

    /**
     * Tests validateDomains when validating an existing instance.
     */
    public function testValidateDomainsWhenSameInstance()
    {
        $this->repository->expects($this->once())->method('findOneBy')
            ->with(
                'domains regexp "^test.domain.com|,\s*test.domain.com\s*,|,\s*test.domain.com$"'
                . ' or domains regexp "^test.com|,\s*test.com\s*,|,\s*test.com$"'
            )
            ->willReturn($this->instance);

        $this->checker->validateDomains($this->instance);
    }
}
