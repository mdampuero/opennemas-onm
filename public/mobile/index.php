<?php
require('../bootstrap.php');
require('../../vendor/UrlDispatcher.php');
define('BASE_PATH', '/mobile');

$routes = array(

    /* Últimas noticias */
    array(
        'regexp'  => array(
                           '%^ultimas\-noticias/$%i',
                           '%^ultimas/$%i',
                          ),
        'handler' => 'MobileRouter::lastestsNews',
    ),

    /* Section opinion */
    array(
        'regexp'  => array(
                           '%^seccion/opinion/$%i',
                           '%^opinions/$%i',
                          ),
        'handler' => 'MobileRouter::frontpageOpinion',
    ),

    /* Sections */
    array(
        'regexp'  => array(
                           '%^seccion/(?P<category_name>[a-z0-9\-\._]+)/$%i',
                           '%^seccion/(?P<category_name>[a-z0-9\-\._]+)/(?P<subcategory_name>[a-z0-9\-\._]+)/$%i',
                          ),
        'handler' => 'MobileRouter::section',
    ),

    /* Article */
    array(
        'regexp'  => '%^articulo/.*?(?P<pk_content>[0-9]+)\.html$%',
        'handler' => 'MobileRouter::article',
    ),

    /* Article */
    array(
        'regexp'  => '%^artigo/.*?(?P<pk_content>[0-9]+)\.html$%',
        'handler' => 'MobileRouter::article',
    ),


    /* Opinion */
    array(
        'regexp'  => '%^opinion/.*?(?P<pk_content>[0-9]+)\.html$%',
        'handler' => 'MobileRouter::opinion',
    ),

    array(
        'regexp'  => '%^redirectWeb/$%',
        'handler' => 'MobileRouter::redirectWeb',
    ),

    /* Index */
    array(
        'regexp'  => array(
                           '%^$%',
                           '%^index$%',
                           '%^index\.(php|htm|html)$%',
                          ),
        'handler' => 'MobileRouter::section',
    ),

    /* Otherwise, dispatcher log */
    array(
        'regexp'  => '%^(.*)$%',
        'handler' => 'MobileRouter::log',
    ),
);

/* HANDLERS DAS PETICIÓNS, código que se vai executar ligado a petición que se faga ************************* */
class MobileRouter
{

    public static function frontpageOpinion()
    {
        require './opinion-index.php';
    }

    public static function opinion()
    {
        require './opinion-inner.php';
    }

    public static function section()
    {
        require './frontpage.php';
    }

    public static function article()
    {
        require './article-inner.php';
    }

    public static function lastestsNews()
    {
        require './lastest-news.php';
    }

    public static function redirectWeb()
    {
        setcookie("confirm_mobile", "1", time()+3600, '/');
        Application::forward('/');
    }

    public static function log()
    {
        require_once '../../vendor/core/Application.php';

        header('Location: /mobile/');
        exit(0);
    }
}

$router = new URLDispatcher($routes);
$router->run();

