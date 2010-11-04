<?php

/**
 * Start up and setup the app
*/
require_once('../bootstrap.php');

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
