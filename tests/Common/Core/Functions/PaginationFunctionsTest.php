<?php

namespace Tests\Common\Core\Functions;

/**
 * Defines test cases for pagination functions.
 */
class PaginationFunctionsTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->container = $this->getMockBuilder('Container')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->globals = $this->getMockBuilder('Common\Core\Component\Core\GlobalVariables')
            ->disableOriginalConstructor()
            ->setMethods([ 'getRequest' ])
            ->getMock();

        $this->kernel = $this->getMockBuilder('Kernel')
            ->setMethods([ 'getContainer' ])
            ->getMock();

        $this->request = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')
            ->disableOriginalConstructor()
            ->setMethods([ 'getRequestUri' ])
            ->getMock();

        $this->template = $this->getMockBuilder('Common\Core\Component\Template\Template')
            ->disableOriginalConstructor()
            ->setMethods([ 'getValue' ])
            ->getMock();

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));

        $this->globals->expects($this->any())->method('getRequest')
            ->willReturn($this->request);

        $this->kernel->expects($this->any())->method('getContainer')
            ->willReturn($this->container);

        $GLOBALS['kernel'] = $this->kernel;
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
            case 'core.globals':
                return $this->globals;

            case 'core.template.frontend':
                return $this->template;

            default:
                return null;
        }
    }

    /**
     * Tests get_pagination_current_page.
     */
    public function testGetPaginationCurrentPage()
    {
        $this->template->expects($this->at(0))->method('getValue')
            ->with('page')->willReturn(null);
        $this->template->expects($this->at(1))->method('getValue')
            ->with('page')->willReturn(10);

        $this->assertEquals(1, get_pagination_current_page());
        $this->assertEquals(10, get_pagination_current_page());
    }

    /**
     * Tests get_pagination_first_page_url.
     */
    public function testGetPaginationFirstPageUrl()
    {
        $this->request->expects($this->once())->method('getRequestUri')
            ->willReturn('/fred/foo?page=205&qux=foobar');

        $this->assertEquals('/fred/foo?qux=foobar', get_pagination_first_page_url());
    }

    /**
     * Tests get_pagination_last_page_url.
     */
    public function testGetPaginationLastPageUrl()
    {
        $this->request->expects($this->once())->method('getRequestUri')
            ->willReturn('/fred/foo?page=2');

        $this->template->expects($this->at(0))->method('getValue')
            ->with('epp')->willReturn(25);
        $this->template->expects($this->at(1))->method('getValue')
            ->with('total')->willReturn(230);

        $this->assertEquals('/fred/foo?page=10', get_pagination_last_page_url());
    }

    /**
     * Tests get_pagination_next_page_url.
     */
    public function testGetPaginationNextPageUrl()
    {
        $this->request->expects($this->once())->method('getRequestUri')
            ->willReturn('/fred/foo?page=19');

        $this->template->expects($this->at(0))->method('getValue')
            ->with('page')->willReturn(19);
        $this->template->expects($this->at(1))->method('getValue')
            ->with('epp')->willReturn(10);
        $this->template->expects($this->at(2))->method('getValue')
            ->with('total')->willReturn(200);

        $this->assertEquals('/fred/foo?page=20', get_pagination_next_page_url());

        $this->template->expects($this->at(0))->method('getValue')
            ->with('page')->willReturn(20);
        $this->template->expects($this->at(1))->method('getValue')
            ->with('epp')->willReturn(10);
        $this->template->expects($this->at(2))->method('getValue')
            ->with('total')->willReturn(200);

        $this->assertNull(get_pagination_next_page_url());
    }

    /**
     * Tests get_pagination_next_pages.
     */
    public function testGetPaginationNextPages()
    {
        $this->template->expects($this->at(0))->method('getValue')
            ->with('page')->willReturn(4);
        $this->template->expects($this->at(1))->method('getValue')
            ->with('epp')->willReturn(10);
        $this->template->expects($this->at(2))->method('getValue')
            ->with('total')->willReturn(200);

        $this->assertEquals([ 5, 6 ], get_pagination_next_pages(2));
    }

    /**
     * Tests get_pagination_page_url.
     */
    public function testGetPaginationPageUrl()
    {
        $this->request->expects($this->at(0))->method('getRequestUri')
            ->willReturn('/fred/foo?page=2');
        $this->request->expects($this->at(1))->method('getRequestUri')
            ->willReturn('/fred/foo?flob=gorp&page=2');
        $this->request->expects($this->at(2))->method('getRequestUri')
            ->willReturn('/fred/foo?page=2&flob=gorp');

        $this->assertEquals('/fred/foo?page=442', get_pagination_page_url(442));
        $this->assertEquals('/fred/foo?page=442&flob=gorp', get_pagination_page_url(442));
        $this->assertEquals('/fred/foo?page=442&flob=gorp', get_pagination_page_url(442));
    }

    /**
     * Tests get_pagination_previous_page_url.
     */
    public function testGetPaginationPrevPageUrl()
    {
        $this->request->expects($this->once())->method('getRequestUri')
            ->willReturn('/fred/foo?page=2');

        $this->template->expects($this->at(0))->method('getValue')
            ->with('page')->willReturn(4);

        $this->assertEquals('/fred/foo?page=3', get_pagination_previous_page_url());

        $this->template->expects($this->at(0))->method('getValue')
            ->with('page')->willReturn(1);

        $this->assertNull(get_pagination_previous_page_url());
    }

    /**
     * Tests get_pagination_previous_pages.
     */
    public function testGetPaginationPrevPages()
    {
        $this->template->expects($this->at(0))->method('getValue')
            ->with('page')->willReturn(4);

        $this->assertEquals([ 1, 2, 3 ], get_pagination_previous_pages(20));
    }

    /**
     * Tests get_pagination_total_pages.
     */
    public function testGetPaginationTotalPages()
    {
        $this->template->expects($this->at(0))->method('getValue')
            ->with('epp')->willReturn(10);
        $this->template->expects($this->at(1))->method('getValue')
            ->with('total')->willReturn(101);

        $this->assertEquals(11, get_pagination_total_pages());
    }

    /**
     * Tests has_pagination.
     */
    public function testHasPagination()
    {
        $this->template->expects($this->at(0))->method('getValue')
            ->with('epp')->willReturn(null);
        $this->template->expects($this->at(1))->method('getValue')
            ->with('total')->willReturn(null);

        $this->template->expects($this->at(2))->method('getValue')
            ->with('epp')->willReturn(10);
        $this->template->expects($this->at(3))->method('getValue')
            ->with('total')->willReturn(101);

        $this->assertFalse(has_pagination());
        $this->assertTrue(has_pagination());
    }

    /**
     * Tests has_pagination_next_page.
     */
    public function testHasPaginationNextPage()
    {
        $this->template->expects($this->at(0))->method('getValue')
            ->with('page')->willReturn(1);
        $this->template->expects($this->at(1))->method('getValue')
            ->with('epp')->willReturn(10);
        $this->template->expects($this->at(2))->method('getValue')
            ->with('total')->willReturn(10);
        $this->template->expects($this->at(3))->method('getValue')
            ->with('page')->willReturn(1);
        $this->template->expects($this->at(4))->method('getValue')
            ->with('epp')->willReturn(10);
        $this->template->expects($this->at(5))->method('getValue')
            ->with('total')->willReturn(30);

        $this->assertFalse(has_pagination_next_page());
        $this->assertTrue(has_pagination_next_page());
    }

    /**
     * Tests has_pagination_previous_page.
     */
    public function testHasPaginationPrevPage()
    {
        $this->template->expects($this->at(0))->method('getValue')
            ->with('page')->willReturn(null);
        $this->template->expects($this->at(1))->method('getValue')
            ->with('page')->willReturn(1);
        $this->template->expects($this->at(2))->method('getValue')
            ->with('page')->willReturn(4);

        $this->assertFalse(has_pagination_previous_page());
        $this->assertFalse(has_pagination_previous_page());
        $this->assertTrue(has_pagination_previous_page());
    }
}
