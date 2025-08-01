<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\Core\Component\Filter;

use Common\Core\Component\Filter\ReplaceImagesUrlFilter;
use Common\Model\Entity\Content;

/**
 * Defines test cases for ReplaeImagesUrlFilter class.
 */
class ReplaceImagesUrlFilterTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->container = $this->getMockBuilder('Container')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->em = $this->getMockBuilder('EntityManager')
            ->setMethods([ 'find' ])
            ->getMock();

        $this->instance = $this->getMockBuilder('Instance')
            ->setMethods([ 'hasMultilanguage' ])
            ->getMock();

        $this->redirector = $this->getMockBuilder('Redirector')
            ->setMethods([ 'getTranslation' ])
            ->getMock();

        $this->kernel = $this->getMockBuilder('Kernel')
            ->setMethods([ 'getContainer' ])
            ->getMock();

        $this->kernel->expects($this->any())->method('getContainer')
            ->willReturn($this->container);

        $this->servicePhoto = $this->getMockBuilder('Api\Service\V1\PhotoService')
            ->disableOriginalConstructor()
            ->setMethods([ 'getItem' ])
            ->getMock();

        $GLOBALS['kernel'] = $this->kernel;

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));

        $this->filter = new ReplaceImagesUrlFilter($this->container, [
            'pattern' => '/(?<slug>.+.jpg)/',
            'path'    => 'plugh/frog'
        ]);
    }

    public function serviceContainerCallback($name)
    {
        switch ($name) {
            case 'core.instance':
                return $this->instance;

            case 'core.redirector':
                return $this->redirector;

            case 'entity_repository':
                return $this->em;

            case 'api.service.photo':
                return $this->servicePhoto;
        }

        return null;
    }

    /**
     * Test filter.
     */
    public function testFilter()
    {
        $photo = new \Content();

        $photo->path = '/2017/01/01/grault-01.jpg';

        $this->redirector->expects($this->once())->method('getTranslation')
            ->willReturn([ 'pk_content' => 1234 ]);

        $this->servicePhoto->expects($this->any())->method('getItem')
            ->with(1234)
            ->willReturn($photo);

        $this->assertEquals(
            'plugh/frog/2017/01/01/grault-01.jpg',
            $this->filter->filter('xyzzy/plugh/grault-01.jpg')
        );
    }
}
