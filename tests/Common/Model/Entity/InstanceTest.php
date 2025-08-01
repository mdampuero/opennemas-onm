<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\Model\Entity;

use Common\Model\Entity\Instance;

class InstanceTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tests getClient for an instance without client and an instance with
     * client.
     */
    public function testGetClient()
    {
        $instance = new Instance();
        $this->assertEmpty($instance->getClient());

        $instance->client = 'glorp';
        $this->assertEquals('glorp', $instance->getClient());
    }

    /**
     * Tests getDatabaseName for an instance without database and an instance
     * with database.
     */
    public function testGetDatabaseName()
    {
        $instance = new Instance();
        $this->assertEmpty($instance->getDatabaseName());

        $instance->settings = [ 'BD_DATABASE' => 'wobble' ];
        $this->assertEquals('wobble', $instance->getDatabaseName());
    }

    /**
     * Tests getDatabaseName for an instance without database and an instance
     * with database.
     */
    public function testGetBaseUrl()
    {
        $instance = new Instance();

        $instance->main_domain       = 0;
        $instance->domains           = ['foo.opennemas.com', 'bar.opennemas.com'];
        $instance->activated_modules = [ ];

        $this->assertEquals('http://foo.opennemas.com', $instance->getBaseUrl());

        $instance->activated_modules = [ 'es.openhost.module.frontendSsl' ];
        $this->assertEquals('https://foo.opennemas.com', $instance->getBaseUrl());
    }

    /**
     * Tests getBaseUrl when subdirectory instance is allowed.
     */
    public function testGetBaseUrlWhenSubdirectory()
    {
        $instance = new Instance([
            'activated_modules' => [],
            'domains'           => [ 'baz.glorp' ],
            'main_domain'       => 0,
            'subdirectory'      => '/subdirectory',
        ]);

        $this->assertEquals('http://baz.glorp/subdirectory', $instance->getBaseUrl(true));
    }

    /**
     * Tests getMainDomain for all combinations of main_domain and domains.
     */
    public function testGetMainDomain()
    {
        $instance = new Instance();
        $this->assertEmpty($instance->getMainDomain());

        $instance->main_domain = 0;
        $this->assertEmpty($instance->getMainDomain());

        $instance->domains = [];
        $this->assertEmpty($instance->getMainDomain());

        $instance->main_domain = 1;
        $this->assertEmpty($instance->getMainDomain());

        $instance->domains = [ 'mumble.opennemas.com' ];
        $this->assertEquals('mumble.opennemas.com', $instance->getMainDomain());

        $instance->main_domain = 5;
        $this->assertEquals('mumble.opennemas.com', $instance->getMainDomain());
    }

    /**
     * Tests getFilesShortPath.
     */
    public function testGetFilesShortPath()
    {
        $instance = new Instance([ 'internal_name' => 'garply' ]);

        $this->assertEquals('/media/garply/files', $instance->getFilesShortPath());
    }

    /**
     * Tests getImagesShortPath.
     */
    public function testGetImagesShortPath()
    {
        $instance = new Instance([ 'internal_name' => 'garply' ]);

        $this->assertEquals('/media/garply/images', $instance->getImagesShortPath());
    }

    /**
     * Tests getMediaShortPath.
     */
    public function testGetMediaShortPath()
    {
        $instance = new Instance([ 'internal_name' => 'garply' ]);

        $this->assertEquals('/media/garply', $instance->getMediaShortPath());
    }

    /**
     * Tests getNewsstandShortPath.
     */
    public function testGetNewsstandShortPath()
    {
        $instance = new Instance([ 'internal_name' => 'garply' ]);

        $this->assertEquals('/media/garply/kiosko', $instance->getNewsstandShortPath());
    }

    /**
     * Tests hasMultilanguage when instance has module enabled and disabled.
     */
    public function testHasMultilanguage()
    {
        $instance = new Instance([ 'activated_modules' => [ 'garply' ] ]);
        $this->assertFalse($instance->hasMultilanguage());

        $instance->activated_modules[] = 'es.openhost.module.multilanguage';
        $this->assertTrue($instance->hasMultilanguage());
    }
}
