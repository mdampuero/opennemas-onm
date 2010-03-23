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
 * Vote
 * 
 * @package    OpenNeMas
 * @copyright  Copyright (c) 2009 Openhost S.L. (http://openhost.es)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: vote.class.php 1 2009-12-10 20:20:46Z   $
 */
class Vote
{
    var $pk_vote    = null;
    var $value_pos  = null;
    var $value_neg  = null;
    var $karma      = null;
    var $ips_count_vote  = null;
    
    /**
     * Messages to use in links and image
     */
   private $messages = array('', 'A Favor',
                              'En Contra');


    function Vote($id=null) {
        // Si existe idcontenido, entonces cargamos los datos correspondientes
        if(!is_null($id)) {
            $this->read($id);
        }
    }

    /**
      * Constructor PHP5
    */
    function __construct($id=null) {
        $this->Vote($id);
    }

    function create($pk_vote,$vote,$ip) {

      
        $sql = "INSERT INTO votes (`pk_vote`,`value_pos`, `value_neg`, `karma`, `ips_count_vote`)
                    VALUES (?,?,?,?,?)";
        if($vote=='2'){ // En contra
            $value_neg=1;
            $karma=100-1;
            $value_pos=0;
        }else{ // A favor
            $value_pos=1;
            $karma=100+1;
            $value_neg=0;
        }
        $ips_count_vote[]  = array('ip' => $ip, 'count' => 1);

        $values = array($pk_vote, $value_pos,$value_neg,$karma, serialize($ips_count_vote));
 
        if($GLOBALS['application']->conn->Execute($sql, $values) === false) {

            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

              return(false);
        }

        return(true);
    }    

    function read($pk_vote) {
        $sql = 'SELECT value_pos, value_neg, ips_count_vote
                FROM votes WHERE pk_vote ='.$pk_vote;

        $rs = $GLOBALS['application']->conn->Execute( $sql );

 
        if ($rs->EOF) {
            //Si no existe un votacion pinta 0
             $sql = "INSERT INTO votes (`pk_vote`,`value_pos`, `value_neg`, `karma`, `ips_count_vote`)
                    VALUES (?,?,?,?,?)";
             $values = array($pk_vote, 0,0,100, serialize(array()));


            if($GLOBALS['application']->conn->Execute($sql, $values) === false) {

                $error_msg = $GLOBALS['application']->conn->ErrorMsg();
                $GLOBALS['application']->logger->debug('Error: '.$error_msg);
                $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

                  return(false);
            }

            $this->pk_vote = $pk_vote;
            $this->value_pos = 0;
            $this->value_neg = 0;
            $this->karma =100;
            $this->ips_count_vote = array();
        }else{

            $this->pk_vote = $pk_vote;
            $this->value_pos = $rs->fields['value_pos'];
            $this->value_neg = $rs->fields['value_neg'];
            $this->karma = $rs->fields['karma'];
            $this->ips_count_vote = unserialize($rs->fields['ips_count_vote']);
        }
        return (true);
    }        
        
    function update($vote,$ip) {
        $this->ips_count_vote = $this->add_count($this->ips_count_vote,$ip);
        if (!$this->ips_count_vote) return(false);
       
        if($vote=='2'){
            $value= ++$this->value_neg;
            $sql = "UPDATE votes SET  `value_neg`=?,  `ips_count_vote`=?
            WHERE pk_vote=".$this->pk_vote;
        }else{
            $value= ++$this->value_pos;
            $sql = "UPDATE votes SET  `value_pos`=?,  `ips_count_vote`=?
            WHERE pk_vote=".$this->pk_vote;
        }
 
        $values = array($value, 
                        serialize($this->ips_count_vote));

        if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return(false);
        }

        //creamos la cookie
        $GLOBALS['application']->setcookie_secure("vote".$this->pk_vote, 'true', time()+60*60*24*30);

        return(true);

    }

    //ADD adressIP to votes array. Only permit 50 from  IP.
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

    //Get karma value
    function get_karma($pk_vote) {
        $sql = 'SELECT karma
                FROM votes WHERE pk_vote ='.$pk_vote;

        $rs = $GLOBALS['application']->conn->Execute( $sql );
        if (!$rs) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
            return;
        }

        return $rs->fields['karma'];

    }
    

    // Render imgs without vote's links.
    private function renderImg($i)
    {
        $imgPath = TEMPLATE_USER_PATH_WEB . "images/utilities/";
        $imageTpl = '<img src="%s%s.png" style="vertical-align:middle;" alt="%s" title="%s" /> ( %d ) ';

        return sprintf($imageTpl, $imgPath, ($i%2==0) ? "vote-down" : "vote-up", $this->messages[$i], $this->messages[$i],
                                 ($i%2==0)? $this->value_neg : $this->value_pos);
    }


    // Render imgs with links.
    private function renderLink($i, $pk_vote, $value)
    {
        /*
            <a href="javascript:vote_comment('".$_SERVER['REMOTE_ADDR']."',1,','".$this->pk_vote."')" title="A favor">
               <img id="$this->pk_vote_1"
                    src="TEMPLATE_USER_PATH_WEB."images/noticias/vote_pos.png "
                    alt="A favor" />
            </a>
        */

        $imgPath = TEMPLATE_USER_PATH_WEB . "images/utilities/";
        $linkTpl = <<< LINKTPLDOC
            <a href="#votar" onclick="javascript:vote_comment('%s', '%s', '%s'); return false;" title="%s">
                <img id="%s_%s" style="vertical-align:middle;"
                     src="{$imgPath}%s.png"
                     alt="%s" /> </a>   ( %d )
            
LINKTPLDOC;

        return sprintf($linkTpl, $_SERVER['REMOTE_ADDR'], $i, $pk_vote,   $this->messages[$i],
                                // $pk_vote, $i,
                                 $i, $pk_vote,
                                 ($i%2==0)? "vote-down" : "vote-up",                                
                                 $this->messages[$i], ($i%2==0)? $this->value_neg : $this->value_pos);
    }


    function render($page, $type, $ajax=0) {
        if (isset($_COOKIE["vote".$this->pk_vote])) $type="result";
        

        $html_out = "";                        
  
                
            if($type=="vote") {
                // Render links
                for($i=1; $i <= 2; $i++) {
                    $results .= $this->renderLink($i, $this->pk_vote, $value);
                }
                $html_out .= "  <div class=\"CVotos\">";
                $html_out .= $results;
                $html_out .= "  </div>";


            } elseif($type=="result") {
                 for($i=1; $i <= 2; $i++) {
                    $results .= $this->renderImg($i);
                }
                $html_out .= "  <div class=\"CVotos\">";
                $html_out .= $results;
                $html_out .= "  </div>";
                $html_out .= "  <div class=\"separadorVotos\"></div>";
                $html_out .= "  <div class=\"CVotos\">";
                $html_out .= "  ¡Gracias por votar!";
                $html_out .= "  </div>";
            }
            if (!$ajax) {
                $html_out = "<div class=\"CComent_Votos_nota\" id=\"vota".$this->pk_vote."\">".$html_out;
                $html_out .= "</div>";
            }

   
        return $html_out;
    }
}
 