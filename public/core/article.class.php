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
    
    static $clonesHash = null;
    /**
      * Constructor PHP5
    */
    public function __construct($id=null)
    {
        parent::__construct($id);
        
        // Si existe idcontenido, entonces cargamos los datos correspondientes
        if(is_numeric($id)) {
            $this->read($id);
        }

        $this->content_type = 'Article';

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
        $rs = $GLOBALS['application']->conn->Execute( $sql );
        
        if (!$rs) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
            
            return;
        }
        
        $this->load( $rs->fields );
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
    
    /* The Clone Methods: Methods to management clone articles {{{ */
    
    /**
     * Clone an article 
     */
    public function createClone($content)
    {
        $id = null;
        $data = array();
        
        if(!is_array($content)) {
            $id = $content;
            
            $commonProperties = array('title', 'category', 'with_comment', 'in_home', 'metadata', 'title_int',
                                      'subtitle', 'agency', 'summary', 'body', 'fk_video', 'img1', 'img1_footer',
                                      'img2', 'img2_footer', 'fk_video2', 'footer_video2', 'starttime', 'endtime',
                                      'description', 'fk_publisher');
            
            // Default properties
            $data['content_status'] = 0;
            $data['fk_publisher'] = $_SESSION['userid'];
            $data['fk_author']    = $_SESSION['userid'];
            
            // Copy other properties from original article
            $article = new Article($id);
            
            foreach($commonProperties as $property) {
                if(property_exists($article, $property)) {
                    $data[ $property ] = $article->{$property};
                }
            }
        } else {
            $id   = $content['id'];
            $data = $content;
            $data['content_status'] = 0;
            $data['fk_publisher']   = $_SESSION['userid']; // ghost value
        }
        
        // To forward action
        $_SESSION['_from'] = $data['category'];
        
        // Clear slash
        String_Utils::disabled_magic_quotes($data);
        
        if($this->create($data)) {
            // Save into table of cloned articles
            $this->saveClone($id, $this->id);
            
            return new Article($this->id);
        }
        
        return null;
    }
    
    /**
     * Save into table `articles_clone`
     *
     * @param string $pk_original
     * @param string $pk_clone
     * @return boolean
     */
    public function saveClone($pk_original, $pk_clone)
    {
        $values = array($pk_original, $pk_clone);
        
        $sql = 'INSERT INTO `articles_clone` (`pk_original`, `pk_clone`) VALUES (?, ?)';
        
        if($GLOBALS['application']->conn->Execute($sql, $values)===false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
            
            return false;
        }
        
        return true;
    }
    
    /**
     * Update from clone edition
     * ONLY fields that clone can update
     *
     * @param string $pk_content
     * @param array $formValues Data values from POST 
     */
    public function updateClone($pk_content, $formValues)
    {                   
        $formValues['fk_user_last_editor'] = $_SESSION['userid'];       
        
        // Update category {{{
        $cm = ContentCategoryManager::get_instance();
        $catName = $cm->get_name($formValues['category']);
        
        $filter = 'pk_fk_content=' . $pk_content;
        $fields = array('pk_fk_content_category' => $formValues['category'], 'catName' => $catName);
        SqlHelper::update('contents_categories', $fields, $filter);
        // }}}
        
        // Update articles table {{{
        $filter = '`pk_article` = ' . $pk_content;
        $fields = array('with_comment', 'columns', 'home_columns');
        SqlHelper::bindAndUpdate('articles', $fields, $formValues, $filter);
        // }}}
        
        // Update contents table {{{
        $formValues['permalink']      = $this->rebuildPermalink($pk_content, $catName);
        $formValues['content_status'] = $formValues['available'];
        $formValues['fk_publisher']   = $_SESSION['userid'];
        $formValues['fk_user_last_editor'] = $_SESSION['userid'];
        
        $filter = '`pk_content` = ' . $pk_content;
        $fields = array('starttime', 'endtime', 'content_status', 'available',
                        'fk_user_last_editor', 'frontpage', 'in_home', 'permalink');
        SqlHelper::bindAndUpdate('contents', $fields, $formValues, $filter);
        // }}}
        
        // Update related content {{{
        $this->saveRelated($formValues['ordenArti'], $pk_content, 'set_rel_position');        
        $this->saveRelated($formValues['ordenArtiInt'], $pk_content, 'set_rel_position_int');
        // }}}
        
        $this->clearCacheClone(array($pk_content));
        
        // Set to forward actions
        $_SESSION['_from'] = $formValues['category'];
        $this->id = $pk_content;
    }
    
    /**
     * Update from original edition
     * Once that original save values update clone values
     *
     * @param string $pk_original
     */
    public function updateCloneFromOriginal($pk_original, $formValues)
    {
        $clonesId = $this->getClones($pk_original);
        
        // Update articles table {{{
        $formValues['subtitle'] = strtoupper($formValues['subtitle']);
        
        $filter = '`pk_article` IN (' . implode(',', $clonesId) . ')';
        $fields = array('subtitle', 'agency', 'summary', 'body', 'img1',
                        'img1_footer', 'img2', 'img2_footer', 'fk_video',
                        'fk_video2', 'footer_video2');
        SqlHelper::bindAndUpdate('articles', $fields, $formValues, $filter);
        // }}}
        
        // Update articles table {{{
        $formValues['fk_user_last_editor'] = $_SESSION['userid'];
        $filter = '`pk_content` IN (' . implode(',', $clonesId) . ')';
        $fields = array('title', 'description', 'metadata', 'changed', 'fk_user_last_editor');
        SqlHelper::bindAndUpdate('contents', $fields, $formValues, $filter);
        // }}}
        
        // Remove caches
        $this->clearCacheClone($clonesId);
    }
    
    /**
     *
     * @param array $clonesId Array of clone pk_content
     */
    public function clearCacheClone($clonesId)
    {
        // remove caches {{{
        $tplManager = new TemplateCacheManager(TEMPLATE_USER_PATH);        
        $ccm = ContentCategoryManager::get_instance();
        
        $sql = 'SELECT `pk_fk_content`,`pk_fk_content_category` FROM `contents_categories` ' .
               'WHERE `pk_fk_content` IN (' . implode(',', $clonesId) . ')';
        $rs = $GLOBALS['application']->conn->Execute($sql);
        
        $cleanedYet = array();
        if($rs !== false) {
            while(!$rs->EOF) {
                $id = $rs->fields['pk_fk_content'];
                $catName = $ccm->get_name($rs->fields['pk_fk_content_category']);
                
                $tplManager->delete($catName . '|' . $id);
                $frontpage = $catName . '|0';
                
                if(!in_array($frontpage, $cleanedYet)) {
                    $tplManager->delete($frontpage);
                    $cleanedYet[] = $frontpage;
                }
                
                $rs->MoveNext();
            }
        }
        // }}}
    }
    
    /**
     * Search into articles_clone for an article to delete
     * Delete by pk_clone and also pk_original
     *
     * @param string $pk_content
     */
    public function deleteClone($pk_content)
    {
        $values = array($pk_content, $pk_content);
        
        $sql = 'DELETE FROM `articles_clone` WHERE (`pk_original` = ?) OR (`pk_clone` = ?)';
        
        if($GLOBALS['application']->conn->Execute($sql, $values)===false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();            
            
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
            
            return false;
        }
        
        return true;
    }
    
    /**
     * Unlink article from original
     *
     * @param string $pk_content
     */
    public function unlinkClone($pk_clone=null)
    {
        if(is_null($pk_clone)) {
            $pk_clone = $this->id;
        }
        $values = array($pk_clone);
        
        $sql = 'DELETE FROM `articles_clone` WHERE (`pk_clone` = ?)';
        
        if($GLOBALS['application']->conn->Execute($sql, $values)===false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
            
            return false;
        }
        
        return true;
    }
    
    public function getOriginal($pk_clone=null)
    {
        /* if(is_null($pk_clone)) {
            $pk_clone = $this->id;
        }
        
        $sql = 'SELECT pk_original FROM `articles_clone` WHERE `pk_clone` = ?';
        $pk_original = $GLOBALS['application']->conn->GetOne($sql, array($pk_clone));
        
        if($pk_original !== false) {
            return new Article($pk_original);
        } */
        
        if(is_null($pk_clone)) {
            $pk_clone = $this->id;
        }        
        
        $values = array();
        foreach(Article::$clonesHash as $clone => $original) {
            if(!strcmp($clone, $pk_clone)) {                
                return new Article($original);
            }
        }
        
        return null;
    }
    
    public function getOriginalPk($pk_clone=null)
    {
        if(is_null($pk_clone)) {
            $pk_clone = $this->id;
        }
        
        $values = array();
        foreach(Article::$clonesHash as $clone => $original) {
            if(!strcmp($clone, $pk_clone)) {
                return $original;
            }
        }
        
        return 0;
    }
    
    /**
     *
     */
    public function isClone($pk_content=null)
    {
        /* $values = array();
        
        if(!is_null($pk_content)) {
            $values = array($pk_content);
        } else {
            $values = array($this->id);
        }
        
        $sql = 'SELECT count(*) FROM `articles_clone` WHERE `pk_clone` = ?';
        return $GLOBALS['application']->conn->GetOne($sql, $values) > 0; */
        
        Article::loadHashClones();
        
        if(is_null($pk_content)) {
            $pk_content = $this->id;
        }
        
        return in_array($pk_content, array_keys(Article::$clonesHash));
    }
    
    /**
     *
     */
    public function hasClone($pk_content=null)
    {
        /* $values = array();
        
        if(!is_null($pk_content)) {
            $values = array($pk_content);
        } else {
            $values = array($this->id);
        }
        
        $sql = 'SELECT count(*) FROM `articles_clone` WHERE `pk_original` = ?';
        return $GLOBALS['application']->conn->GetOne($sql, $values) > 0; */
        
        Article::loadHashClones();
        
        if(is_null($pk_content)) {
            $pk_content = $this->id;
        }
        
        return in_array($pk_content, array_values(Article::$clonesHash));
    }    

    /**
     *
     */
    public function getClones($pk_content=null)
    {
        /* $values = array();
        
        if(!is_null($pk_content)) {
            $values = array($pk_content);
        } else {
            $values = array($this->id);
        }
        
        $sql = 'SELECT pk_clone FROM `articles_clone` WHERE `pk_original` = ?';
        return $GLOBALS['application']->conn->GetCol($sql, $values); */
        
        Article::loadHashClones();        
        
        if(is_null($pk_content)) {
            $pk_content = $this->id;
        }                
        
        $values = array();
        foreach(Article::$clonesHash as $clone => $original) {
            if(!strcmp($original, $pk_content)) {
                $values[] = $clone;
            }
        }
        
        return $values;
    }
    
    static public function loadHashClones()
    {
        if(is_null(self::$clonesHash)) {
            $sql = 'SELECT `pk_original`, `pk_clone` FROM `articles_clone`';
            $rs = $GLOBALS['application']->conn->Execute($sql);
            
            self::$clonesHash = array();
            
            if($rs !== false) {
                while(!$rs->EOF) {
                    self::$clonesHash[$rs->fields['pk_clone']] = $rs->fields['pk_original'];
                    
                    $rs->MoveNext();
                }
            }
        }
    }
    
    /* }}} methods clone */
    
    
    
}
