<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\Core\Component\Exception\Security;

use Common\Core\Component\Exception\Security\InvalidRecaptchaException;

/**
 * Defines test cases for InvalidRecaptchaException class.
 */
class InvalidRecaptchaExceptionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->exception = new InvalidRecaptchaException();
    }

    /**
     * Tests getMessageKey.
     */
    public function testGetMessageKey()
    {
        $this->assertEquals(
            'Invalid reCAPTCHA response',
            $this->exception->getMessageKey()
        );
    }
}
