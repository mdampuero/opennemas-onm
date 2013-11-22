<?php
/**
 * Handles the actions for advertisements
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
namespace Frontend\Controllers;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Onm\Framework\Controller\Controller;
use Onm\Message as m;
use Onm\Settings as s;

/**
 * Handles the actions for advertisements
 *
 * @package Frontend_Controllers
 **/
class UtilitiesController extends Controller
{
    /**
     * Common code for all the actions
     *
     * @return void
     **/
    public function init()
    {
        $this->view = new \Template(TEMPLATE_USER);
    }

    /**
     * Displays an advertisement given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function acceptCookiesAction(Request $request)
    {
        $response = new Response('ok');
        $response->headers->setCookie(new \Symfony\Component\HttpFoundation\Cookie('cookieoverlay_accepted', true));

        return $response;
    }

    /**
     * Integrates the sharrre jQuery plugin into ONM
     *
     * @return Response the response object
     **/
    public function sharrreAction(Request $request)
    {
        $json = array(
            'url'=> '',
            'count' => 0
        );

        $json['url'] = $request->query->filter('url', '', FILTER_SANITIZE_STRING);
        $url = urlencode($request->query->filter('url', '', FILTER_SANITIZE_STRING));
        $type = urlencode($request->query->filter('type', '', FILTER_SANITIZE_STRING));

        if ($json['url']) {
            if ($type == 'googlePlus') {
                //source http://www.helmutgranda.com/2011/11/01/get-a-url-google-count-via-php/
                $content = $this->createCurlRequest("https://plusone.google.com/u/0/_/+1/fastbutton?url=".$url."&count=true");

                $dom = new \DOMDocument;
                $dom->preserveWhiteSpace = false;
                @$dom->loadHTML($content);
                $domxpath = new \DOMXPath($dom);
                $newDom = new \DOMDocument;
                $newDom->formatOutput = true;

                $filtered = $domxpath->query("//div[@id='aggregateCount']");
                if (isset($filtered->item(0)->nodeValue)) {
                    $json['count'] = str_replace('>', '', $filtered->item(0)->nodeValue);
                }

            } elseif ($type == 'stumbleupon') {
                $content = $this->createCurlRequest("http://www.stumbleupon.com/services/1.01/badge.getinfo?url=$url");

                $result = json_decode($content);
                if (isset($result->result->views)) {
                    $json['count'] = $result->result->views;
                }

            }
        }

        $content  = str_replace('\\/', '/', json_encode($json));

        return new Response(
            $content,
            200,
            array('Content-Type' => 'application/json')
        );
    }

    /**
     * Function used in sharrre actoin
     *
     * @param string $encUrl the url to fetch
     *
     * @return string the HTML content of the url
     **/
    private function createCurlRequest($encUrl)
    {
        $options = array(
            CURLOPT_RETURNTRANSFER => true, // return web page
            CURLOPT_HEADER => false, // don't return headers
            CURLOPT_FOLLOWLOCATION => true, // follow redirects
            CURLOPT_ENCODING => "", // handle all encodings
            CURLOPT_USERAGENT => 'sharrre', // who am i
            CURLOPT_AUTOREFERER => true, // set referer on redirect
            CURLOPT_CONNECTTIMEOUT => 5, // timeout on connect
            CURLOPT_TIMEOUT => 10, // timeout on response
            CURLOPT_MAXREDIRS => 3, // stop after 10 redirects
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => false,
        );
        $ch = curl_init();

        $options[CURLOPT_URL] = $encUrl;
        curl_setopt_array($ch, $options);

        $content = curl_exec($ch);
        $err = curl_errno($ch);
        $errmsg = curl_error($ch);

        curl_close($ch);

        if ($errmsg != '' || $err != '') {
            /*print_r($errmsg);
            print_r($errmsg);*/
        }

        return $content;
    }
}
