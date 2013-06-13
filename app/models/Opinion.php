<?php
/**
 * Defines the Opinion class
 *
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package    Model
 */
/**
 * Handles all the CRUD operations over opinions.
 *
 * @package    Model
 **/
class Opinion extends Content
{
    /**
     * The opinion id
     *
     * @var int
     **/
    public $pk_opinion            = null;

    /**
     * The categories this opinion belongs to
     *
     * @var string
     **/
    public $fk_content_categories = null;

    /**
     * The author of this opinion
     *
     * @var int
     **/
    public $fk_author             = null;

    /**
     * The contents of this opinion
     *
     * @var string
     **/
    public $body                  = null;

    /**
     * The opinion author id
     *
     * @var int
     **/
    public $author                = null;

    /**
     * The author img id
     *
     * @var int
     **/
    public $fk_author_img         = null;

    /**
     * Whether allowing comments on this opinion
     *
     * @var boolean
     **/
    public $with_comment          = null;

    /**
     * The image id for the opinion widget
     *
     * @var int
     **/
    public $fk_author_img_widget  = null;

    /**
     * Array of authors names
     *
     * @var array
     */
    private $authorNames         = null;

    /**
     * Initializes the opinion object given an id
     *
     * @param int $id the opinion id to load
     *
     * @return Opinion the opinion object
     **/
    public function __construct($id = null)
    {
        $this->content_type_l10n_name = _('Opinion');

        parent::__construct($id);
    }

    /**
     * Magic method for getting not assigned variables
     *
     * @param string $name the property name
     *
     * @return mixed the value of the property
     **/
    public function __get($name)
    {
        switch ($name) {
            case 'uri':
                if ($this->fk_author == 0) {

                    if ((int) $this->type_opinion == 1) {
                        $authorName = 'Editorial';
                    } elseif ((int) $this->type_opinion == 2) {
                        $authorName = 'Director';
                    } else {
                        $authorName = 'author';
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
                    $authorObj->get_author_photos();
                }

                return $authorObj;

                break;
            default:
                return parent::__get($name);

                break;
        }
    }

    /**
     * Creates a new opinion article given an array of data
     *
     * @param array $data the
     *
     * @return int the id of the opinion article created
     * @return boolean false if the
     **/
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

        if (array_key_exists('summary', $data) && !empty($data['summary'])) {
            parent::setProperty('summary', $data['summary']);
        }

        if (array_key_exists('img1', $data) && !empty($data['img1'])) {
            parent::setProperty('img1', $data['img1']);
        }
        if (array_key_exists('img2', $data) && !empty($data['img2'])) {
            parent::setProperty('img2', $data['img2']);
        }
        if (array_key_exists('img1_footer', $data) && !empty($data['img1_footer'])) {
            parent::setProperty('img1_footer', $data['img1_footer']);
        }
        if (array_key_exists('img2_footer', $data) && !empty($data['img2_footer'])) {
            parent::setProperty('img2_footer', $data['img2_footer']);
        }

        return $this->id;
    }

    /**
     * Loads the opinion article information for the given id
     *
     * @param int $id the opinion id
     *
     * @return Opinion the opinion object
     **/
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

        $this->loadAllContentProperties();

        return $this;
    }

    /**
     * Returns the author name for a given author id
     *
     * @param int $fkAuthor the author id
     *
     * @return string the author name
     */
    public function get_author_name($fkAuthor)
    {
        if (is_null($this->authorNames)) {
            $sql = 'SELECT pk_author, name FROM `authors`';
            $rs = $GLOBALS['application']->conn->Execute($sql);

            while (!$rs->EOF) {
                $this->authorNames[ $rs->fields['pk_author'] ]
                    = $rs->fields['name'];

                $rs->MoveNext();
            }
        }

        if (!isset($this->authorNames[$fkAuthor])) {
            return '';
        }

        return $this->authorNames[$fkAuthor];
    }

    /**
     * Updates the opinion information given a data array
     *
     * @param array $data the new opinion data
     *
     * @return boolean true if the opinion was updated
     **/
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

        if (array_key_exists('summary', $data) && !empty($data['summary'])) {
            parent::setProperty('summary', $data['summary']);
        } else {
            parent::clearProperty('summary');
        }

        if (array_key_exists('img1', $data) && !empty($data['img1'])) {
            parent::setProperty('img1', $data['img1']);
        } else {
            parent::clearProperty('img1');
        }
        if (array_key_exists('img2', $data) && !empty($data['img2'])) {
            parent::setProperty('img2', $data['img2']);
        } else {
            parent::clearProperty('img2');
        }
        if (array_key_exists('img1_footer', $data) && !empty($data['img1_footer'])) {
            parent::setProperty('img1_footer', $data['img1_footer']);
        } else {
            parent::clearProperty('img1_footer');
        }
        if (array_key_exists('img2_footer', $data) && !empty($data['img2_footer'])) {
            parent::setProperty('img2_footer', $data['img2_footer']);
        } else {
            parent::clearProperty('img2_footer');
        }



        $GLOBALS['application']->dispatch('onAfterUpdateOpinion', $this);

        return true;
    }

    /**
     * Removes permanently an opinion
     *
     * @param int $id the opinion article id
     *
     * @return boolean true if the action was performed
     **/
    public function remove($id)
    {
        parent::remove($id);

        $sql = 'DELETE FROM opinions WHERE pk_opinion ='.($id);

        if ($GLOBALS['application']->conn->Execute($sql)===false) {
            \Application::logDatabaseError();

            return false;
        }

        return true;
    }

    /**
     * Removes the cache for an inner opinion and for the opinion frontpage
     *
     * @return void
     **/
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

    /**
     * Renders the opinion article
     *
     * @return string the generated HTML for the opinion
     **/
    public function render($params)
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
        $tpl->assign('actual_category', $params['actual_category']);
        $tpl->assign('actual_category_id', $params['actual_category_id']);
        $tpl->assign('cssclass', 'opinion');

        return $tpl->fetch('frontpage/contents/_opinion.tpl');
    }

    /**
    * Get latest Opinions without opinions present in frontpage
    *
    * @param array $params list of parameters
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

        // TODO: Review this function called in a opinion widget in tribuna theme
        // getInstance function was deleted - print fatal error in widget

        /*
        $ccm = ContentCategoryManager::getInstance();

        // Excluding opinions already present in this frontpage
        $category = (isset($_REQUEST['category']))
            ? $ccm->get_id($_REQUEST['category']) :  0;

        */

        $category = 0;

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
    * @param array $params list of parameters
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
    * @param int $authorID the identificator of the author
    * @param array $params list of parameters
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
        $sqlLimit = " LIMIT ".$options['limit'];

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
