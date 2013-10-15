<?php
/**
 * Defines the Article class
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
 * Class for handling articles.
 *
 * @package    Model
 **/
class Article extends Content
{
    /**
     * The id of the article
     *
     * @var int
     **/
    public $pk_article    = null;

    /**
     * The subtitle of the article
     *
     * @var string
     **/
    public $subtitle      = null;

    /**
     * The agency that authored the article
     *
     * @var string
     **/
    public $agency        = null;

    /**
     * The summary of the article
     *
     * @var string
     **/
    public $summary       = null;

    /**
     * The id of the image assigned for frontpage
     *
     * @var int
     **/
    public $img1          = null;

    /**
     * The footer of the image assigned for frontpage
     *
     * @var string
     **/
    public $img1_footer   = null;

    /**
     * The id of the image assigned for inner
     *
     * @var int
     **/
    public $img2          = null;

    /**
     * The footer of the image assigned for inner
     *
     * @var string
     **/
    public $img2_footer   = null;

    /**
     * The id of the video assigned for frontpage
     *
     * @var int
     **/
    public $fk_video      = null;

    /**
     * The id of the video assigned for inner
     *
     * @var int
     **/
    public $fk_video2     = null;

    /**
     * The footer of the video assigned for inner
     *
     * @var string
     **/
    public $footer_video2 = null;

    /**
     * Wheter allowing comments in this article
     *
     * @var boolean
     **/
    public $with_comment  = null;

    /**
     * The inner title of this article
     *
     * @var string
     **/
    public $title_int     = null;

    /**
     * Initializes the Article object from an ID
     *
     * @param int $id the id of the article we want to initialize
     *
     * @return void
     **/
    public function __construct($id = null)
    {
        $this->content_type_l10n_name = _('Article');

        parent::__construct($id);
    }

    /**
     * Magic method for populate properties on the fly
     *
     * @param string $name the name of the property to fetch
     *
     * @return mixed the value of the property requested
     **/
    public function __get($name)
    {
        switch ($name) {
            case 'uri':

                if (empty($this->category_name)) {
                    $this->category_name = $this->loadCategoryName($this->pk_content);
                }

                if (isset($this->params['bodyLink']) && !empty($this->params['bodyLink'])) {
                    $uri = 'redirect?to='.urlencode($this->params['bodyLink']).'" target="_blank';
                } else {
                    $uri =  Uri::generate(
                        'article',
                        array(
                            'id'       => sprintf('%06d', $this->id),
                            'date'     => date('YmdHis', strtotime($this->created)),
                            'category' => $this->category_name,
                            'slug'     => $this->slug,
                        )
                    );
                }

                return $uri;

                break;
            case 'slug':
                if (!empty($this->slug)) {
                    return $this->slug;
                } else {
                    return StringUtils::get_title($this->title);
                }
                break;
            case 'content_type_name':
                return 'Article';

                break;
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
            $data['description'] = StringUtils::get_num_words($data['body'], 50);
        }

        $data['subtitle']= $data['subtitle'];
        $data['available'] = $data['content_status'];
        $data['img1_footer']
            = (!isset($data['img1_footer']) || empty($data['img1_footer']))
                ? ''
                : $data['img1_footer'];
        $data['img2_footer']
            = (!isset($data['img2_footer']) || empty($data['img2_footer']))
                ? ''
                : $data['img2_footer'];
        $data['with_comment']
            = (!isset($data['with_comment']) || empty($data['with_comment']))
                ? ''
                : intval($data['with_comment']);

        parent::create($data);

        $sql = "INSERT INTO articles (`pk_article`, `subtitle`, `agency`,
                            `summary`, `img1`, `img1_footer`,
                            `img2`, `img2_footer`, `fk_video`, `fk_video2`,
                            `footer_video2`,
                            `with_comment`, `title_int`) " .
                        "VALUES (?,?,?,?, ?,?,?,?, ?,?,?, ?,?)";

        $values = array(
            $this->id,
            $data['subtitle'], $data['agency'],  $data['summary'],
            $data['img1'], $data['img1_footer'],
            $data['img2'], $data['img2_footer'], $data['fk_video'],
            $data['fk_video2'], $data['footer_video2'],
            $data['with_comment'], $data['title_int']
        );

        $rs = $GLOBALS['application']->conn->Execute($sql, $values);
        if ($rs === false) {
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
     * Reads the data for one article given one ID
     *
     * @param int $id the id to get its information
     *
     * @return void
     **/
    public function read($id)
    {
        parent::read($id);

        $sql = 'SELECT * FROM articles WHERE pk_article = ?';
        $rs = $GLOBALS['application']->conn->Execute($sql, array($id));

        if (!$rs) {
            return;
        }

        $this->load($rs->fields);

        $this->permalink = Uri::generate(
            'article',
            array(
                'id'       => $this->id,
                'date'     => date('Y-m-d', strtotime($this->created)),
                'category' => $this->category_name,
                'slug'     => $this->slug,
            )
        );

    }

    /**
     * Updates the information for one article given an array with the new data
     *
     * @param mixed $data array of properties for the article
     *
     * @return boolean true if the article was properly updated
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

        $data['subtitle'] = $data['subtitle'];
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

        parent::update($data);

        $sql = "UPDATE articles "
                ."SET `subtitle`=?, `agency`=?, `summary`=?, "
                ."`img1`=?, `img1_footer`=?, `img2`=?, `img2_footer`=?, "
                ."`fk_video`=?, `fk_video2`=?, `footer_video2`=?, "
                ."`with_comment`=?, `title_int`=? "
                ."WHERE pk_article=?";

        $values = array(
            strtoupper($data['subtitle']), $data['agency'], $data['summary'],
            $data['img1'], $data['img1_footer'], $data['img2'], $data['img2_footer'],
            $data['fk_video'], $data['fk_video2'], $data['footer_video2'],
            $data['with_comment'], $data['title_int'],
            $data['id']
        );

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
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

        $sql = 'DELETE FROM articles WHERE pk_article=?';

        $rel = new RelatedContent();
        $rel->delete($id); //Eliminamos con los que esta relacionados.

        self::deleteComments($id); //Eliminamos  los comentarios.

        if ($GLOBALS['application']->conn->Execute($sql, array($id))===false) {
            return false;
        }
        return true;
    }

    /**
     * Renders the article given a set of parameters
     *
     * @param array $params a list of parameters to pass to the template object
     * @param Template $tpl the smarty instance
     *
     * @return string the final html for the article
     **/
    public function render($params, $tpl = null)
    {
        //  if (!isset($tpl)) {
            $tpl = new Template(TEMPLATE_USER);
        //}

        $params['item'] = $this;
        // $params'cssclass', $params['cssclass']);
        // $tpl->assign('categoryId', $params['categoryId']);


        try {
            $html = $tpl->fetch($params['tpl'], $params);
        } catch (\Exception $e) {
            $html = 'Article not available';
        }

        return $html;
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

        if (is_array($data) && count($data) > 0) {
            foreach ($data as $content) {

                $rel->{$method}($id, $content->position, $content->id);
            }
        }
    }
}
