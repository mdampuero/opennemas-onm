<?php
function smarty_outputfilter_profiler_javascript($tpl_output, &$smarty) {
    if( isset($_COOKIE['profiler_javascript']) &&  ($_COOKIE['profiler_javascript'] == '6f1be758cc8fb44a7ace2fce5f3f9c6e') ) {    
        preg_replace_callback( '@<script([^>]*)>(.*?)</script>@si',
                               'smarty_outputfilter_profilerjs_callback',                                
                               $tpl_output );                
    }        
    
    return($tpl_output);
}


/* Callbacks ***************************************************************** */
// Save javascript to jslint
function smarty_outputfilter_profilerjs_callback($matches) {
    // If src
    if( isset($matches[1]) && !empty($matches[1]) && preg_match('//', $matches[1]) ) {
        
    }
    
    
    // If js code
    $code = $matches[2];    
    
    
    
    
}
