<?php
/**
 * Defines the Frontend Advertisement controller
 *
 * @package Model
 */
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Handles all the CRUD actions over newsletter.
 *
 * @package    Model
 */
class Newsletter
{
    /**
     * The title of the newsletter
     *
     * @var string
     */
    public $title;

    /**
     * Serialized data, contents and other params
     *
     * @var string
     */
    public $data;

    /**
     * The final HTML of the newsletter_archive
     *
     * @var string
     */
    public $html;

    /**
     * The data when the newsletter was created
     *
     * @var string
     */
    public $created;

    /**
     * Whether if the newsletter was sent
     *
     * @var string
     */
    public $sent;

    /**
     * Initializes the newsletter for a given id.
     *
     * @param string $id the content id to initilize.
     *
     * @return Newsletter the object instance
     */
    public function __construct($id = null)
    {
        if (!is_null($id)) {
            return $this->read($id);
        }
    }

    /**
     * Loads the newsletter properties from an array.
     *
     * @param Array $data The data to load.
     *
     * @return Newsletter The current newsletter.
     */
    public function load($data)
    {
        $this->id            = $data['pk_newsletter'];
        $this->pk_newsletter = $data['pk_newsletter'];
        $this->title         = $data['title'];
        $this->data          = $data['data'];
        $this->created       = $data['created'];
        $this->updated       = $data['updated'];
        $this->html          = $data['html'];
        $this->sent          = $data['sent'];

        return $this;
    }

    /**
     * Loads the data for an newsletter given its id
     *
     * @param int $id the object id to load
     *
     * @return mixed The Newsletter if it was loaded. False otherwise.
     */
    public function read($id)
    {
        // If no valid id then return
        if (((int) $id) <= 0) return;

        try {
            $sql = 'SELECT * FROM `newsletter_archive` WHERE pk_newsletter=?';
            $rs  = getService('dbal_connection')->fetchAssoc($sql, [ $id ]);

            if (!$rs) {
                return;
            }

            $this->load($rs);

            return $this;
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * Creates a newsletter from data.
     *
     * @param array $data The newsletter data.
     *
     * @return mixed The Newsletter if it was stored. False otherwise.
     */
    public function create($data)
    {
        $data['created'] = date("Y-m-d H:i:s");

        if (!array_key_exists('sent', $data)) {
            $data['sent'] = 0;
        }

        $conn = getService('dbal_connection');

        try {
            $conn->insert(
                'newsletter_archive',
                [
                    'title'   => $data['title'],
                    'data'    => $data['data'],
                    'html'    => $data['html'],
                    'created' => $data['created'],
                    'updated' => $data['created'],
                    'sent'    => $data['sent']
                ]
            );

            $this->id            = $conn->lastInsertId();
            $this->pk_newsletter = $this->id;
            $this->read($this->id);

            return $this;
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * Updates the newsletter properties given an array of data
     *
     * @param array $newdata array with data for update
     *
     * @return Newsletter the object instance
     */
    public function update($newdata)
    {
        if (array_key_exists('title', $newdata) && !is_null($newdata['title'])) {
            $title = $newdata['title'];
        } else {
            $title = $this->title;
        }

        if (array_key_exists('html', $newdata) && !is_null($newdata['html'])) {
            $html = $newdata['html'];
        } else {
            $html = $this->html;
        }

        if (array_key_exists('data', $newdata) && !is_null($newdata['data'])) {
            $data = $newdata['data'];
        } else {
            $data = $this->data;
        }

        if (array_key_exists('sent', $newdata) && !is_null($newdata['sent'])) {
            if (!empty($this->sent)) {
                $sent = (int)$this->sent + (int)$newdata['sent'];
            } else {
                $sent = $newdata['sent'];
            }
        } else {
            $sent = $this->sent;
        }

        try {
            getService('dbal_connection')->update(
                'newsletter_archive',
                [
                    'title'   => $title,
                    'data'    => $data,
                    'html'    => $html,
                    'updated' => date("Y-m-d H:i:s"),
                    'sent'    => $sent
                ],
                [ 'pk_newsletter' => $this->id ]
            );

            $this->read($this->id);

            return $this;
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * Deletes a newsletter.
     *
     * @return boolean True if the newsletter was deleted. False otherwise.
     */
    public function delete()
    {
        try {
            getService('dbal_connection')->delete(
                'newsletter_archive',
                [ 'pk_newsletter' => $this->id ]
            );

            return true;
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }
}
