<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\Core\Component\Helper;

use Common\Core\Component\Helper\LocaleHelper;
use Common\Model\Entity\Instance;

/**
 * Defines test cases for LocaleHelper class.
 */
class LocaleHelperTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->instance = new Instance([
            'id'                => 15228,
            'activated_modules' => [
                'es.openhost.module.multilanguage',
                'es.openhost.module.translation'
            ]
        ]);

        $this->ds = $this->getMockBuilder('Opennemas\Orm\Core\DataSet')
            ->setMethods([ 'delete', 'get', 'set' ])
            ->getMock();

        $this->em = $this->getMockBuilder('Opennemas\Orm\Core\EntityManager')
            ->disableOriginalConstructor()
            ->setMethods([ 'getDataSet' ])
            ->getMock();

        $this->locale = $this->getMockBuilder('Common\Core\Component\Locale\Locale')
            ->disableOriginalConstructor()
            ->setMethods([ 'getAvailableLocales', 'getLocale', 'getSlugs' ])
            ->getMock();

        $this->query = $this->getMockBuilder('Bag')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->request = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')
            ->getMock();

        $this->rs = $this->getMockBuilder('Symfony\Component\HttpFoundation\RequestStack')
            ->setMethods([ 'getCurrentRequest' ])
            ->getMock();

        $this->security = $this->getMockBuilder('Common\Core\Component\Security\Security')
            ->setMethods([ 'hasExtension' ])
            ->getMock();

        $this->request->query = $this->query;

        $this->em->expects($this->any())->method('getDataSet')
            ->with('Settings', 'instance')->willReturn($this->ds);

        $this->helper = new LocaleHelper(
            $this->em,
            $this->instance,
            $this->locale,
            $this->rs,
            $this->security
        );
    }

    /**
     * Tests getConfiguration.
     */
    public function testGetConfiguration()
    {
        $this->locale->expects($this->at(0))->method('getAvailableLocales')
            ->with('frontend')->willReturn('wibble');
        $this->locale->expects($this->at(1))->method('getLocale')
            ->with('frontend')->willReturn('flob');
        $this->locale->expects($this->at(2))->method('getLocale')
            ->with('frontend')->willReturn('flob');
        $this->locale->expects($this->at(3))->method('getSlugs')
            ->with('frontend')->willReturn('quux');


        $this->security->expects($this->exactly(2))->method('hasExtension')
            ->with('es.openhost.module.translation')->willReturn(false);

        $this->assertEquals([
            'available'          => 'wibble',
            'default'            => 'flob',
            'multilanguage'      => true,
            'selected'           => 'flob',
            'slugs'              => 'quux',
            'translators'        => [],
            'translatorsDefault' => []
        ], $this->helper->getConfiguration());
    }

    /**
     * Tests hasMultilanguage for multiple cases.
     */
    public function testHasMultilanguage()
    {
        $this->assertTrue($this->helper->hasMultilanguage());

        $this->instance->activated_modules = [];
        $this->assertFalse($this->helper->hasMultilanguage());

        $this->instance = null;
        $this->assertFalse($this->helper->hasMultilanguage());
    }

    /**
     * Tests getSelectedLocale when the current request is empty.
     */
    public function testGetSelectedLocaleWhenNoRequest()
    {
        $this->rs->expects($this->once())->method('getCurrentRequest')
            ->willReturn(null);

        $this->locale->expects($this->once())->method('getLocale')
            ->with('frontend')->willReturn('wobble');

        $this->assertEquals('wobble', $this->helper->getSelectedLocale());
    }

    /**
     * Tests getSelectedLocale when there is a request in progress.
     */
    public function testGetSelectedLocaleWhenRequestHasNoLocale()
    {
        $this->rs->expects($this->once())->method('getCurrentRequest')
            ->willReturn($this->request);

        $this->locale->expects($this->once())->method('getLocale')
            ->with('frontend')->willReturn('wobble');

        $this->assertEquals('wobble', $this->helper->getSelectedLocale());
    }

    /**
     * Tests getSelectedLocale when there is a request in progress.
     */
    public function testGetSelectedLocaleWhenRequestHasLocale()
    {
        $this->rs->expects($this->once())->method('getCurrentRequest')
            ->willReturn($this->request);

        $this->query->expects($this->exactly(2))->method('get')
            ->with('locale')->willReturn('fubar');

        $this->assertEquals('fubar', $this->helper->getSelectedLocale());
    }

    /**
     * Tests getTranslators when the es.openhost.module.translation extension
     * is not enabled.
     */
    public function testGetTranslatorsWhenExtensionDisabled()
    {
        $this->security->expects($this->once())->method('hasExtension')
            ->with('es.openhost.module.translation')->willReturn(false);

        $this->assertEmpty($this->helper->getTranslators('es'));
    }

    /**
     * Tests getTranslators when the es.openhost.module.translation extension
     * is enabled but there are translators configured.
     */
    public function testGetTranslatorsWhenConfigured()
    {
        $this->security->expects($this->once())->method('hasExtension')
            ->with('es.openhost.module.translation')->willReturn(true);

        $this->ds->expects($this->once())->method('get')
            ->with('translators')->willReturn([
                [ 'from' => 'es', 'to' => 'gl' ],
                [ 'from' => 'en', 'to' => 'es' ],
            ]);

        $this->assertEquals([ [ 'from' => 'es', 'to' => 'gl' ] ], $this->helper->getTranslators('es'));
    }

    /**
     * Tests getTranslators when the es.openhost.module.translation extension
     * is enabled but there are no translators configured.
     */
    public function testGetTranslatorsWhenNoConfigured()
    {
        $this->security->expects($this->once())->method('hasExtension')
            ->with('es.openhost.module.translation')->willReturn(true);

        $this->ds->expects($this->once())->method('get')
            ->with('translators')->willReturn(null);

        $this->assertEmpty($this->helper->getTranslators('gl'));
    }
}
