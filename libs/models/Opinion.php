<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class Opinion extends Content
{
    /**
     * The name of the setting to save extra field configuration.
     *
     * @var string
     */
    const EXTRA_INFO_TYPE = 'extraInfoContents.OPINION_MANAGER';

    /**
     * {@inheritdoc}
     */
    protected static $l10nExclusiveKeys = [
        'summary', 'img1_footer', 'img2_footer'
    ];

    /**
     * The opinion id
     *
     * @var int
     */
    public $pk_opinion = null;

    /**
     * The categories this opinion belongs to
     *
     * @var string
     */
    public $fk_content_categories = null;

    /**
     * The author id of this opinion
     *
     * @var int
     */
    public $fk_author = null;

    /**
     * The meta property for summary
     *
     * @var int
     */
    protected $summary = null;

    /**
     * The
     *
     * @var int
     */
    protected $img1_footer = null;

    /**
     * The type of the opinion (0,1,2)
     *
     * @var int
     */
    protected $img2_footer = null;

    /**
     * Initializes the opinion object given an id
     *
     * @param int $id the opinion id to load
     *
     * @return Opinion the opinion object
     */
    public function __construct($id = null)
    {
        $this->content_type_l10n_name = _('Opinion');
        $this->content_type           = 4;
        $this->content_type_name      = 'opinion';

        parent::__construct($id);
    }

    /**
     * Magic method for getting not assigned variables
     *
     * @param string $name the property name
     *
     * @return mixed the value of the property
     */
    public function __get($name)
    {
        switch ($name) {
            case 'content_type_name':
                return 'Opinion';
            case 'author_object':
                $ur        = getService('user_repository');
                $authorObj = $ur->find($this->fk_author);
                // TODO: Fix this ASAP
                if (!empty($authorObj) && !empty($authorObj->avatar_img_id)) {
                    $authorObj->photo = getService('entity_repository')
                        ->find('Photo', $authorObj->avatar_img_id);
                }

                return $authorObj;
            case 'uri':
                return ltrim(getService('core.helper.url_generator')->generate($this), '/');
            default:
                return parent::__get($name);
        }
    }

    /**
     * Creates a new opinion article given an array of data
     *
     * @param array $data the
     *
     * @return int the id of the opinion article created
     * @return boolean false if the
     */
    public function create($data)
    {
        parent::create($data);

        $this->pk_opinion = $this->id;

        try {
            getService('dbal_connection')->insert('opinions', [
                'pk_opinion'    => $this->id,
                'fk_author'     => (int) $data['fk_author'],
            ]);

            $metaKeys = ['summary', 'img1', 'img2', 'img1_footer', 'img2_footer'];

            foreach ($metaKeys as $key) {
                if (array_key_exists($key, $data) && !empty($data[$key])) {
                    parent::setMetadata($key, $data[$key]);
                } else {
                    parent::removeMetadata($key);
                }
            }

            $this->saveMetadataFields($data, Opinion::EXTRA_INFO_TYPE);

            // Clear caches
            dispatchEventWithParams('opinion.create', [ 'authorId' => $data['fk_author'] ?? null]);

            return $this->id;
        } catch (\Exception $e) {
            getService('error.log')->error(
                'Error on Opinion::create: ' . $e->getMessage()
            );

            return false;
        }
    }

    /**
     * Loads the opinion article information for the given id
     *
     * @param int $id the opinion id
     *
     * @return Opinion the opinion object
     */
    public function read($id)
    {
        // If no valid id then return
        if ((int) $id <= 0) {
            return;
        }

        try {
            $rs = getService('dbal_connection')->fetchAssoc(
                'SELECT contents.*, opinions.pk_opinion, users.name, users.bio, users.url, users.avatar_img_id '
                . 'FROM contents '
                . 'LEFT JOIN opinions ON pk_content = pk_opinion '
                . 'LEFT JOIN users ON opinions.fk_author = users.id WHERE pk_content=?',
                [ $id ]
            );

            if (!$rs) {
                return false;
            }

            $rs['author'] = $rs['name'];
            $this->load($rs);

            return $this;
        } catch (\Exception $e) {
            getService('error.log')->error($e->getMessage());

            return false;
        }
    }

    /**
     * Updates the opinion information given a data array
     *
     * @param array $data the new opinion data
     *
     * @return boolean true if the opinion was updated
     */
    public function update($data)
    {
        parent::update($data);

        try {
            getService('dbal_connection')->update('opinions', [
                'fk_author'     => (int) $data['fk_author'],
            ], [ 'pk_opinion'    => $data['pk_opinion'] ]);

            $metaKeys = ['summary', 'img1', 'img2', 'img1_footer', 'img2_footer'];

            foreach ($metaKeys as $key) {
                if (array_key_exists($key, $data) && !empty($data[$key])) {
                    parent::setMetadata($key, $data[$key]);
                } else {
                    parent::removeMetadata($key);
                }
            }

            $this->saveMetadataFields($data, Opinion::EXTRA_INFO_TYPE);

            // Clear caches
            dispatchEventWithParams('opinion.update');

            return $this;
        } catch (\Exception $e) {
            getService('error.log')->error(
                'Error on Opinion::update: ' . $e->getMessage()
            );

            return false;
        }
    }

    /**
     * Removes permanently an opinion
     *
     * @param int $id the opinion article id
     *
     * @return boolean true if the action was performed
     */
    public function remove($id)
    {
        if ((int) $id <= 0) {
            return false;
        }

        parent::remove($id);

        try {
            $rs = getService('dbal_connection')->delete(
                "opinions",
                [ 'pk_opinion' => $id ]
            );

            if (!$rs) {
                return false;
            }

            return true;
        } catch (\Exception $e) {
            getService('error.log')->error($e->getMessage());

            return false;
        }
    }

    /**
     * Renders the opinion article
     *
     * @return string the generated HTML for the opinion
     */
    public function render($params)
    {
        $tpl = getService('core.template');

        $author = new \User($this->fk_author);

        $this->name             = \Onm\StringUtils::generateSlug($author->name);
        $this->author_name_slug = $this->name;

        if (array_key_exists('is_blog', $author->meta) && $author->meta['is_blog'] == 1) {
            $params['item'] = $this;
            $template       = 'frontpage/contents/_blog.tpl';

            if ($params['custom'] == 1) {
                $template = $params['tpl'];
            }

            return $tpl->fetch($template, $params);
        }

        $params['item']     = $this;
        $params['cssclass'] = 'opinion';
        $template           = 'frontpage/contents/_opinion.tpl';

        if ($params['custom'] == 1) {
            $template = $params['tpl'];
        }

        return $tpl->fetch($template, $params);
    }

   /**
    * Get latest Opinions without opinions present in frontpage
    *
    * @param array $params list of parameters
    *
    * @return mixed, latest opinions sorted by creation time
    */
    public static function getLatestAvailableOpinions($params = [])
    {
        $contents = [];

        // Setting up default parameters
        $default_params = [ 'limit' => 6 ];
        $options        = array_merge($default_params, $params);
        $_sql_limit     = " LIMIT {$options['limit']}";

        $cm = new ContentManager();

        $category = 0;

        list(, $contentsSuggestedInFrontpage, , ) = getService('api.service.frontpage')
            ->getCurrentVersionForCategory($category);

        $excludedContents = [];
        foreach ($contentsSuggestedInFrontpage as $content) {
            if ($content->content_type == 4) {
                $excludedContents[] = (int) $content->id;
            }
        }

        $sqlExcludedContents = '';
        if (count($excludedContents) > 0) {
            $sqlExcludedContents .=
                ' AND opinions.pk_opinion NOT IN ('
                . implode(', ', $excludedContents) . ') ';
        }

        // Getting latest opinions taking in place later considerations
        $contents = $cm->find(
            'Opinion',
            'contents.content_status=1' . $sqlExcludedContents,
            'ORDER BY contents.created DESC, contents.title ASC ' . $_sql_limit
        );

        // For each opinion get its author and photo
        foreach ($contents as $content) {
            $content->author = new \User($content->fk_author);
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
    public static function getAllLatestOpinions($params = [])
    {
        $contents = [];

        // Setting up default parameters
        $default_params = [ 'limit' => 6 ];

        $options  = array_merge($default_params, $params);
        $limitSql = " LIMIT {$options['limit']}";

        $cm = new ContentManager();

        // Getting All latest opinions
        $contents = $cm->find(
            'Opinion',
            'contents.content_status=1 ',
            'ORDER BY  contents.created DESC,  contents.title ASC ' . $limitSql
        );

        // For each opinion get its author and photo
        foreach ($contents as $content) {
            $content->author = new \User($content->fk_author);

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
    public static function getLatestOpinionsForAuthor($authorID, $params = [])
    {
        $contents = [];

        // Setting up default parameters
        $defaultParams = [ 'limit' => 6 ];
        $options       = array_merge($defaultParams, $params);
        $sqlLimit      = " LIMIT " . $options['limit'];

        if (!isset($authorID)) {
            return [];
        }

        $cm = new ContentManager();

        // Getting All latest opinions
        $contents = $cm->find(
            'Opinion',
            'contents.content_status=1 AND opinions.fk_author = ' . $authorID,
            'ORDER BY contents.created DESC,  contents.title ASC ' . $sqlLimit
        );

        $author = new \User($authorID);

        // For each opinion get its author and photo
        foreach ($contents as $content) {
            $content->author = $author;
            if (isset($content->author->photo->path_img)) {
                $content->photo = $content->author->photo->path_img;
            }

            $content->name = $content->author->name;
        }

        return $contents;
    }
}
