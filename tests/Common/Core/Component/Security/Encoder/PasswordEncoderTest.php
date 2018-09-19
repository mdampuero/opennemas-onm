<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Backend\Tests\Security;

use Common\Core\Component\Security\Encoder\PasswordEncoder;

/**
 * Defines test cases for PasswordEncode class.
 */
class PasswordEncoderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->encoder = new PasswordEncoder();
    }

    /**
     * Tests encodePassword.
     */
    public function testEncodePasswordWithEncodedPassword()
    {
        $encoded = md5(uniqid());
        $raw     = 'md5:' . $encoded;

        $this->assertEquals($encoded, $this->encoder->encodePassword($raw, null));

        $raw     = uniqid();
        $encoded = md5($raw);

        $this->assertEquals($encoded, $this->encoder->encodePassword($raw, null));
    }

    /**
     * Tests isPasswordValid with valid and invalid values.
     */
    public function testIsPasswordValid()
    {
        $raw     = uniqid();
        $encoded = uniqid();

        $this->assertFalse($this->encoder->isPasswordValid($encoded, $raw, null));

        $raw     = uniqid();
        $encoded = $this->encoder->encodePassword($raw, null);

        $this->assertTrue($this->encoder->isPasswordValid($encoded, $raw, null));
    }
}
