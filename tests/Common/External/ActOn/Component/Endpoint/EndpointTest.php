<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\External\ActOn\Endpoint;

use Common\External\ActOn\Component\Endpoint\Endpoint;

/**
 * Defines test cases for EmailCampaignEndpoint class.
 */
class EndpointTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->auth   = $this->getMockBuilder('Authentication')->getMock();
        $this->client = $this->getMockBuilder('HTTPClient')->getMock();

        $this->config = [
            'actions' => [
                'foo' => [
                    'parameters' => [
                        'required' => [ 'title', 'subject' ],
                        'optional' => [ 'body' ]
                    ]
                ]
            ]
        ];

        $this->endpoint = new Endpoint($this->auth, $this->client, 'flob');

        $this->endpoint->setConfiguration($this->config);
    }

    /**
     * Tests areParametersValid with valid and invalid values.
     */
    public function testAreParametersValid()
    {
        $this->assertFalse($this->endpoint->areParametersValid(null, 'foo'));
        $this->assertFalse($this->endpoint->areParametersValid(1, 'foo'));
        $this->assertFalse($this->endpoint->areParametersValid('wubble', 'foo'));
        $this->assertFalse($this->endpoint->areParametersValid([ 'foo'  ], 'foo'));
        $this->assertFalse($this->endpoint->areParametersValid([ 'foo' => 'frog' ], 'foo'));
        $this->assertFalse($this->endpoint->areParametersValid([ 'title' => 'frog' ], 'foo'));

        $this->assertTrue($this->endpoint->areParametersValid([
            'subject' => 'frog', 'title' => 'frog'
        ], 'foo'));

        $this->assertTrue($this->endpoint->areParametersValid([
            'subject' => 'frog', 'title' => 'frog', 'body' => 'norf'
        ], 'foo'));

        $this->assertFalse($this->endpoint->areParametersValid([
            'subject' => 'frog', 'title' => 'frog', 'foo' => 'norf'
        ], 'foo'));
    }

    /**
     * Tests getConfiguration.
     */
    public function testGetAndSetConfiguration()
    {
        $this->assertEquals($this->config, $this->endpoint->getConfiguration());

        $config = [ 'wibble' => 'flob' ];

        $this->endpoint->setConfiguration($config);
        $this->endpoint->setConfiguration(null);

        $this->assertEquals($config, $this->endpoint->getConfiguration());
    }
}
