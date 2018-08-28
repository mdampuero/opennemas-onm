<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\Core\Component\Security;

use Common\ORM\Entity\Instance;
use Common\ORM\Entity\User;
use Common\Core\Component\Security\Security;

/**
 * Defines test cases for Security class.
 */
class SecurityTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->instance = new Instance([
            'activated_modules' => [ 'norf', 'wubble' ],
            'internal_name'     => 'garply'
        ]);

        $this->user = new User([
            'user_groups' => []
        ]);

        $this->security = new Security();

        $this->security->setInstance($this->instance);
        $this->security->setUser($this->user);
        $this->security->setPermissions([ 'fred' ]);
        $this->security->setCategories([ 'frog' ]);
    }

    /**
     * Tests all security methods.
     */
    public function testSecurityGettersAndSetters()
    {
        $this->assertEquals([ 'frog' ], $this->security->getCategories());
        $this->security->setCategories(null);
        $this->assertEquals([], $this->security->getCategories());

        $this->security->setInstances([ 'glorp', 'grault' ]);
        $this->assertEquals([ 'glorp', 'grault' ], $this->security->getInstances());

        $this->assertEquals([ 'fred' ], $this->security->getPermissions());
        $this->security->setPermissions(null);
        $this->assertEquals([], $this->security->getPermissions());

        $this->assertEquals($this->user, $this->security->getUser());

        $this->security->setCliUser();
        $this->assertEquals(0, $this->security->getUser()->id);
        $this->assertEquals('cli', $this->security->getUser()->username);
    }

    /**
     * Tests hasCategory for normal, admin and master users.
     */
    public function testHasCategory()
    {
        $this->assertTrue($this->security->hasCategory(null));

        $this->security->setPermissions([ 'MASTER' ]);
        $this->assertTrue($this->security->hasCategory('wobble'));

        $this->security->setPermissions([ 'ADMIN' ]);
        $this->assertTrue($this->security->hasCategory('wobble'));

        $this->security->setPermissions([]);
        $this->assertTrue($this->security->hasCategory('frog'));
        $this->assertFalse($this->security->hasCategory('wobble'));

        $security = new Security();
        $security->setUser($this->user);
        $security->setInstance($this->instance);

        $this->assertTrue($security->hasCategory('fubar'));
    }

    /**
     * Tests hasExtension for normal, admin and master users.
     */
    public function testHasExtension()
    {
        $this->security->setPermissions([ 'MASTER' ]);
        $this->assertTrue($this->security->hasExtension('wobble'));

        $this->security->setPermissions([ 'ADMIN' ]);
        $this->assertFalse($this->security->hasExtension('wobble'));
        $this->assertTrue($this->security->hasExtension('norf'));
    }

    /**
     * Tests hasInstance for normal, admin and master users.
     */
    public function testHasInstance()
    {
        $this->security->setPermissions([ 'MASTER' ]);
        $this->assertTrue($this->security->hasInstance('plugh'));

        $this->security->setPermissions([ 'PARTNER' ]);
        $this->assertTrue($this->security->hasInstance('manager'));

        $this->security->setInstances([ 'norf' ]);
        $this->assertTrue($this->security->hasInstance('norf'));
    }

    /**
     * Tests hasPermission for normal, admin and master users.
     */
    public function testHasPermission()
    {
        $this->security->setPermissions([ 'MASTER' ]);
        $this->assertTrue($this->security->hasPermission('plugh'));

        $this->security->setPermissions([ 'PARTNER' ]);
        $this->assertFalse($this->security->hasPermission('wubble'));
        $this->security->setInstances([ 'garply' ]);
        $this->assertTrue($this->security->hasPermission('wubble'));

        $this->security->setPermissions([ 'ADMIN' ]);
        $this->assertTrue($this->security->hasPermission('ADMIN'));
    }
}
