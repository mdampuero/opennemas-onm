<?php

namespace Tests\Common\Core\Component\DataLayer;

use Common\Core\Component\DataLayer\DataLayer;

/**
 * Defines test cases for GlobalVariables class.
 */
class DataLayerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->container = $this->getMockBuilder('ServiceContainer')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->dataset = $this->getMockBuilder('Opennemas\Orm\Core\DataSet')
            ->setMethods([ 'delete', 'get', 'init', 'set' ])
            ->getMock();

        $this->em = $this->getMockBuilder('Opennemas\Orm\Core\EntityManager')
            ->disableOriginalConstructor()
            ->setMethods([ 'getDataSet' ])
            ->getMock();

        $this->extractor = $this->getMockBuilder('Common\Core\Component\Core\VariablesExtractor')
            ->disableOriginalConstructor()
            ->setMethods([ 'get' ])
            ->getMock();

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));

        $this->em->expects($this->any())->method('getDataSet')
            ->with('Settings', 'instance')->willReturn($this->dataset);

        $this->dl   = new DataLayer($this->container);
        $this->data = [
            'device' => true
        ];
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
            case 'orm.manager':
                return $this->em;

            case 'core.variables.extractor':
                return $this->extractor;
        }
    }

    /**
     * Tests getDataLayerCode when no data.
     */
    public function testGetDataLayerCodeNoData()
    {
        $this->assertEmpty($this->dl->getDataLayerCode());
    }

    /**
     * Tests getDataLayerCode.
     */
    public function testGetDataLayerCode()
    {
        $dl = $this->getMockBuilder('Common\Core\Component\DataLayer\Datalayer')
            ->disableOriginalConstructor()
            ->setMethods(['getDataLayer'])
            ->getMock();

        $dl->expects($this->any())->method('getDataLayer')
            ->willReturn($this->data);

        $output = '<script>
            var device = (window.innerWidth || document.documentElement.clientWidth '
            . '|| document.body.clientWidth) < 768 ? "phone" : '
            . '((window.innerWidth || document.documentElement.clientWidth '
            . '|| document.body.clientWidth) < 992 ? "tablet" : "desktop");
            dataLayer = [' . json_encode($this->data) . '];'
            . 'dataLayer.push({ "device":device });</script>';

        $this->assertEquals(
            $output,
            $dl->getDataLayerCode()
        );
    }

    /**
     * Tests getDataLayer when no data.
     */
    public function testGetDataLayerNoData()
    {
        $method = new \ReflectionMethod($this->dl, 'getDataLayer');
        $method->setAccessible(true);

        $this->dataset->expects($this->any())->method('get')
            ->willReturn(null);

        $this->assertEmpty($method->invokeArgs($this->dl, []));
    }

    /**
     * Tests getDataLayer.
     */
    public function testGetDataLayer()
    {
        $method = new \ReflectionMethod($this->dl, 'getDataLayer');
        $method->setAccessible(true);

        $this->dataset->expects($this->any())->method('get')
            ->willReturn([
                [ 'key' => 'foo', 'value' => 'thud' ],
                [ 'key' => 'bar', 'value' => 'wobble' ],
                [ 'key' => 'baz', 'value' => 'waldo' ],
            ]);

        $this->extractor->expects($this->any())->method('get')
            ->willReturn('gorp');

        $this->assertEquals(
            [ 'foo' => 'gorp', 'bar' => 'gorp', 'baz' => 'gorp' ],
            $method->invokeArgs($this->dl, [])
        );
    }

    /**
     * Tests getTypes.
     */
    public function testGetTypes()
    {
        $output = [
            'authorId'        => _('Author Id'),
            'authorName'      => _('Author name'),
            'blank'           => _('Blank'),
            'canonicalUrl'    => _('Canonical url'),
            'categoryId'      => _('Category Id'),
            'categoryName'    => _('Category name'),
            'contentId'       => _('Content Id'),
            'device'          => _('Devices'),
            'extension'       => _('Page type'),
            'format'          => _('Page format'),
            'instanceName'    => _('Instance name'),
            'isRestricted'    => _('Subscription'),
            'language'        => _('Language'),
            'lastAuthorId'    => _('Last editor Id'),
            'lastAuthorName'  => _('Last editor name'),
            'mainDomain'      => _('Hostname'),
            'mediaType'       => _('Media element'),
            'pretitle'        => _('Pretitle'),
            'publicationDate' => _('Published date'),
            'tagsSlug'        => _('Seo tags'),
            'tagsName'        => _('Tags'),
            'updateDate'      => _('Updated date'),
        ];

        $this->assertEquals($output, $this->dl->getTypes());
    }
}
