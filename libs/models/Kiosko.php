<?php
/**
 * Handles all the CRUD actions over kioko.
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
 * Handles all the CRUD actions over kioko.
 *
 * @package    Model
 */
class Kiosko extends Content
{
    /**
     * The kiosko id
     *
     * @var int
     */
    public $pk_kiosko = null;

    /**
     * The name of the kiosko
     *
     * @var string
     */
    public $name = null;

    /**
     * The path to the kiosko file
     *
     * @var string
     */
    public $path = null;

    /**
     * The publishing date of the kiosko
     *
     * @var string
     */
    public $date = null;

    /**
     * Whether if this kiosko is marked as favorite or not
     *
     * @var boolean
     */
    public $favorite = 0;

    /**
     * The price of this kiosko
     *
     * @var int
     */
    public $price = 0;

    /**
     * The type of kiosko
     *
     * @var string
     */
    public $type = 0;

    /**
     * The path to the kiosko
     *
     * @var string
     */
    public $kiosko_path = null;

    /**
      * Initializes the Kiosko.
      *
      * @param integer $id The kiosko id.
      */
    public function __construct($id = null)
    {
        $this->content_type_l10n_name = _('Cover');
        $this->kiosko_path            = INSTANCE_MEDIA_PATH . 'kiosko' . DS;

        parent::__construct($id);
    }

    /**
     * Overloads the object properties with an array of the new ones.
     *
     * @param array $properties The list of properties to load.
     */
    public function load($properties)
    {
        if (array_key_exists('name', $properties)) {
            $properties['thumb_url'] =
                str_replace('.pdf', '.jpg', $properties['name']);
        }

        parent::load($properties);
    }

    /**
     * Loads the kiosko data given an id.
     *
     * @param integer $id The kiosko id.
     *
     * @return Kiosko The current kiosko.
     */
    public function read($id)
    {
        // If no valid id then return
        if (((int) $id) <= 0) {
            return false;
        }

        try {
            $rs = getService('dbal_connection')->fetchAssoc(
                'SELECT * FROM contents LEFT JOIN contents_categories ON pk_content = pk_fk_content '
                . 'LEFT JOIN kioskos ON pk_content = pk_kiosko WHERE pk_content = ?',
                [ $id ]
            );

            if (!$rs) {
                return false;
            }

            $this->load($rs);

            return $this;
        } catch (\Exception $e) {
            error_log('Error while fetching Kiosko: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Creates a new kiosko from data.
     *
     * @param array $data The kiosko data.
     *
     * @return boolean True if the object was stored.
     */
    public function create($data)
    {
        $data['price'] = isset($data['price']) ? $data['price'] : 0;
        $data['type']  = isset($data['type']) ? $data['type'] : 0;

        $conn = getService('dbal_connection');

        $conn->beginTransaction();

        try {
            parent::create($data);

            $conn->insert(
                'kioskos',
                [
                    'pk_kiosko' => (int) $this->id,
                    'name'      => $data['name'],
                    'path'      => $data['path'],
                    'date'      => $data['date'],
                    'price'     => $data['price'],
                    'type'      => $data['type']
                ]
            );

            $conn->commit();

            return $this;
        } catch (\Exception $e) {
            error_log('Error while creating Kiosko: ' . $e->getMessage());

            $conn->rollback();

            return false;
        }
    }

    /**
     * Updates the kiosko information given an array of data.
     *
     * @param array $data The new data for the kiosko.
     *
     * @return boolean true If the kiosko was updated.
     */
    public function update($data)
    {
        $conn = getService('dbal_connection');

        $conn->beginTransaction();

        try {
            parent::update($data);

            $conn->update(
                'kioskos',
                [
                    'name'      => $data['name'],
                    'date'      => $data['date'],
                    'price'     => $data['price'],
                    'type'      => $data['type']
                ],
                [ 'pk_kiosko' => (int) $data['id'] ]
            );

            $conn->commit();

            return $this;
        } catch (\Exception $e) {
            $conn->rollback();

            error_log('Error while updating Kiosko: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Removes permanently the kiosko and its files.
     *
     * @param integer $id The kiosko id.
     *
     * @return boolean True if the kiosko was removed.
     */
    public function remove($id)
    {
        $conn = getService('dbal_connection');

        try {
            $conn->beginTransaction();

            parent::remove($this->id);

            $conn->delete('kioskos', [ 'pk_kiosko' => $id ]);

            $conn->commit();

            $this->removeFiles();

            return true;
        } catch (\Exception $e) {
            $conn->rollback();
            error_log('Error while removing Kiosko: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Removes the files assigned to the newsstand
     *
     * @return void
     **/
    public function removeFiles()
    {
        $coverFile  = $this->kiosko_path . $this->path . $this->name;
        $coverThumb = $this->kiosko_path . $this->path . $this->thumb_url;

        // Remove old files if fileinput changed
        if (file_exists($coverFile) && is_file($coverFile)) {
            unlink($coverFile);
        }

        if (file_exists($coverThumb) && is_file($coverThumb)) {
            unlink($coverThumb);
        }

        return;
    }

    /**
     * Saves a  the PDF thumbnail for the kiosko.
     *
     * @param string $file_pdf The filename to the pdf file.
     * @param string $path     The path to the pdf file.
     */
    public function saveFiles($targetPath, $targetFileName, $cover, $thumbnail)
    {
        $absolutePath      = $this->kiosko_path . $targetPath;
        $thumbnailFileName = basename($targetFileName, '.pdf') . '.jpg';

        // Create folder if it doesn't exist
        if (!file_exists($absolutePath)) {
            \Onm\FilesManager::createDirectory($absolutePath);
        }

        $uploadStatus = (
            $cover->isValid() && $cover->move($absolutePath, $targetFileName)
            && $thumbnail->isValid() && $thumbnail->move($absolutePath, $thumbnailFileName)
        );

        return $uploadStatus;
    }

    /**
     * Returns the list of months grouped by years.
     *
     * @return array The list of months grouped by year.
     */
    public function getMonthsByYears()
    {
        $items = [];
        $sql   = 'SELECT DISTINCT MONTH(date) as month, YEAR(date) as year'
            . ' FROM `kioskos` ORDER BY year DESC, month DESC';

        $rs = getService('dbal_connection')->fetchAll($sql);

        foreach ($rs as $value) {
            $items[$value['year']][] = $value['month'];
        }

        return $items;
    }
}
