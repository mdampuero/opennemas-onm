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
use Common\ORM\Entity\Client;
use Common\ORM\FreshBooks\Repository\ClientRepository;

/**
 * Defines test cases for ClientRepository class.
 */
class ClientRepositoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Configures the test environment.
     */
    public function setUp()
    {
        $this->api = $this->getMockBuilder('Freshbooks\FreshBooksApi')
            ->disableOriginalConstructor()
            ->getMock();

        $this->api->method('setMethod')->willReturn(true);
        $this->api->method('post')->willReturn(true);

        $this->metadata = new Metadata([
            'properties' => [
                'id'          => 'integer',
                'first_name'  => 'string',
                'last_name'   => 'string',
                'email'       => 'string',
                'company'     => 'string',
                'phone'       => 'string',
                'address'     => 'string',
                'postal_code' => 'string',
                'city'        => 'string',
                'state'       => 'string',
                'country'     => 'string',
            ],
            'mapping' => [
                'freshbooks' => [
                    'id'          => [ 'name' => 'client_id', 'type' => 'string' ],
                    'first_name'  => [ 'name' => 'first_name', 'type' => 'string' ],
                    'last_name'   => [ 'name' => 'last_name', 'type' => 'string' ],
                    'email'       => [ 'name' => 'email', 'type' => 'string' ],
                    'company'     => [ 'name' => 'organization', 'type' => 'string' ],
                    'phone'       => [ 'name' => 'work_phone', 'type' => 'string' ],
                    'address'     => [ 'name' => 'p_street1', 'type' => 'string' ],
                    'postal_code' => [ 'name' => 'p_code', 'type' => 'string' ],
                    'city'        => [ 'name' => 'p_city', 'type' => 'string' ],
                    'state'       => [ 'name' => 'p_state', 'type' => 'string' ],
                    'country'     => [ 'name' => 'p_country', 'type' => 'string' ],
                ]
            ],
        ]);

        $this->repository = new ClientRepository('flob', 'foo', 'bar', $this->metadata);

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
            ->with('client.get');

        $this->repository->find('1');
    }

    /**
     * Tests find when API returns a valid result.
     */
    public function testFindWithValidId()
    {
        $client = new Client([
            'id'         => 1,
            'first_name' => 'John',
            'last_name'  => 'Doe'
        ]);

        $response = [
            '@attributes' => [ 'status' => 'ok' ],
            'client'      => [
                'client_id'  => '1',
                'first_name' => 'John',
                'last_name'  => 'Doe'
            ],
        ];

        $this->api->method('success')->willReturn(true);
        $this->api->method('getResponse')->willReturn($response);

        $this->api->expects($this->once())->method('setMethod')
            ->with('client.get');

        $this->api->expects($this->once())->method('post');
        $this->api->expects($this->once())->method('success');
        $this->api->expects($this->once())->method('getResponse');

        $this->assertEquals($client, $this->repository->find('1'));
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
            ->with('client.list');

        $this->api->expects($this->once())->method('post');
        $this->api->expects($this->once())->method('success');

        $this->repository->findBy($criteria);
    }

    /*
     * Tests findBy when API returns multiple results.
     */
    public function testFindByWithValidCriteriaMultipleResults()
    {
        $criteria = [ 'email' => 'johndoe@example.org' ];

        $clients = [
            new Client([
                'id'         => 1,
                'first_name' => 'John',
                'last_name'  => 'Doe',
                'email'      => 'johndoe@example.org'
            ]),
            new Client([
                'id'         => '2',
                'first_name' => 'Jane',
                'last_name'  => 'Doe',
                'email'      => 'janedoe@example.org'
            ])
        ];

        $response = [
            '@attributes' => [ 'status' => 'ok' ],
            'clients'     => [
                '@attributes' => [ 'page' => 1, 'total' => 2 ],
                'client'      => [
                    [
                        'client_id'  => '1',
                        'first_name' => 'John',
                        'last_name'  => 'Doe',
                        'email'      => 'johndoe@example.org'
                    ],
                    [
                        'client_id'  => '2',
                        'first_name' => 'Jane',
                        'last_name'  => 'Doe',
                        'email'      => 'janedoe@example.org'
                    ]
                ]
            ]
        ];

        $this->api->method('success')->willReturn(true);
        $this->api->method('getResponse')->willReturn($response);

        $this->api->expects($this->once())->method('setMethod')
            ->with('client.list');

        $this->api->expects($this->once())->method('post');
        $this->api->expects($this->once())->method('success');
        $this->api->expects($this->once())->method('getResponse');

        $response = $this->repository->findBy($criteria);

        $this->assertEquals(count($clients), count($response));

        $response = array_values($response);
        for ($i = 0; $i < count($response); $i++) {
            $this->assertEquals($clients[$i], $response[$i]);
        }
    }

    /**
     * Tests findBy when API returns one result.
     */
    public function testFindByWithValidCriteriaOneResult()
    {
        $criteria = [ 'email' => 'johndoe@example.org' ];

        $client = new Client([
            'id'         => 1,
            'first_name' => 'John',
            'last_name'  => 'Doe',
            'email'      => 'johndoe@example.org'
        ]);

        $response = [
            '@attributes' => [ 'status' => 'ok' ],
            'clients'     => [
                '@attributes' => [ 'page' => 1, 'total' => 1 ],
                'client'      => [
                    'client_id'  => '1',
                    'first_name' => 'John',
                    'last_name'  => 'Doe',
                    'email'      => 'johndoe@example.org'
                ]
            ]
        ];

        $this->api->method('success')->willReturn(true);
        $this->api->method('getResponse')->willReturn($response);

        $this->api->expects($this->once())->method('setMethod')
            ->with('client.list');

        $this->api->expects($this->once())->method('post');
        $this->api->expects($this->once())->method('success');
        $this->api->expects($this->once())->method('getResponse');

        $response = $this->repository->findBy($criteria);

        $this->assertEquals(1, count($response));
        $this->assertEquals($client, $response[0]);
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
}
