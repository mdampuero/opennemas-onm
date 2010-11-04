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
// $Id: insert.rating.php, v 0.03 Mon Sep 13 2010 17:28:34 GMT+0200 (CEST) Antonio Jozzolino $
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
* @version    $Id: insert.rating.php, v 0.03 Mon Sep 13 2010 17:28:34 GMT+0200 (CEST) Antonio Jozzolino $
* @since      Mon Sep 13 2010 17:04:41 GMT+0200 (CEST)
* @access     public
* @see        http://www.sgd.com.br
* @uses       file.ext|elementname|class::methodname()|class::$variablename|functionname()|function functionname  description of how the element is used
* @example    relativepath/to/example.php  description
*/
/**
 * insert.rating.php, Smarty insert plugin to insert the rating bar
 * 
 * @package  OpenNeMas
 * @author Toni Martínez <toni@openhost.es>
 * @version  0.6-rc1
 */

/**
 * smarty_insert_rating, Smarty insert plugin to insert the rating bar
 * <code>
 * {insert name="rating" id="2009051723543313996" page="article" type="vote"}
 * </code>
 *
 * @author Toni Martínez <toni@openhost.es>
 * @param array $params  Parameters of smarty function
 * @param Smarty $smarty Object reference to Smarty class
 * @return string Return a HTML code of the rating bar
 */
function smarty_insert_rating($params, $smarty)
{
    if (empty($params['id']) || empty($params['page']) || empty($params['type'])) {
        $smarty->trigger_error("insert rating: missing parameters");
        return;
    }
    $rating = new Rating($params['id']);
    return $rating->render($params['page'], $params['type']);
}
?>