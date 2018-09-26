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
        $this->ds = $this->getMockBuilder('DataSet')
            ->setMethods([ 'get', 'set' ])
            ->getMock();

        $this->em = $this->getMockBuilder('EntityManager')
            ->setMethods([ 'getDataSet' ])
            ->getMock();

        $this->symfonyValidator = $this->getMockBuilder('Validator')
            ->setMethods([ 'validate' ])
            ->getMock();

        $this->em->expects($this->any())->method('getDataSet')
            ->with('Settings', 'instance')->willReturn($this->ds);
    }

    public function testConstructor()
    {
        $validator = new Validator\Validator($this->em, $this->symfonyValidator);

        $this->assertAttributeEquals($this->ds, 'ds', $validator);
        $this->assertAttributeEquals($this->symfonyValidator, 'validator', $validator);
    }

    public function testValidate()
    {
        $this->ds->expects($this->once())->method('get')->with('blacklist.comment')
            ->willReturn('1,2,3,4');

        $validator = new Validator\Validator($this->em, $this->symfonyValidator);
        $data      = [];

        $returnValue = $validator->validate($data, Validator\Validator::BLACKLIST_RULESET_COMMENTS);

        $this->assertEquals([], $returnValue);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testValidateWithInvalidSection()
    {
        $validator = new Validator\Validator($this->em, $this->symfonyValidator);
        $data      = [];

        $this->assertEquals([], $validator->validate($data, 'invalidsection'));
    }

    public function testValidateComment()
    {
        $this->ds->expects($this->once())->method('get')->with('blacklist.comment')
            ->willReturn('1,2,3,4');
        $this->symfonyValidator->expects($this->once())->method('validate')
            ->willReturn([]);

        $validator = new Validator\Validator($this->em, $this->symfonyValidator);
        $data      = [];

        $this->assertEquals([], $validator->validate($data, Validator\Validator::BLACKLIST_RULESET_COMMENTS));
    }

    public function testValidateCommentWithErrors()
    {
        $this->ds->expects($this->once())->method('get')->with('blacklist.comment')
            ->willReturn('1,2,3,4');
        $this->symfonyValidator->expects($this->once())->method('validate')
            ->willReturn([
                new \Exception('Error message.')
            ]);

        $validator = new Validator\Validator($this->em, $this->symfonyValidator);
        $data      = [];

        $this->assertEquals(
            ['type' => 'fatal', 'errors' => ['Error message.']],
            $validator->validate($data, Validator\Validator::BLACKLIST_RULESET_COMMENTS)
        );
    }

    public function testGetConfig()
    {
        $rules = 'test@foo';
        $this->ds->expects($this->once())->method('get')->with('blacklist.comment')
            ->willReturn($rules);

        $validator = new Validator\Validator($this->em, $this->symfonyValidator);

        $this->assertEquals($rules, $validator->getConfig(Validator\Validator::BLACKLIST_RULESET_COMMENTS));
    }

    public function testSetConfig()
    {
        $rules = 'test@foo';
        $this->ds->expects($this->once())->method('set')->with('blacklist.comment', $rules)
            ->willReturn(true);

        $validator = new Validator\Validator($this->em, $this->symfonyValidator);

        $this->assertTrue($validator->setConfig(Validator\Validator::BLACKLIST_RULESET_COMMENTS, $rules));
    }
}
