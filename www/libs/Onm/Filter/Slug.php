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
 
/**
 * Onm_Filter_Slug
 * 
 * @package    Onm
 * @subpackage Filter
 * @copyright  Copyright (c) 2010 Openhost S.L. (http://openhost.es)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Slug.php 1 2010-01-14 13:22:43Z vifito $
 */
class Onm_Filter_Slug implements Zend_Filter_Interface
{
    /**
     * Convert value to a valid slug
     *
     * @param string $value
     * @return string Value slugged
     */
    public function filter($value)
    {
        // Decode specials chars html encoded
        $value = html_entity_decode($value, ENT_COMPAT, 'UTF-8');
        $value = mb_strtolower($value, 'UTF-8');
        $value = mb_ereg_replace('[^a-z0-9áéíóúñüç_\,\- ]', ' ', $value);
        
        $trade = array( 'á'=>'a', 'à'=>'a', 'ã'=>'a', 'ä'=>'a', 'â'=>'a',
                        'é'=>'e', 'è'=>'e', 'ë'=>'e', 'ê'=>'e',
                        'í'=>'i', 'ì'=>'i', 'ï'=>'i', 'î'=>'i',
                        'ó'=>'o', 'ò'=>'o', 'õ'=>'o', 'ö'=>'o', 'ô'=>'o',
                        'ú'=>'u', 'ù'=>'u', 'ü'=>'u', 'û'=>'u',
                        '$'=>'', '@'=>'', '!'=>'', '#'=>'_',
                        '%'=>'', '^'=>'', '&'=>'', '*'=>'', '('=>'-', ')'=>'-', '-'=>'-', '+'=>'',
                        '='=>'', '\\'=>'-', '|'=>'-','`'=>'', '~'=>'', '/'=>'-', '\"'=>'-','\''=>'',
                        '<'=>'', '>'=>'', '?'=>'-', ','=>'-', 'ç'=>'c', 'Ç'=>'C', '·'=>'',
                        '.'=>'', ';'=>'-', '['=>'-', ']'=>'-','ñ'=>'n','Ñ'=>'n');
        $value = strtr($value, $trade);
        
        $value = preg_replace('/[ ]+/', '-', $value);
        $value = preg_replace('/[\-]+/', '-', $value);
        
        return $value;
    }        
}
