<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\Migration\Component\Translator;

use Common\Migration\Component\Translator\ImportTranslator;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Defines test cases for class class.
 */
class ImportTranslatorTest extends KernelTestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->conn = $this->getMockBuilder('Common\ORM\Core\Connection')
            ->disableOriginalConstructor()
            ->setMethods([ 'fetchAll' ])
            ->getMock();

        $this->translator = new ImportTranslator($this->conn);
    }

    /**
     * Tests loadTranslations.
     */
    public function testLoadTranslations()
    {
        $this->conn->expects($this->once())->method('fetchAll')
            ->with('SELECT pk_content, urn_source, slug FROM contents')
            ->willReturn([
                [
                    'urn_source' => 'frog',
                    'pk_content' => 'fubar',
                    'slug'       => 'glorp'
                ]
            ]);

        $this->translator->loadTranslations();

        $this->assertTrue($this->translator->isTranslated('frog'));
    }
}
