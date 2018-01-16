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
 */
class Article extends Content
{
    /**
     * The id of the article
     *
     * @var int
     */
    public $pk_article = null;

    /**
     * The subtitle of the article
     *
     * @var string
     */
    protected $subtitle = null;

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
     *
     * @return void
     */
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
     */
    public function __get($name)
    {
        switch ($name) {
            case 'author':
                return $this->getAuthor();

            case 'content_type_name':
                return 'Article';

            case 'permalink':
                return Uri::generate('article', [
                    'id'       => $this->id,
                    'date'     => date('Y-m-d', strtotime($this->created)),
                    'category' => urlencode($this->category_name),
                    'slug'     => urlencode($this->__get('slug')),
                ]);

            default:
                return parent::__get($name);
        }
    }

    /**
     * Load object properties
     *
     * @param array $properties
     *
     * @return void
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
     * @return void
     */
    public function read($id)
    {
        // If no valid id then return
        if (((int) $id) <= 0) {
            return;
        }

        try {
            $rs = getService('dbal_connection')->fetchAssoc(
                'SELECT * FROM contents LEFT JOIN contents_categories ON pk_content = pk_fk_content '
                . 'LEFT JOIN articles ON pk_content = pk_article WHERE pk_content = ?',
                [ $id ]
            );

            if (!$rs) {
                return false;
            }

            $this->load($rs);

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
     * @return null if the article was not created
     * @return int  the id of the article
     */
    public function create($data)
    {
        if (!isset($data['description'])) {
            $data['description'] = \Onm\StringUtils::getNumWords($data['body'], 50);
        }

        // If content is created without publish, don't save any starttime
        if ($data['content_status'] == 0) {
            $data['starttime'] = null;
        }

        try {
            // Start transaction
            $conn = getService('dbal_connection');
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
                'subtitle'   => (!array_key_exists('subtitle', $data) || empty($data['subtitle']))
                    ? null : $data['subtitle'],
                'title_int'   => (!array_key_exists('title_int', $data) || empty($data['title_int']))
                    ? null : $data['title_int'],
                'img1'   => (!array_key_exists('img1', $data) || empty($data['img1']))
                    ? '0' : $data['img1'],
                'img1_footer'   => (!array_key_exists('img1_footer', $data) || is_null($data['img1_footer']))
                    ? null : $data['img1_footer'],
                'img2'   => (!array_key_exists('img2', $data) || empty($data['img2']))
                    ? '0' : $data['img2'],
                'img2_footer'   => (!array_key_exists('img2_footer', $data) || is_null($data['img2_footer']))
                    ? null : $data['img2_footer'],
                'fk_video'   => (!array_key_exists('fk_video', $data) || empty($data['fk_video']))
                    ? '0' : $data['fk_video'],
                'footer_video1'   => (!array_key_exists('footer_video1', $data) || empty($data['footer_video1']))
                    ? null : $data['footer_video1'],
                'fk_video2'   => (!array_key_exists('fk_video2', $data) || empty($data['fk_video2']))
                    ? '0' : $data['fk_video2'],
                'footer_video2'   => (!array_key_exists('footer_video2', $data) || empty($data['footer_video2']))
                    ? null : $data['footer_video2'],
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

            $metaDataFields = [
                ['key' => 'wCapital', 'type' => 'text', 'name' => 'Capital'],
                ['key' => 'wCurrency', 'type' => 'text', 'name' => 'Currency'],
                ['key' => 'wPopulation', 'type' => 'text', 'name' => 'Population'],
                ['key' => 'wTimeZone', 'type' => 'text', 'name' => 'Time Zone'],
                ['key' => 'wWeather', 'type' => 'text', 'name' => 'Weather']
            ];

            foreach ($metaDataFields as $metaDataField) {
                if (array_key_exists($metaDataField['key'], $data) && !empty($metaDataField['key'])) {
                    parent::setMetadata($metaDataField['key'], $data[$metaDataField['key']]);
                }
            }

            return $this->id;
        } catch (\Exception $e) {
            $conn->rollback();
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

        // Update an article
        if (!$data['description']) {
            $data['description'] = \Onm\StringUtils::getNumWords($data['body'], 50);
        }

        $contentData = [
            'agency'   => (!array_key_exists('agency', $data) || empty($data['agency']))
            ? null : $data['agency'],
            'summary'   => (!array_key_exists('summary', $data) || empty($data['summary']))
                ? null : $data['summary'],
            'subtitle'   => (!array_key_exists('subtitle', $data) || empty($data['subtitle']))
                ? null : $data['subtitle'],
            'title_int'   => (!array_key_exists('title_int', $data) || empty($data['title_int']))
                ? null : $data['title_int'],
            'img1'   => (!array_key_exists('img1', $data) || empty($data['img1']))
                ? '0' : $data['img1'],
            'img1_footer'   => (!array_key_exists('img1_footer', $data) || is_null($data['img1_footer']))
                ? null : $data['img1_footer'],
            'img2'   => (!array_key_exists('img2', $data) || empty($data['img2']))
                ? '0' : $data['img2'],
            'img2_footer'   => (!array_key_exists('img2_footer', $data) || is_null($data['img2_footer']))
                ? null : $data['img2_footer'],
            'fk_video'   => (!array_key_exists('fk_video', $data) || empty($data['fk_video']))
                ? '0' : $data['fk_video'],
            'footer_video1'   => (!array_key_exists('footer_video1', $data) || empty($data['footer_video1']))
                ? null : $data['footer_video1'],
            'fk_video2'   => (!array_key_exists('fk_video2', $data) || empty($data['fk_video2']))
                ? '0' : $data['fk_video2'],
            'footer_video2'   => (!array_key_exists('footer_video2', $data) || empty($data['footer_video2']))
                ? null : $data['footer_video2'],
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

            $metaDataFields = [
                ['key' => 'wCapital', 'type' => 'text', 'name' => 'Capital'],
                ['key' => 'wCurrency', 'type' => 'text', 'name' => 'Currency'],
                ['key' => 'wPopulation', 'type' => 'text', 'name' => 'Population'],
                ['key' => 'wTimeZone', 'type' => 'text', 'name' => 'Time Zone'],
                ['key' => 'wWeather', 'type' => 'text', 'name' => 'Weather']
            ];

            foreach ($metaDataFields as $metaDataField) {
                if (array_key_exists($metaDataField['key'], $data) && !empty($metaDataField['key'])) {
                    parent::setMetadata($metaDataField['key'], $data[$metaDataField['key']]);
                }
            }

            $this->category_name = $this->loadCategoryName($this->id);

            return true;
        } catch (\Exception $e) {
            $conn->rollback();
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
     * @return void
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

            // Delete related
            getService('related_contents')->delete($id);

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

        return true;
    }

    /**
     * Renders the article given a set of parameters
     *
     * @param array $params a list of parameters to pass to the template object
     * @param Template $tpl the smarty instance
     *
     * @return string the final html for the article
     */
    public function render($params, $tpl = null)
    {
        $tpl = getService('core.template');

        $params['item'] = $this;

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
     */
    public function saveRelated($data, $id, $method)
    {
        $rel = getService('related_contents');

        if (is_array($data) && count($data) > 0) {
            for ($i = 0; $i < count($data); $i++) {
                $rel->{$method}($id, $i, $data[$i]);
            }
        }
    }

    /**
     * Returns the author object of this article
     *
     * @return array the author data
     */
    public function getAuthor()
    {
        if (empty($this->author)) {
            $this->author = getService('user_repository')->find($this->fk_author);
        }

        return $this->author;
    }

    /**
     * Returns the list of properties that support multiple languages.
     *
     * @param boolean $exclusive The list of article's exclusive properties
     *                           that support multiple languages.
     *
     * @return array The list of properties that can be localized to multiple
     *               languages.
     */
    public static function getL10nKeys($exclusive = false)
    {
        $keys = [
            'footer_video', 'footer_video2', 'img1_footer', 'img2_footer',
            'subtitle', 'summary', 'title_int'
        ];

        if ($exclusive) {
            return $keys;
        }

        return array_merge(parent::getL10nKeys(), $keys);
    }
}
