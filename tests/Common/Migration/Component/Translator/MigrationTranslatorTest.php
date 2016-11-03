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

use Common\Migration\Component\Translator\MigrationTranslator;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Defines test cases for class class.
 */
class MigrationTranslatorTest extends KernelTestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->conn = $this->getMockBuilder('Common\ORM\Core\Connection')
            ->disableOriginalConstructor()
            ->setMethods([ 'executeQuery', 'fetchAll' ])
            ->getMock();

        $this->translator = new MigrationTranslator($this->conn);
    }

    /**
     * Tests loadTranslations.
     */
    public function testLoadTranslations()
    {
        $this->conn->expects($this->once())->method('fetchAll')
            ->with('SELECT * FROM translation_ids')
            ->willReturn([
                [
                    'pk_content_old' => 'frog',
                    'pk_content'     => 'fubar',
                    'type'           => 'wibble',
                    'slug'           => 'glorp'
                ]
            ]);

        $this->translator->loadTranslations();

        $this->assertTrue($this->translator->isTranslated('frog', 'wibble'));
    }

    /**
     * Tests persist.
     */
    public function testPersist()
    {
        $property = new \ReflectionProperty($this->translator, 'translations');
        $property->setAccessible(true);

        $property->setValue($this->translator, [
            [ 'source_id' => 'xyzzy', 'type' => 'plugh', 'slug' => 'quux', 'target_id' => 'corge' ],
            [ 'source_id' => 'xyzzy', 'type' => 'plugh', 'slug' => 'fubar', 'target_id' => 'thud' ]
        ]);

        $this->conn->expects($this->once())->method('executeQuery')
            ->with(
                'REPLACE INTO translation_ids VALUES (?,?,?,?),(?,?,?,?)',
                [ 'xyzzy', 'corge', 'plugh', 'quux', 'xyzzy', 'thud', 'plugh', 'fubar'  ]
            );

        $this->translator->persist();
    }
}
