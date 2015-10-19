<?php

namespace Backend\Tests\Security;

use Backend\Security\OnmPasswordEncoder;

class PasswordEncoderTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->encoder = new OnmPasswordEncoder();
    }

    /**
     * @covers \Backend\Security\OnmPasswordEncoder::encodePassword
     */
    public function testEncodePasswordWithEncodedPassword()
    {
        $encoded = md5(uniqid());
        $raw     = 'md5:' . $encoded;

        $this->assertEquals($encoded, $this->encoder->encodePassword($raw, null));
    }

    /**
     * @covers \Backend\Security\OnmPasswordEncoder::encodePassword
     */
    public function testEncodePasswordWithPlainPassword()
    {
        $raw     = uniqid();
        $encoded = md5($raw);

        $this->assertEquals($encoded, $this->encoder->encodePassword($raw, null));
    }

    /**
     * @covers \Backend\Security\OnmPasswordEncoder::isPasswordValid
     */
    public function testIsPasswordValidWithInvalidPassword()
    {
        $raw     = uniqid();
        $encoded = uniqid();

        $this->assertFalse($this->encoder->isPasswordValid($encoded, $raw, null));
    }

    /**
     * @covers \Backend\Security\OnmPasswordEncoder::isPasswordValid
     */
    public function testIsPasswordValidWithValidPassword()
    {
        $raw     = uniqid();
        $encoded = $this->encoder->encodePassword($raw, null);

        $this->assertTrue($this->encoder->isPasswordValid($encoded, $raw, null));
    }
}
