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
 */
class Opinion extends Content
{

    const EXTRA_INFO_TYPE = 'extraInfoContents.OPINION_MANAGER';

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
     * The author img id
     *
     * @var int
     */
    public $fk_author_img = null;

    /**
     * The type of the opinion (0,1,2)
     *
     * @var int
     */
    public $type_opinion = null;

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
            case 'uri':
                $type = 'opinion';
                if ($this->fk_author == 0) {
                    if ((int) $this->type_opinion == 1) {
                        $authorName = 'Editorial';
                    } elseif ((int) $this->type_opinion == 2) {
                        $authorName = 'Director';
                    } else {
                        $authorName = 'author';
                    }
                } else {
                    $author = $this->author;

                    if (!is_object($author)) {
                        $author = getService('user_repository')
                            ->find($this->fk_author);
                    }

                    if (is_object($author)) {
                        $authorName = $author->name;
                    } else {
                        $authorName = 'author';
                    }

                    if (is_object($author)
                        && is_array($author->meta) &&
                        array_key_exists('is_blog', $author->meta) &&
                        $author->meta['is_blog'] == 1
                    ) {
                        $type = 'blog';
                    }
                }

                $uri = Uri::generate($type, [
                    'id'       => sprintf('%06d', $this->id),
                    'date'     => date('YmdHis', strtotime($this->created)),
                    'slug'     => urlencode($this->slug),
                    'category' => urlencode(\Onm\StringUtils::generateSlug($authorName)),
                ]);

                return $uri;
            case 'content_type_name':
                return 'Opinion';
            case 'author_object':
                $ur = getService('user_repository');
                if ((int) $this->type_opinion == 1) {
                    $authorObj = $ur->findBy("username='editorial'", 1);
                    if (is_array($authorObj) && array_key_exists(0, $authorObj)) {
                        $authorObj = $authorObj[0];
                    }
                } elseif ((int) $this->type_opinion == 2) {
                    $authorObj = $ur->findBy("username='director'", 1);
                    if (is_array($authorObj) && array_key_exists(0, $authorObj)) {
                        $authorObj = $authorObj[0];
                    }
                } else {
                    $authorObj = $ur->find($this->fk_author);
                }
                return $authorObj;
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
        $data['position'] = 1;
        $data['category'] = 4; // force internal category name

        // Editorial or director
        if (!isset($data['fk_author'])) {
            $data['fk_author'] = $data['type_opinion'];
        }

        // Set author img to null if not exist
        $data['fk_author_img'] = isset($data['fk_author_img']) ?
            $data['fk_author_img'] : null;

        parent::create($data);

        try {
            getService('dbal_connection')->insert('opinions', [
                'pk_opinion'    => $this->id,
                'fk_author'     => (int) $data['fk_author'],
                'fk_author_img' => (int) $data['fk_author_img'],
                'type_opinion'  => $data['type_opinion'],
            ]);

            if (array_key_exists('summary', $data) && !empty($data['summary'])) {
                parent::setMetadata('summary', $data['summary']);
            }

            if (array_key_exists('img1', $data) && !empty($data['img1'])) {
                parent::setMetadata('img1', $data['img1']);
            }

            if (array_key_exists('img2', $data) && !empty($data['img2'])) {
                parent::setMetadata('img2', $data['img2']);
            }

            if (array_key_exists('img1_footer', $data) && !empty($data['img1_footer'])) {
                parent::setMetadata('img1_footer', $data['img1_footer']);
            }

            if (array_key_exists('img2_footer', $data) && !empty($data['img2_footer'])) {
                parent::setMetadata('img2_footer', $data['img2_footer']);
            }

            $this->saveMetadataFields($data, Opinion::EXTRA_INFO_TYPE);

            return $this->id;
        } catch (\Exception $e) {
            error_log('Error on Opinion::create: ' . $e->getMessage());
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
                'SELECT contents.*, opinions.*, contents_categories.*, users.name, users.bio, users.url, '
                . 'users.avatar_img_id FROM contents '
                . 'LEFT JOIN contents_categories ON pk_content = pk_fk_content '
                . 'LEFT JOIN opinions ON pk_content = pk_opinion '
                . 'LEFT JOIN users ON opinions.fk_author = users.id WHERE pk_content=?',
                [ $id ]
            );

            if (!$rs) {
                return false;
            }
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return false;
        }

        if ((int) $rs['type_opinion'] == 1) {
            $rs['author'] = 'Editorial';
        } elseif ((int) $rs['type_opinion'] == 2) {
            $rs['author'] = 'Director';
        } else {
            $rs['author'] = $rs['name'];
        }

        $this->load($rs);

        $this->loadAllContentProperties();

        return $this;
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
        if (!isset($data['fk_author'])) {
            $data['fk_author'] = $data['type_opinion'];
        } // Editorial o director

        $data['fk_author_img'] = isset($data['fk_author_img']) ?
            $data['fk_author_img'] : null;

        parent::update($data);

        try {
            getService('dbal_connection')->update('opinions', [
                'fk_author'     => (int) $data['fk_author'],
                'fk_author_img' => (int) $data['fk_author_img'],
                'type_opinion'  => $data['type_opinion'],
            ], [ 'pk_opinion' => (int) $data['id'] ]);

            if (array_key_exists('summary', $data) && !empty($data['summary'])) {
                parent::setMetadata('summary', $data['summary']);
            } else {
                parent::removeMetadata('summary');
            }

            if (array_key_exists('img1', $data) && !empty($data['img1'])) {
                parent::setMetadata('img1', $data['img1']);
            } else {
                parent::removeMetadata('img1');
            }

            if (array_key_exists('img2', $data) && !empty($data['img2'])) {
                parent::setMetadata('img2', $data['img2']);
            } else {
                parent::removeMetadata('img2');
            }

            if (array_key_exists('img1_footer', $data) && !empty($data['img1_footer'])) {
                parent::setMetadata('img1_footer', $data['img1_footer']);
            } else {
                parent::removeMetadata('img1_footer');
            }

            if (array_key_exists('img2_footer', $data) && !empty($data['img2_footer'])) {
                parent::setMetadata('img2_footer', $data['img2_footer']);
            } else {
                parent::removeMetadata('img2_footer');
            }

            $this->saveMetadataFields($data, Opinion::EXTRA_INFO_TYPE);

            return $this;
        } catch (\Exception $e) {
            error_log('Error on Opinion::update: ' . $e->getMessage());
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
            error_log($e->getMessage());
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

        if ((int) $this->type_opinion == 1) {
            $this->author_name_slug = 'editorial';
        } elseif ((int) $this->type_opinion == 2) {
            $this->author_name_slug = 'director';
        } else {
            $author = new \User($this->fk_author);

            $this->name             = \Onm\StringUtils::getTitle($author->name);
            $this->author_name_slug = $this->name;

            if (array_key_exists('is_blog', $author->meta) && $author->meta['is_blog'] == 1) {
                $params['item'] = $this;
                $template       = 'frontpage/contents/_blog.tpl';

                if ($params['custom'] == 1) {
                    $template = $params['tpl'];
                }

                return $tpl->fetch($template, $params);
            }
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

        $contentsSuggestedInFrontpage = $cm->getContentsForHomepageOfCategory($category);

        foreach ($contentsSuggestedInFrontpage as $content) {
            if ($content->content_type == 4) {
                $excludedContents[] = (int) $content->id;
            }
        }

        if (count($excludedContents) > 0) {
            $sqlExcludedContents  = ' AND opinions.pk_opinion NOT IN (';
            $sqlExcludedContents .= implode(', ', $excludedContents);
            $sqlExcludedContents .= ') ';
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
