<?php
/**
 * Defines the StaticPage class.
 *
 * @package    Model
 */
/*
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

/**
 * Handles all the CRUD actions over StaticPages.
 *
 * @package    Model
 */
class StaticPage extends Content
{
    /**
     * The static page id
     *
     * @var int
     **/
    public $pk_static_page = null;

    /**
     * The content type of the static_page
     *
     * @var string
     **/
    public $content_type = 'static_page';

    /**
     * Loads the static page information given an id
     *
     * @param int $id
     *
     * @return StaticPage the static page object
     */
    public function __construct($id = null)
    {
        $this->content_type_l10n_name = _('Static Page');

        parent::__construct($id);
    }

    /**
     * Creates a new static page given an array of information
     *
     * @param array $data The data of the new static page
     *
     * @return boolean true if the static page was created
     **/
    public function create($data)
    {
        $data['category'] = 0;

        parent::create($data);

        $sql = "INSERT INTO `static_pages` (`static_pages`.`pk_static_page`)
                VALUES (?)";
        $values = array(
            'pk_static_page' => $this->id,
        );

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            return false;
        }

        return true;
    }

    /**
     * Updates the static page given an array of information
     *
     * @param  array   $data the new static page information
     *
     * @return boolean true if the static page was updated
     */
    public function update($data)
    {
        $data['category'] = 0;

        return parent::update($data);
    }

    /**
     * Deletes an static page given its id
     *
     * @param  int $id Identifier
     *
     * @return boolean true if the static page was removed
     */
    public function remove($id)
    {
        parent::remove($id);

        $sql = 'DELETE FROM `static_pages` WHERE `pk_static_page`=?';
        $values = array($id);

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            return false;
        }

        return true;
    }

    /**
     * Returns the slug for a set of information
     *
     * @param string $slug the slug of the static page
     * @param int $id the id of the static page
     * @param string $title the title of the slug
     *
     * @return string the slug
     **/
    public function buildSlug($slug, $id, $title = null)
    {
        if (empty($slug) && !empty($title)) {
            $slug = StringUtils::get_title($title, $useStopList = false);
        }
        $slug = StringUtils::get_title($slug, $useStopList = false);

        // Get titles to check unique value
        $slugs = $this->getSlugs('pk_static_page<>"' . $id . '"');
        $i = 0;
        $tmp = $slug;
        while (in_array($tmp, $slugs)) {
            $tmp = $slug . '-' . ++$i;
        }

        return $tmp;
    }

    /**
     * Searches and returns a static page object by its slug
     *
     * @param string $slug the slug to search for the static page
     *
     * @return StaticPage the static page object
     **/
    public static function getPageBySlug($slug)
    {
        $slug = preg_replace('/\*%_\?/', '', $slug);
        $sql = 'SELECT pk_static_page
                FROM `static_pages`, `contents` WHERE
                in_litter = 0 AND
                `contents`.`pk_content`= `static_pages`.`pk_static_page` AND
                `contents`.`slug` LIKE ?
                ORDER BY  pk_static_page DESC';

        $id = $GLOBALS['application']->conn->GetOne($sql, array($slug));

        if ($id === false) {
            return null;
        }

        return new StaticPage($id);
    }

    /**
     * Returns a list of assigned static page slugs
     *
     * @param string $filter the WHERE statement to filter the slugs
     *
     * @return array the list of slugs
     **/
    public function getSlugs($filter = null)
    {
        $titles = array();
        $cm = new ContentManager();
        $pages = $cm->find(
            'Static_Page',
            $filter,
            'ORDER BY created DESC ',
            'pk_content, pk_static_page, slug'
        );
        foreach ($pages as $p) {
            $titles[] = $p->slug;
        }

        return $titles;
    }
}
