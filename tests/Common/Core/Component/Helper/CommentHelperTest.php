<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\Core\Component\Helper;

use Common\Core\Component\Helper\CommentHelper;
use Common\ORM\Entity\User;
use Common\ORM\Entity\UserGroup;

/**
 * Defines test cases for class class.
 */
class CommentHelperTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->ormManager = $this->getMockBuilder('OrmManager' . uniqid())
            ->setMethods([ 'getDataSet' ])
            ->getMock();

        $this->dataSet = $this->getMockBuilder('DataSet')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->ormManager->expects($this->any())
            ->method('getDataSet')
            ->with('Settings', 'instance')
            ->willReturn($this->dataSet);

        $this->defaultConfigs = [
            'disable_comments'      => false,
            'with_comments'         => true,
            'number_elements'       => 10,
            'moderation_manual'     => true,
            'moderation_autoreject' => false,
            'moderation_autoaccept' => false,
        ];

        $this->helper = new CommentHelper($this->ormManager, $this->defaultConfigs);
    }

    /**
     * Tests helper constructor.
     */
    public function testConstructor()
    {
        $this->dataSet->expects($this->any())->method('get')
            ->with('comments_config', [])
            ->willReturn([]);

        $this->assertAttributeEquals($this->dataSet, 'sm', $this->helper);
        $this->assertAttributeEquals($this->defaultConfigs, 'defaultConfigs', $this->helper);
    }

    /**
     * Tests autoReject.
     */
    public function testAutoReject()
    {
        $this->assertEquals(false, $this->helper->autoReject());

        $this->dataSet->expects($this->any())->method('get')
            ->willReturn([ 'moderation_autoreject' => true ]);

        $this->helper = new CommentHelper($this->ormManager, $this->defaultConfigs);
        $this->assertEquals(true, $this->helper->autoReject());
    }

    /**
     * Tests moderateManually.
     */
    public function testAutoAccept()
    {
        $this->assertEquals(false, $this->helper->autoAccept());

        $this->dataSet->expects($this->any())->method('get')
            ->willReturn([ 'moderation_autoaccept' => true ]);

        $this->helper = new CommentHelper($this->ormManager, $this->defaultConfigs);
        $this->assertEquals(true, $this->helper->autoAccept());
    }

    /**
     * Tests getConfigs.
     */
    public function testGetConfigs()
    {
        $this->assertEquals($this->defaultConfigs, $this->helper->getConfigs());

        $answer = [ 'moderation_autoaccept' => true ];
        $this->dataSet->expects($this->any())->method('get')
            ->willReturn($answer);

        $this->helper = new CommentHelper($this->ormManager, $this->defaultConfigs);
        $this->assertEquals(
            array_merge($this->defaultConfigs, $answer),
            $this->helper->getConfigs()
        );
    }

    /**
     * Tests getDefaultConfigs.
     */
    public function testGetDefaultConfigs()
    {
        $this->assertEquals($this->defaultConfigs, $this->helper->getDefaultConfigs());
    }

    /**
     * Tests enableCommentsByDefault.
     */
    public function testEnableCommentsByDefault()
    {
        $this->assertEquals(true, $this->helper->enableCommentsByDefault());

        $this->dataSet->expects($this->any())->method('get')
            ->willReturn([ 'with_comments' => false ]);

        $this->helper = new CommentHelper($this->ormManager, $this->defaultConfigs);
        $this->assertEquals(false, $this->helper->enableCommentsByDefault());
    }

    /**
     * Tests moderateManually.
     */
    public function testModerateManually()
    {
        $this->assertEquals(true, $this->helper->moderateManually());

        $this->dataSet->expects($this->any())->method('get')
            ->willReturn([ 'moderation_manual' => false ]);

        $this->helper = new CommentHelper($this->ormManager, $this->defaultConfigs);

        $this->assertEquals(false, $this->helper->moderateManually());
    }
}
