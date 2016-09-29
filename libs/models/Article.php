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
                    return \Onm\StringUtils::getTitle($this->title);
                }
                break;
            case 'author':
                return $this->getAuthor();

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
     * Load object properties
     *
     * @param array $properties
     *
     * @return void
     **/
    public function load($data)
    {
        parent::load($data);

        $this->permalink = Uri::generate(
            'article',
            [
                'id'       => $this->id,
                'date'     => date('Y-m-d', strtotime($this->created)),
                'category' => $this->category_name,
                'slug'     => $this->slug,
            ]
        );

        return $this;
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
        // If no valid id then return
        if (((int) $id) <= 0) {
            return;
        }

        try {
            $rs = getService('dbal_connection')->fetchAssoc(
                'SELECT * FROM contents LEFT JOIN contents_categories ON pk_content = pk_fk_content '
                .'LEFT JOIN articles ON pk_content = pk_article WHERE pk_content = ?',
                [ $id ]
            );

            if (!$rs) {
                return false;
            }

            $this->load($rs);

            return $this;
        } catch (\Exception $e) {
            error_log('Error fetching article (ID:'.$id.'): '.$e->getMessage());
            return false;
        }
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
            $data['description'] = \Onm\StringUtils::getNumWords($data['body'], 50);
        }

        $data['subtitle']= $data['subtitle'];

        try {
            // Start transaction
            $conn = getService('dbal_connection');
            $conn->beginTransaction();

            parent::create($data);

            $conn->insert('articles', [
                'pk_article'    => $this->id,
                'subtitle'      => $data['subtitle'],
                'agency'        => $data['agency'],
                'summary'       => $data['summary'],
                'title_int'     => $data['title_int'],
                'img1'          => (int) $data['img1'],
                'img1_footer'   => (!isset($data['img1_footer']) || is_null($data['img1_footer']))
                    ? null : $data['img1_footer'],
                'img2'          => (int) $data['img2'],
                'img2_footer'   => (!isset($data['img2_footer']) || is_null($data['img2_footer']))
                    ? null : $data['img2_footer'],
                'fk_video'      => (int) $data['fk_video'],
                'fk_video2'     => (int) $data['fk_video2'],
                'footer_video2' => $data['footer_video2'],
            ]);

            $conn->commit();

            // Moving related contents saving code out of transaction due to ONM-1368
            if (!empty($data['relatedFront'])) {
                $this->saveRelated($data['relatedFront'], $this->id, 'setRelationPosition');
            }
            if (!empty($data['relatedInner'])) {
                $this->saveRelated($data['relatedInner'], $this->id, 'setRelationPositionForInner');
            }

            if (!empty($data['relatedHome'])) {
                $this->saveRelated($data['relatedHome'], $this->id, 'setHomeRelations');
            }

            return $this->id;
        } catch (\Exception $e) {
            $conn->rollback();
            error_log('Error creating article: '.$e->getMessage());
            return false;
        }
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
        // Update an article
        if (!$data['description']) {
            $data['description'] = \Onm\StringUtils::getNumWords($data['body'], 50);
        }

        $contentData = [
            'subtitle'      => $data['subtitle'],
            'agency'        => $data['agency'],
            'summary'       => $data['summary'],
            'title_int'     => $data['title_int'],
            'img1'          => empty($data['img1']) ? null: (int) $data['img1'],
            'img1_footer'   => (!isset($data['img1_footer']) || is_null($data['img1_footer']))
                ? null: $data['img1_footer'],
            'img2'          => empty($data['img2']) ? null: (int) $data['img2'],
            'img2_footer'   => (!isset($data['img2_footer']) || is_null($data['img2_footer']))
                ? null: $data['img2_footer'],
            'fk_video'      => (int) $data['fk_video'],
            'fk_video2'     => (int) $data['fk_video2'],
            'footer_video2' => $data['footer_video2'],
        ];

        try {
            // Start transaction
            $conn = getService('dbal_connection');
            $conn->beginTransaction();
            parent::update($data);

            $conn->update(
                'articles',
                $contentData,
                [ 'pk_article' => $data['id'] ]
            );

            $conn->commit();

            // Moving related contents saving code out of transaction due to ONM-1368
            // Drop related and insert new ones
            getService('related_contents')->delete($data['id']);

            // Insert new related contents
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
        } catch (\Exception $e) {
            $conn->rollback();
            error_log('Error updating article (ID:'.$data['id'].': '.$e->getMessage());
            return false;
        }
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
        if ((int) $id <= 0) {
            return false;
        }

        $conn = getService('dbal_connection');
        try {
            $conn->beginTransaction();

            parent::remove($id);

            $rs = getService('dbal_connection')->delete(
                "articles",
                [ 'pk_article' => $id ]
            );

            // Delete related
            getService('related_contents')->delete($id);

            // Delete comments
            getService('comment_repository')->deleteFromFilter(['content_id' => $id]);
            $conn->commit();

            return true;
        } catch (\Exception $e) {
            $conn->rollback();
            error_log('Error deleting article (ID:'.$id.'): '.$e->getMessage());
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
            $tpl = getService('core.template');
        //}

        $params['item'] = $this;
        // $params'cssclass', $params['cssclass']);
        // $tpl->assign('categoryId', $params['categoryId']);


        try {
            $html = $tpl->fetch($params['tpl'], $params);
        } catch (\Exception $e) {
            $html = _('Article not available');
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
        $rel = getService('related_contents');

        if (is_array($data) && count($data) > 0) {
            foreach ($data as $content) {
                $rel->{$method}($id, $content->position, $content->id);
            }
        }
    }

    /**
     * Returns the author object of this article
     *
     * @return array the author data
     **/
    public function getAuthor()
    {
        if (empty($this->author)) {
            $this->author= getService('user_repository')->find($this->fk_author);
        }

        return $this->author;
    }
}
