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

use Framework\Import\Parser\EuropaPress\EuropaPressIdeal;
use Framework\Import\Resource\Resource;

class EuropaPressIdealTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $factory = $this->getMockBuilder('Framework\Import\ParserFactory')
            ->disableOriginalConstructor()
            ->getMock();

        $this->parser = new EuropaPressIdeal($factory);

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
            <FIRMA2>Sample signature not EP</FIRMA2>
            <FOTOP>
                <NOMBRE>photo1.jpg</NOMBRE>
                <PIE>Photo description</PIE>
                <EXTENSION>.jpg</EXTENSION>
            </FOTOP>
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

        $this->photo  = new Resource();
        $this->photop = new Resource();

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
            'urn:europapressideal:europapress:20150921181604:photo:20150921181604';

        $this->photop->created_time =
            \DateTime::createFromFormat('d/m/Y H:i:s', '21/09/2015 18:16:04', new \DateTimeZone('UTC'));

        $this->photop->created_time =
            $this->photop->created_time->format('Y-m-d H:i:s');

        $this->photop->agency_name = 'Grupo Idealgallego';
        $this->photop->id          = '20150921181604front_ig.photo';
        $this->photop->extension   = 'jpg';
        $this->photop->file_path   = 'photo1.jpg';
        $this->photop->file_name   = 'photo1.jpg';
        $this->photop->image_type  = 'image/jpg';
        $this->photop->title       = 'Photo description';
        $this->photop->summary     = 'Photo description';
        $this->photop->description = 'Photo description';
        $this->photop->type        = 'photo';
        $this->photop->urn         =
            'urn:europapressideal:europapress:20150921181604:photo:20150921181604front';

        $this->text = new Resource();

        $this->text->agency_name  = 'Grupo Idealgallego';
        $this->text->body         = 'Sample body';
        $this->text->category     = 'Politics POL';
        $this->text->created_time = \DateTime::createFromFormat(
            'd/m/Y H:i:s',
            '21/09/2015 18:16:04',
            new \DateTimeZone('UTC')
        );

        $this->text->created_time =
            $this->text->created_time->format('Y-m-d H:i:s');

        $this->text->id        = '20150921181604';
        $this->text->pretitle  = 'Sample pretitle';
        $this->text->priority  = 4;
        $this->text->related   = [ '20150921181604front_ig.photo', '20150921181604.photo' ];
        $this->text->summary   = 'Sample summary';
        $this->text->tags      = '';
        $this->text->title     = 'Sample title';
        $this->text->type      = 'text';
        $this->text->urn       = 'urn:europapressideal:europapress:20150921181604:text:20150921181604';
        $this->text->signature = 'Sample signature not EP';
    }

    public function testCheckFormat()
    {
        $this->assertFalse($this->parser->checkFormat($this->invalid));
        $this->assertTrue($this->parser->checkFormat($this->valid));
    }

    public function testGetSignature()
    {
        $this->assertEmpty($this->parser->getSignature($this->invalid));

        $this->assertEquals('Sample signature not EP', $this->parser->getSignature($this->valid));
    }

    public function testGetPhotoFront()
    {
        $this->assertEmpty($this->parser->getPhoto($this->invalid));

        $this->assertEquals($this->photop, $this->parser->getPhotoFront($this->valid));
    }

    public function testParse()
    {
        $resource = new Resource();

        $resource->agency_name  = 'Grupo Idealgallego';
        $resource->type         = 'text';
        $resource->urn          = 'urn:europapressideal:europapress::';

        $resources = $this->parser->parse($this->invalid);

        foreach ($resources as $resource) {
            $this->assertEquals('Grupo Idealgallego', $resource->agency_name);
            $this->assertEquals('text', $resource->type);

            $this->assertEquals(1, preg_match(
                '/urn:europapressideal:europapress:\d{14}:/',
                $resource->urn
            ));
        }

        $this->assertEquals(
            [ $this->text, $this->photop, $this->photo ],
            $this->parser->parse($this->valid)
        );
    }
}
