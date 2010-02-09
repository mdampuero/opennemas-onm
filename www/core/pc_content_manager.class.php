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
 * @copyright  Copyright (c) 2009 Openhost S.L. (http://openhost.es)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * PC_content_manager
 *
 * @package    OpenNeMas - Colabora
 * @copyright  Copyright (c) 2009 Openhost S.L. (http://openhost.es)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: pc_content.class.php 1 2009-12-15 18:16:56Z  $
 */
 
class PC_ContentManager {

    var $content_type = NULL;
    var $table = NULL;
    var $pager = NULL;

    function PC_ContentManager($content_type=NULL) {

         $this->cache = new MethodCacheManager($this, array('ttl' => 30));
    	// Nombre de la tabla en minúsculas y  tipo de contenido con la sintáxis del nombre de la clase
    	if(!is_null($content_type)) {
    	    $this->init($content_type);
        }
      
    }

    function __construct($content_type=NULL) {
        $this->PC_ContentManager($content_type);
    }

    function init($content_type) {
        $this->table = $this->pluralize( $content_type ); //pc_+nombre+s
        $this->content_type = $content_type;
    }

    function load_obj($rs,$content_type){
	$items=array();

    	while(!$rs->EOF) {           
            $obj = new $content_type();
            $obj->load($rs->fields);
            
            $items[] = $obj;
            
            $rs->MoveNext();
        }        
        return $items ;
    }
        
    function find($content_type, $filter=NULL, $_order_by='ORDER BY 1', $fields='*') {

        $this->init($content_type);
	$items = array();
        $_where = ' `pc_contents`.`in_litter`=0';

        if( !is_null($filter) ) {
            if( $filter == 'in_litter=1') { //se busca desde la litter.php
                $_where = $filter;
            } else{
                $_where = ' `pc_contents`.`in_litter`=0 AND '.$filter;
            }
        }

        $sql = 'SELECT '.$fields.' FROM pc_contents, '.$this->table. 
                    ' WHERE `pc_contents`.`pk_pc_content` =  `'.$this->table.'`.`pk_'.strtolower($content_type).'` ' .
                    ' AND '.$_where.' '.$_order_by;

        $rs = $GLOBALS['application']->conn->Execute($sql);
       	$items = $this->load_obj($rs,$content_type);
	               
	return $items;
    }


    function count($content_type, $filter=NULL, $pk_fk_content_category=NULL) {
        $this->init($content_type);
    
        $items = array();
        $_where = 'in_litter=0';

        if( !is_null($filter) ) {
            if( $filter == 'in_litter=1') { //se busca desde la litter.php
                    $_where = $filter;
            } else{
                $_where = ' in_litter=0 AND '.$filter;
            }
        }

        if( intval($pk_fk_content_category) != NULL) {
            $sql = 'SELECT COUNT(*) FROM pc_contents_categories, pc_contents, '.$this->table.'  ' .
                ' WHERE '.$_where.' AND pk_fk_content_category='.$pk_fk_content_category.
                ' AND pk_pc_content=pk_'.strtolower($content_type).' AND  pk_fk_content = pk_pc_content ';
        } else {
	    $sql = 'SELECT COUNT(*) AS total FROM `pc_contents`, `'.$this->table.'` ' .
                ' WHERE '.$_where.' AND pk_pc_content=pk_'.strtolower($content_type).' ';
        }

        $rs = $GLOBALS['application']->conn->GetOne($sql);

        return $rs;
    }

    // ContentManager::find_pages(<TIPO_CONTENIDO>, <CLAUSE_WHERE>, <CLAUSE_ORDER>,<PAGE>,<ITEMS_PER_PAGE>,<CATEGORY>);
    /* find_pages: Se utiliza para generar los listados en la parte de administracion.
     * Genera las consultas de find o find_by_category y la paginacion
     * Devuelve el array con el segmento de contents que se visualizan en la pagina dada.
     * Params:  $content_type - tipo contenido.
     * 			$filter=NULL - condiciones para clausula where.
     * 			$_order_by='ORDER BY 1' - orden de visualizacion
     * 			$page - pagina que se quiere visualizar.
     * 			$items_page - numero de elementos por pagina.
     *  		$pk_fk_content_category=NULL - id de categoria (para find_by_category y si NULL es find).
     */

   function find_pages($content_type, $filter=NULL, $_order_by='ORDER BY 1', $page=1, $items_page=10,$pk_fk_content_category=NULL ) {
        $this->init($content_type);
 
        $items = array();
        $_where = 'in_litter=0';

        if( !is_null($filter) ) {
            if( $filter == 'in_litter=1') { //se busca desde la litter.php
                $_where = $filter;
            } else {
                $_where = ' in_litter=0 AND '.$filter;
            }
        }
        $total_contents=$this->count($content_type, $filter, $pk_fk_content_category);
    
        $_limit = 'LIMIT '.($page-1)*$items_page.', '.($items_page);

        if( intval($pk_fk_content_category) != NULL) { 
            $sql = 'SELECT * FROM pc_contents_categories, pc_contents, '.$this->table.'  ' .
                   ' WHERE '.$_where.' AND `pc_contents_categories`.`pk_fk_content_category`='.$pk_fk_content_category.
                   '  AND `pc_contents`.`pk_pc_content`=`'.$this->table.'`.`pk_'.strtolower($content_type).'` AND  `pc_contents_categories`.`pk_fk_content` = `pc_contents`.`pk_pc_content` '.
                   $_order_by.' '.$_limit;
        } else {
            $sql = 'SELECT * FROM `pc_contents`, `'.$this->table.'` ' .
                   ' WHERE '.$_where.' AND `pc_contents`.`pk_pc_content`=`'.$this->table.'`.`pk_'.strtolower($content_type).'` '.
                   $_order_by.' '.$_limit;
        }

        $rs = $GLOBALS['application']->conn->Execute($sql);
 
        $items = $this->load_obj($rs, $content_type);
 
	$pager_options = array(
            'mode'        => 'Sliding',
            'perPage'     => $items_page,
            'delta'       => 4,
            'clearIfVoid' => true,
            'urlVar'      => 'page',
            'totalItems'  => $total_contents,
        );
        $pager = Pager::factory($pager_options);

        return array($items, $pager);
    }


    function find_by_category($content_type, $pk_fk_content_category, $filter=NULL, $_order_by='ORDER BY 1',$fields='*') {

        $this->init($content_type);

        $items = array();
	$_where = ' in_litter=0';
	
        if( !is_null($filter) ) {
            if( $filter == 'in_litter=1') { //se busca desde la litter.php
                $_where = $filter;                
            }else{
                $_where = $filter.' AND in_litter=0';
            }
        }

        if( intval($pk_fk_content_category) > 0 ) {
            $sql = 'SELECT '.$fields.' FROM pc_contents_categories, pc_contents, '.$this->table.
                   ' WHERE pk_fk_content_category='.$pk_fk_content_category.' AND pk_pc_content=pk_'.strtolower($content_type).
                   ' AND  pk_fk_content = pk_pc_content AND '.$_where.' '.$_order_by;
        }else{
            return( $items );
        }

        $rs = $GLOBALS['application']->conn->Execute($sql);
        $items = $this->load_obj($rs,$content_type);
 
        return $items;
    }


    function find_by_category_name($content_type, $pk_fk_content_category, $filter=NULL, $_order_by='ORDER BY 1', $fields='*') {

        $this->init($content_type);

    	$items = array();
	$_where = '1=1  AND in_litter=0';
	
        if( !is_null($filter) ) {
            if( preg_match('/in_litter=1/i', $filter) ) { //se busca desde la litter.php
              $_where = $filter;
            } else{
                $_where = $filter.' AND in_litter=0';
            }
        }
        $sql = 'SELECT '.$fields.' FROM pc_contents_categories, pc_contents, '.$this->table.
                ' WHERE catname=\''.$pk_fk_content_category.'\' AND pk_pc_content=pk_'.strtolower($content_type).
                ' AND pk_fk_content = pk_pc_content AND '.$_where.' '.$_order_by;
                
        $rs = $GLOBALS['application']->conn->Execute($sql);

        $items = $this->load_obj($rs,$content_type);
        return $items;
    }

   
  
    
	/* FIXME: Establecer los plurales siguiendo el criterio del idioma español
	para otros casos ya tenemos versiones inglesas */
	
	//Para buscar la tabla que le corresponde, en plan conecta comienzan por pc_

	function pluralize($name) {
            $name = strtolower($name);
            return($name.'s');
	}

    //Returns cetegory id
    function get_id($category) {
        $sql = 'SELECT pk_content_type FROM pc_content_types WHERE title = \''.$category.'\'';
        //echo "<hr>".$sql."<br>";
        $rs = $GLOBALS['application']->conn->Execute( $sql );

        if (!$rs) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
            return;
        }
        return $rs->fields['pk_content_type'];
    }

      // TODO: Incluir mas opciones para personalizar la paginacion
    function paginate($items)
    {
        $_items = array();

        foreach($items as $v) {
        $_items[] = $v->id;
        }

        $items_page = (defined(ITEMS_PAGE))?ITEMS_PAGE: 10;

        $params = array(
            'itemData' => $_items,
            'perPage' => $items_page,
            'delta' => 1,
            'append' => true,
            'separator' => '|',
            'spacesBeforeSeparator' => 1,
            'spacesAfterSeparator' => 1,
            'clearIfVoid' => true,
            'urlVar' => 'page',
            'mode'  => 'Sliding',
            'linkClass' => 'pagination',
            'altFirst' => 'primera p&aacute;gina',
            'altLast' => '&uacute;ltima p&aacute;gina',
            'altNext' => 'p&aacute;gina seguinte',
            'altPrev' => 'p&aacute;gina anterior',
            'altPage' => 'p&aacute;gina'
        );

        $this->pager = &Pager::factory($params);
        $data  = $this->pager->getPageData();

        $result = array();
        foreach($items as $k => $v) {
            if( in_array($v->id, $data) ) {
                $result[] = $v; // Array 0-n compatible con sections Smarty
            }
        }

        return($result);
    }

    //Paginate para imagenes de 30
    function paginate_num($items, $num_pages)
    {
        $_items = array();

        foreach($items as $v) {
        $_items[] = $v->id;
        }

        $items_page = (defined(ITEMS_PAGE))?ITEMS_PAGE: $num_pages;

        $params = array(
        'itemData' => $_items,
                'perPage' => $items_page,
                'delta' => 1,
                'append' => true,
                'separator' => '|',
        'spacesBeforeSeparator' => 1,
        'spacesAfterSeparator' => 1,
                'clearIfVoid' => true,
                'urlVar' => 'page',
                'mode'  => 'Sliding',
        'linkClass' => 'pagination',
        'altFirst' => 'primera p&aacute;gina',
        'altLast' => '&uacute;ltima p&aacute;gina',
        'altNext' => 'p&aacute;gina seguinte',
        'altPrev' => 'p&aacute;gina anterior',
        'altPage' => 'p&aacute;gina'
        );

        $this->pager = &Pager::factory($params);
        $data  = $this->pager->getPageData();

            $result = array();
            foreach($items as $k => $v) {
                if( in_array($v->id, $data) ) {
            $result[] = $v; // Array 0-n compatible con sections Smarty
                }
            }
            return($result);
    }
}
 