<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\Tests\ORM\FreshBooks\Repository;

use Common\ORM\FreshBooks\Repository\InvoiceRepository;
use Freshbooks\FreshBooksApi;

class InvoiceRepositoryTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->api = $this->getMockBuilder('Freshbooks\FreshBooksApi')
            ->disableOriginalConstructor()
            ->getMock();

        $this->api->method('setMethod')->willReturn(true);
        $this->api->method('post')->willReturn(true);

        $this->repository = new InvoiceRepository('foo', 'bar');

        $property = new \ReflectionProperty($this->repository, 'api');
        $property->setAccessible(true);
        $property->setValue($this->repository, $this->api);
    }

    /**
     * Tests countBy.
     *
     * @expectedException \Exception
     */
    public function testCountBy()
    {
        $this->repository->countBy();
    }

    /**
     * Tests find when API call fails.
     *
     * @expectedException Common\ORM\Core\Exception\EntityNotFoundException
     */
    public function testFindWithInvalidId()
    {
        $this->api->method('success')->willReturn(false);

        $this->api->expects($this->once())->method('setMethod')
            ->with('invoice.get');

        $this->repository->find('1');
    }

    /**
     * Tests find when API returns a valid result.
     */
    public function testFindWithValidId()
    {
        $invoice = [
            'invoice_id' => '1',
            'number'     => '1',
            'client_id'  => '1',
            'lines' => [
                'line' => []
            ]
        ];

        $response = [
            '@attributes' => [ 'status' => 'ok' ],
            'invoice'     => $invoice,
        ];

        $this->api->method('success')->willReturn(true);
        $this->api->method('getResponse')->willReturn($response);

        $this->api->expects($this->once())->method('setMethod')
            ->with('invoice.get');

        $this->api->expects($this->once())->method('post');
        $this->api->expects($this->once())->method('success');
        $this->api->expects($this->once())->method('getResponse');

        $this->assertEquals($invoice, $this->repository->find('1')->getData());
    }

    /**
     * Tests findBy when the search criteria is invalid.
     *
     * @expectedException Common\ORM\Core\Exception\InvalidCriteriaException
     */
    public function testFindByWithInvalidCriteria()
    {
        $criteria = [ 'invalid_field' => 'johndoe@example.org' ];

        $this->api->method('success')->willReturn(false);

        $this->api->expects($this->once())->method('setMethod')
            ->with('invoice.list');

        $this->api->expects($this->once())->method('post');
        $this->api->expects($this->once())->method('success');

        $this->repository->findBy($criteria);
    }

    /**
     * Tests findBy when API returns multiple results.
     */
    public function testFindByWithValidCriteriaMultipleResults()
    {
        $criteria = [ 'email' => 'johndoe@example.org' ];

        $invoices = [
            [
                'invoice_id' => '1',
                'number'     => '1',
                'client_id'  => '1',
                'lines' => [
                    'line' => []
                ]
            ],
            [
                'invoice_id' => '2',
                'number'     => '2',
                'client_id'  => '1',
                'lines' => [
                    'line' => []
                ]
            ]
        ];

        $response = [
            '@attributes' => [ 'status' => 'ok' ],
            'invoices'    => [
                '@attributes' => [ 'page' => 1, 'total' => 2 ],
                'invoice'     => $invoices
            ]

        ];

        $this->api->method('success')->willReturn(true);
        $this->api->method('getResponse')->willReturn($response);


        $this->api->expects($this->once())->method('setMethod')
            ->with('invoice.list');

        $this->api->expects($this->once())->method('post');
        $this->api->expects($this->once())->method('success');
        $this->api->expects($this->once())->method('getResponse');

        $response = $this->repository->findBy($criteria);

        $this->assertEquals(count($invoices), count($response));

        for ($i = 0; $i < count($response); $i++) {
            $this->assertEquals($invoices[$i], $response[$i]->getData());
        }
    }

    /**
     * Tests findBy when API returns one result.
     */
    public function testFindByWithValidCriteriaOneResult()
    {
        $criteria = [ 'email' => 'johndoe@example.org' ];

        $invoices = [
            'invoice_id' => '1',
            'number'     => '1',
            'client_id'  => '1',
            'lines' => [
                'line' => []
            ]
        ];

        $response = [
            '@attributes' => [ 'status' => 'ok' ],
            'invoices'    => [
                '@attributes' => [ 'page' => 1, 'total' => 1 ],
                'invoice'     => $invoices
            ]
        ];

        $this->api->method('success')->willReturn(true);
        $this->api->method('getResponse')->willReturn($response);

        $this->api->expects($this->once())->method('setMethod')
            ->with('invoice.list');

        $this->api->expects($this->once())->method('post');
        $this->api->expects($this->once())->method('success');
        $this->api->expects($this->once())->method('getResponse');

        $response = $this->repository->findBy($criteria);

        $this->assertEquals(1, count($response));
        $this->assertEquals($invoices, $response[0]->getData());
    }

    /**
     * Tests findOneBy.
     *
     * @expectedException \Exception
     */
    public function testFindOneBy()
    {
        $this->repository->findOneBy();
    }

    /**
     * Tests getPdf when searched the entity is not found.
     *
     * @expectedException Common\ORM\Core\Exception\EntityNotFoundException
     */
    public function testGetPdfWithInvalidId()
    {
        $this->api->method('success')->willReturn(false);

        $this->api->expects($this->once())->method('setMethod')
            ->with('invoice.getPDF');

        $this->repository->getPdf('1');
    }

    /**
     * Tests getPdf when the API returns a valid result.
     */
    public function testGetPdfWithValidId()
    {
        $response = '%PDF-1.4....';

        $this->api->method('success')->willReturn(true);
        $this->api->method('getResponse')->willReturn($response);

        $this->api->expects($this->once())->method('setMethod')
            ->with('invoice.getPDF');

        $this->api->expects($this->once())->method('post');
        $this->api->expects($this->once())->method('success');
        $this->api->expects($this->once())->method('getResponse');

        $this->assertEquals(strpos($this->repository->getPdf('1'), '%PDF-1.4'), 0);
    }
}
