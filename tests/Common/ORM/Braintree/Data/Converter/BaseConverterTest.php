<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\ORM\Braintree\Data\Converter;

use Common\ORM\Core\Metadata;
use Common\ORM\Entity\Client;
use Common\ORM\Braintree\Data\Converter\BaseConverter;

/**
 * Defines test cases for BaseConverter class.
 */
class BaseConverterTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the test environment.
     */
    public function setUp()
    {
        $this->metadata = new Metadata([
            'properties' => [
                'id'         => 'integer',
                'first_name' => 'string',
                'last_name'  => 'string',
                'email'      => 'string',
                'company'    => 'string',
                'phone'      => 'string',
            ],
            'mapping' => [
                'braintree' => [
                    'id'         => [ 'name' => 'id', 'type' => 'string' ],
                    'first_name' => [ 'name' => 'firstName', 'type' => 'string' ],
                    'last_name'  => [ 'name' => 'lastName', 'type' => 'string' ],
                    'email'      => [ 'name' => 'email', 'type' => 'string' ],
                    'company'    => [ 'name' => 'company', 'type' => 'string' ],
                    'phone'      => [ 'name' => 'phone', 'type' => 'string' ],
                ]
            ],
        ]);

        $this->converter = new BaseConverter($this->metadata);
    }

    /**
     * Tests braintreefy when empty metadata provided.
     *
     * @expectedException \Exception
     */
    public function testBraintreefyInvalid()
    {
        $converter = new BaseConverter(new Metadata([]));
        $converter->braintreefy([]);
    }

    /**
     * Tests braintreefy when valid metadata provided.
     */
    public function testBraintreefyValid()
    {
        $original = [
            'id'        => 1,
            'first_name' => 'John',
            'last_name'  => 'Doe',
            'email'     => 'johndoe@example.org',
            'company'   => 'John Doe, Inc.',
            'phone'     => '555-555-555',
        ];

        $converted = [
            'id'        => 1,
            'firstName' => 'John',
            'lastName'  => 'Doe',
            'email'     => 'johndoe@example.org',
            'company'   => 'John Doe, Inc.',
            'phone'     => '555-555-555',
        ];

        $this->assertEquals($converted, $this->converter->braintreefy($original));
        $this->assertEquals($converted, $this->converter->braintreefy(new Client($original)));
    }

    /**
     * Tests objectify for data from braintree.
     */
    public function testObjectifyWhenNoMappingInformation()
    {
        $client = json_decode(json_encode([
            'id'        => 1,
            'firstName' => 'John',
            'lastName'  => 'Doe',
            'email'     => 'johndoe@example.org',
            'company'   => 'John Doe, Inc.',
            'phone'     => '555-555-555',
        ]));

        $converter = new BaseConverter(new Metadata([ 'mapping' => [] ]));

        $this->assertEquals($client, $converter->objectify($client));
    }

    /**
     * Tests objectify for data from braintree.
     */
    public function testObjectify()
    {
        $client = json_decode(json_encode([
            'id'        => 1,
            'firstName' => 'John',
            'lastName'  => 'Doe',
            'email'     => 'johndoe@example.org',
            'company'   => 'John Doe, Inc.',
            'phone'     => '555-555-555',
        ]));

        $this->assertEquals(
            [
                'id'        => 1,
                'first_name' => 'John',
                'last_name'  => 'Doe',
                'email'     => 'johndoe@example.org',
                'company'   => 'John Doe, Inc.',
                'phone'     => '555-555-555',
            ],
            $this->converter->objectify($client)
        );
    }
}
