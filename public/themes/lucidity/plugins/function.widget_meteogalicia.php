<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
//
// +----------------------------------------------------------------------+
// | PHP version 4                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2010 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.0 of the PHP license,       |
// | that is bundled with this package in the file LICENSE, and is        |
// | available through the world-wide-web at                              |
// | http://www.php.net/license/2_02.txt.                                 |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Authors: Antonio Jozzolino <info@sgd.com.br>                         |
// +----------------------------------------------------------------------+
//
// $Id: function.widget_meteogalicia.php, v 0.08 Tue Aug 17 2010 13:14:02 GMT+0200 (CEST) Antonio Jozzolino $
//

/**
* Short desc
*
* Long description first sentence starts here
* and continues on this line for a while
* finally concluding here at the end of
* this paragraph
*
* @package    ABHO | SSCF | SGD
* @subpackage
* @author     Antonio Jozzolino <info@sgd.com.br>
* @version    $Id: function.widget_meteogalicia.php, v 0.08 Tue Aug 17 2010 13:14:02 GMT+0200 (CEST) Antonio Jozzolino $
* @since      Tue Aug 17 2010 13:11:40 GMT+0200 (CEST)
* @access     public
* @see        http://www.sgd.com.br
* @uses       file.ext|elementname|class::methodname()|class::$variablename|functionname()|function functionname  description of how the element is used
* @example    relativepath/to/example.php  description
*/
/**
 * function.widget_meteogalicia.php
 *
 * @package  OpenNeMas
 * @author   Tomás Vilariño <vifito@openhost.es>
 * @license  GPL
 * @version  v0.8-2
 */

/**
 * smarty_function_widget_meteogalicia
 * <code>
 * {smarty_function_widget_meteogalicia cache="true" cachelife="120"}
 * </code>
 *
 * @author Tomás Vilariño <vifito@openhost.es>
 * @param array $params  Parameters of smarty function
 * @param Smarty $smarty Object reference to Smarty class
 * @return string Return a HTML code
 */
function smarty_function_widget_meteogalicia($params, &$smarty)
{
    // Output
    $html = '';

    // URL
    $url = 'http://www.meteogalicia.es/web/RSS/rssCPrazo.action?dia=0';

    $cache     = ( isset($params['cache']) )? $params['cache']: false;
    $cachelife = ( isset($params['cachelife']) )? intval($params['cachelife']) * 60: 6 * 60 * 60; // 6 horas
    $cachename = 'meteogalicia';

    // Name cachefile
    $cachefilename = CACHE_PATH . '/' . $cachename. '.cache';



    // Flag to control if a request failed or content can't be parse
    $is_valid_content = false;

    if( !$cache || !file_exists($cachefilename)  || (filemtime( $cachefilename ) < (time() - $cachelife)) ) {
        // Trigger onbefore event
        if( isset($params['onbefore']) ) {
            if( function_exists($params['onbefore']) ) {
                // $params['onbefore']( $url );
                call_user_func($params['onbefore'], $url);
            }
        }

        // cURL Handle
        $ch = curl_init();

        // Options
        curl_setopt($ch, CURLOPT_URL,            $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Capture output
        curl_setopt($ch, CURLOPT_HEADER,         false);
        curl_setopt($ch, CURLOPT_USERAGENT,      'Mozilla/5.0 (Windows; U; Windows NT 5.1; pl; rv:1.9) Gecko/2008052906 Firefox/3.0');
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2); // 2 seconds timeout


        ob_start();
        // Exec
        $xmlString = curl_exec($ch);
        ob_end_clean();

        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if($http_code != 200) {
            $html = '';
            $is_valid_content = false;
        } else {
            $xml = simplexml_load_string($xmlString);
            $temps = $xml->xpath('//CPrazo:temperaturas');

            $is_valid_content = count($temps) > 0;

            $html = '<div id="widget_weather_top">';
            foreach($temps as $temp) {
                $html .= '<ul><li><div class="widget-litte-city">' . $temp . '</div></li>';
                $html .= '<li><div class="widget-litte-temperatures max_temperature">' . $temp['tmax'] . 'º C</div></li>';
                $html .= '<li><div class="widget-litte-temperatures min_temperature">' . $temp['tmin'] . 'º C</div></li>';
                $html .= '</ul>';
            }
            $html .= '</div>';
            $html .= '<script type="text/javascript">$("#widget_weather_top").cycle({fx: "fade"});</script>';
        }

        // Close cURL
        curl_close($ch);

        if($is_valid_content) {
            file_put_contents($cachefilename, $html);
        } else {
            if( file_exists($cachefilename) ) {
                // Recovery oldest content from cache
                $html = file_get_contents( $cachefilename ).'<!-- cached '.date('YmdHis', filemtime($cachefilename)).' -->';
            }
        }

    } else {
        // Recovery content from cache
        $html = file_get_contents($cachefilename).'<!-- cached '.date('YmdHis', filemtime($cachefilename)).' -->';
    }

    return $html;
}
