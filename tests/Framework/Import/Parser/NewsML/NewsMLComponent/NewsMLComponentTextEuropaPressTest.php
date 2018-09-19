<?php
/*
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\Tests\Import\Parser\NewsML;

use Framework\Import\Parser\NewsML\NewsMLComponent\NewsMLComponentTextEuropaPress;

class NewsMLComponentTextEuropaPressTest extends \PHPUnit\Framework\TestCase
{
    public function setUp()
    {
        $this->invalid = simplexml_load_string('<foo></foo>');
        $this->valid   = simplexml_load_string("<NewsComponent>
            <NewsLines>
                <HeadLine>Headline</HeadLine>
                <ByLine />
                <DateLine></DateLine>
                <CreditLine>Europa Press</CreditLine>
                <CopyrightLine></CopyrightLine>
                  <NewsLine>
                     <NewsLineType FormalName=\"Caption\" />
                     <NewsLineText>Foobar baz</NewsLineText>
                  </NewsLine>
            </NewsLines>
            <ContentItem>
              <MediaType FormalName=\"Text\"/>
              <Format FormalName=\"bcNITF2.5\"/>
              <NewsItemId>040729054956.xm61wen7</NewsItemId>
              <DataContent>
                <p>Paragraph 1</p>
              </DataContent>
            </ContentItem>
        </NewsComponent> ");

        $factory = $this->getMockBuilder('Framework\Import\ParserFactory')
            ->disableOriginalConstructor()
            ->setMethods([ 'get' ])
            ->getMock();

        $this->parser = new NewsMLComponentTextEuropaPress($factory);
    }

    public function testCheckFormat()
    {
        $this->assertFalse($this->parser->checkFormat($this->invalid));
        $this->assertTrue($this->parser->checkFormat($this->valid));
    }

    public function testGetAgencyName()
    {
        $this->assertEmpty($this->parser->getAgencyName($this->invalid));
        $this->assertEquals(
            'Europa Press',
            $this->parser->getAgencyName($this->valid)
        );
    }
}
