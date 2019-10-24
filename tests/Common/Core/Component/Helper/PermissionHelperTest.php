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

use Common\Core\Component\Helper\PermissionHelper;

/**
 * Defines test cases for PermissionHelper class.
 */
class PermissionHelperTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->security = $this->getMockBuilder('Common\Core\Component\Security\Security')
            ->setMethods([ 'hasPermission' ])
            ->getMock();

        $this->helper = new PermissionHelper($this->security);
    }

    /**
     * Tests constructor.
     */
    public function testConstructor()
    {
        $property = new \ReflectionProperty($this->helper, 'permissions');
        $property->setAccessible(true);

        $this->assertTrue(is_array($property->getValue($this->helper)));
        $this->assertNotEmpty($property->getValue($this->helper));
    }

    /**
     * Tests getAvailable for master users.
     */
    public function testGetAvailableForMaster()
    {
        $this->security->expects($this->any())->method('hasPermission')
            ->with('MASTER')->willReturn(true);

        $this->assertArrayHasKey(190, $this->helper->getAvailable());
    }

    /**
     * Tests getAvailable for non-master users.
     */
    public function testGetAvailable()
    {
        $this->security->expects($this->any())->method('hasPermission')
            ->with('MASTER')->willReturn(false);

        $this->assertArrayNotHasKey(190, $this->helper->getAvailable());
    }

    /**
     * Tests getByModule.
     */
    public function testGetByModule()
    {
        $permissions = $this->helper->getByModule();

        $this->assertArrayHasKey('CATEGORY_MANAGER', $permissions);
        $this->assertNotEmpty($permissions['CATEGORY_MANAGER']);
    }

    /**
     * Test getNames with known and unknown permission ids.
     */
    public function testGetNames()
    {
        $this->assertEquals(
            [ 1 => 'CATEGORY_ADMIN', 20 => 'ADVERTISEMENT_UPDATE' ],
            $this->helper->getNames([ 0, 1, 20 ])
        );
    }
}
