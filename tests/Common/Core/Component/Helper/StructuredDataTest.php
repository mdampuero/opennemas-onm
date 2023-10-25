<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Test\Common\Core\Component\Helper;

use Common\Model\Entity\Content;
use Common\Model\Entity\User;

/**
 * Defines test cases for StructuredData class.
 */
class StructuredDataTest extends \PHPUnit\Framework\TestCase
{
    public function setUp()
    {
        $this->container = $this->getMockForAbstractClass('Symfony\Component\DependencyInjection\ContainerInterface');


        $this->ds = $this->getMockBuilder('DataSet')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->em = $this->getMockBuilder('EntityManager')
            ->setMethods([ 'getDataSet' ])
            ->getMock();

        $this->helper = $this->getMockBuilder('ContentMediaHelper')
            ->setMethods([ 'getMedia' ])
            ->getMock();

        $this->instance = $this->getMockBuilder('Instance')
            ->setMethods([ 'getMediaShortPath', 'getBaseUrl', 'hasMultilanguage' ])
            ->getMock();

        $this->locale = $this->getMockBuilder('Common\Core\Component\Locale\Locale')
            ->disableOriginalConstructor()
            ->setMethods([ 'getSlugs', 'getLocale' ])
            ->getMock();

        $this->kernel = $this->getMockBuilder('Kernel')
            ->setMethods([ 'getContainer' ])
            ->getMock();

        $this->tpl = $this->getMockBuilder('TemplateAdmin')
            ->setMethods([ 'fetch' ])
            ->getMock();

        $this->ah = $this->getMockBuilder('Common\Core\Component\Helper\AuthorHelper')
            ->disableOriginalConstructor()
            ->setMethods([ 'getAuthor', 'getAuthorName' ])
            ->getMock();

        $this->sh = $this->getMockBuilder('Common\Core\Component\Helper\SettingHelper')
            ->disableOriginalConstructor()
            ->setMethods([ 'hasLogo', 'getLogo' ])
            ->getMock();

        $this->ph = $this->getMockBuilder('Common\Core\Component\Helper\PhotoHelper')
            ->disableOriginalConstructor()
            ->setMethods([ 'getPhotoPath' ])
            ->getMock();

        $this->ts = $this->getMockBuilder('Api\Service\V1\TagService')
            ->disableOriginalConstructor()
            ->setMethods([ 'getListByIds' ])
            ->getMock();

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));

        $this->em->expects($this->any())->method('getDataSet')
            ->with('Settings', 'instance')->willReturn($this->ds);

        $this->kernel->expects($this->any())->method('getContainer')
            ->willReturn($this->container);

        $GLOBALS['kernel'] = $this->kernel;

        $this->object = $this->getMockBuilder('Common\Core\Component\Helper\StructuredData')
            ->setConstructorArgs([ $this->container ])
            ->getMock();
    }

    public function serviceContainerCallback($name)
    {
        switch ($name) {
            case 'core.template.admin':
                return $this->tpl;

            case 'core.instance':
                return $this->instance;

            case 'orm.manager':
                return $this->em;

            case 'core.helper.author':
                return $this->ah;

            case 'api.service.tag':
                return $this->ts;

            case 'core.helper.content_media':
                return $this->helper;

            case 'core.helper.setting':
                return $this->sh;

            case 'core.helper.photo':
                return $this->ph;

            case 'core.locale':
                return $this->locale;
        }

        return null;
    }

    /**
     * Tests __construct
     */
    public function testConstruct()
    {
        $this->assertEquals($this->tpl, $this->object->tpl);
        $this->assertEquals($this->ts, $this->object->ts);
        $this->assertEquals($this->ds, $this->object->ds);
    }

    /**
     * Tests extractParamsFromData
     */
    public function testExtractParamsFromData()
    {
        $data                   = [];
        $data['content']        = new Content();
        $data['content']->tags  = [1,2,3,4];
        $data['content']->title = 'This is the title';
        $data['content']->body  = 'Ymir';
        $data['video']          = new Content();
        $data['video']->tags    = [1,2,3,4,5];

        $output                     = [];
        $output['content']          = new Content();
        $output['video']            = new Content();
        $output['content']->tags    = [1,2,3,4];
        $output['video']->tags      = [1,2,3,4,5];
        $output['videoKeywords']    = 'keywords,object,json,linking,data';
        $output['keywords']         = 'keywords,object,json,linking';
        $output['siteName']         = 'site name';
        $output['siteUrl']          = 'http://opennemas.com';
        $output['siteDescription']  = 'site description';
        $output['content']->title   = 'This is the title';
        $output['content']->body    = 'Ymir';
        $output['title']            = 'This is the title';
        $output['description']      = 'This is the description';
        $output['wordCount']        = 4;
        $output['logo']             = 'logo';
        $output['author']           = 'author';
        $output['languages']        = '';
        $output['body']             = 'Ymir';
        $output['externalServices'] = '[]';



        $object = $this->getMockBuilder('Common\Core\Component\Helper\StructuredData')
            ->setConstructorArgs([ $this->container ])
            ->setMethods([ 'getTags', 'getLogoData', 'getDescription', 'getAuthorData', 'getMediaData' ])
            ->getMock();

        $object->expects($this->at(0))->method('getLogoData')->willReturn('logo');
        $object->expects($this->at(1))->method('getDescription')->willReturn('This is the description');
        $object->expects($this->at(2))->method('getAuthorData')->willReturn('author');

        $object->expects($this->at(3))->method('getTags')
            ->with($data['content']->tags)
            ->willReturn('keywords,object,json,linking');
        $object->expects($this->at(4))->method('getTags')
            ->with($data['video']->tags)
            ->willReturn('keywords,object,json,linking,data');

        $this->instance->expects($this->once())
            ->method('getBaseUrl')
            ->willReturn('http://opennemas.com');

        $this->ds->expects($this->at(0))->method('get')
            ->with('site_name')
            ->willReturn('site name');
        $this->ds->expects($this->at(1))->method('get')
            ->with('site_description')
            ->willReturn('site description');

        $this->assertEquals($output, $object->extractParamsFromData($data));
    }

    /**
     * Tests extractParamsFromData without content
     */
    public function testExtractParamsFromDataWithoutContent()
    {
        $output['logo']             = 'logo';
        $output['siteName']         = 'site name';
        $output['siteUrl']          = 'http://opennemas.com';
        $output['siteDescription']  = 'site description';
        $output['languages']        = null;
        $output['externalServices'] = '[]';


        $object = $this->getMockBuilder('Common\Core\Component\Helper\StructuredData')
            ->setConstructorArgs([ $this->container ])
            ->setMethods([ 'getLogoData'])
            ->getMock();

        $object->expects($this->at(0))->method('getLogoData')->willReturn('logo');

        $this->instance->expects($this->once())
            ->method('getBaseUrl')
            ->willReturn('http://opennemas.com');

        $this->ds->expects($this->at(0))->method('get')
            ->with('site_name')
            ->willReturn('site name');
        $this->ds->expects($this->at(1))->method('get')
            ->with('site_description')
            ->willReturn('site description');

        $this->assertEquals($output, $object->extractParamsFromData([]));
    }

    /**
     * Tests generateJsonLDCode
     */
    public function testGenerateJsonLDCodeWithAlbum()
    {
        $data                               = [];
        $data['content']                    = new \Content();
        $data['content']->tags              = [1,2,3,4];
        $data['content']->content_type_name = 'album';
        $data['summary']                    = 'This is a test summary';
        $data['created']                    = '10-10-2010 00:00:00';
        $data['changed']                    = '10-10-2010 00:00:00';
        $data['url']                        = 'http://console/';
        $data['author']                     = 'John Doe';


        $object = $this->getMockBuilder('Common\Core\Component\Helper\StructuredData')
            ->setConstructorArgs([ $this->container ])
            ->setMethods([ 'getTags', 'extractParamsFromData' ])
            ->getMock();

        $object->expects($this->once())->method('extractParamsFromData')
            ->with($data)
            ->willReturn($data);

        $this->tpl->expects($this->at(0))->method('fetch')
            ->with('common/helpers/structured_album_data.tpl', $data);

        $object->generateJsonLDCode($data);
    }

    /**
     * Tests generateJsonLDCode
     */
    public function testGenerateJsonLDCodeArticleWithVideo()
    {
        $data                               = [];
        $data['content']                    = new \Content();
        $data['content']->tags              = [1,2,3,4];
        $data['content']->content_type_name = 'article';
        $data['summary']                    = 'This is a test summary';
        $data['created']                    = '10-10-2010 00:00:00';
        $data['changed']                    = '10-10-2010 00:00:00';
        $data['url']                        = 'http://console/';
        $data['author']                     = 'John Doe';
        $data['video']                      = new Content();


        $object = $this->getMockBuilder('Common\Core\Component\Helper\StructuredData')
            ->setConstructorArgs([ $this->container ])
            ->setMethods([ 'getTags', 'extractParamsFromData' ])
            ->getMock();

        $object->expects($this->once())->method('extractParamsFromData')
            ->with($data)
            ->willReturn($data);

        $this->tpl->expects($this->at(0))->method('fetch')
            ->with('common/helpers/structured_content_data.tpl', $data);

        $object->generateJsonLDCode($data);
    }

    /**
     * Tests generateJsonLDCode
     */
    public function testGenerateJsonLDCodeWithVideo()
    {
        $data                               = [];
        $data['content']                    = new \Content();
        $data['content']->tags              = [1,2,3,4];
        $data['content']->content_type_name = 'video';
        $data['summary']                    = 'This is a test summary';
        $data['created']                    = '10-10-2010 00:00:00';
        $data['changed']                    = '10-10-2010 00:00:00';
        $data['url']                        = 'http://console/';
        $data['author']                     = 'John Doe';
        $data['video']                      = new Content();


        $object = $this->getMockBuilder('Common\Core\Component\Helper\StructuredData')
            ->setConstructorArgs([ $this->container ])
            ->setMethods([ 'getTags', 'extractParamsFromData' ])
            ->getMock();

        $object->expects($this->once())->method('extractParamsFromData')
            ->with($data)
            ->willReturn($data);

        $this->tpl->expects($this->at(0))->method('fetch')
            ->with('common/helpers/structured_video_data.tpl', $data);

        $object->generateJsonLDCode($data);
    }

    /**
     * Tests getAuthorData
     */
    public function testGetAuthorData()
    {
        $author = new User([ 'name' => 'John Dow' ]);
        $method = new \ReflectionMethod($this->object, 'getAuthorData');
        $method->setAccessible(true);

        $content = new \Content();

        $this->ah->expects($this->once())
            ->method('getAuthorName')
            ->willReturn($author->name);

        $method->invokeArgs($this->object, [ $content ]);
    }

    /**
     * Tests getAuthorData without author
     */
    public function testGetAuthorDataWithoutAuthor()
    {
        $method = new \ReflectionMethod($this->object, 'getAuthorData');
        $method->setAccessible(true);

        $content = new \Content();

        $this->ah->expects($this->once())
            ->method('getAuthorName')
            ->willReturn(null);

        $this->ds->expects($this->once())->method('get')
            ->with('site_name')
            ->willReturn('site name');

        $method->invokeArgs($this->object, [ $content ]);
    }


    /**
     * Tests getDescription
     */
    public function testGetDescription()
    {
        $method = new \ReflectionMethod($this->object, 'getDescription');
        $method->setAccessible(true);

        $content              = new \Content();
        $content->description = 'Foo bar baz';

        $this->assertEquals(
            $content->description,
            $method->invokeArgs($this->object, [ $content ])
        );

        $content->description = '';

        $this->ds->expects($this->once())->method('get')
            ->with('site_description')
            ->willReturn('Site description');

        $this->assertEquals(
            'Site description',
            $method->invokeArgs($this->object, [ $content ])
        );
    }

    /**
     * Tests getLogoData
     */
    public function testGetLogoData()
    {
        $method = new \ReflectionMethod($this->object, 'getLogoData');
        $method->setAccessible(true);

        $this->instance->expects($this->once())
            ->method('getBaseUrl')
            ->willReturn('/media/opennemas');

        $this->sh->expects($this->at(0))
            ->method('hasLogo')
            ->with('default')
            ->willReturn(true);

        $media = new Content(
            [
                'pk_content'        => 2,
                'content_type_name' => 'photo',
                'path'   => '/path_to_file/logo.jpg',
                'width'             => 1920,
                'height'            => 1080
            ]
        );

        $this->sh->expects($this->at(1))
            ->method('getLogo')
            ->with('default')
            ->willReturn($media);

        $this->ph->expects($this->any())
            ->method('getPhotoPath')
            ->with($media, null, [], true)
            ->willReturn('https://opennemas.com/media/opennemas/path_to_file/logo.jpg');

        $this->assertEquals(
            'https://opennemas.com/media/opennemas/path_to_file/logo.jpg',
            $method->invokeArgs($this->object, [])
        );
    }

    /**
     * Tests getLogoData without logo
     */
    public function testGetLogoDataWithoutLogo()
    {
        $method = new \ReflectionMethod($this->object, 'getLogoData');
        $method->setAccessible(true);

        $this->instance->expects($this->once())
            ->method('getBaseUrl')
            ->willReturn('/media/opennemas');

        $this->sh->expects($this->at(0))
            ->method('hasLogo')
            ->with('default')
            ->willReturn(false);

        $this->assertEquals(
            '/media/opennemas/assets/images/logos/opennemas-powered-horizontal.png',
            $method->invokeArgs($this->object, [])
        );
    }

    /**
     * Tests getTags
     */
    public function testGetTags()
    {
        $method = new \ReflectionMethod($this->object, 'getTags');
        $method->setAccessible(true);

        $ids = [1];

        $tag       = new \Content();
        $tag->name = 'keywords';

        $this->ts->expects($this->once())->method('getListByIds')
            ->willReturn([ 'items' => [ $tag ]]);

        $method->invokeArgs($this->object, [ $ids ]);
    }

    /**
     * Tests getLanguagesData without multilanguage
     */
    public function testGetLanguagesDataWithoutMultilanguage()
    {
        $method = new \ReflectionMethod($this->object, 'getLanguagesData');
        $method->setAccessible(true);

        $languageAvailable = 'es_ES';

        $this->instance->expects($this->any())->method('hasMultilanguage')
            ->willReturn(false);
        $this->locale->expects($this->at(0))->method('getLocale')
            ->with('frontend')
            ->willReturn($languageAvailable);

        $this->assertEquals(
            'es-ES',
            $method->invokeArgs($this->object, [])
        );
    }

    /**
     * Tests getLanguagesData with multilanguage
     */
    public function testGetLanguagesDataWithMultilanguage()
    {
        $method = new \ReflectionMethod($this->object, 'getLanguagesData');
        $method->setAccessible(true);

        $languagesAvailables = ['es_ES' => 'es', 'en_EN' => 'en'];

        $this->instance->expects($this->any())->method('hasMultilanguage')
            ->willReturn(true);
        $this->locale->expects($this->any())->method('getSlugs')
            ->with('frontend')
            ->willReturn($languagesAvailables);

        $this->assertEquals(
            'es-ES, en-EN',
            $method->invokeArgs($this->object, [])
        );
    }

    /**
     * Tests getExternalServicesData
     */
    public function testGetExternalServicesData()
    {
        $method = new \ReflectionMethod($this->object, 'getExternalServicesData');
        $method->setAccessible(true);

        $externalServices = 'url';

        $this->ds->expects($this->at(0))->method('get')
            ->willReturn($externalServices);
        $this->ds->expects($this->at(1))->method('get')
            ->willReturn(['page' => $externalServices]);
        $this->ds->expects($this->at(2))->method('get')
            ->willReturn($externalServices);
        $this->ds->expects($this->at(3))->method('get')
            ->willReturn($externalServices);
        $this->ds->expects($this->at(4))->method('get')
            ->willReturn($externalServices);
        $this->ds->expects($this->at(5))->method('get')
            ->willReturn($externalServices);
        $this->ds->expects($this->at(6))->method('get')
            ->willReturn($externalServices);
        $this->ds->expects($this->at(7))->method('get')
            ->willReturn($externalServices);
        $this->ds->expects($this->at(8))->method('get')
            ->willReturn($externalServices);
        $this->ds->expects($this->at(9))->method('get')
            ->willReturn($externalServices);
        $this->ds->expects($this->at(10))->method('get')
            ->willReturn($externalServices);

        $this->assertEquals(
            '["url","url","url","url","url","url","url","url","url","url","url"]',
            $method->invokeArgs($this->object, [])
        );
    }
}
