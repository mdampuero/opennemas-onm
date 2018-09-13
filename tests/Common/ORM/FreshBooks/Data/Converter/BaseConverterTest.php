<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\ORM\FreshBooks\Data\Converter;

use Common\ORM\Core\Metadata;
use Common\ORM\FreshBooks\Data\Converter\BaseConverter;

/**
 * Defines test cases for BaseConverter class.
 */
class BaseConverterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Configures the test environment.
     */
    public function setUp()
    {
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
                    'id'          => [ 'name' => 'id', 'type' => 'string' ],
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

        $this->converter = new BaseConverter($this->metadata);
    }

    /**
     * Tests freshbooksfy when empty metadata provided.
     *
     * @expectedException \Exception
     */
    public function testFreshbooksfyInvalid()
    {
        $converter = new BaseConverter(new Metadata([]));
        $converter->freshbooksfy([]);
    }

    /**
     * Tests freshbooksfy when valid metadata provided.
     */
    public function testFreshbooksfyValid()
    {
        $this->assertEquals(
            [
                'id'           => 1,
                'first_name'   => 'John',
                'last_name'    => 'Doe',
                'email'        => 'johndoe@example.org',
                'organization' => 'John Doe, Inc.',
                'work_phone'   => '555-555-555',
                'p_country'    => 'Spain'
            ],
            $this->converter->freshbooksfy([
                'id'        => 1,
                'first_name' => 'John',
                'last_name'  => 'Doe',
                'email'     => 'johndoe@example.org',
                'company'   => 'John Doe, Inc.',
                'phone'     => '555-555-555',
                'country'   => 'ES'
            ])
        );
    }

    /**
     * Tests objectify for data from FreshBooks.
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
     * Tests objectify for data from FreshBooks.
     */
    public function testObjectify()
    {
        $client = [
            'id'           => 1,
            'first_name'   => 'John',
            'last_name'    => 'Doe',
            'email'        => 'johndoe@example.org',
            'organization' => 'John Doe, Inc.',
            'work_phone'   => '555-555-555',
            'p_street1'    => 'Fake Street',
            'p_code'       => '12345',
            'p_city'       => 'Bar',
            'p_state'      => 'Fred',
            'p_country'    => 'Spain'
        ];

        $this->assertEquals(
            [
                'id'          => 1,
                'first_name'  => 'John',
                'last_name'   => 'Doe',
                'email'       => 'johndoe@example.org',
                'company'     => 'John Doe, Inc.',
                'phone'       => '555-555-555',
                'address'     => 'Fake Street',
                'postal_code' => '12345',
                'city'        => 'Bar',
                'state'       => 'Fred',
                'country'     => 'ES'
            ],
            $this->converter->objectify($client)
        );
    }

    /**
     * Tests normalizeCountry.
     */
    public function testNormalizeCountry()
    {
        $method = new \ReflectionMethod($this->converter, 'normalizeCountry');
        $method->setAccessible(true);

        $this->assertEquals(
            [ 'country' => 'Spain' ],
            $method->invokeArgs($this->converter, [ [ 'country' => 'ES' ] ])
        );
    }

    /**
     * Tests normalizeLines.
     */
    public function testNormalizeLines()
    {
        $method = new \ReflectionMethod($this->converter, 'normalizeLines');
        $method->setAccessible(true);

        $this->assertEquals(
            [ 'lines' => [ 'line' => [ [] ] ] ],
            $method->invokeArgs($this->converter, [ [ 'lines' => [] ] ])
        );
    }

    /**
     * Tests normalizeType.
     */
    public function testNormalizeType()
    {
        $method = new \ReflectionMethod($this->converter, 'normalizeType');
        $method->setAccessible(true);

        $this->assertEquals(
            [ 'type' => 'Credit Card' ],
            $method->invokeArgs($this->converter, [ [ 'type' => 'CreditCard' ] ])
        );

        $this->assertEquals(
            [ 'type' => 'PayPal' ],
            $method->invokeArgs($this->converter, [ [ 'type' => 'PayPalAccount' ] ])
        );

    }

    /**
     * Tests unNormalizeCountry.
     */
    public function testUnNormalizeCountry()
    {
        $method = new \ReflectionMethod($this->converter, 'unNormalizeCountry');
        $method->setAccessible(true);

        $this->assertEquals(
            [ 'country' => 'ES' ],
            $method->invokeArgs($this->converter, [ [ 'country' => 'Spain' ] ])
        );
    }

    /**
     * Tests unNormalizeLines.
     */
    public function testUnNormalizeLines()
    {
        $method = new \ReflectionMethod($this->converter, 'unnormalizeLines');
        $method->setAccessible(true);

        $this->assertEquals(
            [ 'lines' => [] ],
            $method->invokeArgs($this->converter, [ [ 'lines' => [ 'line' => [] ] ] ])
        );
    }

    /**
     * Tests unNormalizeType.
     */
    public function testUnNormalizeType()
    {
        $method = new \ReflectionMethod($this->converter, 'unNormalizeType');
        $method->setAccessible(true);

        $this->assertEquals(
            [ 'type' => 'CreditCard' ],
            $method->invokeArgs($this->converter, [ [ 'type' => 'Credit Card' ] ])
        );

        $this->assertEquals(
            [ 'type' => 'PayPalAccount' ],
            $method->invokeArgs($this->converter, [ [ 'type' => 'PayPal' ] ])
        );

    }
}
