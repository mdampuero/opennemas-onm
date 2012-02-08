<?php 



/**
 * Prints the message only if the third param is false
 *
 * @return void
 * @author 
 **/
function printInstallResults($strSetting, $strInstructions, $blnCondition) {
        
    
    if (!$blnCondition) {
        if(php_sapi_name() == 'cli' && empty($_SERVER['REMOTE_ADDR'])) {
            printf("* %s\n",$strInstructions);
        } else {
            printf("<div class=\"install_instructions\">%s</div>\n", $strInstructions);
        }
    }
    
    return $blnCondition;
}


// Check if all needed php extensions are installed
$arrExtension = array('mysql', 'curl', 'ffmpeg', 'imagick', 'zlib', 'ftp', 'pspell', 'mysqli');
foreach($arrExtension as $strExtensionName) {
    printInstallResults(
        $strExtensionName . ' php extension loaded', 
        sprintf('This version of OpenNeMas needs the %s php extension, please install php-%s or php5-%s', $strExtensionName, $strExtensionName, $strExtensionName), 
        extension_loaded($strExtensionName)
    );
}
