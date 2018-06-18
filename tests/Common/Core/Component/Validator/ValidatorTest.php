<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\Core\Component\Translator;

use Common\Core\Component\Validator;

/**
 * Defines test cases for TranslatorFactory class.
 */
class ValidatorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->settingsManager = $this->getMockBuilder('SettingsManager')
            ->setMethods([ 'get', 'set' ])
            ->getMock();

        $this->symfonyValidator = $this->getMockBuilder('Validator')
            ->setMethods([ 'validate' ])
            ->getMock();
    }

    /**
     * @covers Common\Core\Component\Validator\Validator::__construct
     */
    public function testConstructor()
    {
        $validator = new Validator\Validator($this->settingsManager, $this->symfonyValidator);

        $this->assertAttributeEquals($this->settingsManager, 'sm', $validator);
        $this->assertAttributeEquals($this->symfonyValidator, 'validator', $validator);
    }

    /**
     * @covers Common\Core\Component\Validator\Validator::validate
     */
    public function testValidate()
    {
        $this->settingsManager->expects($this->once())->method('get')->with('blacklist.comment')
            ->willReturn('1,2,3,4');

        $validator = new Validator\Validator($this->settingsManager, $this->symfonyValidator);
        $data      = [];

        $this->assertEquals([], $validator->validate($data, Validator\Validator::BLACKLIST_RULESET_COMMENTS));
    }

    /**
     * @covers Common\Core\Component\Validator\Validator::validate
     * @expectedException InvalidArgumentException
     */
    public function testValidateWithInvalidSection()
    {
        $validator = new Validator\Validator($this->settingsManager, $this->symfonyValidator);
        $data      = [];

        $this->assertEquals([], $validator->validate($data, 'invalidsection'));
    }

    /**
     * @covers Common\Core\Component\Validator\Validator::validateComment
     */
    public function testValidateComment()
    {
        $this->settingsManager->expects($this->once())->method('get')->with('blacklist.comment')
            ->willReturn('1,2,3,4');
        $this->symfonyValidator->expects($this->once())->method('validate')
            ->willReturn([]);

        $validator = new Validator\Validator($this->settingsManager, $this->symfonyValidator);
        $data      = [];

        $this->assertEquals([], $validator->validate($data, Validator\Validator::BLACKLIST_RULESET_COMMENTS));
    }

    /**
     * @covers Common\Core\Component\Validator\Validator::validateComment
     */
    public function testValidateCommentWithErrors()
    {
        $this->settingsManager->expects($this->once())->method('get')->with('blacklist.comment')
            ->willReturn('1,2,3,4');
        $this->symfonyValidator->expects($this->once())->method('validate')
            ->willReturn([
                new \Exception('Error message.')
            ]);

        $validator = new Validator\Validator($this->settingsManager, $this->symfonyValidator);
        $data      = [];

        $this->assertEquals(
            ['type' => 'fatal', 'errors' => ['Error message.']],
            $validator->validate($data, Validator\Validator::BLACKLIST_RULESET_COMMENTS)
        );
    }

    /**
     * @covers Common\Core\Component\Validator\Validator::getConfig
     */
    public function testGetConfig()
    {
        $rules = 'test@foo';
        $this->settingsManager->expects($this->once())->method('get')->with('blacklist.comment')
            ->willReturn($rules);

        $validator = new Validator\Validator($this->settingsManager, $this->symfonyValidator);

        $this->assertEquals($rules, $validator->getConfig(Validator\Validator::BLACKLIST_RULESET_COMMENTS));
    }

    /**
     * @covers Common\Core\Component\Validator\Validator::setConfig
     */
    public function testSetConfig()
    {
        $rules = 'test@foo';
        $this->settingsManager->expects($this->once())->method('set')->with('blacklist.comment', $rules)
            ->willReturn(true);

        $validator = new Validator\Validator($this->settingsManager, $this->symfonyValidator);

        $this->assertTrue($validator->setConfig(Validator\Validator::BLACKLIST_RULESET_COMMENTS, $rules));
    }
}
