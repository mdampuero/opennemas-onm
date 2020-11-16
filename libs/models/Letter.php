<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class Letter extends Content
{
    /**
     * The letter id
     *
     * @var int
     */
    public $pk_letter = null;

    /**
     * The author id
     *
     * @var int
     */
    public $author = null;

    /**
     * Initializes Letter object instance
     *
     * @param int $id the letter id
     */
    public function __construct($id = null)
    {
        $this->content_type_l10n_name = _('Letter to editor');
        $this->content_type           = 17;
        $this->content_type_name      = 'letter';

        parent::__construct($id);
    }

    /**
     * Magic method for generating property values
     *
     * @param string $name the property name
     *
     * @return mixed the property value
     */
    public function __get($name)
    {
        switch ($name) {
            case 'summary':
                $summary = substr(strip_tags($this->body), 0, 200);
                $pos     = strripos($summary, ".");

                if ($pos > 100) {
                    $summary = substr($summary, 0, $pos) . ".";
                } else {
                    $summary = substr($summary, 0, strripos($summary, " "));
                }
                return $summary;

            default:
                return parent::__get($name);
        }
    }

    /**
     * Overloads the object properties with an array of the new ones.
     *
     * @param array $properties The list of properties to load.
     */
    public function load($properties)
    {
        parent::load($properties);

        if (is_array($this->params) && array_key_exists('ip', $this->params)) {
            $this->ip = $this->params['ip'];
        }
    }

    /**
     * Loads the letter information given its id
     *
     * @param int $id the letter id
     *
     * @return Letter the letter instance
     */
    public function read($id)
    {
        // If no valid id then return
        if (((int) $id) <= 0) {
            return;
        }

        try {
            $rs = getService('dbal_connection')->fetchAssoc(
                'SELECT * FROM contents LEFT JOIN content_category ON pk_content = content_id '
                . 'LEFT JOIN letters ON pk_content = pk_letter WHERE pk_content=?',
                [ $id ]
            );

            if (!$rs) {
                return false;
            }

            $this->load($rs);

            return $this;
        } catch (\Exception $e) {
            getService('error.log')->error($e->getMessage());

            return false;
        }
    }

    /**
     * Creates a new letter from data.
     *
     * @param array $data The letter data.
     *
     * @return mixed The letter if it was store successfully
     */
    public function create($data)
    {
        $data['position'] = 1;

        parent::create($data);

        try {
            getService('dbal_connection')->insert(
                'letters',
                [
                    'pk_letter' => $this->id,
                    'author'    => $data['author'],
                    'email'     => $data['email']
                ]
            );

            if (array_key_exists('image', $data) && !empty($data['image'])) {
                $this->setMetadata('image', $data['image']);
            }

            if (array_key_exists('url', $data) && !empty($data['url'])) {
                $this->setMetadata('url', $data['url']);
            }

            return $this;
        } catch (\Exception $e) {
            getService('error.log')->error(
                'Error creating Letter: ' . $e->getMessage()
            );

            return false;
        }
    }

    /**
     * Updates the letter information given an array of data
     *
     * @param array $data the data array
     *
     * @return boolean true if the letter was updated
     */
    public function update($data)
    {
        $data['position'] = 1;

        parent::update($data);

        try {
            getService('dbal_connection')->update(
                'letters',
                [
                    'author'    => $data['author'],
                    'email'     => $data['email']
                ],
                [ 'pk_letter' => $this->id ]
            );

            if (array_key_exists('image', $data) && !empty($data['image'])) {
                $this->setMetadata('image', $data['image']);
            }

            if (array_key_exists('url', $data) && !empty($data['url'])) {
                $this->setMetadata('url', $data['url']);
            }

            return true;
        } catch (\Exception $e) {
            getService('error.log')->error($e->getMessage());

            return false;
        }
    }

    /**
     * Removes permanently the letter.
     *
     * @param integer $id The letter id.
     *
     * @return boolean True if the letter was removed.
     */
    public function remove($id)
    {
        parent::remove($id);

        try {
            getService('dbal_connection')
                ->delete('letters', [ 'pk_letter' => $id ]);

            return true;
        } catch (\Exception $e) {
            getService('error.log')->error($e->getMessage());

            return false;
        }
    }

    /**
     * Determines if the content of a comment has bad words.
     *
     * @param array $data The data from the comment.
     *
     * @return boolean True if the letter contains bad words. Otherwise, returns
     *                 false.
     */
    public function hasBadWords($data)
    {
        $text = $data['title'] . ' ' . $data['body'];

        if (isset($data['author'])) {
            $text .= ' ' . $data['author'];
        }

        $weight = \Onm\StringUtils::getWeightBadWords($text);

        return $weight > 100;
    }
}
