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
 * Article
 * 
 * @package    OpenNeMas
 * @copyright  Copyright (c) 2009 Openhost S.L. (http://openhost.es)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: article.class.php 1 2009-11-23 13:11:18Z vifito $
 */
class Article extends Content
{
    /**#@+
     * Article properties
     * 
     * @access public
     */
    var $pk_article    = null;
    var $subtitle      = null;
    var $agency        = null;
    var $summary       = null;
    var $body          = null;
    var $img1          = null;
    var $img1_footer   = null;
    var $img2          = null;
    var $img2_footer   = null;
    var $fk_video      = null;
    var $fk_video2     = null;
    var $footer_video2 = null;
    var $with_comment  = null;
    var $columns       = null;
    var $home_columns  = null;
    var $title_int     = null;
    /**#@-*/    
    
    public function __construct($id=null)
    {
        parent::__construct($id);
        
        if($id != null) {
            $this->read($id);
        }
    }

    public function create($data)
    {
        if(!$data['description']) {
            $data['description'] = String_Utils::get_num_words($data['body'], 50);
        }

        $data['subtitle']=mb_strtoupper($data['subtitle'],'UTF-8');
        $data['columns'] = 1;
        $data['home_columns'] = 1;
        $data['available'] = $data['content_status'];        
        
        parent::create($data);
        
        $sql = "INSERT INTO articles (`pk_article`, `subtitle`, `agency`, `summary`,`body`,
                               `img1`, `img1_footer`, `img2`, `img2_footer`,
                                `fk_video`, `fk_video2`, `footer_video2`,
                               `columns`, `home_columns`, `with_comment`, `title_int`) " .
                        "VALUES (?,?,?,?,?, ?,?,?,?, ?,?,?, ?,?,?,?)";
        
        $values = array($this->id, $data['subtitle'], $data['agency'],  $data['summary'], $data['body'], 
                    $data['img1'], $data['img1_footer'], $data['img2'], $data['img2_footer'],
                    $data['fk_video'], $data['fk_video2'],$data['footer_video2'],
                    $data['columns'], $data['home_columns'], $data['with_comment'], $data['title_int']);

        if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return false;
        }
 
        $this->saveRelated($data['ordenArti'], $this->id, 'set_rel_position');        
        $this->saveRelated($data['ordenArtiInt'], $this->id, 'set_rel_position_int');
        
        return true;
    }
    
    public function saveRelated($data, $id, $method)
    {
        $rel = new Related_content();
        
        //Articulos relacionados en portada
        if(isset($data)) {
            $tok = strtok($data, ",");
            $pos = 1;
            while (($tok !== false) && ($tok != " ")) {
                $rel->{$method}($id, $pos, $tok);
                $tok = strtok(",");
                $pos++;
            }
        }        
    }
    
    
    public function read($id)
    {
        parent::read($id);
        
        $sql = 'SELECT * FROM articles WHERE pk_article = '.($id);
        $rs  = $this->conn->Execute( $sql );
        
        if ($rs === false) {
            $error_msg = $this->conn->ErrorMsg();
            Zend_Registry::get('logger')->emerg($error_msg);
            
            return null;
        }
        
        $this->load( $rs->fields );
        
        return $this;
    }   
    
    public function update($data)
    {
        // If it's clone use special update {{{
        if($this->isClone($data['id'])) {
            $data = $this->updateClone($data['id'], $data);
            return true;
        }
        // }}} 
        
        // Update an article
        if(!$data['description']) {
            $data['description'] = String_Utils::get_num_words($data['body'], 50);
        }
        
        if(isset($data['available']) and !isset($data['content_status'])) {
            $data['content_status'] = $data['available'];
        }

        $data['subtitle']=mb_strtoupper($data['subtitle'],'UTF-8');
        
        $GLOBALS['application']->dispatch('onBeforeUpdate', $this);
        parent::update($data);
        
        if(!isset($data['home_columns'])) {
            $sql = "UPDATE articles SET `subtitle`=?, `agency`=?, `summary`=?, `body`=?, " .
                "`img1`=?, `img1_footer`=?, `img2`=?, `img2_footer`=?, ".
                "`fk_video`=?, `fk_video2`=?, `footer_video2`=?, ".
                "`columns`=?, `with_comment`=?, `title_int`=? " .
                "WHERE pk_article=".($data['id']);
            
            $values = array(strtoupper($data['subtitle']), $data['agency'], $data['summary'], $data['body'], 
                    $data['img1'], $data['img1_footer'], $data['img2'], $data['img2_footer'], 
                    $data['fk_video'], $data['fk_video2'], $data['footer_video2'],
                    $data['columns'], $data['with_comment'], $data['title_int']);
        } else {
            $sql = "UPDATE articles SET `subtitle`=?, `agency`=?, `summary`=?, `body`=?, " .
                "`img1`=?, `img1_footer`=?, `img2`=?, `img2_footer`=?, ".
                "`fk_video`=?, `fk_video2`=?, `footer_video2`=?, ".
                "`home_columns`=?, `with_comment`=?, `title_int`=? " .
                "WHERE pk_article=".($data['id']);
            
            $values = array(strtoupper($data['subtitle']), $data['agency'], $data['summary'], $data['body'], 
                    $data['img1'], $data['img1_footer'], $data['img2'], $data['img2_footer'], 
                    $data['fk_video'], $data['fk_video2'], $data['footer_video2'],
                    $data['home_columns'], $data['with_comment'], $data['title_int']);
        }
        
        if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
            return;
        }
        
        // articulos ordenArti y attaches ordenAtt
        $rel = new Related_content();
        
        //Eliminamos para volver a insertar por si borraron.
        $rel->delete($data['id']);
        
        $this->saveRelated($data['ordenArti'], $data['id'], 'set_rel_position');        
        $this->saveRelated($data['ordenArtiInt'], $data['id'], 'set_rel_position_int');                
        
        $this->category_name = $this->loadCategoryName($this->id);
        $GLOBALS['application']->dispatch('onAfterUpdate', $this);
        
        // If has clone then update
        if($this->hasClone($this->id)) {
            $this->updateCloneFromOriginal($this->id, $data);
        }
        
        return true;
    }

    public function remove($id)
    {
        parent::remove($id);
        
        $sql = 'DELETE FROM articles WHERE pk_article='.($id);
        
        $rel = new Related_content();
        $rel->delete($id); //Eliminamos con los que esta relacionados.
        
        $rel = new Comment();
        $rel->delete_comments($id); //Eliminamos  los comentarios.
        
        $this->deleteClone($id); // Eliminar clones
        
        if($GLOBALS['application']->conn->Execute($sql)===false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
            return;
        }                        
    }
    
    /**
     * Rebuild permalink using pk_content and catName
     *
     * @param string $pk_article
     * @param string $catName
     * @return string New permalink
     */
    public function rebuildPermalink($pk_article, $catName=null)
    {
        $article = new Article($pk_article);
        $slug = String_Utils::get_title($article->title, false);
        
        // prevent overflow field permalink
        $slug = String_Utils::str_stop($slug, 180);
        
        if(is_null($catName)) {
            $cm = ContentCategoryManager::get_instance();
            $catName = $cm->get_name($article->category);
        }
        
        $permalink = '/artigo/' . date('Y/m/d') . '/' . $catName .
                     '/' . $slug . '/' . $pk_article . '.html';
        
        return $permalink;
    }
    
    
    
    
}
