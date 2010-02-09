<?php
/**
 * Smarty plugin
 *
 * File:     prefilter.sniff_ads.php 
 * Type:     prefilter 
 * Name:     pre01 
 * Purpose:  Convert html tags to be lowercase. 
*/ 
function smarty_prefilter_sniff_ads($source, &$tpl) 
{
    $matches = array();
    
    if(!function_exists('callback_sniffAds')) {
        function callback_sniffAds($matches) {
            $GLOBALS['application']->logger->debug($_SERVER['SCRIPT_NAME'] . ' banner: ' . $matches[2]);
            return $matches[1];
        }
    }
    
    //$source = preg_replace('/(\{renderbanner.*?banner=\$([^ ]+).*?\})/s', '\1<script>console.log("\2");</script>', $source);
    $source = preg_replace_callback('/(\{renderbanner.*?banner=\$([^ ]+).*?\})/s', "callback_sniffAds", $source);    

    // Return same $source
    return $source; 
} 