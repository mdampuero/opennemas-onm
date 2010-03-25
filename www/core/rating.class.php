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
 * Rating
 * 
 * @package    OpenNeMas
 * @copyright  Copyright (c) 2009 Openhost S.L. (http://openhost.es)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: rating.class.php 1 2009-11-30 14:20:46Z vifito $
 */
class Rating
{
    var $pk_rating    = null;
    var $total_votes  = null;
    var $total_value  = null;
    var $ips_count_rating  = null;
    
    /**
     * Messages to use in links and image
     */
    private $messages = array('', 'Sin interés',
                              'Poco interesante',
                              'De interés',
                              'Muy interesante',
                              'Imprescindible');

    function Rating($id=null) {
        // Si existe idcontenido, entonces cargamos los datos correspondientes
        if(!is_null($id)) {
            $this->read($id);
        }
    }

    /**
      * Constructor PHP5
    */
    function __construct($id=null) {
        $this->Rating($id);
    }

    function create($pk_rating) {
        $sql = "INSERT INTO contents (`pk_rating`,`total_votes`, `total_value`, `ips_count_rating`)
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
            //comporbamos que el contenido exista y depues creamos la valoración
            $this->create($pk_rating);
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
    
    
    private function renderLink($i, $page, $pk_rating, $value)
    {
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
        if($page=='video'){
             $sufijo='-black';
        }else{
            $sufijo='';
        }
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
    
    private function renderImg($i, $value, $page='article')
    {
        $imgPath = TEMPLATE_USER_PATH_WEB . "images/utilities/";
        $imageTpl = '<img src="%s%s.png" alt="%s" title="%s" />';
        if($page=='video'){
             $sufijo='-black';
        }else{
            $sufijo='';
        }
        return sprintf($imageTpl, $imgPath, ($value>=$i) ? "f-star".$sufijo : "e-star".$sufijo, $this->messages[$i], $this->messages[$i]);
        

    }
    
    function render($page, $type, $ajax=0) {
        if (isset($_COOKIE["vote".$this->pk_rating])) $type="result";
        
        ($this->total_votes==0 ? $value=0 : $value = floor($this->total_value/$this->total_votes));
        
        $html_out = "";                        
        if($page == "home") {
            if($type == "vote") {
                // Render links
                for($i=1; $i <= 5; $i++) {
                    $html_out .= $this->renderLink($i, 'home', $this->pk_rating, $value);
                }                
                
                $html_out .= " ".$this->total_votes." voto/s";
                
            } elseif($type === "result") {                
                $value = floor($this->total_value/$this->total_votes);
                
                // Render images
                for($i=1; $i <= 5; $i++) {
                    $html_out .= $this->renderImg($i, $value, $page);
                }                
                
                $html_out .= " ".$this->total_votes." voto/s";
                
            }
            if (!$ajax) {
                $html_out = "<div class=\"vota".$this->pk_rating."\">".$html_out;
                $html_out .= "</div>";
            }
        } elseif($page=="article") {
            
            // Render images
            for($i=1; $i <= 5; $i++) {
                $results .= $this->renderImg($i, $value, $page);
            }
            
            $results .= " ".$this->total_votes." voto/s";
                
            if($type=="vote") {
               
                $html_out .= " ";
                $html_out .= "  Vota <ul class='voting'>";
                // Render links
                for($i=1; $i <= 5; $i++) {
                    $html_out .= $this->renderLink($i, $page, $this->pk_rating, $value);
                }                                                
                $html_out .= "  </ul>";
                $html_out .= "    Resultados <ul class='voting'>";
                $html_out .= $results;
                $html_out .= "  </ul>";
                $html_out .= "  ";
            } elseif($type==="result") {
               
                $html_out .= "  <span class=\"CVotos\">";
                $html_out .= "  ¡Gracias por su participación! ";
                $html_out .= "  </span> ";
                $html_out .= "  <span class=\"CVotos\">";
                $html_out .= $results;
                $html_out .= "  </span>";
                $html_out .= "  ";
            }
            if (!$ajax) {
                $html_out = "<span class=\"vota".$this->pk_rating."\">".$html_out;
                $html_out .= "</span>";
            }
        }elseif($page=="video") {

            // Render images
            for($i=1; $i <= 5; $i++) {
                $results .= $this->renderImg($i, $value, $page);
            }

            $results .= " ".$this->total_votes." voto/s";

            if($type=="vote") {

                $html_out .= " ";
                $html_out .= "  Vota <ul class='voting'>";
                // Render links
                for($i=1; $i <= 5; $i++) {
                    $html_out .= $this->renderLink($i, $page, $this->pk_rating, $value);
                }
                $html_out .= "  </ul>";
                $html_out .= "    Resultados <ul class='voting'>";
                $html_out .= $results;
                $html_out .= "  </ul>";
                $html_out .= "  ";
            } elseif($type==="result") {

                $html_out .= "  <span class=\"CVotos\">";
                $html_out .= "  ¡Gracias por su participación! ";
                $html_out .= "  </span> ";
                $html_out .= "  <span class=\"CVotos\">";
                $html_out .= $results;
                $html_out .= "  </span>";
                $html_out .= "  ";
            }
            if (!$ajax) {
                $html_out = "<span class=\"vota".$this->pk_rating."\">".$html_out;
                $html_out .= "</span>";
            }
        }
   
        return $html_out;
    }
}
?>