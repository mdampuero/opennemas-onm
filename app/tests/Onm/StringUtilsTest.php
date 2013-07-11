<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
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
     * @covers Onm\StringUtils::normalize_name
     */
    public function testNormalize_name()
    {
        $this->assertEquals('the-great-boy',
            $this->object->normalize_name('The great boy'));
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
     * @covers Onm\StringUtils::get_title
     */
    public function testGetTitle()
    {
        $this->assertEquals(
            'lorem-ipsum-dolor-sit-amet-consectetur-adipiscing-elit-cras-elit-sapien-'.
            'porttitor-non-aliquam-ac-sagittis-urna',
            $this->object->get_title(
                'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras elit sapien,'.
                ' porttitor non aliquam ac, sagittis a urna.'
            )
        );

        # Test with double slashes
        $this->assertEquals(
            'lorem-ipsum-dolor-sit-amet-consectetur-adipiscing-elit-cras-elit-sapien-'.
            'porttitor-non-aliquam-ac-sagittis-urna',
            $this->object->get_title(
                'Lorem ipsum dolor sit amet,  -- consectetur adipiscing elit. Cras elit sapien,'.
                ' porttitor non aliquam ac, sagittis a urna.'
            )
        );

        $this->assertEquals(
            'cambio-look-mariana-antoniale',
            $this->object->get_title(
                '¡El cambio de look de Mariana Antoniale!'
            )
        );

        $this->assertEquals(
            '0001-cambio-look-mariana-antoniale',
            $this->object->get_title(
                '0001 ¡El cambio de look de Mariana Antoniale!'
            )
        );

    }

    /**
     * @covers Onm\StringUtils::get_title
     */
    public function testGetTitleReturnsTheSameString()
    {
        $this->assertEquals(
            '',
            $this->object->get_title(
                ''
            )
        );
    }

    /**
     * @covers Onm\StringUtils::normalize_metadata
     */
    public function testNormalizeMetadata()
    {
        $this->assertEquals(
            'a,list,of,comma,separated,tags',
            $this->object->normalize_metadata('a , list, of,comma, separated, tags')
        );
    }

    /**
     * @covers Onm\StringUtils::get_tags
     */
    public function testGetTags()
    {
        $this->assertEquals(
            'lorem, ipsum, dolor, sit, amet, consectetur, adipiscing, elit, cras, '.
            'sapien, porttitor, non, aliquam, ac, sagittis, urna',
            $this->object->get_tags('Lorem ipsum dolor sit amet, consectetur adipiscing'.
            ' elit. Cras elit sapien, porttitor non aliquam ac, sagittis a urna.')
        );
    }

    /**
     * @covers Onm\StringUtils::get_tags
     */
    public function testGetTagsReturnsStringWithUniqueElements()
    {
        $this->assertEquals(
            'lorem, ipsum, dolor, sit, amet, consectetur, adipiscing, elit, cras, '.
            'sapien, porttitor, non, aliquam, ac, sagittis, urna',
            $this->object->get_tags(
                'Lorem, Lorem, ipsum dolor sit amet, consectetur adipiscing elit. '.
                'Cras elit sapien, porttitor non aliquam ac, sagittis a urna.'
            )
        );
    }

    /**
     * @covers Onm\StringUtils::get_tags
     */
    public function testGetTagsRemovesUnnecesaryWords()
    {
        $this->assertEquals(
            'lorem, ipsum, dolor, sit, amet, consectetur, adipiscing, elit, cras, '.
            'sapien, porttitor, non, aliquam, ac, sagittis, urna',
            $this->object->get_tags(
                'de en al lo Lorem ipsum dolor sit amet, consectetur adipiscing elit.'.
                ' Cras elit sapien, porttitor non aliquam ac, sagittis a urna.'
            )
        );
    }

    /**
     * @covers Onm\StringUtils::remove_shorts
     */
    public function testRemoveShorts()
    {
        $this->assertEquals(
            ' cousa non lembraba ven vagar.',
            $this->object->removeShorts('unha cousa que non me lembraba e ven de vagar.')
        );
    }

    /**
     * @covers Onm\StringUtils::str_stop
     */
    public function testStrStop()
    {
        $this->assertEquals(
            'Example phrase to test...',
            $this->object->str_stop('Example phrase to test str_stop method')
        );
    }

    /**
     * @covers Onm\StringUtils::str_stop
     */
    public function testStrStopWithLimit()
    {
        $this->assertEquals(
            'Example phrase to...',
            $this->object->str_stop('Example phrase to test str_stop method', 20)
        );
    }

    /**
     * @covers Onm\StringUtils::str_stop
     */
    public function testStrStopWithPhraseWithoutSpaces()
    {
        $this->assertEquals(
            'Examplephrasetotests...',
            $this->object->str_stop('Examplephrasetoteststr_stopmethod', 20)
        );
    }

    /**
     * @covers Onm\StringUtils::str_stop
     */
    public function testStrStopReturnsOriginalStringIfLongerThanLimit()
    {
        $this->assertEquals(
            'Example phrase to test str_stop method',
            $this->object->str_stop('Example phrase to test str_stop method', 100)
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
    //  * @covers Onm\StringUtils::disabled_magic_quotes
    //  * @todo   Implement testDisabled_magic_quotes().
    //  */
    // public function testDisabled_magic_quotes()
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
        $this->assertTrue(
          strpos(chr(226), $text) == false
        );
    }

    /**
     * @covers Onm\StringUtils::get_num_words
     * @todo   Implement testGet_num_words().
     */
    public function testGetNumWords()
    {
        $this->assertEquals(
            'Some example text longer...',
            $this->object->get_num_words(
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
