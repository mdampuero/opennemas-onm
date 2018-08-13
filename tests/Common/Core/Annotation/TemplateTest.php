<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\Core\Annotation;

use Common\Core\Annotation\Template;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Defines test cases for Template class.
 */
class TemplateTest extends KernelTestCase
{
    /**
     * Tests the annotation creation and getter methors.
     */
    public function testTemplate()
    {
        $annotation = new Template([ 'name' => 'frog', 'file' => 'plugh' ]);

        $this->assertEquals('frog', $annotation->getName());
        $this->assertEquals('plugh', $annotation->getFile());
    }
}
