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

use Common\Core\Component\Instance\Validator;

/**
 * Defines test cases for Geo class.
 */
class ValidatorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->repository = $this->getMockBuilder('\Common\ORM\Database\Repository\BaseRepository')
            ->disableOriginalConstructor()
            ->setMethods([ 'findOneBy' ])
            ->getMock();

        $this->em = $this->getMockBuilder('\Common\ORM\Core\EntityManager')
            ->disableOriginalConstructor()
            ->setMethods([ 'getRepository' ])
            ->getMock();

        $this->em->expects($this->any())->method('getRepository')->willReturn($this->repository);

        $this->validator = new Validator($this->em);
    }

    /**
     * Check the getBadWords method
     */
    public function testGetBadWords()
    {
        $method = new \ReflectionMethod($this->validator, 'getBadWords');
        $method->setAccessible(true);

        // Check that getBadWords returns an array of strings.
        $result = $method->invoke($this->validator);

        $this->assertTrue(is_array($result));
    }

    /**
     * Checks the validateEmail method
     */
    public function testValidateEmail()
    {
        $method = new \ReflectionMethod($this->validator, 'validateEmail');
        $method->setAccessible(true);

        // Test valid email
        $method->invokeArgs($this->validator, ['test@opennemas.com']);
        $this->assertFalse($this->validator->hasErrors());

        // Test invalid email
        $method->invokeArgs($this->validator, ['test_opennemas.com']);
        $this->assertTrue($this->validator->hasErrors());
    }

    /**
     * Check the validateDomains method
     */
    public function testValidateDomains()
    {
        $method = new \ReflectionMethod($this->validator, 'validateDomains');
        $method->setAccessible(true);

        // Test valid Domains
        $method->invokeArgs($this->validator, [ ['test.opennemas.com'] ]);
        $this->assertFalse($this->validator->hasErrors());


        // Test invalid domains due to being too short
        $method->invokeArgs($this->validator, [ ['tes'] ]);
        $this->assertTrue($this->validator->hasErrors());
    }

    /**
     * Check the validateDomains method
     */
    public function testValidateInternalName()
    {
        $method = new \ReflectionMethod($this->validator, 'validateInternalName');
        $method->setAccessible(true);

        // Test valid internal name
        $method->invokeArgs($this->validator, [ 'testament' ]);
        $this->assertFalse($this->validator->hasErrors());

        // Test valid internal name, too short
        $method->invokeArgs($this->validator, [ 'test' ]);
        $this->assertTrue($this->validator->hasErrors());


        // Test invalid internal name due to '.' char included
        $method->invokeArgs($this->validator, [ 'test' ]);
        $this->assertTrue($this->validator->hasErrors());


        // Test invalid internal name due to '@' char included
        $method->invokeArgs($this->validator, [ 'tes@' ]);
        $this->assertTrue($this->validator->hasErrors());

        // Test invalid internal name due to bad word match
        $method->invokeArgs($this->validator, [ 'fellatio' ]);
        $this->assertTrue($this->validator->hasErrors());
    }

    /**
     * Check the validateBadWords method
     */
    public function testValidateBadWords()
    {
        $method = new \ReflectionMethod($this->validator, 'validateBadWords');
        $method->setAccessible(true);

        // Test valid bad words
        $result = $method->invokeArgs($this->validator, [ 'validname' ]);
        $this->assertTrue($result);

        // Test invalid internal name
        $result = $method->invokeArgs($this->validator, [ 'bestiality' ]);
        $this->assertFalse($result);

        // Test invalid internal name
        $result = $method->invokeArgs($this->validator, [ 'fellation' ]);
        $this->assertFalse($result);

        // Test invalid internal name
        $result = $method->invokeArgs($this->validator, [ 'shit' ]);
        $this->assertFalse($result);
    }

    /**
     * Check the getErrors method
     */
    public function testGetErrors()
    {
        $this->assertTrue(is_array($this->validator->getErrors()));
    }

    /**
     * Check the validate method
     */
    public function testValidateForInvalidInstance()
    {
        $instance                = new \Common\ORM\Entity\Instance();
        $instance->internal_name = 'test';
        $instance->domains       = [
            'test.domain.com'
        ];
        $instance->contact_mail  = 'test@opennemas.com';
        $this->validator->validate($instance);
        $this->assertTrue(count($this->validator->getErrors()) > 0);
    }

    /**
     * Tests validate when no instance found by email or domains.
     */
    public function testValidateForValidInstance()
    {
        $this->repository->expects($this->exactly(2))->method('findOneBy')
            ->will($this->throwException(new \Exception()));

        $instance                = new \Common\ORM\Entity\Instance();
        $instance->internal_name = 'foofubarnorf';
        $instance->domains       = [ 'test.domain.com' ];
        $instance->contact_mail  = 'test@opennemas.com';

        $this->validator->validate($instance);

        $this->assertTrue(count($this->validator->getErrors()) === 0);
    }
}
