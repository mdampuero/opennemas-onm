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

function underscore($name)
{
    $withUnderscore = strtolower(preg_replace('/(?<=\\w)([A-Z])/', '_\\1', $name));

    return $withUnderscore;
}

function classify($name)
{
    $parts = explode('_', $name);

    $parts = array_map(
        function ($token) {
            return ucfirst($token);
        },
        $parts
    );
    $className = implode('', $parts);

    return $className;
}

function tableize($name)
{
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


/**
 * Stablishes a cookie value in a secure way
 *
 * @param string $name the name of the cookie
 * @param mixed $value the value to set into the cookie
 * @param int $expires the seconds during the cookie will be valid
 * @param int $domain the path for which the cookie will be valid
 *
 * @return void
 */
function setCookieSecure($name, $value, $expires = 0, $domain = '/')
{
    setcookie(
        $name,
        $value,
        $expires,
        $domain,
        $_SERVER['SERVER_NAME'],
        isset($_SERVER['HTTPS']),
        true
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

    if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])
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
            if (preg_match("/^([0-9]+\.[0-9]+\.[0-9]+\.[0-9]+)/", $entry, $ipList)) {
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
 * Cleans double slashes and trailing slash from an string url
 *
 * @param string $url the url to normalize
 * @return string the normalized url
 **/
function normalizeUrl($url)
{
    $urlParts = explode('?', $url);
    $url      = $urlParts[0];

    $urlParams = '';
    if (array_key_exists('1', $urlParts)) {
        $urlParams = '?'.$urlParts[1];
    }
    $url = rtrim($url, '/');

    if ($urlParams !== '' && $url !== '/') {
        while (strpos($url, '//') != false) {
            $url = str_replace('//', '/', $url);
        }
    }

    return $url.$urlParams;
}

/**
 * Register in the log one event in the content
 *
 * @return void
 **/
function logContentEvent($action = null, $content = null)
{
    $logger = getService('logger');

    $msg = 'User '.$_SESSION['username'].'(ID:'.$_SESSION['userid'].') has executed '
    .'the action '.$action;
    if (!empty($content)) {
        $msg.=' at '.get_class($content).' (ID:'.$content->id.')';
    }

    $logger->notice($msg);
}

/**
 * Returns the autogenerated url given its name and a set of parameters
 *
 * @param string   $urlName the name of the url, i.e. admin_sytem_settings
 * @param array    $params additional params to generate the url
 * @param boolean  $absolute whether generate an absolute url
 *
 * @return string  the url
 **/
function url($urlName, $params = array(), $absolute = false)
{
    global $kernel;
    return $kernel->getContainer()->get('router')->generate($urlName, $params, $absolute);
}

/**
 * Helper function to check existance one element in translation_ids table
 */
function getOriginalIDForContentTypeAndID($content_type, $content_id)
{
    $sql = 'SELECT * FROM `translation_ids` WHERE `pk_content_old`=? AND type=? LIMIT 1';

    $_values = array($content_id, $content_type);
    $_sql = $GLOBALS['application']->conn->Prepare($sql);
    $rss = $GLOBALS['application']->conn->Execute($_sql, $_values);

    if (!$rss) {
        $returnValue = false;
    } else {
        if ($rss->_numOfRows > 0) {

            $returnValue =  $rss->fields['pk_content'];

        } else {
            $returnValue = false;
        }
    }

    return $returnValue;

}


function getOriginalIdAndContentTypeFromID($content_id)
{
    $sql = 'SELECT * FROM `translation_ids` WHERE `pk_content_old`=? LIMIT 1';

    $_values = $content_id;
    $_sql = $GLOBALS['application']->conn->Prepare($sql);
    $rss = $GLOBALS['application']->conn->Execute($_sql, $_values);

    if (!$rss) {
        $returnValue = false;
    } else {
        if ($rss->_numOfRows > 0) {
            $returnValue =  array($rss->fields['type'], $rss->fields['pk_content']);

        } else {
            $returnValue = false;
        }
    }

    return $returnValue;
}

function getOriginalIdAndContentTypeFromSlug($slug)
{
    $sql = 'SELECT * FROM `translation_ids` WHERE `slug`=? LIMIT 1';

    $GLOBALS['application']->conn->SetFetchMode(ADODB_FETCH_ASSOC);
    $rss = $GLOBALS['application']->conn->Execute($sql, array($slug));

    if (!$rss) {
        $returnValue = false;
    } else {
        if ($rss->_numOfRows > 0) {
            $returnValue =  array($rss->fields['type'], $rss->fields['pk_content']);

        } else {
            $returnValue = false;
        }
    }

    return $returnValue;
}

// Used in the Photo class
function map_entities($str)
{
    // $str = mb_convert_encoding($str, 'UTF-8', mb_detect_encoding($str));
    $str = mb_convert_encoding(
        $str,
        "UTF-8",
        "CP1252,CP1251,ISO-8859-1,UTF-8, ISO-8859-15"
    );

    return mb_strtolower($str, 'UTF-8');
    // return htmlentities($str, ENT_COMPAT, 'UTF-8');
}

function stripslashes_deep($value)
{
    $value = is_array($value) ?
                array_map('stripslashes_deep', $value) :
                stripslashes($value);

    return $value;
}

function render_output($content, $banner)
{
    if (is_object($banner)
        && property_exists($banner, 'type_advertisement')
        && ((($banner->type_advertisement + 50)%100) == 0)
    ) {
        $content = json_encode($content);

        $timeout = intval($banner->timeout) * 1000; // convert to ms
        $pk_advertisement = date('YmdHis', strtotime($banner->created)).
                            sprintf('%06d', $banner->pk_advertisement);

        /*
         * intersticial = new IntersticialBanner({iframeSrc: '/sargadelos.html?cacheburst=1254325526',
         *                                        timeout: -1,
         *                                        useIframe: true});
         */
        $output = <<< JSINTERSTICIAL
<script type="text/javascript" language="javascript">
/* <![CDATA[ */
var intersticial = new IntersticialBanner({
publiId: "$pk_advertisement",
cookieName: "ib_$pk_advertisement",
content: $content,
timeout: $timeout});
/* ]]> */
</script>
JSINTERSTICIAL;

        return $output;
    }

    return $content;
}

/**
 * Returns the current time in UNIX forma including microsecs
 *
 * @return float
 **/
function microtime_float()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}

/**
 * Detect a mobile device and redirect to mobile version
 *
 * @param  boolean $autoRedirect
 *
 * @return boolean True if it's a mobile device and $autoRedirect is false
 */
function mobileRouter($autoRedirect = true)
{
    $isMobileDevice = false;
    $showDesktop = filter_input(INPUT_GET, 'show_desktop', FILTER_DEFAULT);
    if ($showDesktop) {
        $autoRedirect = false;
        $_COOKIE['confirm_mobile'] = 1;
    }

    // Browscap library
    require APPLICATION_PATH .DS.'vendor'.DS.'Browscap.php';

    // Creates a new Browscap object (loads or creates the cache)
    $bc = new \Browscap(APPLICATION_PATH .DS.'tmp'.DS.'cache');
    $browser = $bc->getBrowser(); //isBanned

    if (!empty($browser->isMobileDevice)
        && ($browser->isMobileDevice == true)
        && !(isset($_COOKIE['confirm_mobile']))
    ) {
        if ($autoRedirect) {
            header("Location: ".'/mobile' . $_SERVER['REQUEST_URI']);
            exit(0);
        } else {
            $isMobileDevice = true;
        }
    }

    return $isMobileDevice;
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

/**
 * Try to get the real IP of the client
 *
 * @return string the client ip
 **/
function getRealIp()
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

    if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])
        && !empty($_SERVER['HTTP_X_FORWARDED_FOR']) ) {
        $clientIp =
            ( isset($_SERVER['REMOTE_ADDR'])
                && !empty($_SERVER['REMOTE_ADDR']) ) ?
            $_SERVER['REMOTE_ADDR']
                :
                ( ( isset($_ENV['REMOTE_ADDR'])
                    && !empty($_ENV['REMOTE_ADDR']) ) ?
                $_ENV['REMOTE_ADDR']
                    :
                    "unknown" );

        // los proxys van añadiendo al final de esta cabecera
        // las direcciones ip que van "ocultando". Para localizar la ip real
        // del usuario se comienza a mirar por el principio hasta encontrar
        // una dirección ip que no sea del rango privado. En caso de no
        // encontrarse ninguna se toma como valor el REMOTE_ADDR

        $entries = preg_split('/[, ]/', $_SERVER['HTTP_X_FORWARDED_FOR']);

        reset($entries);
        while (list(, $entry) = each($entries)) {
            $entry = trim($entry);
            if (preg_match("/^([0-9]+\.[0-9]+\.[0-9]+\.[0-9]+)/", $entry, $ipList)) {
                // http://www.faqs.org/rfcs/rfc1918.html
                $privateIp = array(
                    '/^0\./',
                    '/^127\.0\.0\.1/',
                    '/^192\.168\..*/',
                    '/^172\.((1[6-9])|(2[0-9])|(3[0-1]))\..*/',
                    '/^10\..*/'
                );

                $foundIp = preg_replace($privateIp, $clientIp, $ipList[1]);

                if ($clientIp != $foundIp) {
                    return  $foundIp;
                }
            }
        }
    } else {
        $clientIp =
            ( isset($_SERVER['REMOTE_ADDR'])
                && !empty($_SERVER['REMOTE_ADDR']) ) ?
            $_SERVER['REMOTE_ADDR']
                :
                ( ( isset($_ENV['REMOTE_ADDR'])
                    && !empty($_ENV['REMOTE_ADDR']) ) ?
                $_ENV['REMOTE_ADDR']
                    :
                    "unknown" );
    }

    return $clientIp;
}

function getService($serviceName)
{
    global $kernel;
    return $kernel->getContainer()->get($serviceName);
}

function getContainerParameter($paramName)
{
    global $kernel;
    return $kernel->getContainer()->getParameter($paramName);
}

function generateRandomString($length = 10)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}

/**
 * Prepares HTML code to use it as html entity attribute
 *
 * @param string $string the string to clean
 *
 * @return string $string the cleaned string
 **/
function html_attribute($string)
{
    $string = html_entity_decode($string, ENT_QUOTES, 'UTF-8');
    return htmlspecialchars(strip_tags(stripslashes($string)), ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

// added Claudio Bustos  clbustos#entelchile.net
if (!defined('ADODB_ERROR_HANDLER_TYPE')) {
    define('ADODB_ERROR_HANDLER_TYPE', E_USER_ERROR);
}
if (!defined('ADODB_ERROR_HANDLER')) {
    define('ADODB_ERROR_HANDLER', 'adoDBErrorHandler');
}

/**
* Default Error Handler. This will be called with the following params
*
* @param $dbms      the RDBMS you are connecting to
* @param $fn        the name of the calling function (in uppercase)
* @param $errno     the native error number from the database
* @param $errmsg    the native error msg from the database
* @param $p1        $fn specific parameter - see below
* @param $p2        $fn specific parameter - see below
* @param $thisConn  $current connection object - can be false if no connection object created
*/
function adoDBErrorHandler($dbms, $fn, $errno, $errmsg, $p1, $p2, &$thisConnection)
{
    if (error_reporting() == 0) {
        return; // obey @ protocol
    }

    switch ($fn) {
        case 'EXECUTE':
            $sql = $p1;
            $inputparams = $p2;

            $s = "$dbms error: [$errno: $errmsg] in $fn(\"$sql\")\n";
            break;

        case 'PCONNECT':
        case 'CONNECT':
            $host = $p1;
            $database = $p2;

            $s = "$dbms error: [$errno: $errmsg] in $fn($host, '****', '****', $database)\n";
            break;
        default:
            $s = "$dbms error: [$errno: $errmsg] in $fn($p1, $p2)\n";
            break;
    }

    $logger = getService('logger');
    $logger->error('[Database Error] '.$s);
}

/**
 * Sets the PHP environment given an environmen
 * name 'production', 'development'
 *
 * @param string $environment The current environment
 *
 * @return void
 **/
function initEnvironment($environment = 'production')
{
    if ($environment == 'development') {
        ini_set('expose_php', 'On');
        ini_set('error_reporting', E_ALL | E_STRICT);
        ini_set('display_errors', 'On');
        ini_set('display_startup_errors', 'On');
        ini_set('html_errors', 'On');
    } else {
        ini_set('expose_php', 'Off');
        ini_set('error_reporting', E_ALL | E_STRICT);
        ini_set('display_errors', 'Off');
        ini_set('display_startup_errors', 'Off');
        ini_set('html_errors', 'Off');
    }
    ini_set('apc.slam_defense', '0');
}

/**
 * undocumented function
 *
 * @return void
 * @author
 **/
function dispatchEventWithParams($eventName, $params = array())
{
    $eventDispatcher = getService('event_dispatcher');

    $event = new \Symfony\Component\EventDispatcher\GenericEvent();
    foreach ($params as $paramName => $paramValue) {
        $event->setArgument($paramName, $paramValue);
    }

    $eventDispatcher->dispatch($eventName, $event);
}

function debug()
{
    if (array_key_exists('debug', $_REQUEST) && $_REQUEST['debug'] == 1) {
        $functionArgs = func_get_args();

        call_user_func_array('var_dump', $functionArgs);
    }
}
