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
     */
    public $pk_static_page = null;

    /**
     * The content type of the static_page
     *
     * @var string
     */
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
     */
    public function create($data)
    {
        $data['category'] = 0;

        try {
            parent::create($data);

            $rs = getService('dbal_connection')->insert(
                'static_pages',
                [
                    'pk_static_page' => $this->id
                ]
            );

            return true;
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return false;
        }
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
        if ((int) $id <= 0) return false;

        try {
            parent::remove($id);
            $rs = getService('dbal_connection')->delete(
                "static_pages",
                [ 'pk_static_page' => $id ]
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
     * Returns the slug for a set of information
     *
     * @param string $slug the slug of the static page
     * @param int $id the id of the static page
     * @param string $title the title of the slug
     *
     * @return string the slug
     */
    public function buildSlug($slug, $id, $title = null)
    {
        if (empty($slug) && !empty($title)) {
            $slug = \Onm\StringUtils::generateSlug($title, $useStopList = false);
        } else {
            $slug = \Onm\StringUtils::generateSlug($slug, $useStopList = false);
        }

        // Get titles to check unique value
        $slugs = $this->getSlugs([
            'pk_content' => (int) $id,
            'slug'       => $slug,
        ]);

        $i = 0;
        $tmp = $slug;
        while (in_array($tmp, $slugs)) {
            $tmp = $slug . '-' . ++$i;
        }

        return $tmp;
    }

    /**
     * Returns a list of assigned static page slugs
     *
     * @param string $filter the WHERE statement to filter the slugs
     *
     * @return array the list of slugs
     */
    public function getSlugs($filter = null)
    {
        try {
            $rs = getService('dbal_connection')->fetchAll(
                'SELECT slug FROM contents WHERE pk_content <> ? AND slug LIKE ?',
                [
                    $filter['pk_content'],
                    '%'.$filter['slug'].'%',
                ]
            );

            $slugs = [];
            foreach ($rs as $slug) {
                $slugs [] = $slug['slug'];
            }

            return $slugs;

        } catch (\Exception $e) {
            error_log($e->getMessage());
            return [];
        }
    }
}
