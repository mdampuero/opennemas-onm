<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\Data\Filter;

use Common\Data\Filter\ReplaceImagesUrlFilter;

/**
 * Defines test cases for ReplaeImagesUrlFilter class.
 */
class ReplaceImagesUrlFilterTest extends \PHPUnit_Framework_TestCase
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
            ->setMethods([ 'getTranslationBySlug' ])
            ->getMock();

        $this->kernel = $this->getMockBuilder('Kernel')
            ->setMethods([ 'getContainer' ])
            ->getMock();

        $this->kernel->expects($this->any())->method('getContainer')
            ->willReturn($this->container);

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
        }

        return null;
    }

    /**
     * Test filter.
     */
    public function testFilter()
    {
        $photo = new \Photo();

        $photo->path_img = '/2017/01/01/grault-01.jpg';

        $this->redirector->expects($this->once())->method('getTranslationBySlug')
            ->willReturn([ 'pk_content' => 1234 ]);
        $this->em->expects($this->once())->method('find')
            ->with('photo', 1234)->willReturn($photo);

        $this->assertEquals(
            'plugh/frog/2017/01/01/grault-01.jpg',
            $this->filter->filter('xyzzy/plugh/grault-01.jpg')
        );
    }
}
