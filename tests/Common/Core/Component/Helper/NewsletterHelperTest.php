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

use Common\Core\Component\Helper\NewsletterHelper;

/**
 * Defines test cases for class class.
 */
class NewsletterHelperTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->container = $this->getMockBuilder('ServiceContainer')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->ds = $this->getMockBuilder('DataSet')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->em = $this->getMockBuilder('Common\ORM\Core\EntityManager')
            ->disableOriginalConstructor()
            ->setMethods([ 'getDataSet' ])
            ->getMock();

        $this->kernel = $this->getMockBuilder('Kernel')
            ->setMethods([ 'getContainer' ])
            ->getMock();

        $this->security = $this->getMockBuilder('Sercurity')
            ->setMethods([ 'hasExtension' ])
            ->getMock();

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));

        $this->kernel->expects($this->any())->method('getContainer')
            ->willReturn($this->container);

        $this->service = $this->getMockBuilder('Api\Service\Service')
            ->setMethods([
                'createItem', 'delete', 'deleteItem', 'deleteList', 'getItem',
                'getList', 'patchItem', 'patchList', 'responsify', 'updateItem',
            ])->getMock();

        $this->em->expects($this->any())->method('getDataSet')
            ->with('Settings', 'instance')->willReturn($this->ds);

        $GLOBALS['kernel'] = $this->kernel;

        $this->helper = new NewsletterHelper($this->em, $this->service);
    }

    /**
     * Returns a mock basing on the requested service name.
     *
     * @return mixed A mock.
     */
    public function serviceContainerCallback($name)
    {
        switch ($name) {
            case 'core.security':
                return $this->security;
        }

        return null;
    }

    /**
     * Tests helper constructor.
     */
    public function testConstructor()
    {
        $this->assertAttributeEquals($this->em, 'em', $this->helper);
        $this->assertAttributeEquals($this->service, 'service', $this->helper);
    }

    /**
     * Tests getContentTypes.
     */
    public function testGetContentTypes()
    {
        $this->security->expects($this->any())->method('hasExtension')
            ->willReturn(true);

        $types = $this->helper->getContentTypes();

        $this->assertNotEmpty($types);
        $this->assertNotContains('comment', $types);
    }

    /**
     * Tests getSubscriptions.
     */
    public function testGetRecipients()
    {
        $helper = $this->getMockBuilder('Common\Core\Component\Helper\NewsletterHelper')
            ->disableOriginalConstructor()
            ->setMethods([
                'getActOnSubscriptions', 'getExternalSubscriptions',
                'getInternalSubscriptions',
            ])->getMock();

        $helper->expects($this->once())->method('getActOnSubscriptions')
            ->willReturn([]);
        $helper->expects($this->once())->method('getExternalSubscriptions')
            ->willReturn([]);
        $helper->expects($this->once())->method('getInternalSubscriptions')
            ->willReturn([ [ 'id' => 1, 'type' => 'list', 'name' => 'frog' ] ]);

        $this->assertEquals(
            [ [ 'id' => 1, 'type' => 'list', 'name' => 'frog' ] ],
            $helper->getRecipients()
        );
    }

    /**
     * Tests getSubscriptionType.
     */
    public function testGetSubscriptionType()
    {
        $this->ds->expects($this->once())->method('get')
            ->with('newsletter_subscriptionType')
            ->willReturn('list');

        $this->assertEquals('list', $this->helper->getSubscriptionType());
    }

    /**
     * Tests getActonSubscriptions for multiple stored values in database.
     */
    public function testGetActOnSubscriptions()
    {
        $method = new \ReflectionMethod($this->helper, 'getActOnSubscriptions');
        $method->setAccessible(true);

        $this->ds->expects($this->at(0))->method('get')
            ->with('actOn.marketingLists')->willReturn(null);
        $this->ds->expects($this->at(1))->method('get')
            ->with('actOn.marketingLists')->willReturn([]);
        $this->ds->expects($this->at(2))->method('get')
            ->with('actOn.marketingLists')->willReturn([
                [ 'name' => 'wobble', 'id' => 42335 ]
            ]);

        $this->assertEquals([], $method->invokeArgs($this->helper, []));
        $this->assertEquals([], $method->invokeArgs($this->helper, []));
        $this->assertEquals(
            [ [ 'id' => 42335, 'name' => 'wobble', 'type' => 'acton' ] ],
            $method->invokeArgs($this->helper, [])
        );
    }

    /**
     * Tests getActonSubscriptions for multiple stored values in database.
     */
    public function testGetExternalSubscriptions()
    {
        $method = new \ReflectionMethod($this->helper, 'getExternalSubscriptions');
        $method->setAccessible(true);

        $this->ds->expects($this->at(0))->method('get')
            ->with('newsletter_maillist')->willReturn(null);
        $this->ds->expects($this->at(1))->method('get')
            ->with('newsletter_maillist')->willReturn([]);
        $this->ds->expects($this->at(2))->method('get')
            ->with('newsletter_maillist')->willReturn([
                'email' => 'gorp@garply.com'
            ]);

        $this->assertEquals([], $method->invokeArgs($this->helper, []));
        $this->assertEquals([], $method->invokeArgs($this->helper, []));
        $this->assertEquals([ [
            'email' => 'gorp@garply.com',
            'name' => 'gorp@garply.com',
            'type' => 'external'
        ] ], $method->invokeArgs($this->helper, []));
    }

    /**
     * Tests getActonSubscriptions for multiple stored values in database.
     */
    public function testGetInternalSubscriptions()
    {
        $method = new \ReflectionMethod($this->helper, 'getInternalSubscriptions');
        $method->setAccessible(true);

        $this->service->expects($this->at(0))->method('getList')
            ->willReturn([
                'items' => [
                    json_decode(json_encode([
                        'pk_user_group' => 1,
                        'name'          => 'thud',
                        'privileges'    => []
                    ])),
                    json_decode(json_encode([
                        'pk_user_group' => 2,
                        'name'          => 'glork',
                        'privileges'    => [ 224 ]
                    ])),
                    json_decode(json_encode([
                        'pk_user_group' => 3,
                        'name'          => 'plugh',
                        'privileges'    => [ 224 ]
                    ])),
                ],
                'total' => 1
            ]);

        $this->assertEquals([
            [
                'id'    => '2',
                'name'  => 'glork',
                'type'  => 'list'
            ],
            [
                'id'    => '3',
                'name'  => 'plugh',
                'type'  => 'list'
            ]
        ], $method->invokeArgs($this->helper, []));
    }
}
