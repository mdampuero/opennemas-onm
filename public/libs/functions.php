<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
/**
 * This file stores shared function that could be used by the framework
 */

function underscore($name) {
    $withUnderscore = strtolower(preg_replace('/(?<=\\w)([A-Z])/', '_\\1', $name));

    return $withUnderscore;
}

function tableize($name) {
    return pluralize(underscore($name));
}

    function pluralize($name)
{
    $name = strtolower($name);
    return $name . 's';
}

function clearslash($string)
{
    $string = stripslashes($string);
    $string = str_replace("\\", '', $string);

    return stripslashes($string);
}

// TODO: move to a separated file called functions.php
/**
* Stablishes a cookie value in a secure way
*/
function setCookieSecure($name, $value, $expires=0, $domain='/') {
    setcookie(
        $name, $value, $expires, $domain,
        $_SERVER['SERVER_NAME'], isset($_SERVER['HTTPS']), true
    );
}


/**
 * Try to get the real IP of the client
 *
 * @return string the client ip
 **/
function getUserRealIP()
{
    // REMOTE_ADDR: dirección ip del cliente
    // HTTP_X_FORWARDED_FOR: si no está vacío indica que se ha utilizado
    // un proxy. Al pasar por el proxy lo que hace este es poner su
    // dirección IP como REMOTE_ADDR y añadir la que estaba como
    // REMOTE_ADDR al final de esta cabecera.
    // En el caso de que la petición pase por varios proxys cada uno
    // repite la operación, por lo que tendremos una lista de direcciones
    // IP que partiendo del REMOTE_ADDR original irá indicando los proxys
    // por los que ha pasado.

    if (
        isset($_SERVER['HTTP_X_FORWARDED_FOR'])
        && $_SERVER['HTTP_X_FORWARDED_FOR'] != ''
    ) {
        $clientIp = ( !empty($_SERVER['REMOTE_ADDR']) ) ?
            $_SERVER['REMOTE_ADDR']
            :
            ( ( !empty($_ENV['REMOTE_ADDR']) ) ?
                $_ENV['REMOTE_ADDR']
                :
                "unknown" );

        // los proxys van añadiendo al final de esta cabecera
        // las direcciones ip que van "ocultando". Para localizar la ip real
        // del usuario se comienza a mirar por el principio hasta encontrar
        // una dirección ip que no sea del rango privado. En caso de no
        // encontrarse ninguna se toma como valor el REMOTE_ADDR

        $entries = split('[, ]', $_SERVER['HTTP_X_FORWARDED_FOR']);

        reset($entries);
        while (list(, $entry) = each($entries)) {
            $entry = trim($entry);
            if ( preg_match("/^([0-9]+\.[0-9]+\.[0-9]+\.[0-9]+)/", $entry, $ipList) ) {
                // http://www.faqs.org/rfcs/rfc1918.html
                $privateIp = array(
                      '/^0\./',
                      '/^127\.0\.0\.1/',
                      '/^192\.168\..*/',
                      '/^172\.((1[6-9])|(2[0-9])|(3[0-1]))\..*/',
                      '/^10\..*/');

                $foundIP = preg_replace($privateIp, $clientIp, $ipList[1]);

                if ($clientIp != $foundIP) {
                   $clientIp = $foundIP;
                   break;
                }
            }
        }
    } else {
        $clientIp = ( !empty($_SERVER['REMOTE_ADDR']) ) ?
            $_SERVER['REMOTE_ADDR']
            :
            ( ( !empty($_ENV['REMOTE_ADDR']) ) ?
                $_ENV['REMOTE_ADDR']
                :
                "unknown" );
    }

    return $clientIp;
}

/**
 * Register in the log one event in the content
 *
 * @return void
 **/
function logContentEvent($action = NULL, $content = NULL)
{
    $logger = Application::getLogger();

    $msg = 'User '.$_SESSION['username'].'(ID:'.$_SESSION['userid'].') has executed '
    .'the action '.$action;
    if(!empty($content)){ $msg.=' at '.get_class($content).' (ID:'.$content->id.')';}

    $logger->notice( $msg );
}

/**
 * Register in the Database error handler one error message
 *
 * @return boolean true if all was sucessfully performed
 **/
function logDatabaseError()
{
    $errorMsg = $GLOBALS['application']->conn->ErrorMsg();
    $GLOBALS['application']->logger->debug('Error: '.$errorMsg);
    $GLOBALS['application']->errors[] = 'Error: '.$errorMsg;
    return $errorMsg;
}

/**
* Raise an HTTP redirection to given url
*
* Use the header PHP function to redirect browser to another page
*
* @param string $url the url to redirect to
*/
function forward($url)
{
    header("Location: ".$url);
    exit(0);
}

/**
 * Detect a mobile device and redirect to mobile version
 *
 * @param boolean $autoRedirect
 * @return boolean True if it's a mobile device and $autoRedirect is false
 */
function isMobileBrowser($autoRedirect=true)
{
    $isMobileDevice = false;
    $showDesktop = filter_input(INPUT_GET,'show_desktop',FILTER_DEFAULT);
    if ($showDesktop) {
        $autoRedirect = false;
        $_COOKIE['confirm_mobile'] = 1;
    }

    // Browscap library
    require APPLICATION_PATH .DS.'vendor'.DS.'Browscap.php';

    // Creates a new Browscap object (loads or creates the cache)
    $bc = new Browscap( APPLICATION_PATH .DS.'tmp'.DS.'cache');
    $browser = $bc->getBrowser(); //isBanned

    if (
        !empty($browser->isMobileDevice)
        && ($browser->isMobileDevice == true)
        && !(isset($_COOKIE['confirm_mobile']))
    ) {
        if ($autoRedirect) {
            Application::forward('/mobile' . $_SERVER['REQUEST_URI'] );
        } else {
            $isMobileDevice = true;
        }
    }

    return $isMobileDevice;
}

/**
 * Check if current request is from backend
 *
 * Checks if the current URI requrested belongs to admin panel
 *
 * @return boolean true if request is from backend
*/
function isBackendUrl()
{
    return strncasecmp($_SERVER['REQUEST_URI'], '/admin/', 7) == 0 ;
}

/**
* Perform a permanently redirection (301)
*
* Use the header PHP function to redirect browser to another page
*
* @param string $url the url to redirect to
*/
function forward301($url)
{
    header('HTTP/1.1 301 Moved Permanently');
    header('Location: ' . $url);
    exit(0);
}

// TODO: move to a separated file called functions.php
/**
* Wrapper to output content to AJAX requests
*
*
* @param string $htmlout, the content to output
* @return null
*/
function ajax_out($htmlout)
{
    header("Cache-Control: no-cache");
    header("Pragma: nocache");
    echo $htmlout;
    exit(0);
}