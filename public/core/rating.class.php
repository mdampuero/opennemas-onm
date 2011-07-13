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
// | Authors: Toni Martinez <toni@openhost.es>                            |
// |          Fran Dieguez  <fran@openhost.es>                            |
// +----------------------------------------------------------------------+
//
// $Id: rating.class.php, v 0.97 Mon Sep 13 2010 19:22:54 GMT+0200 (CEST) Antonio Jozzolino $
//

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
 * Rating
 * 
 * @package    OpenNeMas
 * @copyright  Copyright (c) 2009 Openhost S.L. (http://openhost.es)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: rating.class.php, v 0.97 Mon Sep 13 2010 19:22:54 GMT+0200 (CEST) Antonio Jozzolino $
 */
class Rating
{
    var $pk_rating    = null;
    var $total_votes  = null;
    var $total_value  = null;
    var $ips_count_rating  = null;
    var $num_of_stars = 5;
    
    /**
     * Messages to use in links and image
     */
    private $messages = array('',
                              'Sin interés',
                              'Poco interesante',
                              'De interés',
                              'Muy interesante',
                              'Imprescindible');

    /**
      * Constructor PHP5
    */
    function __construct($id=null) {
        // Si existe idcontenido, entonces cargamos los datos correspondientes
        if(!is_null($id)) {
            $this->read($id);
        }
    }

    function create($pk_rating) {
        $sql = "INSERT INTO ratings (`pk_rating`,`total_votes`, `total_value`, `ips_count_rating`)
                VALUES (?,?,?,?)";

        $values = array($pk_rating,0, 0, serialize(array()));


        if($GLOBALS['application']->conn->Execute($sql, $values) === false) {

            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

              return(false);
        }

        return(true);
    }    

    function read($pk_rating) {
        $sql = 'SELECT total_votes, total_value, ips_count_rating
                FROM ratings WHERE pk_rating ='.$pk_rating;

        $rs = $GLOBALS['application']->conn->Execute( $sql );

        if ($rs->EOF) {
            
            //Si no existe un valoración para dicho contenido
            //comprobamos que el contenido exista y depues creamos la valoración
            //$this->create($pk_rating);
            
            $this->pk_rating = $pk_rating;
            $this->total_value = 0;
            $this->total_votes = 0;
            $this->ips_count_rating = array();
            
            //Lo creamos en la bd
            $sql = "INSERT INTO ratings (`pk_rating`,`total_votes`,
                                        `total_value`, `ips_count_rating`)
                        VALUES (?,?,?,?)";            
            
            $values = array($this->pk_rating, $this->total_votes, $this->total_value, 
                            serialize($this->ips_count_rating));
    
            if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
                $error_msg = $GLOBALS['application']->conn->ErrorMsg();
                $GLOBALS['application']->logger->debug('Error: '.$error_msg);
                $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

                return(false);
            }
            
            return;
        }

        if (!$rs) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
            return;
        }

        $this->pk_rating = $pk_rating;
        $this->total_votes = $rs->fields['total_votes'];
        $this->total_value = $rs->fields['total_value'];
        $this->ips_count_rating = unserialize($rs->fields['ips_count_rating']);
    }
    
     function get_value($pk_rating) {
        $sql = 'SELECT total_votes, total_value 
                FROM ratings WHERE pk_rating ='.$pk_rating;

        $rs = $GLOBALS['application']->conn->Execute( $sql );
            if (!$rs) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
            return;
        }
        
        $value = 0;
       
        if($rs->fields['total_votes']!=0){
            $valor=$rs->fields['total_value'] / $rs->fields['total_votes'];
            
              $value=round($valor * 100) / 100; 
        }        
        return $value;
        
     }
        
    function update($vote_value,$ip) {
        $this->ips_count_rating = $this->add_count($this->ips_count_rating,$ip);
        if (!$this->ips_count_rating) return(false);
        $this->total_votes++;
        $this->total_value = $this->total_value + $vote_value;
        
        $sql = "UPDATE ratings SET  `total_votes`=?, `total_value`=?, `ips_count_rating`=?
        WHERE pk_rating=".$this->pk_rating;

        $values = array($this->total_votes, $this->total_value, 
                        serialize($this->ips_count_rating));

        if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return(false);
        }

        //creamos la cookie
        $GLOBALS['application']->setcookie_secure("vote".$this->pk_rating, 'true', time()+60*60*24*30);
        
        return(true);

    }

    function add_count($ips_count, $ip) {
        $ips = array();
        foreach($ips_count as $ip_array){
            $ips[] = $ip_array['ip'];
        }
        //Se busca si existe algún voto desde la ip
        $kip_count = array_search($ip, $ips);
        
        if($kip_count === FALSE) {
            //No se ha votado desde esa ip
            $ips_count[] = array('ip' => $ip, 'count' => 1);
        } else {
            if ($ips_count[$kip_count]['count'] ==50) return FALSE;
            $ips_count[$kip_count]['count']++;
        }

        return $ips_count;
    }
    
    
    private function renderLink($i, $page, $pk_rating, $value) {
        /*
            <a href="javascript:rating('".$_SERVER['REMOTE_ADDR']."',1,'home','".$this->pk_rating."')" title="Sin interés">
               <img id="$this->pk_rating_1"
                    onmouseover="change_rating(1, '$this->pk_rating')"
                    onmouseout="change_rating($value, '$this->pk_rating')"
                    src="TEMPLATE_USER_PATH_WEB."images/home_noticias/semaforo ($value>=1 ? $html_out .= "Azul" : $html_out .= "Gris") .gif\"
                    alt="Sin interés" />
            </a>            
        */
        
        $imgPath = TEMPLATE_USER_PATH_WEB . "images/utilities/";
        ($page=='video') ? $sufijo='-black' : $sufijo='' ;
        
        $linkTpl = <<< LINKTPLDOC
           <li> <a href="#votar" onclick="javascript:rating('%s', %d, '%s', '%s'); return false;" title="%s">
                <img class="%s_%d"
                     onmouseover="change_rating(%d, '%s','{$sufijo}')"
                     onmouseout="change_rating(%d, '%s','{$sufijo}')"
                     src="{$imgPath}%s{$sufijo}.png"
                     alt="%s" />
            </a></li>
LINKTPLDOC;
        
        return sprintf($linkTpl, $_SERVER['REMOTE_ADDR'], $i, $page, $pk_rating,  $this->messages[$i],
                                 $pk_rating, $i,
                                 $i, $pk_rating,
                                 $value, $pk_rating,
                                 ($value>=$i)? "f-star" : "e-star",
                                 $this->messages[$i]);
    }
    
    private function renderImg($i, $value, $page='article') {
        $imgPath = TEMPLATE_USER_PATH_WEB . "images/utilities/";
        $imageTpl = '<li> <img src="%s%s.png" alt="%s" title="%s" /></li> ';
        ($page=='video') ? $sufijo='-black' : $sufijo='' ;
        return sprintf($imageTpl, $imgPath, ($value>=$i) ? "f-star".$sufijo : "e-star".$sufijo, $this->messages[$i], $this->messages[$i]);
        

    }
    
        /**
    * Get an integer and returns an string with the humanized num of votes
    *
    * @param   integer $total_votes num of votes
    * @return  string description
    * @author  Fran Dieguez <fran@openhost.es>
    * @since   Mon Sep 13 2010 18:12:58 GMT+0200 (CEST)
    */
    private function humanizeNumVotes($total_votes){
        return $total_votes.(($total_votes > 1)?" votos":" voto");
    }
    
    
    /**
    * Prints the list of img elements representing the actual votes
    *
    * @param   dobule $actual_votes average of votes
    * @param   string $kind_of_page the kind of page this'll be rendered in
    * @return  string elements imgs representing the actual votes
    * @author  Fran Dieguez <fran@openhost.es>
    * @since   Mon Sep 13 2010 18:12:58 GMT+0200 (CEST)
    */
    private function getVotesOnImages($actual_votes, $kind_of_page){
       
        $votes_on_images = '';
        
        for($i=1; $i <= $this->num_of_stars; $i++) {
            $votes_on_images .= $this->renderImg($i,$actual_votes, $kind_of_page);
        }

        return $votes_on_images;
    
    }
    
    
    /**
    * Prints the list of elements links representing the actual votes
    *
    * @param   dobule $actual_votes average of votes
    * @param   string $kind_of_page the kind of page this'll be rendered in
    * @return  string elements links representing the actual votes
    * @author  Fran Dieguez <fran@openhost.es>
    * @since   Mon Sep 13 2010 18:12:58 GMT+0200 (CEST)
    */
    private function getVotesOnLinks($actual_votes, $kind_of_page){
        
        $votes_on_links = '';
        
        for($i=1; $i <= $this->num_of_stars; $i++) {
            $votes_on_links .= $this->renderLink($i, $kind_of_page, $this->pk_rating, $actual_votes);
        }
        
        return $votes_on_links;
        
    }
    
    
    /**
    * Get an integer and returns an string with the humanized num of votes
    *
    * @param   string $page num of votes
    * @param   string $type the type of 
    * @return  string description
    * @author  Fran Dieguez <fran@openhost.es>
    * @since   Mon Sep 13 2010 18:12:58 GMT+0200 (CEST)
    */
    function render($kind_of_page, $action, $ajax=0) {
        
        /**
         * If the vote+id cookie exist just show the results and don't allow to vote again
         */
        if (isset($_COOKIE["vote".$this->pk_rating])) $action="result";
        /**
         * Calculate the total votes to render
         */
        ($this->total_votes==0  ? $actual_votes = 0
                                : $actual_votes = (int)floor($this->total_value/$this->total_votes));
        
        $html_out = "";
        
        switch ($kind_of_page){
            
            case "home":
            case "article":
            case "video":
                
                $html_out .= "<ul class=\"voting\">";
                
                // if the user can vote render the links to vote
                if($action == "vote") {
                    
                    // Render links
                    $html_out .= $this->getVotesOnLinks($actual_votes, $kind_of_page);  
                   
                //if the user can't vote render the static images 
                } elseif($action === "result") {

                    // Render images
                    $html_out .= $this->getVotesOnImages($actual_votes, $kind_of_page);
                    
                }
                
                $html_out .= "</ul> ";
                
                // append the counter of total votes
                //$html_out .= $this->humanizeNumVotes($this->total_votes);
                
                // if this request is not an AJAX request wrap it.
                if (!$ajax) {
                    $html_out = "<span class=\"vota".$this->pk_rating."\">".$html_out."</span>";
                }
                break;
            
            default:
                $html_out = 'content type not supported by the vote system';
                
        }
        
        return $html_out;
    }
    
}
?>