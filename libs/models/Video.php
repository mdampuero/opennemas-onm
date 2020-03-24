<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class Video extends Content
{
    /**
     * The video id
     *
     * @var  int
     */
    public $pk_video = null;

    /**
     * Serialized array with the video information
     *
     * @var string
     */
    public $information = null;

    /**
     * The original video url, if it comes from an external source
     *
     * @var string
     */
    public $video_url = null;

    /**
     * The video author name
     *
     * @var string
     */
    public $author_name = null;

    /**
     * Initializes the Video object
     *
     * @param int $id the video id to load
     *
     * @return Video the video object instance
     */
    public function __construct($id = null)
    {
        $this->content_type_l10n_name = _('Video');
        $this->content_type           = 9;
        $this->content_type_name      = 'video';

        parent::__construct($id);
    }

    /**
     * Magic function to get uninitilized object properties.
     *
     * @param string $name the name of the property to get.
     *
     * @return mixed the value for the property
     */
    public function __get($name)
    {
        switch ($name) {
            case 'content_type_name':
                return 'Video';
            case 'uri':
                return ltrim(getService('core.helper.url_generator')->generate($this), '/');
            default:
                return parent::__get($name);
        }
    }

    /**
     * Loads a video identified by a the given id
     *
     * @param int $id the video id to load
     *
     * @return null|Video the video object instance
     */
    public function read($id)
    {
        // If no valid id then return
        if ((int) $id <= 0) {
            return null;
        }

        try {
            $rs = getService('dbal_connection')->fetchAssoc(
                'SELECT * FROM contents LEFT JOIN contents_categories ON pk_content = pk_fk_content '
                    . 'LEFT JOIN videos ON pk_content = pk_video WHERE pk_content = ?',
                [$id]
            );

            if (!$rs) {
                return null;
            }

            $this->load($rs);
            $this->information = unserialize($rs['information']);

            return $this;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Load properties into this instance
     *
     * @param array $properties Array properties
     */
    public function load($properties)
    {
        parent::load($properties);

        $this->thumb = $this->getThumb();
    }

    /**
     * Creates a new video from a given data array
     *
     * @param array $data the video data
     *
     * @return boolean true if the videos was created
     */
    public function create($data)
    {
        $conn = getService('dbal_connection');

        try {
            // Start transaction
            $conn->beginTransaction();

            parent::create($data);

            $this->pk_video   = $this->id;
            $this->pk_content = $this->id;

            $conn->insert('videos', [
                'pk_video'    => $this->id,
                'video_url'   => $data['video_url'],
                'information' => array_key_exists('information', $data) ?
                    serialize($data['information']) : null,
                'author_name' => $data['author_name'],
            ]);

            $conn->commit();

            return $this->id;
        } catch (\Exception $e) {
            $conn->rollback();

            getService('error.log')->error(
                $e->getMessage() . ' ' . $e->getTraceAsString()
            );

            return false;
        }
    }

    /**
     * Updates the video given an array of data
     *
     * @param array $data the new video data
     *
     * @return boolean true if the video was updated
     */
    public function update($data)
    {
        $conn = getService('dbal_connection');

        try {
            // Start transaction
            $conn->beginTransaction();

            parent::update($data);

            $conn->update(
                "videos",
                [
                    'video_url' => $data['video_url'],
                    'information' => serialize($data['information']),
                    'author_name' => $data['author_name'],
                ],
                ['pk_video' => (int) $data['id']]
            );

            $conn->commit();

            $this->load($data);

            return $this;
        } catch (\Exception $e) {
            $conn->rollback();

            getService('error.log')->error(
                $e->getMessage() . ' ' . $e->getTraceAsString()
            );

            return false;
        }
    }

    /**
     * Removes permanently a video given an id
     *
     * @param int $id the video id
     *
     * @return boolean true if the video was removed
     */
    public function remove($id)
    {
        if ((int) $id <= 0) {
            return false;
        }

        parent::remove($id);

        try {
            $rs = getService('dbal_connection')->delete(
                "videos",
                ['pk_video' => $id]
            );

            if (!$rs) {
                return false;
            }

            return true;
        } catch (\Exception $e) {
            getService('error.log')->error(
                $e->getMessage() . ' ' . $e->getTraceAsString()
            );

            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getL10nKeys($exclusive = false)
    {
        $parent = parent::getL10nKeys($exclusive);

        $parent = array_filter($parent, function ($el) {
            return $el !== 'body';
        });

        return $parent;
    }

    /**
     * Returns the thumb url of this video
     *
     * @return string the thumb url
     */
    public function getThumb()
    {
        if (!is_array($this->information)) {
            $information = unserialize($this->information);
        } else {
            $information = $this->information;
        }

        if ($this->author_name == 'internal') {
            return MEDIA_IMG_PATH_WEB . "/../" . $information['thumbnails']['normal'];
        }

        if (empty($information)
            || !is_array($information)
            || ! array_key_exists('thumbnail', $information)
        ) {
            return null;
        }

        if ($this->author_name == 'external' || $this->author_name == 'script') {
            $this->thumb_image = getService('entity_repository')
                ->find('Photo', $information['thumbnail']);

            if (!empty($this->thumb_image->name)) {
                return MEDIA_IMG_PATH_WEB . $this->thumb_image->getRelativePath();
            }
        }

        return $information['thumbnail'];
    }

    /**
     * Renders the video object in frontpage
     *
     * @param array $params the parameters for changing the rendering behaviour
     *
     * @return string the final HTML for this video
     */
    public function render($params)
    {
        $tpl = getService('core.template');

        $params['item'] = $this;
        $template       = 'frontpage/contents/_video.tpl';

        if ($params['custom'] == 1) {
            $template = $params['tpl'];
        }

        try {
            $html = $tpl->fetch($template, $params);
        } catch (\Exception $e) {
            getService('error.log')->error(
                $e->getMessage() . ' ' . $e->getTraceAsString()
            );

            $html = _('Video not available');
        }

        return $html;
    }
}
