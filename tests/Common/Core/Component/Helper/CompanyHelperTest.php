<?php

namespace Tests\Common\Core\Component\Helper;

use Common\Core\Component\Helper\CompanyHelper;
use Common\Model\Entity\Content;

/**
 * Defines test cases for ContentMediaHelper class.
 */
class CompanyHelperTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->container = $this->getMockBuilder('ServiceContainer')
            ->setMethods([ 'get', 'getParameter' ])
            ->getMock();

            $this->content = new Content([
                'content_status' => 1,
                'in_litter'      => 0,
                'starttime'      => new \Datetime('2020-01-01 00:00:00'),
                'maps'           => 'Link to GoogleMaps',
                'mortuary'       => 'Location',
                'website'        => 'Website',
                'date'           => '2019-02-01'
            ]);

        $this->service = $this->getMockBuilder('Api\Service\V1\CompanyService')
            ->disableOriginalConstructor()
            ->setMethods([ 'getItem' ])
            ->getMock();

        $this->orm = $this->getMockBuilder('Opennemas\Orm\Core\EntityManager')
            ->disableOriginalConstructor()
            ->setMethods([ 'getDataSet' ])
            ->getMock();

        $this->photoService = $this->getMockBuilder('Api\Service\V1\PhotoService')
            ->disableOriginalConstructor()
            ->setMethods([ 'getItem' ])
            ->getMock();

        $this->ch = $this->getMockBuilder('Common\Core\Component\Helper\ContentHelper')
            ->disableOriginalConstructor()
            ->setMethods([ 'getContent' ])
            ->getMock();

        $this->rh = $this->getMockBuilder('Common\Core\Component\Helper\RelatedHelper')
            ->disableOriginalConstructor()
            ->setMethods([ 'getRelated' ])
            ->getMock();

        $this->instance = $this->getMockBuilder('Common\Model\Entity\Instance')
            ->disableOriginalConstructor()
            ->setMethods([ 'getMediaShortPath' ])
            ->getMock();

        $this->template = $this->getMockBuilder('Common\Core\Component\Template\Template')
            ->disableOriginalConstructor()
            ->setMethods([ 'getValue' ])
            ->getMock();

        $this->ugh = $this->getMockBuilder('Common\Core\Component\Helper\UrlGeneratorHelper')
            ->disableOriginalConstructor()
            ->setMethods([ 'generate' ])
            ->getMock();

        $this->instance->expects($this->any())->method('getMediaShortPath')
            ->willReturn('/media/foo');

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));

        $this->dataSet = $this->getMockBuilder('DataSet')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->orm->expects($this->any())
            ->method('getDataSet')
            ->with('Settings')
            ->willReturn($this->dataSet);

        $this->helper = new CompanyHelper($this->container);
    }

    /**
    * Returns a mocked service based on the service name.
    *
    * @param string $name The service name.
    *
    * @return mixed The mocked service.
    */
    public function serviceContainerCallback($name)
    {
        switch ($name) {
            case 'api.service.category':
                return $this->service;

            case 'api.service.photo':
                return $this->photoService;

            case 'core.instance':
                return $this->instance;

            case 'core.template.frontend':
                return $this->template;

            case 'core.helper.url_generator':
                return $this->ugh;

            case 'core.helper.content':
                return $this->ch;

            case 'core.helper.related':
                return $this->rh;

            case 'orm.manager':
                return $this->orm;
            default:
                return null;
        }
    }

    /**
    * Tests getCompanyLogo.
    */
    public function testGetCompanyLogo()
    {
        $this->rh->expects($this->at(0))->method('getRelated')
            ->willReturn([]);

        $this->assertNull($this->helper->getCompanyLogo($this->content));

        $this->rh->expects($this->at(0))->method('getRelated')
            ->willReturn([['Ymir']]);

        $this->assertEquals(['Ymir'], $this->helper->getCompanyLogo($this->content));
    }

    /**
    * Tests getLocalitiesAndProvices.
    */
    public function testGetLocalitiesAndProvices()
    {
        $this->container->expects($this->any())->method('getParameter')
            ->willReturn('/home/opennemas/current/app/../public');

        $municipios = file_get_contents('/home/opennemas/current/app/../public/assets/utilities/municipios.json');
        $provincias = file_get_contents('/home/opennemas/current/app/../public/assets/utilities/provincias.json');
        $this->assertEquals(
            ['localities' => $municipios, 'provinces' => $provincias],
            $this->helper->getLocalitiesAndProvices()
        );
    }

    /**
    * Tests getCompanyFieldsSufix.
    */
    public function testGetCompanyFieldsSufix()
    {
        $this->assertEquals('company_field_', $this->helper->getCompanyFieldsSufix());
    }

    /**
    * Tests hasCompanyLogo.
    */
    public function testHasCompanyLogo()
    {
        $this->rh->expects($this->at(0))->method('getRelated')
            ->willReturn([['Ymir']]);

        $this->assertTrue($this->helper->hasCompanyLogo($this->content));
    }

    /**
    * Tests getProducts.
    */
    public function testGetProducts()
    {
        $this->rh->expects($this->at(0))->method('getRelated')
            ->willReturn([['Ymir']]);

        $this->assertEquals([['Ymir']], $this->helper->getProducts($this->content));
    }

    /**
    * Tests hasProducts.
    */
    public function testHasProducts()
    {
        $this->rh->expects($this->at(0))->method('getRelated')
            ->willReturn([['Ymir']]);

        $this->assertTrue($this->helper->hasProducts($this->content));
    }

    /**
    * Tests getSocialMedia.
    */
    public function testGetSocialMedia()
    {
        $this->content->whatsapp  = 'Freya';
        $this->content->linkedin  = 'Kali';
        $this->content->youtube   = 'Heimdallr';
        $this->content->tiktok    = 'Jormungandr';
        $this->content->instagram = 'Ishtar';
        $this->content->facebook  = 'Khepri';
        $this->content->twitter   = 'Horus';
        $this->content->phone     = 'Set';
        $this->content->email     = 'Odin';

        $result = [
            'whatsapp'  => 'Freya',
            'linkedin'  => 'Kali',
            'youtube'   => 'Heimdallr',
            'tiktok'    => 'Jormungandr',
            'instagram' => 'Ishtar',
            'facebook'  => 'Khepri',
            'twitter'   => 'Horus',
            'phone'     => 'Set',
            'email'     => 'Odin',
        ];

        $this->assertEquals($result, $this->helper->getSocialMedia($this->content));
    }

    /**
    * Tests hasSocialMedia.
    */
    public function testHasSocialMedia()
    {
        $this->content->whatsapp  = 'Freya';

        $this->assertTrue($this->helper->hasSocialMedia($this->content));
    }

    /**
    * Tests getAddress.
    */
    public function testGetAddress()
    {
        $this->content->address  = 'Scylla';

        $this->assertEquals('Scylla', $this->helper->getAddress($this->content));
    }

    /**
    * Tests getSuggestedFields.
    */
    public function testGetSuggestedFields()
    {
        $this->dataSet->expects($this->at(0))->method('get')
            ->willReturn(['company_custom_fields' => []]);

        $this->assertEquals([], $this->helper->getSuggestedFields($this->content));
    }

    /**
    * Tests hasAddress.
    */
    public function testHasAddress()
    {
        $this->assertFalse($this->helper->hasAddress($this->content));
    }

    /**
    * Tests getWhatsapp.
    */
    public function testGetWhatsapp()
    {
        $this->assertNull($this->helper->getWhatsapp($this->content));
    }

    /**
    * Tests hasWhatsapp.
    */
    public function testHasWhatsapp()
    {
        $this->assertFalse($this->helper->hasWhatsapp($this->content));
    }

    /**
    * Tests getLinkedin.
    */
    public function testGetLinkedin()
    {
        $this->assertNull($this->helper->getLinkedin($this->content));
    }

    /**
    * Tests hasLinkedin.
    */
    public function testHasLinkedin()
    {
        $this->assertFalse($this->helper->hasLinkedin($this->content));
    }

    /**
    * Tests getYoutube.
    */
    public function testGetYoutube()
    {
        $this->assertNull($this->helper->getYoutube($this->content));
    }

    /**
    * Tests hasYoutube.
    */
    public function testHasYoutube()
    {
        $this->assertFalse($this->helper->hasYoutube($this->content));
    }

    /**
    * Tests getTiktok.
    */
    public function testGetTiktok()
    {
        $this->assertNull($this->helper->getTiktok($this->content));
    }

    /**
    * Tests hasTiktok.
    */
    public function testHasTiktok()
    {
        $this->assertFalse($this->helper->hasTiktok($this->content));
    }

    /**
    * Tests getTwitter.
    */
    public function testGetTwitter()
    {
        $this->assertNull($this->helper->getTwitter($this->content));
    }

    /**
    * Tests hasTwitter.
    */
    public function testHasTwitter()
    {
        $this->assertFalse($this->helper->hasTwitter($this->content));
    }

    /**
    * Tests getFacebook.
    */
    public function testGetFacebook()
    {
        $this->assertNull($this->helper->getFacebook($this->content));
    }

    /**
    * Tests hasFacebook.
    */
    public function testHasFacebook()
    {
        $this->assertFalse($this->helper->hasFacebook($this->content));
    }

    /**
    * Tests getInstagram.
    */
    public function testGetInstagram()
    {
        $this->assertNull($this->helper->getInstagram($this->content));
    }

    /**
    * Tests hasInstagram.
    */
    public function testHasInstagram()
    {
        $this->assertFalse($this->helper->hasInstagram($this->content));
    }

    /**
    * Tests getPhone.
    */
    public function testGetPhone()
    {
        $this->assertNull($this->helper->getPhone($this->content));
    }

    /**
    * Tests hasPhone.
    */
    public function testHasPhone()
    {
        $this->assertFalse($this->helper->hasPhone($this->content));
    }

    /**
    * Tests getEmail.
    */
    public function testGetEmail()
    {
        $this->assertNull($this->helper->getEmail($this->content));
    }

    /**
    * Tests hasEmail.
    */
    public function testHasEmail()
    {
        $this->assertFalse($this->helper->hasEmail($this->content));
    }

    /**
    * Tests getTimetable.
    */
    public function testGetTimetable()
    {
        $this->assertEquals([], $this->helper->getTimetable($this->content));

        $this->content->timetable = [
            [ 'name' => _('Monday'), 'enabled' => true, 'schedules' => [] ],
            [ 'name' => _('Tuesday'), 'enabled' => false, 'schedules' => [] ],
            [ 'name' => _('Wednesday'), 'enabled' => false, 'schedules' => [] ],
            [ 'name' => _('Thursday'), 'enabled' => false, 'schedules' => [] ],
            [ 'name' => _('Friday'), 'enabled' => false, 'schedules' => [] ],
            [ 'name' => _('Saturday'), 'enabled' => false, 'schedules' => [] ],
            [ 'name' => _('Sunday'), 'enabled' => false, 'schedules' => [] ],
            [ 'name' => _('Holiday'), 'enabled' => false, 'schedules' => [] ],
        ];
        $this->assertEquals(
            [[ 'name' => _('Monday'), 'enabled' => true, 'schedules' => [] ]],
            $this->helper->getTimetable($this->content)
        );
    }

    /**
    * Tests hasTimetable.
    */
    public function testHasTimetable()
    {
        $this->assertFalse($this->helper->hasTimetable($this->content));
    }
}
