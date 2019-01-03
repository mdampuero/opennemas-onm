<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\ORM\Core;

use Common\ORM\Core\Schema\Schema;

/**
 * Defines test cases for Schema class.
 */
class SchemaTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->schema = new Schema();
    }

    /**
     * Tests getClassName.
     */
    public function testGetClassName()
    {
        $this->assertEquals('Schema', $this->schema->getClassName());
    }
}
