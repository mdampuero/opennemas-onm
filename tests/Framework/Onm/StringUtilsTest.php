<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Onm;

class StringUtilsTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var StringUtils
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new StringUtils;
    }

    /**
     * @covers \Onm\StringUtils::normalize
     */
    public function testNormalize()
    {
        // I have a lot of problems with char encoding
        $this->assertEquals(
            'a marinha lucense na que compre ir no dia',
            $this->object->normalize('Á marinha lucense na que cómpre ir no día')
        );
    }

    /**
     * @covers \Onm\StringUtils::generateSlug
     */
    public function testGenerateSlug()
    {
        $this->assertEquals(
            'es-por-tu-bien',
            $this->object->generateSlug(
                '"Es por tu bien..."'
            )
        );

        $this->assertEquals(
            '',
            $this->object->generateSlug(null)
        );

        $this->assertEquals(
            'es-por-tu-bien',
            $this->object->generateSlug(
                '"Es por tu bien…"'
            )
        );

        $this->assertEquals(
            '10000-foo-bar-300',
            $this->object->generateSlug(
                '10.000 foo bar 3,00'
            )
        );

        $this->assertEquals(
            'lorem-ipsum-dolor-sit-amet-consectetur-adipiscing-elit-cras-elit-sapien-' .
            'porttitor-non-aliquam-ac-sagittis-urna',
            $this->object->generateSlug(
                'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras elit sapien,' .
                ' porttitor non aliquam ac, sagittis a urna.'
            )
        );

        // Test with double slashes
        $this->assertEquals(
            'lorem-ipsum-dolor-sit-amet-consectetur-adipiscing-elit-cras-elit-sapien-' .
            'porttitor-non-aliquam-ac-sagittis-urna',
            $this->object->generateSlug(
                'Lorem ipsum dolor sit amet,  -- consectetur adipiscing elit. Cras elit sapien,' .
                ' porttitor non aliquam ac, sagittis a urna.'
            )
        );

        $this->assertEquals(
            'cambio-look-mariana-antoniale',
            $this->object->generateSlug(
                '¡El cambio de look de Mariana Antoniale!'
            )
        );

        $this->assertEquals(
            'cambio-look-mariana-antoniale',
            $this->object->generateSlug(
                $this->object->generateSlug(
                    'cambio-look-mariana-antoniale'
                )
            )
        );

        $this->assertEquals(
            '0001-cambio-look-mariana-antoniale-padre',
            $this->object->generateSlug(
                '0001 ¡El cambio de look de Mariana Antoniale y su padre! ??'
            )
        );

        $this->assertEquals(
            '0001-cambio-look-mariana-antoniale',
            $this->object->generateSlug(
                '0001 ¡El cambio de look de Mariana Antoniale!'
            )
        );

        $this->assertEquals(
            '0001-cambio-look-mariana-antoniale',
            $this->object->generateSlug(
                '0001 ¡El cambio de look de Mariana Antoniale! -‐‒–—―⁃'
            )
        );

        $this->assertEquals(
            'detienen-dieciseis-personas-robo-joyas-kim-kardashian',
            $this->object->generateSlug(
                'Detienen a dieciséis personas por el &#10;robo de joyas de Kim Kardashian'
            )
        );

        $this->assertEquals(
            'detienen-dieciseis-personas-robo-joyas-kim-kardashian',
            $this->object->generateSlug(
                'detienen-dieciseis-personas-robo-joyas-kim-kardashian'
            )
        );

        $this->assertEquals([
            'detienen-dieciseis-personas-robo-joyas-kim-kardashian',
            '0001-cambio-look-mariana-antoniale',
        ], $this->object->generateSlug([
            'detienen-dieciseis-personas-robo-joyas-kim-kardashian',
            '0001 ¡El cambio de look de Mariana Antoniale! -‐‒–—―⁃'
        ]));

        $this->assertEquals([
            'chorando-aprendese-fito-musica-contemporanea'
        ], $this->object->generateSlug([
            ' “Chorando apréndese\' é un fito na música contemporánea”'
        ]));

        $this->assertEquals([], $this->object->generateSlug([]));

        $this->assertEquals([1 => null], $this->object->generateSlug([1 => null]));

        $this->assertEquals([''], $this->object->generateSlug(['']));

        $this->assertEquals(
            [
                'detienen-dieciseis-personas-robo-joyas-kim-kardashian',
                '',
            ],
            $this->object->generateSlug(
                [
                    'detienen-dieciseis-personas-robo-joyas-kim-kardashian',
                    ''
                ]
            )
        );
    }
}
