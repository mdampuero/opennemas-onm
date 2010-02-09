<?php
/**
 * Fix object tags, remove embed tags for this page can be w3c complaint
 *
 */
function smarty_outputfilter_clean_script_tags($tpl_output, &$smarty) {
    
    /* 'smarty_outputfilter_fix_object_tags_callback_xhtml', */
    $tpl_output = preg_replace( '@<script([^>]*>)(.*?)</script>@si',
                                '',                                
                                $tpl_output );
    
    return($tpl_output);
}


