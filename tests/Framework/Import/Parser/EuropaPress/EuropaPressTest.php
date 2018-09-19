<?php
/*
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\Tests\Import\Parser\EuropaPress;

use Framework\Import\Parser\EuropaPress\EuropaPress;
use Framework\Import\Resource\Resource;

class EuropaPressTest extends \PHPUnit\Framework\TestCase
{
    public function setUp()
    {
        $factory = $this->getMockBuilder('Framework\Import\ParserFactory')
            ->disableOriginalConstructor()
            ->getMock();

        $this->parser = new EuropaPress($factory);

        $this->invalid = simplexml_load_string('<foo></foo>');
        $this->valid   = simplexml_load_string("<NOTICIA>
            <CODIGO>20150921181604</CODIGO>
            <AGENCIA>0000000010</AGENCIA>
            <PRIORIDAD>30</PRIORIDAD>
            <SERVICIO>ZZ</SERVICIO>
            <SECCION>POL</SECCION>
            <TIPOINFO>PLT</TIPOINFO>
            <CLAVE></CLAVE>
            <FECHA>21/09/2015</FECHA>
            <HORA>18:16:04</HORA>
            <ANTETITULO>Sample pretitle</ANTETITULO>
            <TITULAR>Sample title</TITULAR>
            <ENTRADILLA>Sample summary</ENTRADILLA>
            <CONTENIDO>Sample body</CONTENIDO>
            <FOTO>
                <NOMBRE>photo1.jpg</NOMBRE>
                <PIE>Photo description</PIE>
                <EXTENSION>.jpg</EXTENSION>
            </FOTO>
        </NOTICIA>");

        $this->miss = simplexml_load_string("<NOTICIA>
            <PRIORIDAD>400</PRIORIDAD>
            <SECCION>XYZ</SECCION>
        </NOTICIA>");

        $this->photo = new Resource();

        $this->photo->created_time =
            \DateTime::createFromFormat('d/m/Y H:i:s', '21/09/2015 18:16:04', new \DateTimeZone('UTC'));

        $this->photo->created_time =
            $this->photo->created_time->format('Y-m-d H:i:s');

        $this->photo->agency_name = 'EuropaPress';
        $this->photo->id          = '20150921181604.photo';
        $this->photo->extension   = 'jpg';
        $this->photo->file_path   = 'photo1.jpg';
        $this->photo->file_name   = 'photo1.jpg';
        $this->photo->image_type  = 'image/jpg';
        $this->photo->title       = 'Photo description';
        $this->photo->summary     = 'Photo description';
        $this->photo->description = 'Photo description';
        $this->photo->type        = 'photo';
        $this->photo->urn         =
            'urn:europapress:europapress:20150921181604:photo:20150921181604';

        $this->text = new Resource();

        $this->text->agency_name  = 'EuropaPress';
        $this->text->body         = 'Sample body';
        $this->text->category     = 'Politics POL';
        $this->text->created_time = \DateTime::createFromFormat(
            'd/m/Y H:i:s',
            '21/09/2015 18:16:04',
            new \DateTimeZone('UTC')
        );

        $this->text->created_time =
            $this->text->created_time->format('Y-m-d H:i:s');

        $this->text->id       = '20150921181604';
        $this->text->pretitle = 'Sample pretitle';
        $this->text->priority = 4;
        $this->text->related  = [ '20150921181604.photo' ];
        $this->text->summary  = 'Sample summary';
        $this->text->tags     = '';
        $this->text->title    = 'Sample title';
        $this->text->type     = 'text';
        $this->text->urn      = 'urn:europapress:europapress:20150921181604:text:20150921181604';
    }

    public function testCheckFormat()
    {
        $this->assertFalse($this->parser->checkFormat($this->invalid));
        $this->assertTrue($this->parser->checkFormat($this->valid));
    }

    public function testGetBody()
    {
        $this->assertEmpty($this->parser->getBody($this->invalid));

        $this->assertEquals(
            'Sample body',
            $this->parser->getBody($this->valid)
        );
    }

    public function testGetCategory()
    {
        $this->assertEmpty($this->parser->getCategory($this->invalid));

        $this->assertEquals(_('Politics POL'), $this->parser->getCategory($this->valid));

        $this->assertEquals('XYZ', $this->parser->getCategory($this->miss));
    }

    public function testGetCreatedTime()
    {
        $date = new \DateTime('now');
        $this->assertTrue($date <= $this->parser->getCreatedTime($this->invalid));

        $date = \DateTime::createFromFormat('d/m/Y H:i:s', '21/09/2015 18:16:04', new \DateTimeZone('UTC'));

        $this->assertEquals($date, $this->parser->getCreatedTime($this->valid));
    }

    public function testGetId()
    {
        $this->assertEmpty($this->parser->getId($this->invalid));

        $this->assertEquals('20150921181604', $this->parser->getId($this->valid));
    }

    public function testGetPhoto()
    {
        $this->assertEmpty($this->parser->getPhoto($this->invalid));

        $this->assertEquals($this->photo, $this->parser->getPhoto($this->valid));
    }

    public function testGetPriority()
    {
        $this->assertEquals(5, $this->parser->getPriority($this->invalid));

        $this->assertEquals(4, $this->parser->getPriority($this->valid));

        $this->assertEquals(5, $this->parser->getPriority($this->miss));
    }

    public function testGetSummary()
    {
        $this->assertEmpty($this->parser->getSummary($this->invalid));

        $this->assertEquals(
            'Sample summary',
            $this->parser->getSummary($this->valid)
        );
    }

    public function testGetTitle()
    {
        $this->assertEmpty($this->parser->getTitle($this->invalid));

        $this->assertEquals(
            'Sample title',
            $this->parser->getTitle($this->valid)
        );
    }

    public function testGetUrn()
    {
        $this->assertEquals(1, preg_match(
            '/urn:europapress:europapress:\d{14}:/',
            $this->parser->getUrn($this->invalid)
        ));

        $this->assertEquals(
            'urn:europapress:europapress:20150921181604:text:20150921181604',
            $this->parser->getUrn($this->valid)
        );
    }

    public function testParse()
    {
        $resource = new Resource();

        $resource->agency_name = 'EuropaPress';
        $resource->type        = 'text';
        $resource->urn         = 'urn:europapress:europapress::';

        $resources = $this->parser->parse($this->invalid);

        foreach ($resources as $resource) {
            $this->assertEquals('EuropaPress', $resource->agency_name);
            $this->assertEquals('text', $resource->type);

            $this->assertEquals(1, preg_match(
                '/urn:europapress:europapress:\d{14}:/',
                $resource->urn
            ));
        }

        $this->assertEquals(
            [ $this->text, $this->photo ],
            $this->parser->parse($this->valid)
        );
    }
}
