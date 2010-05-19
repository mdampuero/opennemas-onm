<?php
/* -*- Mode: PHP; tab-width: 4 -*- */
/**
 * OpenNeMas project
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   OpenNeMas
 * @package    OpenNeMas
 * @copyright  Copyright (c) 2010 Openhost S.L. (http://openhost.es)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
 
function smarty_function_get_category($params, &$smarty)
{
    if(!isset($params['id'])) {
        $smarty->_trigger_fatal_error('[plugin] get_category needs a "id" param');
        return;
    }
    
	$sql = 'SELECT pk_fk_content_category FROM contents_categories WHERE pk_fk_content = '.intval($params['id']);
    
    $conn = Zend_Registry::get('conn');
    $rs = $conn->Execute( $sql );

    if (!$rs) {
        return 0;
    }

    return $rs->fields['pk_fk_content_category'];
}
