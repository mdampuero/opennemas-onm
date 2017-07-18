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

class StringUtilsTest extends \PHPUnit_Framework_TestCase
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
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Onm\StringUtils::normalizeName
     */
    public function testNormalizeName()
    {
        $this->assertEquals('the-great-boy', $this->object->normalizeName('The great boy'));
    }

    /**
     * @covers Onm\StringUtils::normalize
     */
    public function testNormalize()
    {
        // I have a lot of problems with char encoding
        // $this->assertEquals('a marinha lucense na que c��mpre ir no d��a',
        //     $this->object->normalize('Á marinha lucense na que cómpre ir no día'));
    }

    /**
     * @covers Onm\StringUtils::clearSpecialChars
     * @todo   Implement testClearSpecialChars().
     */
    public function testClearSpecialChars()
    {
        $this->assertEquals(
            'a mariña lucense na que cómpre ir no día',
            $this->object->clearSpecialChars('A mariña lucense na que cómpre ir no día')
        );
    }

    /**
     * @covers Onm\StringUtils::setSeparator
     */
    public function testSetSeparator()
    {
        $this->assertEquals(
            'Lorem-ipsum-dolor-sit-amet,-consectetur-adipiscing-elit.-Cras-elit-sapien,'.
            '-porttitor-non-aliquam-ac,-sagittis-a-urna.',
            $this->object->setSeparator(
                'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras elit '.
                'sapien, porttitor non aliquam ac, sagittis a urna.'
            )
        );
        $this->assertEquals(
            'Lorem=ipsum=dolor=sit=amet,=consectetur=adipiscing=elit.=Cras=elit=sapien,'.
            '=porttitor=non=aliquam=ac,=sagittis=a=urna.',
            $this->object->setSeparator(
                'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras elit '.
                'sapien, porttitor non aliquam ac, sagittis a urna.',
                '='
            )
        );
    }

    /**
     * @covers Onm\StringUtils::getTitle
     */
    public function testGetTitle()
    {
        $this->assertEquals(
            'es-por-tu-bien',
            $this->object->getTitle(
                '"Es por tu bien..."'
            )
        );

        $this->assertEquals(
            'es-por-tu-bien',
            $this->object->getTitle(
                '"Es por tu bien…"'
            )
        );

        $this->assertEquals(
            'lorem-ipsum-dolor-sit-amet-consectetur-adipiscing-elit-cras-elit-sapien-'.
            'porttitor-non-aliquam-ac-sagittis-urna',
            $this->object->getTitle(
                'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras elit sapien,'.
                ' porttitor non aliquam ac, sagittis a urna.'
            )
        );

        # Test with double slashes
        $this->assertEquals(
            'lorem-ipsum-dolor-sit-amet-consectetur-adipiscing-elit-cras-elit-sapien-'.
            'porttitor-non-aliquam-ac-sagittis-urna',
            $this->object->getTitle(
                'Lorem ipsum dolor sit amet,  -- consectetur adipiscing elit. Cras elit sapien,'.
                ' porttitor non aliquam ac, sagittis a urna.'
            )
        );

        $this->assertEquals(
            'cambio-look-mariana-antoniale',
            $this->object->getTitle(
                '¡El cambio de look de Mariana Antoniale!'
            )
        );

        $this->assertEquals(
            '0001-cambio-look-mariana-antoniale-padre',
            $this->object->getTitle(
                '0001 ¡El cambio de look de Mariana Antoniale y su padre! ??'
            )
        );

        $this->assertEquals(
            '0001-cambio-look-mariana-antoniale',
            $this->object->getTitle(
                '0001 ¡El cambio de look de Mariana Antoniale!'
            )
        );
    }

    /**
     * @covers Onm\StringUtils::getTitle
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
            'es-por-tu-bien',
            $this->object->generateSlug(
                '"Es por tu bien…"'
            )
        );

        $this->assertEquals(
            'lorem-ipsum-dolor-sit-amet-consectetur-adipiscing-elit-cras-elit-sapien-'.
            'porttitor-non-aliquam-ac-sagittis-urna',
            $this->object->generateSlug(
                'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras elit sapien,'.
                ' porttitor non aliquam ac, sagittis a urna.'
            )
        );

        # Test with double slashes
        $this->assertEquals(
            'lorem-ipsum-dolor-sit-amet-consectetur-adipiscing-elit-cras-elit-sapien-'.
            'porttitor-non-aliquam-ac-sagittis-urna',
            $this->object->generateSlug(
                'Lorem ipsum dolor sit amet,  -- consectetur adipiscing elit. Cras elit sapien,'.
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
    }

    /**
     * @covers Onm\StringUtils::getTitle
     */
    public function testGetTitleReturnsTheSameString()
    {
        $this->assertEquals(
            '',
            $this->object->getTitle(
                ''
            )
        );
    }

    /**
     * @covers Onm\StringUtils::normalizeMetadata
     */
    public function testNormalizeMetadata()
    {
        $this->assertEquals(
            'a,list,of,comma,separated,tags',
            $this->object->normalizeMetadata('a , list, of,comma, separated, tags')
        );
    }

    /**
     * @covers Onm\StringUtils::getTags
     */
    public function testGetTags()
    {
        $this->assertEquals(
            'Lorem, ipsum, dolor, sit, amet, consectetur, adipiscing, elit, Cras, '.
            'sapien, porttitor, non, aliquam, ac, sagittis, urna',
            $this->object->getTags(
                'Lorem ipsum dolor sit amet, consectetur adipiscing'.
                ' elit. Cras elit sapien, porttitor non aliquam ac, sagittis a urna.'
            )
        );
    }

    /**
     * @covers Onm\StringUtils::getTags
     */
    public function testGetTagsReturnsStringWithUniqueElements()
    {
        $this->assertEquals(
            'Lorem, ipsum, dolor, sit, amet, consectetur, adipiscing, elit, Cras, '.
            'sapien, porttitor, non, aliquam, ac, sagittis, urna',
            $this->object->getTags(
                'Lorem, Lorem, ipsum dolor sit amet, consectetur adipiscing elit. '.
                'Cras elit sapien, porttitor non aliquam ac, sagittis a urna.'
            )
        );
    }

    /**
     * @covers Onm\StringUtils::getTags
     */
    public function testGetTagsRemovesUnnecesaryWords()
    {
        $this->assertEquals(
            'Lorem, ipsum, dolor, sit, amet, consectetur, adipiscing, elit, Cras, '.
            'sapien, porttitor, non, aliquam, ac, sagittis, urna',
            $this->object->getTags(
                'de en al lo Lorem ipsum dolor sit amet, consectetur adipiscing elit.'.
                ' Cras elit sapien, porttitor non aliquam ac, sagittis a urna.'
            )
        );
    }

    /**
     * @covers Onm\StringUtils::removeShorts
     */
    public function testRemoveShorts()
    {
        $this->assertEquals(
            'cousa non lembraba ven vagar.',
            $this->object->removeShorts('unha cousa que non me lembraba e ven de vagar.')
        );
    }

    /**
     * @covers Onm\StringUtils::strStop
     */
    public function testStrStop()
    {
        $this->assertEquals(
            'Example phrase to test...',
            $this->object->strStop('Example phrase to test strStop method')
        );
    }

    /**
     * @covers Onm\StringUtils::strStop
     */
    public function testStrStopWithLimit()
    {
        $this->assertEquals(
            'Example phrase to...',
            $this->object->strStop('Example phrase to test strStop method', 20)
        );
    }

    /**
     * @covers Onm\StringUtils::strStop
     */
    public function testStrStopWithPhraseWithoutSpaces()
    {
        $this->assertEquals(
            'Examplephrasetotests...',
            $this->object->strStop('ExamplephrasetoteststrStopmethod', 20)
        );
    }

    /**
     * @covers Onm\StringUtils::strStop
     */
    public function testStrStopReturnsOriginalStringIfLongerThanLimit()
    {
        $this->assertEquals(
            'Example phrase to test strStop method',
            $this->object->strStop('Example phrase to test strStop method', 100)
        );
    }

    // /**
    //  * @covers Onm\StringUtils::unhtmlentities
    //  * @todo   Implement testUnhtmlentities().
    //  */
    // public function testUnhtmlentities()
    // {
    //     // Remove the following lines when you implement this test.
    //     $this->markTestIncomplete(
    //       'This test has not been implemented yet.'
    //     );
    // }

    // /**
    //  * @covers Onm\StringUtils::disabledMagicQuotes
    //  * @todo   Implement testdisabledMagicQuotes().
    //  */
    // public function testdisabledMagicQuotes()
    // {
    //     // Remove the following lines when you implement this test.
    //     $this->markTestIncomplete(
    //       'This test has not been implemented yet.'
    //     );
    // }

    /**
     * @covers Onm\StringUtils::clearBadChars
     * @todo   Implement testClearBadChars().
     */
    public function testClearBadChars()
    {
        $text = $this->object->clearBadChars('Text'.chr(226).chr(128).chr(169));
        $this->assertTrue(strpos(chr(226), $text) == false);
    }

    /**
     * @covers Onm\StringUtils::getNumWords
     * @todo   Implement testgetNumWords().
     */
    public function testGetNumWords()
    {
        $this->assertEquals(
            'Some example text longer...',
            $this->object->getNumWords(
                'Some example text longer than four words',
                4
            )
        );
    }

    /**
     * @covers Onm\StringUtils::loadBadWords
     * @todo   Implement testLoadBadWords().
     */
    public function testLoadBadWords()
    {
        $this->assertTrue(
            is_array($this->object->loadBadWords())
        );
        $this->assertTrue(
            count($this->object->loadBadWords()) > 0
        );
    }

    /**
     * @covers Onm\StringUtils::filterBadWords
     */
    public function testFilterBadWords()
    {
        // Remove the following lines when you implement this test.
        $this->assertEquals(
            'O fulano ese é un e un fillo de',
            $this->object->filterBadWords(
                'O fulano ese é un marica e un fillo de puta.'
            )
        );
    }

    /**
     * @covers Onm\StringUtils::filterBadWords
     */
    public function testFilterBadWordsWithMinWeight()
    {
        // Remove the following lines when you implement this test.
        $this->assertEquals(
            'O fulano ese é un marica e un',
            $this->object->filterBadWords(
                'O fulano ese é un marica e un fillo de puta.',
                20
            )
        );
    }

    /**
     * @covers Onm\StringUtils::filterBadWords
     */
    public function testFilterBadWordsWithMinWeightAndReplaceString()
    {
        // Remove the following lines when you implement this test.
        $this->assertEquals(
            'O fulano ese é un marica e un xxx',
            $this->object->filterBadWords(
                'O fulano ese é un marica e un fillo de puta.',
                20,
                'xxx'
            )
        );
    }

    /**
     * @covers Onm\StringUtils::filterBadWords
     */
    public function testFilterBadWordsWithReplaceString()
    {
        // Remove the following lines when you implement this test.
        $this->assertEquals(
            'O fulano ese é un xxx e un fillo de xxx',
            $this->object->filterBadWords(
                'O fulano ese é un marica e un fillo de puta.',
                0,
                'xxx'
            )
        );
    }

    /**
     * @covers Onm\StringUtils::getWeightBadWords
     */
    public function testGetWeightBadWordsWithCleanText()
    {
        $this->assertEquals(
            0,
            $this->object->getWeightBadWords(
                'Some text without bad words.'
            )
        );
    }

    /**
     * @covers Onm\StringUtils::getWeightBadWords
     */
    public function testGetWeightBadWords()
    {
        $this->assertEquals(
            70,
            $this->object->getWeightBadWords('Hija de puta')
        );
        $this->assertEquals(
            5,
            $this->object->getWeightBadWords('Carallo')
        );
        $this->assertEquals(
            10,
            $this->object->getWeightBadWords('ostia')
        );
        $this->assertEquals(
            70,
            $this->object->getWeightBadWords('fillo de puta')
        );
    }

    /**
     * @covers Onm\StringUtils::toHttpParams
     * @todo   Implement testToHttpParams().
     */
    public function testToHttpParams()
    {
        $this->assertEquals(
            'action=test&action=1',
            $this->object->toHttpParams(array(
                array('action' => 'test'),
                array('action' => '1'),
            ))
        );
    }

    // /**
    //  * @covers Onm\StringUtils::ext_str_ireplace
    //  * @todo   Implement testExt_str_ireplace().
    //  */
    // public function testExt_str_ireplace()
    // {
    //     // Remove the following lines when you implement this test.
    //     $this->markTestIncomplete(
    //       'This test has not been implemented yet.'
    //     );
    // }

    /**
     * @covers Onm\StringUtils::generatePassword
     */
    public function testGeneratePasswordReturnsStringWithExactLength()
    {
        $this->assertTrue(
            9 == strlen($this->object->generatePassword(9))
        );
    }

    /**
     * @covers Onm\StringUtils::generatePassword
     */
    public function testGeneratePasswordReturnsRandomString()
    {
        $this->assertTrue(
            $this->object->generatePassword(9) != $this->object->generatePassword(9)
        );
    }
}
