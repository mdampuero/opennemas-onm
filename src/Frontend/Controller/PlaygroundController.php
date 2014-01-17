<?php
/**
 * Playground where to test new functions
 *
 * @package Frontend_Controllers
 **/
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Frontend\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Onm\Framework\Controller\Controller;
use Onm\Message as m;
use Onm\Settings as s;

/**
 * Playground where to test new functions
 *
 * @package Frontend_Controllers
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
     * Dispatches the actions through the rest of methods in this class
     *
     * @param Request $request the request object
     *
     * @return Response the response object
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
     * Tests for memcache service
     *
     * @param Request $request the request object
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

                $cdump = @$memcache->getExtendedStats('cachedump', (int)$slabId);
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
     * Tests for logger service
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function logger(Request $request)
    {

        $logger = $this->get('logger');

        $logger->notice('texto de prueba');
        $logger->error('error de prueba');

        var_dump($logger);
    }

    /**
     * tests for mailer service
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function mailer(Request $request)
    {
        // Pass it as a parameter when you create the message
        $message = \Swift_Message::newInstance('Subject here', 'My amazing body');

        $message->setFrom('fran@openhost.es');

        // Or set it after like this
        $message->setBody('My <em>amazing</em> body', 'text/html');

        // Add alternative parts with addPart()
        $message->addPart('My amazing body in plain text', 'text/plain');

        $message->setTo(array('fran@openhost.es' => 'Fran Dieguez',));

        // var_dump($message);die();

        $mailer = $this->get('mailer');

        $mailer->send($message);

        var_dump($mailer);
    }

    /**
     * Tests for session in container
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function session(Request $request)
    {
        $session = $this->get('session');
        $session->start();
        $this->get('session')->getFlashBag()->add(
            'notice',
            'Your changes were saved!'
        );

        foreach ($session->getFlashBag()->get('notice', array()) as $message) {
            echo "<div class='flash-notice'>$message</div>";
        }
    }
}
