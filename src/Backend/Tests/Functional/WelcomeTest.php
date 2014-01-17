<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Backend\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DemoControllerTest extends WebTestCase
{
    public function testIndex()
    {
        // $client = static::createClient();
        // $crawler = $client->request('GET', '/admin', array(), array(), array(
        //     'HTTP_HOST'       => 'opennemas.onm:8080',
        //     'HTTP_USER_AGENT' => 'Symfony/2.0',
        // ));
        // var_dump($crawler);die();

        // $this->assertGreaterThan(
        //     0,
        //     $crawler->filter('html:contains("Hello Fabien")')->count()
        // );
    }
}
