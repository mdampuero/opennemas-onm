<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <onm-devs@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\Tests\ORM\Core\Loader;

use Framework\ORM\Core\Loader\Loader;
use Framework\Fixture\FixtureLoader;

class LoaderTest extends \PHPUnit_Framework_TestCase
{
    public function testLoader()
    {
        $loader = new Loader(__DIR__ . '/../../../../../app/config/orm');
        $this->assertNotEmpty($loader->load());
    }
}
