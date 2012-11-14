<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Frontend\Controllers;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Onm\Framework\Controller\Controller;
use Onm\Message as m;
use Onm\Settings as s;

/**
 * Handles the actions for the system information
 *
 * @package Backend_Controllers
 **/
class PlaygroundController extends Controller
{
    /**
     * Common code for all the actions
     *
     * @return void
     **/
    public function init()
    {
        $this->view = new \TemplateAdmin(TEMPLATE_ADMIN);
    }

    /**
     * Some tests for Memcache
     *
     * @return void
     * @author
     **/
    public function defaultAction($request)
    {
        $action = $request->query->get('action', null);

        if (!is_null($action)) {
            $response = $this->{$action}($request);
            return new Response($response, 200);
        } else {
            return new Response('not valid action', 400);
        }

    }

    /**
     * Tests for memcache
     *
     * @return Response the response object
     **/
    protected function cache($request)
    {
        $cache = $this->container->get('cache');
        $cache->save(time(), 1223);
        $cache->deleteAll();

        $list = array();
        $memcache = new \Memcache();
        $memcache->addServer('localhost', 11211);
        $allSlabs = @$memcache->getExtendedStats('slabs');
        $items = @$memcache->getExtendedStats('items');

        foreach ($allSlabs as $server => $slabs) {
            foreach ($slabs as $slabId => $slabMeta) {

                $cdump = @$memcache->getExtendedStats('cachedump',(int)$slabId);
                foreach ($cdump as $server => $entries) {
                    if ($entries) {
                        foreach ($entries as $eName => $eData) {
                            $list[$eName] = array(
                                'key'    => $eName,
                                'server' => $server,
                                'slabId' => $slabId,
                                'detail' => $eData,
                                'age'    => $items[$server]['items'][$slabId]['age'],
                            );
                        }
                    }
                }
            }
        }
        ksort($list);

        var_dump($list);
        die();
    }

    /**
     * Playground for redis testing
     *
     * @return Response the response instance
     **/
    public function redis()
    {
        $cache = $this->container->get('cache');
        $cache->save('test', 'hola');
        var_dump($cache->fetch('test', 'hola'));die();

    }

}
