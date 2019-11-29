<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Libs\Smarty;

use Common\ORM\Entity\Category;
use Common\ORM\Entity\Instance;

/**
 * Defines test cases for SmartyStructuredDataTagsTest class.
 */
class SmartyStructuredDataTagsTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        include_once './libs/smarty-onm-plugins/function.structured_data_tags.php';

        $this->instance = new Instance([
            'activated_modules' => [],
            'internal_name'     => 'foobar'
        ]);

        $this->fm = $this->getMockBuilder('FilterManager')
            ->setMethods([ 'filter', 'get', 'set' ])
            ->getMock();

        $this->smarty = $this->getMockBuilder('Smarty')
            ->setMethods([ 'getContainer', 'getTemplateVars', 'getValue' ])
            ->getMock();

        $this->container = $this->getMockBuilder('Container')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->em = $this->getMockBuilder('Common\ORM\Core\EntityManager')
            ->disableOriginalConstructor()
            ->setMethods([ 'getDataSet' ])
            ->getMock();

        $this->requestStack = $this->getMockBuilder('RequestStack')
            ->setMethods([ 'getCurrentRequest' ])
            ->getMock();

        $this->request = $this->getMockBuilder('Request')
            ->setMethods([ 'getUri' ])
            ->getMock();

        $this->ds = $this->getMockBuilder('Dataset')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->ts = $this->getMockBuilder('Api\Service\V1\TagService')
            ->disableOriginalConstructor()
            ->getMock();

        $this->cs = $this->getMockBuilder('CategoryService')
            ->setMethods([ 'getItem' ])
            ->getMock();

        $this->um = $this->getMockBuilder('UserManager')
            ->setMethods([ 'find' ])
            ->getMock();

        $this->helper = $this->getMockBuilder('ContentMediaHelper')
            ->setMethods([ 'getContentMediaObject' ])
            ->getMock();

        $this->kernel = $this->getMockBuilder('Kernel')
            ->setMethods([ 'getContainer' ])
            ->getMock();

        $this->kernel->expects($this->any())->method('getContainer')
            ->willReturn($this->container);

        $this->smarty->expects($this->any())
            ->method('getContainer')
            ->willReturn($this->container);

        $this->structuredData = $this
            ->getMockBuilder('Common\Core\Component\Helper\StructuredData')
            ->setMethods(['generateJsonLDCode', 'extractParamsFromData'])
            ->setConstructorArgs([ $this->instance, $this->em, $this->ts ])
            ->getMock();

        $this->requestStack->expects($this->any())
            ->method('getCurrentRequest')
            ->willReturn($this->request);

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));

        $this->cs->expects($this->any())->method('getItem')
            ->willReturn(new Category([ 'title' => 'Mundo' ]));

        $this->em->expects($this->any())->method('getDataset')
            ->with('Settings', 'instance')->willReturn($this->ds);

        $this->fm->expects($this->any())->method('set')
            ->willReturn($this->fm);
        $this->fm->expects($this->any())->method('filter')
            ->willReturn($this->fm);

        $this->request->expects($this->any())
            ->method('getUri')
            ->willReturn('http://route/to/content.html');

        $GLOBALS['kernel'] = $this->kernel;
    }

    /**
     * Return a mock basing on the service name.
     *
     * @param string $name The service name.
     *
     * @return mixed
     */
    public function serviceContainerCallback($name)
    {
        switch ($name) {
            case 'api.service.category':
                return $this->cs;

            case 'core.helper.content_media':
                return $this->helper;

            case 'core.helper.structured_data':
                return $this->structuredData;

            case 'core.instance':
                return $this->instance;

            case 'data.manager.filter':
                return $this->fm;

            case 'orm.manager':
                return $this->em;

            case 'request_stack':
                return $this->requestStack;

            case 'user_repository':
                return $this->um;
        }

        return null;
    }
}
