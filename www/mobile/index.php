<?php
require('../libs/urldispatcher.class.php');
define('BASE_URL', '/mobile'); 

$routes = array(
    
    /* Últimas noticias */
    array(
        'regexp'  => array(
                           '%^ultimas\-noticias/$%i',
                           '%^ultimas/$%i',
                          ),
        'handler' => 'MobileRouter::ultimas_noticias',
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
        'regexp'  => '%^artigo/.*?(?P<pk_content>[0-9]+)\.html$%',
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
        'handler' => 'MobileRouter::index',
    ),        
    
    /* Otherwise, dispatcher log */
    array(
        'regexp'  => '%^(.*)$%',
        'handler' => 'MobileRouter::log', 
    ),
);

/* HANDLERS DAS PETICIÓNS, código que se vai executar ligado a petición que se faga ************************* */
class MobileRouter {    
    
    function index() {
        require './portada.php';        
    }
    
    function frontpage_opinion() {        
        require './opinion_index.php';
    }
    
    function section() {
        require './portada.php'; 
    }
    
    function article() {
        require './article.php';        
    }
    
    function opinion() {
        require './opinion.php';        
    }
    
    function ultimas_noticias() {
        require './ultimas_noticias.php';
    }
    
    function redirect_web() {
        require_once('../core/application.class.php');
        setcookie("confirm_mobile", "1", time()+3600, '/');
        Application::forward('/');
    }
    
    function log() {
        require_once('../core/application.class.php');
        
    }
}

//$router = new URLDispatcher( $routes ); 
//$router->run();

header('Location: /');
exit(0);