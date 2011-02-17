<?php
require('../libs/urldispatcher.class.php');
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
        'handler' => 'MobileRouter::frontpage_opinion',
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


    /* Opinion */
    array(
        'regexp'  => '%^opinions/.*?(?P<pk_content>[0-9]+)\.html$%',
        'handler' => 'MobileRouter::opinion',
    ),

    array(
        'regexp'  => '%^redirect_web/$%',
        'handler' => 'MobileRouter::redirect_web',
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
class MobileRouter {

    static function frontpage_opinion() {
        require './opinion-index.php';
    }

    static function opinion() {
        require './opinion-inner.php';
    }

    static function section() {
        require './frontpage.php';
    }

    static function article() {
        require './article-inner.php';
    }

    static function lastestsNews() {
        require './lastest-news.php';
    }

    static function redirect_web() {
        require_once('../core/application.class.php');
        setcookie("confirm_mobile", "1", time()+3600, '/');
        Application::forward('/');
    }

    static function log() {
        require_once('../core/application.class.php');
    }
}

$router = new URLDispatcher( $routes );
$router->run();


//header('Location: /');
//exit(0);
