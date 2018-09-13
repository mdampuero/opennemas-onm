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

use Common\ORM\Core\Metadata;
use Common\ORM\Entity\Invoice;
use Common\ORM\FreshBooks\Repository\InvoiceRepository;

class InvoiceRepositoryTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->api = $this->getMockBuilder('Freshbooks\FreshBooksApi')
            ->disableOriginalConstructor()
            ->getMock();

        $this->api->method('setMethod')->willReturn(true);
        $this->api->method('post')->willReturn(true);

        $this->metadata = new Metadata([
            'properties' => [
                'id'        => 'integer',
                'client_id' => 'integer',
                'date'      => 'datetime',
                'status'    => 'string',
                'lines'     => 'array'
            ],
            'mapping' => [
                'freshbooks' => [
                    'id'        => [ 'name' => 'invoice_id', 'type' => 'string' ],
                    'client_id' => [ 'name' => 'client_id', 'type' => 'string' ],
                    'date'      => [ 'name' => 'date', 'type' => 'datetime' ],
                    'status'    => [ 'name' => 'status', 'type' => 'string' ],
                    'lines'     => [ 'name' => 'lines', 'type' => 'array' ],
                ]
            ],
        ]);

        $this->repository = new InvoiceRepository('flob', 'foo', 'bar', $this->metadata);

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
     * @expectedException \Common\ORM\Core\Exception\EntityNotFoundException
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
        $invoice = new Invoice([
            'id'        => 1,
            'client_id' => 1,
            'lines'     => []
        ]);

        $response = [
            '@attributes' => [ 'status' => 'ok' ],
            'invoice'     => [
                'invoice_id' => '1',
                'client_id'  => '1',
                'lines' => [
                    'line' => []
                ]
            ]
        ];

        $this->api->method('success')->willReturn(true);
        $this->api->method('getResponse')->willReturn($response);

        $this->api->expects($this->once())->method('setMethod')
            ->with('invoice.get');

        $this->api->expects($this->once())->method('post');
        $this->api->expects($this->once())->method('success');
        $this->api->expects($this->once())->method('getResponse');

        $this->assertEquals($invoice, $this->repository->find('1'));
    }

    /**
     * Tests findBy when the search criteria is invalid.
     *
     * @expectedException \Common\ORM\Core\Exception\InvalidCriteriaException
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
            new Invoice([
                'id'        => 1,
                'client_id' => 1,
                'lines'     => []
            ]),
            new Invoice([
                'id'        => 2,
                'client_id' => 1,
                'lines'     => []
            ])
        ];

        $response = [
            '@attributes' => [ 'status' => 'ok' ],
            'invoices'    => [
                '@attributes' => [ 'page' => 1, 'total' => 2 ],
                'invoice'     => [
                    [
                        'invoice_id' => '1',
                        'client_id'  => '1',
                        'lines' => [
                            'line' => []
                        ]
                    ],
                    [
                        'invoice_id' => '2',
                        'client_id'  => '1',
                        'lines' => [
                            'line' => []
                        ]
                    ]
                ]
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
            $this->assertEquals($invoices[$i], $response[$i]);
        }
    }

    /**
     * Tests findBy when API returns one result.
     */
    public function testFindByWithValidCriteriaOneResult()
    {
        $criteria = [ 'email' => 'johndoe@example.org' ];

        $invoice = new Invoice([
            'id'        => 1,
            'client_id' => 1,
            'lines'     => []
        ]);

        $response = [
            '@attributes' => [ 'status' => 'ok' ],
            'invoices'    => [
                '@attributes' => [ 'page' => 1, 'total' => 1 ],
                'invoice'     => [
                    'invoice_id' => '1',
                    'number'     => '1',
                    'client_id'  => '1',
                    'lines' => [
                        'line' => []
                    ]
                ]
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
        $this->assertEquals($invoice, $response[0]);
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
     * @expectedException \Common\ORM\Core\Exception\EntityNotFoundException
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
