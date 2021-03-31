<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class Article extends Content
{
    const EXTRA_INFO_TYPE = 'extraInfoContents.ARTICLE_MANAGER';

    /**
     * {@inheritdoc}
     */
    protected static $l10nExclusiveKeys = [
        'footer_video', 'footer_video2', 'img1_footer', 'img2_footer',
        'pretitle', 'summary', 'title_int'
    ];

    /**
     * The id of the article
     *
     * @var int
     */
    public $pk_article = null;

    /**
     * The pretitle of the article
     *
     * @var string
     */
    protected $pretitle = null;

    /**
     * The agency that authored the article
     *
     * @var string
     */
    public $agency = null;

    /**
     * The summary of the article
     *
     * @var string
     */
    protected $summary = null;

    /**
     * The id of the image assigned for frontpage
     *
     * @var int
     */
    public $img1 = null;

    /**
     * The footer of the image assigned for frontpage
     *
     * @var string
     */
    protected $img1_footer = null;

    /**
     * The id of the image assigned for inner
     *
     * @var int
     */
    public $img2 = null;

    /**
     * The footer of the image assigned for inner
     *
     * @var string
     */
    protected $img2_footer = null;

    /**
     * The id of the video assigned for frontpage
     *
     * @var int
     */
    public $fk_video = null;

    /**
     * The id of the video assigned for inner
     *
     * @var int
     */
    public $fk_video2 = null;

    /**
     * The footer of the video assigned for frontpage
     *
     * @var string
     */
    protected $footer_video = null;

    /**
     * The footer of the video assigned for inner
     *
     * @var string
     */
    protected $footer_video2 = null;

    /**
     * The inner title of this article
     *
     * @var string
     */
    protected $title_int = null;

    /**
     * Initializes the Article object from an ID
     *
     * @param int $id the id of the article we want to initialize
     */
    public function __construct($id = null)
    {
        $this->content_type_l10n_name = _('Article');
        $this->content_type           = 1;
        $this->content_type_name      = 'article';

        parent::__construct($id);
    }

    /**
     * Magic method for populate properties on the fly
     *
     * @param string $name the name of the property to fetch
     *
     * @return mixed the value of the property requested
     */
    public function __get($name)
    {
        switch ($name) {
            default:
                return parent::__get($name);
        }
    }

    /**
     * Load object properties
     *
     * @param array $properties
     *
     * @return \Article
     */
    public function load($data)
    {
        parent::load($data);

        return $this;
    }

    /**
     * Reads the data for one article given one ID
     *
     * @param int $id the id to get its information
     *
     * @return null|boolean|Article
     */
    public function read($id)
    {
        // If no valid id then return
        if (((int) $id) <= 0) {
            return null;
        }

        try {
            $rs = getService('dbal_connection')->fetchAssoc(
                'SELECT * FROM contents LEFT JOIN content_category ON pk_content = content_id '
                . 'LEFT JOIN articles ON pk_content = pk_article WHERE pk_content = ?',
                [ $id ]
            );

            if (!$rs) {
                return false;
            }

            $this->load($rs);
            $this->id = $id;

            return $this;
        } catch (\Exception $e) {
            getService('error.log')->error(
                'Error fetching article (ID:' . $id . '): ' . $e->getMessage() .
                ' Stack Trace: ' . $e->getTraceAsString()
            );

            return false;
        }
    }

    /**
     * Creates one article from an array of properties
     *
     * @param mixed $data array of properties for the article
     *
     * @return boolean|int  the id of the article
     */
    public function create($data)
    {
        // If content is created without publish, don't save any starttime
        if ($data['content_status'] == 0) {
            $data['starttime'] = null;
        }

        $conn = getService('dbal_connection');

        try {
            // Start transaction
            $conn->beginTransaction();

            parent::create($data);

            foreach ($this->getL10nKeys() as $key) {
                if (!array_key_exists($key, $data) || !is_array($data[$key])) {
                    continue;
                }

                if (empty($data[$key])
                    || empty(array_filter($data[$key], function ($a) {
                        return !empty($a);
                    }))
                ) {
                    $data[$key] = null;

                    continue;
                }

                $data[$key] = serialize($data[$key]);
            }

            $this->pk_article = $this->id;
            $this->pk_content = $this->id;

            $conn->insert('articles', [
                'pk_article'    => $this->id,
                'agency'   => (!array_key_exists('agency', $data) || empty($data['agency']))
                ? null : $data['agency'],
                'summary'   => (!array_key_exists('summary', $data) || empty($data['summary']))
                    ? null : $data['summary'],
                'pretitle'   => (!array_key_exists('pretitle', $data) || empty($data['pretitle']))
                    ? null : $data['pretitle'],
                'title_int'   => (!array_key_exists('title_int', $data) || empty($data['title_int']))
                    ? null : $data['title_int'],
                'img1'   => (!array_key_exists('img1', $data) || empty($data['img1']))
                    ? null : $data['img1'],
                'img1_footer'   => (!array_key_exists('img1_footer', $data) || is_null($data['img1_footer']))
                    ? null : $data['img1_footer'],
                'img2'   => (!array_key_exists('img2', $data) || empty($data['img2']))
                    ? null : $data['img2'],
                'img2_footer'   => (!array_key_exists('img2_footer', $data) || is_null($data['img2_footer']))
                    ? null : $data['img2_footer'],
                'fk_video'   => (!array_key_exists('fk_video', $data) || empty($data['fk_video']))
                    ? null : $data['fk_video'],
                'footer_video1'   => (!array_key_exists('footer_video1', $data) || empty($data['footer_video1']))
                    ? null : $data['footer_video1'],
                'fk_video2'   => (!array_key_exists('fk_video2', $data) || empty($data['fk_video2']))
                    ? null : $data['fk_video2'],
                'footer_video2'   => (!array_key_exists('footer_video2', $data) || empty($data['footer_video2']))
                    ? null : $data['footer_video2'],
            ]);

            $conn->commit();
        } catch (\Exception $e) {
            getService('error.log')->error(
                'Error creating article (ID:' . $this->id . '): ' . $e->getMessage() .
                ' Stack Trace: ' . $e->getTraceAsString()
            );

            $conn->rollback();

            return false;
        }

        try {
            // Moving related contents saving code out of transaction due to ONM-1638
            $this->saveRelated($data)
                ->saveMetadataFields($data, Article::EXTRA_INFO_TYPE);

            return $this->id;
        } catch (\Exception $e) {
            getService('error.log')->error(
                'Error creating article (ID:' . $this->id . '): ' . $e->getMessage() .
                ' Stack Trace: ' . $e->getTraceAsString()
            );
            return false;
        }
    }

    /**
     * Updates the information for one article given an array with the new data
     *
     * @param mixed $data array of properties for the article
     *
     * @return boolean true if the article was properly updated
     */
    public function update($data)
    {
        foreach ($this->getL10nKeys(true) as $key) {
            if (array_key_exists($key, $data) && is_array($data[$key])) {
                $data[$key] = serialize($data[$key]);
            }
        }

        $contentData = [
            'agency'   => (!array_key_exists('agency', $data) || empty($data['agency']))
            ? null : $data['agency'],
            'summary'   => (!array_key_exists('summary', $data) || empty($data['summary']))
                ? null : $data['summary'],
            'pretitle'   => (!array_key_exists('pretitle', $data) || empty($data['pretitle']))
                ? null : $data['pretitle'],
            'title_int'   => (!array_key_exists('title_int', $data) || empty($data['title_int']))
                ? null : $data['title_int'],
            'img1'   => (!array_key_exists('img1', $data) || empty($data['img1']))
                ? null : $data['img1'],
            'img1_footer'   => (!array_key_exists('img1_footer', $data) || is_null($data['img1_footer']))
                ? null : $data['img1_footer'],
            'img2'   => (!array_key_exists('img2', $data) || empty($data['img2']))
                ? null : $data['img2'],
            'img2_footer'   => (!array_key_exists('img2_footer', $data) || is_null($data['img2_footer']))
                ? null : $data['img2_footer'],
            'fk_video'   => (!array_key_exists('fk_video', $data) || empty($data['fk_video']))
                ? null : $data['fk_video'],
            'footer_video1'   => (!array_key_exists('footer_video1', $data) || empty($data['footer_video1']))
                ? null : $data['footer_video1'],
            'fk_video2'   => (!array_key_exists('fk_video2', $data) || empty($data['fk_video2']))
                ? null : $data['fk_video2'],
            'footer_video2'   => (!array_key_exists('footer_video2', $data) || empty($data['footer_video2']))
                ? null : $data['footer_video2'],
        ];

        $conn = getService('dbal_connection');

        try {
            // Start transaction
            $conn->beginTransaction();
            parent::update($data);

            $conn->update(
                'articles',
                $contentData,
                [ 'pk_article' => $data['id'] ]
            );

            $conn->commit();
        } catch (\Exception $e) {
            getService('error.log')->error(
                'Error updating article (ID:' . $this->id . '): ' . $e->getMessage() .
                ' Stack Trace: ' . $e->getTraceAsString()
            );
            $conn->rollback();
            return false;
        }

        try {
            // Moving related contents saving code out of transaction due to ONM-1638
            $this->saveRelated($data)
                ->saveMetadataFields($data, Article::EXTRA_INFO_TYPE);

            return true;
        } catch (\Exception $e) {
            getService('error.log')->error(
                'Error updating article (ID:' . $this->id . '): ' . $e->getMessage() .
                ' Stack Trace: ' . $e->getTraceAsString()
            );
            return false;
        }
    }

    /**
     * Deletes permanently one article given one  id
     *
     * @param int $id the id of the article we want to delete
     *
     * @return boolean
     */
    public function remove($id)
    {
        if ((int) $id <= 0) {
            return false;
        }

        $conn = getService('dbal_connection');
        try {
            $conn->beginTransaction();

            parent::remove($id);

            getService('dbal_connection')->delete(
                "articles",
                [ 'pk_article' => $id ]
            );

            // Delete comments
            getService('comment_repository')->deleteFromFilter(['content_id' => $id]);
            $conn->commit();

            return true;
        } catch (\Exception $e) {
            $conn->rollback();
            getService('error.log')->error(
                'Error deleting article (ID:' . $id . '): ' . $e->getMessage() .
                ' Stack Trace: ' . $e->getTraceAsString()
            );

            return false;
        }
    }
}
