<?php
/*
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
/**
 * Class for handling articles.
 *
 * @package    Onm
 * @subpackage Model
 * @author     Fran Dieguez <fran@openhost.es>, Xov Ago 25 13:57:22 2011
 **/
class Article extends Content
{
    /**#@+
     * Article properties
     *
     * @access public
     */
    public $pk_article    = null;
    public $subtitle      = null;
    public $agency        = null;
    public $summary       = null;
    public $body          = null;
    public $img1          = null;
    public $img1_footer   = null;
    public $img2          = null;
    public $img2_footer   = null;
    public $fk_video      = null;
    public $fk_video2     = null;
    public $footer_video2 = null;
    public $with_comment  = null;
    public $columns       = null;
    public $home_columns  = null;
    public $title_int     = null;
    /**#@-*/

    public static $clonesHash = null;

    /**
     * Initializes the Article object from an ID
     *
     * @param int $id the id of the article we want to initialize
     *
     * @return void
     **/
    public function __construct($id=null)
    {
        parent::__construct($id);

        // Si existe idcontenido, entonces cargamos los datos correspondientes
        if (is_numeric($id)) {
            $this->read($id);
        }

        $this->content_type = 'Article';
    }

    /**
     * Magic method for populate properties on the fly
     *
     * @return mixed
     * @author
     **/
    public function __get($name)
    {
        switch ($name) {
            case 'uri':

                if (empty($this->category_name)) {
                    $this->category_name = $this->loadCategoryName($this->pk_content);
                }
                $uri =  Uri::generate(
                    'article',
                    array(
                        'id'       => sprintf('%06d', $this->id),
                        'date'     => date('YmdHis', strtotime($this->created)),
                        'category' => $this->category_name,
                        'slug'     => $this->slug2,
                    )
                );

                return $uri;
                break;

            case 'slug2':
                return StringUtils::get_title($this->title);
                break;

            case 'content_type_name':
                return 'Article';

            default:
                break;
        }

        return parent::__get($name);

    }

    /**
     * Creates one article from an array of properties
     *
     * @param mixed $data array of properties for the article
     *
     * @return null if the article was not created
     * @return int  the id of the article
     **/
    public function create($data)
    {
        if (!isset($data['description'])) {
            $data['description'] =
                StringUtils::get_num_words($data['body'], 50);
        }

        $data['subtitle']= $data['subtitle'];
        $data['columns'] = 1;
        $data['home_columns'] = 1;
        $data['available'] = $data['content_status'];
        $data['img1_footer'] =
            (!isset($data['img1_footer']) || empty($data['img1_footer']))
                ? ''
                : $data['img1_footer'];
        $data['img2_footer'] =
                (!isset($data['img2_footer']) || empty($data['img2_footer']))
                    ? ''
                    : $data['img2_footer'];
        $data['with_comment'] =
            (!isset($data['with_comment']) || empty($data['with_comment']))
                ? ''
                : intval($data['with_comment']);

        parent::create($data);

        $sql = "INSERT INTO articles (`pk_article`, `subtitle`, `agency`,
                            `summary`,`body`, `img1`, `img1_footer`,
                            `img2`, `img2_footer`, `fk_video`, `fk_video2`,
                            `footer_video2`, `columns`, `home_columns`,
                            `with_comment`, `title_int`) " .
                        "VALUES (?,?,?,?,?, ?,?,?,?, ?,?,?, ?,?,?,?)";

        $values = array(
            $this->id, $data['subtitle'], $data['agency'],  $data['summary'],
            $data['body'], $data['img1'], $data['img1_footer'],
            $data['img2'], $data['img2_footer'], $data['fk_video'],
            $data['fk_video2'], $data['footer_video2'], $data['columns'],
            $data['home_columns'], $data['with_comment'], $data['title_int']);

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $errorMsg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$errorMsg);
            $GLOBALS['application']->errors[] = 'Error: '.$errorMsg;

            return false;
        }
        if (!empty($data['relatedFront'])) {
            $this->saveRelated(
                $data['relatedFront'],
                $this->id,
                'setRelationPosition'
            );
        }
        if (!empty($data['relatedInner'])) {
            $this->saveRelated(
                $data['relatedInner'],
                $this->id,
                'setRelationPositionForInner'
            );
        }

        if (!empty($data['relatedHome'])) {
            $this->saveRelated(
                $data['relatedHome'],
                $this->id,
                'setHomeRelations'
            );
        }

        return $this->id;
    }

    /**
     * Relates a list of content ids to another one
     *
     * @param string $data   list of related content IDs
     * @param int    $id     the id of the content we want to relate other contents
     * @param string $method the method to bind related contents
     *
     * @return void
     **/
    public function saveRelated($data, $id, $method)
    {
        $rel = new RelatedContent();

        $contents = json_decode(json_decode($data), true);

        foreach ($contents as $content) {
            $rel->{$method}($id, $content['position'], $content['id']);
        }

    }

    /**
     * Reads the data for one article given one ID
     *
     * @param int $id the id to get its information
     *
     * @return void
     **/
    public function read($id)
    {
        parent::read($id);

        $sql = 'SELECT * FROM articles WHERE pk_article = '.($id);
        $rs = $GLOBALS['application']->conn->Execute($sql);

        if (!$rs) {
            \Application::logDatabaseError();

            return;
        }

        $this->load($rs->fields);

        $this->permalink = Uri::generate(
            'article',
            array(
                'id' => $this->id,
                'date' => date('Y-m-d', strtotime($this->created)),
                'category' => $this->category_name,
                'slug' => $this->slug,
            )
        );

    }

    /**
     * Updates the information for one article given an array for the new data
     *
     * @return void
     * @author
     **/
    public function update($data)
    {
        if (isset($data['content_status']) && !isset($data['available'])) {
            $data['available'] = $data['content_status'];
        }

        // Update an article
        if (!$data['description']) {
            $data['description'] = StringUtils::get_num_words(
                $data['body'],
                50
            );
        }

        $data['subtitle']=mb_strtoupper($data['subtitle'], 'UTF-8');
        $data['img1_footer'] =
            (!isset($data['img1_footer']) || empty($data['img1_footer']))
            ? ''
            : $data['img1_footer'];
        $data['img2_footer'] =
            (!isset($data['img2_footer']) || empty($data['img2_footer']))
            ? ''
            : $data['img2_footer'];
        $data['with_comment'] =
        (!isset($data['with_comment']) || empty($data['with_comment']))
            ? ''
            : intval($data['with_comment']);

        $GLOBALS['application']->dispatch('onBeforeUpdate', $this);
        parent::update($data);

        $sql = "UPDATE articles "
                ."SET `subtitle`=?, `agency`=?, `summary`=?, `body`=?, "
                ."`img1`=?, `img1_footer`=?, `img2`=?, `img2_footer`=?, "
                ."`fk_video`=?, `fk_video2`=?, `footer_video2`=?, "
                ."`columns`=?, `with_comment`=?, `title_int`=? "
                ."WHERE pk_article=".($data['id']);

        $values = array(
            strtoupper($data['subtitle']), $data['agency'],
            $data['summary'], $data['body'],
            $data['img1'], $data['img1_footer'], $data['img2'],
            $data['img2_footer'], $data['fk_video'], $data['fk_video2'],
            $data['footer_video2'],
            (isset($data['columns']))?$data['columns']:'',
            $data['with_comment'], $data['title_int']
        );

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            \Application::logDatabaseError();

            return;
        }

        // articulos ordenArti y attaches ordenAtt
        $rel = new RelatedContent();

        //Eliminamos para volver a insertar por si borraron.
        $rel->delete($data['id']);

        if (!empty($data['relatedFront'])) {
            $this->saveRelated(
                $data['relatedFront'],
                $data['id'],
                'setRelationPosition'
            );
        }
        if (!empty($data['relatedInner'])) {
            $this->saveRelated(
                $data['relatedInner'],
                $data['id'],
                'setRelationPositionForInner'
            );
        }

        if (!empty($data['relatedHome'])) {
            $this->saveRelated(
                $data['relatedHome'],
                $this->id,
                'setHomeRelations'
            );
        }

        $this->category_name = $this->loadCategoryName($this->id);


        return true;
    }

    /**
     * Deletes permanently one article given one  id
     *
     * @param int $id the id of the article we want to delete
     *
     * @return void
     **/
    public function remove($id)
    {
        parent::remove($id);

        $sql = 'DELETE FROM articles WHERE pk_article='.($id);

        $rel = new RelatedContent();
        $rel->delete($id); //Eliminamos con los que esta relacionados.

        $rel = new Comment();
        $rel->delete_comments($id); //Eliminamos  los comentarios.

        $this->deleteClone($id); // Eliminar clones

        if ($GLOBALS['application']->conn->Execute($sql)===false) {
            \Application::logDatabaseError();

            return;
        }
    }

    /**
     * Rebuilds the article permalink given its id and catName
     *
     * @param string $articlePK the id of the article
     * @param string $catName   the name of the article category
     *
     * @return string The new permalink
     */
    public function rebuildPermalink($articlePK, $catName=null)
    {
        $article = new Article($articlePK);
        $slug = StringUtils::get_title($article->title, false);

        // prevent overflow field permalink
        $slug = StringUtils::str_stop($slug, 180);

        if (is_null($catName)) {
            $cm = ContentCategoryManager::get_instance();
            $catName = $cm->get_name($article->category);
        }

        $permalink = '/artigo/' . date('Y/m/d') . '/' . $catName .
                     '/' . $slug . '/' . $articlePK . '.html';

        return $permalink;
    }

    public function createClone($content)
    {
        $id = null;
        $data = array();

        if (!is_array($content)) {
            $id = $content;

            $commonProperties = array(
                'title', 'category', 'with_comment', 'in_home', 'metadata',
                'title_int', 'subtitle', 'agency', 'summary', 'body',
                'fk_video', 'img1', 'img1_footer', 'img2', 'img2_footer',
                'fk_video2', 'footer_video2', 'starttime', 'endtime',
                'description', 'fk_publisher'
            );

            // Default properties
            $data['content_status'] = 0;
            $data['fk_publisher'] = $_SESSION['userid'];
            $data['fk_author']    = $_SESSION['userid'];

            // Copy other properties from original article
            $article = new Article($id);

            foreach ($commonProperties as $property) {
                if (property_exists($article, $property)) {
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
        StringUtils::disabled_magic_quotes($data);

        if ($this->create($data)) {
            // Save into table of cloned articles
            $this->saveClone($id, $this->id);

            return new Article($this->id);
        }

        return null;
    }

    /**
     * Save into table `articles_clone`
     *
     * @param  string  $originalPK
     * @param  string  $clonePK
     * @return boolean
     */
    public function saveClone($originalPK, $clonePK)
    {
        $values = array($originalPK, $clonePK);

        $sql = 'INSERT INTO `articles_clone` (`pk_original`, `pk_clone`)'
                .'VALUES (?, ?)';

        if ($GLOBALS['application']->conn->Execute($sql, $values)===false) {
            \Application::logDatabaseError();

            return false;
        }

        return true;
    }

    /**
     * Update from clone edition
     * ONLY fields that clone can update
     *
     * @param string $contentPK
     * @param array  $formValues Data values from POST
     */
    public function updateClone($contentPK, $formValues)
    {
        $formValues['fk_user_last_editor'] = $_SESSION['userid'];

        // Update category {{{
        $cm = ContentCategoryManager::get_instance();
        $catName = $cm->get_name($formValues['category']);

        $filter = 'pk_fk_content=' . $contentPK;
        $fields = array(
            'pk_fk_content_category' => $formValues['category'],
            'catName' => $catName
        );
        SqlHelper::update('contents_categories', $fields, $filter);
        // }}}

        // Update articles table {{{
        $filter = '`pk_article` = ' . $contentPK;
        $fields = array('with_comment', 'columns', 'home_columns');
        SqlHelper::bindAndUpdate('articles', $fields, $formValues, $filter);
        // }}}

        // Update contents table {{{
        $formValues['permalink']      = $this->rebuildPermalink(
            $contentPK, $catName
        );
        $formValues['content_status'] = $formValues['available'];
        $formValues['fk_publisher']   = $_SESSION['userid'];
        $formValues['fk_user_last_editor'] = $_SESSION['userid'];

        $filter = '`pk_content` = ' . $contentPK;
        $fields = array(
            'starttime', 'endtime', 'content_status', 'available',
            'fk_user_last_editor', 'frontpage', 'in_home', 'permalink'
        );
        SqlHelper::bindAndUpdate('contents', $fields, $formValues, $filter);
        // }}}

        // Update related content {{{
        $this->saveRelated(
            $formValues['ordenArti'],
            $contentPK,
            'setRelationPosition'
        );
        $this->saveRelated(
            $formValues['ordenArtiInt'],
            $contentPK,
            'setRelationPositionForInner'
        );
        // }}}

        $this->clearCacheClone(array($contentPK));

        // Set to forward actions
        $_SESSION['_from'] = $formValues['category'];
        $this->id = $contentPK;
    }

    /**
     * Update from original edition
     * Once that original save values update clone values
     *
     * @param string $originalPK
     */
    public function updateCloneFromOriginal($originalPK, $formValues)
    {
        $clonesId = $this->getClones($originalPK);

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
        $fields = array(
            'title', 'description',
            'metadata', 'changed',
            'fk_user_last_editor'
        );
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

        $sql = 'SELECT `pk_fk_content`,`pk_fk_content_category`'
            .'FROM `contents_categories` '
            .'WHERE `pk_fk_content` IN (' . implode(',', $clonesId) . ')';
        $rs = $GLOBALS['application']->conn->Execute($sql);

        $cleanedYet = array();
        if ($rs !== false) {
            while (!$rs->EOF) {
                $id = $rs->fields['pk_fk_content'];
                $catName = $ccm->get_name(
                    $rs->fields['pk_fk_content_category']
                );

                $tplManager->delete($catName . '|' . $id);
                $frontpage = $catName . '|0';

                if (!in_array($frontpage, $cleanedYet)) {
                    $tplManager->delete($frontpage);
                    $cleanedYet[] = $frontpage;
                }

                $rs->MoveNext();
            }
        } else {
            \Application::logDatabaseError();

            return false;
        }
        // }}}
    }

    /**
     * Search into articles_clone for an article to delete
     * Delete by pk_clone and also pk_original
     *
     * @param string $contentPK
     */
    public function deleteClone($contentPK)
    {
        $values = array($contentPK, $contentPK);

        $sql =  'DELETE FROM `articles_clone` '
                .'WHERE (`pk_original` = ?) OR (`pk_clone` = ?)';

        if ($GLOBALS['application']->conn->Execute($sql, $values)===false) {
            $errorMsg = Application::logDatabaseError();

            return false;
        }

        return true;
    }

    /**
     * Unlink article from original
     *
     * @param string $contentPK
     */
    public function unlinkClone($clonePK=null)
    {
        if (is_null($clonePK)) {
            $clonePK = $this->id;
        }
        $values = array($clonePK);

        $sql = 'DELETE FROM `articles_clone` WHERE (`pk_clone` = ?)';

        if ($GLOBALS['application']->conn->Execute($sql, $values)===false) {
            $errorMsg = $GLOBALS['application']->conn->ErrorMsg();

            $GLOBALS['application']->logger->debug('Error: '.$errorMsg);
            $GLOBALS['application']->errors[] = 'Error: '.$errorMsg;

            return false;
        }

        return true;
    }

    public function getOriginal($clonePK=null)
    {
        /* if (is_null($clonePK)) {
            $clonePK = $this->id;
        }

        $sql = 'SELECT pk_original FROM `articles_clone` WHERE `pk_clone` = ?';
        $originalPK = $GLOBALS['application']->conn->GetOne(
            $sql,
            array($clonePK)
        );

        if ($originalPK !== false) {
            return new Article($originalPK);
        } */

        if (is_null($clonePK)) {
            $clonePK = $this->id;
        }

        $values = array();
        foreach (Article::$clonesHash as $clone => $original) {
            if (!strcmp($clone, $clonePK)) {
                return new Article($original);
            }
        }

        return null;
    }

    public function getOriginalPk($clonePK=null)
    {
        if (is_null($clonePK)) {
            $clonePK = $this->id;
        }

        $values = array();
        foreach (Article::$clonesHash as $clone => $original) {
            if (!strcmp($clone, $clonePK)) {
                return $original;
            }
        }

        return 0;
    }

    /**
     *
     */
    public function isClone($contentPK=null)
    {
        /* $values = array();

        if (!is_null($contentPK)) {
            $values = array($contentPK);
        } else {
            $values = array($this->id);
        }

        $sql = 'SELECT count(*) FROM `articles_clone` WHERE `pk_clone` = ?';

        return $GLOBALS['application']->conn->GetOne($sql, $values) > 0; */

        Article::loadHashClones();

        if (is_null($contentPK)) {
            $contentPK = $this->id;
        }

        return in_array($contentPK, array_keys(Article::$clonesHash));
    }

    /**
     *
     */
    public function hasClone($contentPK=null)
    {
        /* $values = array();

        if (!is_null($contentPK)) {
            $values = array($contentPK);
        } else {
            $values = array($this->id);
        }

        $sql = 'SELECT count(*) FROM `articles_clone` WHERE `pk_original` = ?';

        return $GLOBALS['application']->conn->GetOne($sql, $values) > 0; */

        Article::loadHashClones();

        if (is_null($contentPK)) {
            $contentPK = $this->id;
        }

        return in_array($contentPK, array_values(Article::$clonesHash));
    }

    /**
     *
     */
    public function getClones($contentPK=null)
    {
        /* $values = array();

        if (!is_null($contentPK)) {
            $values = array($contentPK);
        } else {
            $values = array($this->id);
        }

        $sql = 'SELECT pk_clone FROM `articles_clone` WHERE `pk_original` = ?';

        return $GLOBALS['application']->conn->GetCol($sql, $values); */

        Article::loadHashClones();

        if (is_null($contentPK)) {
            $contentPK = $this->id;
        }

        $values = array();
        foreach (Article::$clonesHash as $clone => $original) {
            if (!strcmp($original, $contentPK)) {
                $values[] = $clone;
            }
        }

        return $values;
    }

    public static function loadHashClones()
    {
        if (is_null(self::$clonesHash)) {
            $sql = 'SELECT `pk_original`, `pk_clone` FROM `articles_clone`';
            $rs = $GLOBALS['application']->conn->Execute($sql);

            self::$clonesHash = array();

            if ($rs !== false) {
                while (!$rs->EOF) {
                    self::$clonesHash[$rs->fields['pk_clone']] =
                        $rs->fields['pk_original'];

                    $rs->MoveNext();
                }
            }
        }
    }

    /* }}} methods clone */

    /**
     * Renders the article given a set of parameters
     *
     * @return string the final html for the article
     **/
    public function render($params, $tpl = null)
    {

        //  if (!isset($tpl)) {
            $tpl = new Template(TEMPLATE_USER);
        //}

        $tpl->assign('item', $this);
        $tpl->assign('cssclass', $params['cssclass']);

        try {
            $html = $tpl->fetch($params['tpl']);
        } catch (\Exception $e) {
            $html = 'Article not available';
        }

        return $html;
    }

}
