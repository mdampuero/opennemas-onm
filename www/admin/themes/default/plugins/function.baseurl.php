<?php

function smarty_function_baseurl($params, &$smarty)
{
    $fc = Zend_Controller_Front::getInstance();
    
    return $fc->getBaseUrl();
}