<?php
/*
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
/**
 * Handles all the CRUD operations over opinions.
 *
 * @package    Onm
 * @subpackage Model
 * @author     Fran Dieguez <fran@openhost.es>
 **/
class Opinion extends Content
{

    public $pk_opinion            = null;
    public $fk_content_categories = null;
    public $fk_author             = null;
    public $body                  = null;
    public $author                = null;
    public $fk_author_img         = null;
    public $with_comment          = null;
    public $fk_author_img_widget  = null;

    private static $instance     = null;

    /**
    * Array of authors
    */
    private $authorNames         = null;

    public function __construct($id = null)
    {
        parent::__construct($id);

        if (is_numeric($id)) {
            $this->read($id);
        }

        $this->content_type = 'Opinion';
        $this->content_type_l10n_name = _('Opinion');
    }

    public function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new Opinion();

            return self::$instance;
        } else {
            return self::$instance;
        }
    }

    public function __get($name)
    {
        switch ($name) {
            case 'uri':
                if ($this->fk_author == 0) {

                    if ((int) $this->type_opinion == 1) {
                        $authorName = 'Editorial';
                    } elseif ((int) $this->type_opinion == 2) {
                        $authorName = 'Director';
                    }

                } else {
                    $author     = new Author($this->fk_author);
                    $authorName = $author->name;
                    if (empty($authorName)) {
                        $authorName = 'author';
                    }
                }

                $uri =  Uri::generate(
                    'opinion',
                    array(
                        'id'       => sprintf('%06d', $this->id),
                        'date'     => date('YmdHis', strtotime($this->created)),
                        'slug'     => $this->slug,
                        'category' => StringUtils::get_title($authorName),
                    )
                );
                //'opinion/_AUTHOR_/_DATE_/_SLUG_/_ID_.html'
                return $uri;

                break;
            case 'slug':
                return StringUtils::get_title($this->title);

                break;
            case 'content_type_name':
                return 'Opinion';

                break;
            case 'author_object':
                if ((int) $this->type_opinion == 1) {
                    $authorObj = new Stdclass();
                    $authorObj->name = 'Editorial';
                } elseif ((int) $this->type_opinion == 2) {
                    $authorObj = new Stdclass();
                    $authorObj->name = 'Director';
                } else {
                    $authorObj = new Author($this->fk_author);
                }

                return $authorObj;

                break;
            default:
                return parent::__get($name);

                break;
        }
    }

    public function create($data)
    {
        $data['content_status'] = $data['available'];
        $data['position']   =  1;
         // Editorial o director
        if (!isset($data['fk_author'])) {
            $data['fk_author'] = $data['type_opinion'];
        }
        (isset($data['fk_author_img']))
            ? $data['fk_author_img'] : $data['fk_author_img'] = null ;
        (isset($data['fk_author_img_widget']))
            ? $data['fk_author_img_widget'] : $data['fk_author_img_widget']=null;

        parent::create($data);

        $sql = 'INSERT INTO opinions (`pk_opinion`, `fk_author`, `body`,
            `fk_author_img`,`with_comment`, type_opinion,fk_author_img_widget)
            VALUES (?,?,?,?,?,?,?)';

        $values = array(
            $this->id,
            $data['fk_author'],
            $data['body'],
            $data['fk_author_img'],
            $data['with_comment'],
            $data['type_opinion'],
            $data['fk_author_img_widget']
        );

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            \Application::logDatabaseError();

            return false;
        }

        return $this->id;
    }

    public function read($id)
    {
        parent::read($id);
        $sql = 'SELECT opinions.*, authors.name, authors.condition, '
            .'authors.blog, authors.politics, author_imgs.path_img  '
            .'FROM opinions '
            .'LEFT JOIN authors ON (opinions.fk_author=authors.pk_author)'
            .'LEFT JOIN author_imgs ON (opinions.fk_author_img=author_imgs.pk_img)'
            .' WHERE pk_opinion = '.($id).' ';

        $rs = $GLOBALS['application']->conn->Execute($sql);

        if (!$rs) {
            \Application::logDatabaseError();

            return;
        }

        if ((int) $rs->fields['fk_author'] == 0) {
            if ((int) $rs->fields['type_opinion'] == 1) {
                $rs->fields['author'] = 'Editorial';
            } elseif ((int) $rs->fields['type_opinion'] == 2) {
                $rs->fields['author'] = 'Director';
            }
        } else {
            $rs->fields['author'] =  $rs->fields['name'] ;
        }

        $this->load($rs->fields);
    }

    /**
     * Returns the author name
     *
     * @param int $fkAuthor
     *
     * @return String Return name of author
     */
    public function get_author_name($fkAuthor)
    {
        if (is_null($this->authorNames)) {
            $sql = 'SELECT pk_author, name FROM `authors`';
            $rs = $GLOBALS['application']->conn->Execute($sql);

            while (!$rs->EOF) {
                $this->authorNames[ $rs->fields['pk_author'] ] =
                    $rs->fields['name'];

                $rs->MoveNext();
            }
        }

        if (!isset($this->authorNames[$fkAuthor])) {
            return '';
        }

        return $this->authorNames[$fkAuthor];
    }

    public function update($data)
    {
        $data['content_status']= $data['available'];
        if (!isset($data['fk_author'])) {
            $data['fk_author'] = $data['type_opinion'];
        } // Editorial o director
        (isset($data['fk_author_img']))
            ? $data['fk_author_img'] : $data['fk_author_img'] = null ;
        (isset($data['fk_author_img_widget']))
            ? $data['fk_author_img_widget'] : $data['fk_author_img_widget']=null;

        parent::update($data);

        $sql = "UPDATE opinions "
             . "SET `fk_author`=?, `body`=?,`fk_author_img`=?, "
             . "`with_comment`=?, `type_opinion`=?, `fk_author_img_widget`=? "
             . "WHERE pk_opinion=?";

        $values = array(
            $data['fk_author'],
            $data['body'],
            $data['fk_author_img'],
            $data['with_comment'],
            $data['type_opinion'],
            $data['fk_author_img_widget'],
            $data['id']
        );

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            \Application::logDatabaseError();

            return false;
        }

        $GLOBALS['application']->dispatch('onAfterUpdateOpinion', $this);

        return true;
    }

    public function remove($id)
    {
        parent::remove($id);

        $sql = 'DELETE FROM opinions WHERE pk_opinion ='.($id);

        if ($GLOBALS['application']->conn->Execute($sql)===false) {
            \Application::logDatabaseError();

            return;
        }
    }

    public function find_by_gender($gender)
    {
        $sql = 'SELECT contents.title, opinions.pk_opinion, opinions.fk_author,'
             . '       opinions.fk_author_img, opinions.fk_author_img_widget, '
             . '       contents.pk_content, contents.permalink, authors.name '
             . 'FROM opinions, contents, authors WHERE in_litter=0 '
             . 'AND content_status=1 and type_opinion=0 '
             . 'AND authors.pk_author= opinions.fk_author '
             . 'AND  pk_opinion = pk_content '
             . 'AND  gender=? ORDER BY created DESC';

        $rs = $GLOBALS['application']->conn->Execute($sql, array($gender));
        $i = 0;

        while (!$rs->EOF) {
            $items[$i]->pk_opinion           = $rs->fields['pk_opinion'];
            $items[$i]->permalink            = $rs->fields['permalink'];
            $items[$i]->title                = $rs->fields['title'];
            $items[$i]->name                 = $rs->fields['name'];
            $items[$i]->fk_author            = $rs->fields['fk_author'];
            $items[$i]->fk_author_img        = $rs->fields['fk_author_img'];
            $items[$i]->fk_author_img_widget = $rs->fields['fk_author_img_widget'];

            $i++;
            $rs->MoveNext();
        }

        return $items ;
    }

    //Poner en una clase aparte
    public function get_opinion_algoritm()
    {
        $sql = 'SELECT `value` FROM settings `name`=`opinion_algoritm`';
        $rs = $GLOBALS['application']->conn->Execute($sql);

        if (!$rs) {
            \Application::logDatabaseError();

            return false;
        }

        return $rs->fields['opinion_algoritm'];
    }

    public function set_opinion_algoritm($value)
    {
        $sql = "UPDATE settings SET `value`='".$value
             . "' WHERE `name`=`opinion_algoritm`";
        $rs = $GLOBALS['application']->conn->Execute($sql);
        if ($rs === false) {
            \Application::logDatabaseError();

            return;
        }
    }

    public function count_inhome_type($opinionType = null)
    {
        if ($opinionType == null && $this->type_opinion) {
            $opinionType = $this->type_opinion;
        }

        $sql = "SELECT count(pk_content) FROM contents, opinions "
             . "WHERE `contents`.`in_litter`=0 AND "
             . "`contents`.`in_home`=1 AND `opinions`.`type_opinion`=?"
             ." AND `contents`.`pk_content`= `opinions`.pk_opinion";

        $rs = $GLOBALS['application']->conn->Execute($sql, array($opinionType));
        if (!$rs) {
            \Application::logDatabaseError();

            return false;
        }

        return $rs->fields['count(pk_content)'];
    }

    public function onUpdateClearCacheOpinion()
    {
        $tplManager = new TemplateCacheManager(TEMPLATE_USER_PATH);

        if (property_exists($this, 'pk_opinion')) {
            $tplManager->delete('opinion|' . $this->pk_opinion);
            $tplManager->fetch(SITE_URL . $this->permalink);
            if (isset($this->in_home) && $this->in_home) {
                $tplManager->delete('home|0');
            }
        }
    }

    public function render()
    {
        $tpl = new Template(TEMPLATE_USER);

        if ((int) $this->type_opinion == 1) {
            $this->author_name_slug = 'editorial';
        } elseif ((int) $this->type_opinion == 2) {
            $this->author_name_slug = 'director';
        } else {

            $aut = new Author($this->fk_author);
            $this->name = StringUtils::get_title($aut->name);
            $this->author_name_slug = $this->name;
        }

        $tpl->assign('item', $this);
        $tpl->assign('cssclass', 'opinion');

        return $tpl->fetch('frontpage/contents/_opinion.tpl');
    }

    /**
    * Get latest Opinions without opinions present in frontpage
    *
    * @return mixed, latest opinions sorted by creation time
    */
    public static function getLatestAvailableOpinions($params = array())
    {

        $contents = array();

        // Setting up default parameters
        $default_params = array(
            'limit' => 6,
        );
        $options = array_merge($default_params, $params);
        $_sql_limit = " LIMIT {$options['limit']}";

        $cm = new ContentManager();
        $ccm = ContentCategoryManager::getInstance();

        // Excluding opinions already present in this frontpage
        $category = (isset($_REQUEST['category']))
            ? $ccm->get_id($_REQUEST['category']) :  0;
        $contentsSuggestedInFrontpage =
            $cm->getContentsForHomepageOfCategory($category);
        foreach ($contentsSuggestedInFrontpage as $content) {
            if ($content->content_type == 4) {
                $excludedContents []= $content->id;
            }
        }

        if (count($excludedContents) > 0) {
            $sqlExcludedContents = ' AND opinions.pk_opinion NOT IN (';
            $sqlExcludedContents .= implode(', ', $excludedContents);
            $sqlExcludedContents .= ') ';
        }

        // Getting latest opinions taking in place later considerations
        $contents = $cm->find(
            'Opinion',
            'contents.content_status=1 AND contents.available=1'. $sqlExcludedContents,
            'ORDER BY contents.created DESC, contents.title ASC ' .$_sql_limit
        );

        // For each opinion get its author and photo
        foreach ($contents as $content) {
            $content->author = new Author($content->fk_author);
            $content->author->photo =
                $content->author->get_photo($content->fk_author_img);
            if (isset($content->author->photo->path_img)) {
                $content->photo = $content->author->photo->path_img;
            }
            $content->name = $content->author->name;
        }

        return $contents;
    }

    /**
    * Get all latest Opinions
    *
    * @return mixed, all latest opinions sorted by creation time
    */
    public static function getAllLatestOpinions($params = array())
    {
        $contents = array();

        // Setting up default parameters
        $default_params = array(
            'limit' => 6,
        );
        $options = array_merge($default_params, $params);
        $limitSql = " LIMIT {$options['limit']}";

        $cm = new ContentManager();

        // Getting All latest opinions
        $contents = $cm->find(
            'Opinion',
            'contents.available=1 ',
            'ORDER BY  contents.created DESC,  contents.title ASC ' .$limitSql
        );

        // For each opinion get its author and photo
        foreach ($contents as $content) {
            $content->author = new Author($content->fk_author);
            $content->author->photo =
                $content->author->get_photo($content->fk_author_img);
            if (isset($content->author->photo->path_img)) {
                $content->photo = $content->author->photo->path_img;
            }
            $content->name = $content->author->name;
        }

        return $contents;
    }

    /**
    * Get all latest Opinions from an author given his id
    *
    * @return mixed, all latest opinions sorted by creation time
    */
    public static function getLatestOpinionsForAuthor(
        $authorID,
        $params = array()
    ) {
        $contents = array();

        // Setting up default parameters
        $defaultParams = array(
            'limit' => 6,
        );
        $options  = array_merge($defaultParams, $params);
        $sqlLimit = " LIMIT {$options['limit']}";

        if (!isset($authorID)) {
            return array();
        }

        $cm = new ContentManager();

        // Getting All latest opinions
        $contents = $cm->find(
            'Opinion',
            'contents.available=1 AND opinions.fk_author = '.$authorID,
            'ORDER BY  contents.created DESC,  contents.title ASC ' .$sqlLimit
        );

        $author = new Author($authorID);

        // For each opinion get its author and photo
        foreach ($contents as $content) {
            $content->author = $author;
            $content->author->photo =
                $content->author->get_photo($content->fk_author_img);
            if (isset($content->author->photo->path_img)) {
                $content->photo = $content->author->photo->path_img;
            }
            $content->name = $content->author->name;
        }

        return $contents;
    }
}

