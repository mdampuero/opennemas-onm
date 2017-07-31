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
    public $pk_kiosko   = null;

    /**
     * The name of the kiosko
     *
     * @var string
     */
    public $name        = null;

    /**
     * The path to the kiosko file
     *
     * @var string
     */
    public $path        = null;

    /**
     * The publishing date of the kiosko
     *
     * @var string
     */
    public $date        = null;

    /**
     * Whether if this kiosko is marked as favorite or not
     *
     * @var boolean
     */
    public $favorite    = 0;

    /**
     * The price of this kiosko
     *
     * @var int
     */
    public $price       = 0;

    /**
     * The type of kiosko
     *
     * @var string
     */
    public $type        = 0;

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
        $this->kiosko_path = INSTANCE_MEDIA_PATH . 'kiosko' . DS;

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
                .'LEFT JOIN kioskos ON pk_content = pk_kiosko WHERE pk_content = ?',
                [ $id ]
            );

            if (!$rs) {
                return false;
            }

            $this->load($rs);

            return $this;
        } catch (\Exception $e) {
            error_log('Error while fetching Kiosko: '.$e->getMessage());
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

        parent::create($data);

        try {
            $this->createThumb($data['name'], $data['path']);

            getService('dbal_connection')->insert(
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

            return $this;
        } catch (\Exception $e) {
            error_log('Error while creating Kiosko: '.$e->getMessage());
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
        parent::update($data);

        try {
            getService('dbal_connection')->update(
                'kioskos',
                [
                    'name'      => $data['name'],
                    'date'      => $data['date'],
                    'price'     => $data['price'],
                    'type'      => $data['type']
                ],
                [ 'pk_kiosko' => (int) $data['id'] ]
            );

            return $this;
        } catch (\Exception $e) {
            error_log('Error while updating Kiosko: '.$e->getMessage());
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
        parent::remove($this->id);

        try {
            $paperPdf      = $this->kiosko_path . $this->path . $this->name;
            $paperImage    = $this->kiosko_path . $this->path
                . preg_replace("/.pdf$/", ".jpg", $this->name);
            $bigPaperImage = $this->kiosko_path . $this->path .
                preg_replace('/.pdf$/', '.jpg', '650-' . $this->name);

            unlink($paperPdf);
            unlink($paperImage);
            unlink($bigPaperImage);

            getService('dbal_connection')
                ->delete('kioskos', [ 'pk_kiosko' => $id ]);

            return true;
        } catch (\Exception $e) {
            error_log('Error while removing Kiosko: '.$e->getMessage());
            return false;
        }
    }

    /**
     * Creates the PDF thumbnail for the kiosko.
     *
     * @param string $file_pdf The filename to the pdf file.
     * @param string $path     The path to the pdf file.
     */
    public function createThumb($file_pdf, $path)
    {
        $imageFileName = basename($file_pdf, '.pdf') . '.jpg';
        $tmpName       = '/tmp/' . basename($file_pdf, '.pdf') . '.png';

        // Thumbnail first page (see [0])
        if (!file_exists($this->kiosko_path . $path . $file_pdf)) {
            return;
        }

        try {
            $imagick = new \Imagick($this->kiosko_path . $path . $file_pdf . '[0]');
            $imagick->setImageBackgroundColor('white');
            $imagick->thumbnailImage(650, 0);

            // Deprecated: $imagick = $imagick->flattenImages();
            // $imagick->setImageAlphaChannel(imagick::ALPHACHANNEL_REMOVE);
            // This constant used above is not supported for all versions of Imagick
            // Use number (11) to solve problem
            // http://php.net/manual/en/imagick.flattenimages.php#116956
            $imagick->setImageAlphaChannel(11);
            $imagick->mergeImageLayers(imagick::LAYERMETHOD_FLATTEN);
            $imagick->setFormat('png');

            // First, save to PNG (*.pdf => /tmp/xxx.png)
            $imagick->writeImage($tmpName);

            // Finally, save to jpg (/tmp/xxx.png => *.jpg) to avoid
            // problems with image
            $imagick = new \Imagick($tmpName);
            $imagick->writeImage($this->kiosko_path . $path . '650-' . $imageFileName);
            $imagick->thumbnailImage(180, 0);

            // Write the new image to a file
            $imagick->writeImage($this->kiosko_path.$path.$imageFileName);

            //remove temp image
            unlink($tmpName);
        } catch (\Exception $e) {
        }
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
            .  ' FROM `kioskos` ORDER BY year DESC, month DESC';

        $rs = getService('dbal_connection')->fetchAll($sql);

        foreach ($rs as $value) {
            $items[$value['year']][] = $value['month'];
        }

        return $items;
    }
}
