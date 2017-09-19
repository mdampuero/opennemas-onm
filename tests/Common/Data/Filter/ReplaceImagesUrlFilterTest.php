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
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Defines test cases for ReplaeImagesUrlFilter class.
 */
class ReplaceImagesUrlFilterTest extends KernelTestCase
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

        $this->redirector = $this->getMockBuilder('Redirector')
            ->setMethods([ 'getTranslationBySlug' ])
            ->getMock();

        $this->container->expects($this->at(0))->method('get')
            ->with('core.redirector')->willReturn($this->redirector);
        $this->container->expects($this->at(1))->method('get')
            ->with('entity_repository')->willReturn($this->em);

        $this->filter = new ReplaceImagesUrlFilter($this->container, [
            'pattern' => '/(?<slug>.+.jpg)/',
            'path'    => 'plugh/frog'
        ]);
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
