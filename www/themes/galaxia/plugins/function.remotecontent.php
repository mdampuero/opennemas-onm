<?php
/**
 * function.remotecontent.php, Smarty function plugin to web scrap content
 * and embed into content template.
 * 
 * @package  OpenNeMas
 * @author   Tomás Vilariño <vifito@openhost.es>
 * @license  GPL
 * @version  0.6-rc1
 */

/**
 * smarty_function_remotecontent, Smarty function plugin to web scrap content
 * and embed into content template.
 * <code>
 * {remotecontent url=$url onafter="remotecontent_onafter_aemet"
 *  cache="true" cachelife="120" cachename=$cachename}
 * </code>
 *
 * @author Tomás Vilariño <vifito@openhost.es>
 * @param array $params  Parameters of smarty function
 * @param Smarty $smarty Object reference to Smarty class
 * @return string Return a HTML code to perform web scrap to $params['url']
 */
function smarty_function_remotecontent($params, &$smarty)
{
    // TODO: volver a implementar facendo uso de Zend_Http evitando as excesivas
    // sentencias condicionais. http://framework.zend.com/manual/en/zend.http.html
    
    // Output
    $html = '';
    
    // URL
    $url = $params['url'];
        
    $cache     = ( isset($params['cache']) )? $params['cache']: false;
    $cachelife = ( isset($params['cachelife']) )? intval($params['cachelife']) * 60: 6 * 60 * 60; // 6 horas    
    
    if( isset($params['cachename']) ) {
        $cachename = $params['cachename'];
    } else {
        $url_parts = parse_url($url);
        $cachename = $url_parts['host'].str_replace('/', '.', $url_parts['path']);
    }    
    
    // Name cachefile
    $cachefilename = dirname(__FILE__).'/../cache_widgets/'.$cachename.'.cache';
    
    // Flag to control if a request failed or content can't be parse
    $is_valid_content = false;        
    
    if( !$cache || !file_exists($cachefilename)  || (filemtime( $cachefilename ) < (time() - $cachelife)) ) {    
        // Trigger onbefore event    
        if( isset($params['onbefore']) ) {
            if( function_exists($params['onbefore']) ) {
                // $params['onbefore']( $url );
                call_user_func($params['onbefore'], &$url);
            }
        }
        
        // cURL Handle
        $ch = curl_init();
        
        // Options
        curl_setopt($ch, CURLOPT_URL,            $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Capture output        
        curl_setopt($ch, CURLOPT_HEADER,         false);
        curl_setopt($ch, CURLOPT_USERAGENT,      'Mozilla/5.0 (Windows; U; Windows NT 5.1; pl; rv:1.9) Gecko/2008052906 Firefox/3.0');
        
        
        ob_start();
        // Exec
        $html = curl_exec($ch);
        ob_end_clean();
        
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if($http_code!=200) {
            $html = '';
            $is_valid_content = false;            
        } else {
            // Trigger onafter event
            if( isset($params['onafter']) ) {
                if( function_exists($params['onafter']) ) {
                    //$html = $params['onafter']( $html );
                    call_user_func($params['onafter'], &$html, &$is_valid_content);                                
                }
            }
        }
        
        // Close cURL
        curl_close($ch);
        
        if($is_valid_content) {
            file_put_contents($cachefilename, $html);
        } else {
            if(defined('SYS_LOG_EMAIL')) {
                // FIXME: create a class to report errors similar to error_log
                error_log('ERROR: remotecontent plugin of smarty failed trying connect to: '.$url,
                          1, SYS_LOG_EMAIL);
            }
            
            if( file_exists($cachefilename) ) {
                // Recovery oldest content from cache
                $html = file_get_contents( $cachefilename ).'<!-- cached '.date('YmdHis', filemtime($cachefilename)).' -->';
            }
        }
        
    } else {
        // Recovery content from cache
        $html = file_get_contents($cachefilename).'<!-- cached '.date('YmdHis', filemtime($cachefilename)).' -->';
    }
    
    return( $html );
}


/* CALLBACKS ***************************************************************** */
// FIXME: extraer o código das callbacks a outro ficheiro noutro directorio, 

/**
 * remotecontent_onafter_infobolsa, onafter callback to remote content to
 * web scrap: http://www.infobolsa.es/mini-ficha/ibex35.htm
 *
 * @param string $html HTML code to parse
 * @param boolean $is_valid_content flag to control is a valid content
 */
function remotecontent_onafter_infobolsa(&$html, &$is_valid_content) {
    $mathes = array();
    
    // modifier "s" to PCRE_DOTALL  
    if( preg_match('/<span><body[^>]*>(?P<content>.*?)<\/body><\/span>/si', $html, $matches) ) {
        $html = $matches['content'];
        
        // clean warnings w3c 
        $html = preg_replace('@<div class="miniFV_logo">(.*?)</div></a></div>@s', '', $html);
        $html = preg_replace('@<nobr[^>]*>(.*?)</nobr>@si', '<span style="white-space: nowrap;">$1</span>', $html);
        $html = preg_replace('@&amp;MV=I IB   &amp;@', '&amp;MV=I%20IB%20%20%20&amp;', $html);
        $html = preg_replace('@border="0"></a>@', 'border="0" /></a>', $html);
        $html = preg_replace('@<div></div>@', '', $html);    
        
        // $html  = '<style>@import url("http://www.infobolsa.es/mini-ficha/css/miniFV.css");</style>';
        // $html .= $matches['content'];
        $is_valid_content = true;
    }    
}

/**
 * remotecontent_onafter_aemet, onafter callback to remote content to
 * web scrap: http://www.aemet.es/es/eltiempo/prediccion/localidades?$querystring
 *
 * @param string $html HTML code to parse
 * @param boolean $is_valid_content flag to control is a valid content
 */
function remotecontent_onafter_aemet(&$html, &$is_valid_content) {
    $mathes = array();
    
    // modifier "s" to PCRE_DOTALL  
    if( preg_match('/<table[^>]*>(?P<content>.*?)<\/table>/si', $html, $matches) ) { 
        $html = $matches['content'];
            
        $serverpath = preg_replace('|([^:])//|', '\1/', MEDIA_PATH_URL);
        $html = preg_replace('|<img src="/imagenes/gif/(.+)\.gif"|i',
                             '<img src="'.$serverpath.'/weather/aemet/\1.png"', $html);
        
        // Override $html content
        $html = utf8_encode($html);
        
        $is_valid_content = true;
    }
}


function remotecontent_onafter_combinatoria(&$html, &$is_valid_content) {
    $is_valid_content = true;
    
    $html = preg_replace('/<style[^>]*>(.*?)<\/style>/si', '', $html);
}