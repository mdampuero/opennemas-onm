<?php
require('config.inc.php');
require_once('core/application.class.php');

Application::import_libs('*');
$app = Application::load();
require_once('core/method_cache_manager.class.php');
require_once('core/content_manager.class.php');
require_once('core/content.class.php');
require_once('core/content_category.class.php');

require_once('core/poll.class.php');
require_once('core/pollgraph.class.php');

// 2009022522002234689 prueba
$poll = new Poll( $_GET['id'] );
$items = $poll->get_items( $_GET['id'] );

foreach( $items as $item ) {
    $labels[] = $item['item'];
    $values[] = $item['votes'];
}

switch( $_GET['type'] ) {
    case 'v':
    case 'vertical':
        $params = array('title' => '',
                        'max_length_title' => 200, 
                        'width'   => 540,
                        'height'  => 230,
                        'margins' => array('vertical' => array( 4, 4, 20, 90)), // L, R, T, B
                        'labels'  => $labels,
                        'values'  => $values );
        
        $graph = new PollGraph();
        
        $graph->setOptions( $params );
        $graph->render('vertical');        
    break;

    case 'horizontal':
    case 'h':
    default:
        $params = array('title' => '',
                        'max_length_title' => 30, 
                        'width'   => 170,
                        'height'  => 170,
                        'margins' => array('horizontal' => array( 44, 20, 4, 4)), // L, R, T, B
                        'labels'  => $labels,
                        'values'  => $values );
        
        $graph = new PollGraph();
        
        $graph->setOptions( $params );
        $graph->render('horizontal');    
    break;
}
