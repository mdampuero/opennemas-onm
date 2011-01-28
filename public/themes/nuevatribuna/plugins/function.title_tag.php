<?php

/**
* Generates the html <title> tag from one array of values and its configuration
*
* <code>$titleValues = array(
*    'use_default_text' => true,
*    'default_text' => 'OpenNemas System',
*    'default_text_in_front' => false,
*    'parts_separator' => ' ',
*    'parts'=> array(
*                   
*                    array(
*                        'text' => 'One',
*                        'continue' => true
*                    ),
*                    array(
*                        'text' => 'Two',
*                        'continue' => false
*                    ),
*                    array(
*                        'text' => 'Third',
*                        'continue' => false
*                    )
*                   
*                   ),
* );</code>
* For append
* 
*
* @param mixed $titleValues, the array containing the parts of the title and some configuration
* @return type[, explanation]
* @throws ExceptionClass [description]
*/    
function smarty_function_title_tag($params, &$smarty) {
    
    
    $titleDefaultValues  = array(
        'use_default_text' => true,
        'default_text' => 'OpenNemas System',
        'default_text_in_front' => false,
        'parts_separator' => ' ',
    );

    
    // the html to print in the view
    $output = array();
    
    // title variables merged with default ones
    $finalTitleValues = array_merge($titleDefaultValues, $params['config'] );
    
    foreach($titleValues['parts']  as $part) {
        
        $output[] = $part['text']; 
        
        // don't continue if this parts tells it explicitly
        if(($part['continue'] === true )) { break; }
    }
    
    if($finalTitleValues['use_default_text'] === true) {
        $output[] = $finalTitleValues['default_text'];
    }
    
    // return all the parts joined by one space
    return implode($finalTitleValues['parts'], $finalTitleValues['parts_separator']);
    
    return $output;
}