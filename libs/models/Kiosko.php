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
 **/
class Kiosko extends Content
{
    /**
     * The kiosko id
     *
     * @var int
     **/
    public $pk_kiosko   = null;

    /**
     * The name of the kiosko
     *
     * @var string
     **/
    public $name        = null;

    /**
     * The path to the kiosko file
     *
     * @var string
     **/
    public $path        = null;

    /**
     * The publishing date of the kiosko
     *
     * @var string
     **/
    public $date        = null;

    /**
     * Whether if this kiosko is marked as favorite or not
     *
     * @var boolean
     **/
    public $favorite    = 0;

    /**
     * The price of this kiosko
     *
     * @var int
     **/
    public $price       = 0;

    /**
     * The type of kiosko
     *
     * @var string
     **/
    public $type        = 0;

    /**
     * The path to the kiosko
     *
     * @var string
     **/
    public $kiosko_path = null;

    /**
      * Initializes the kiosko object
      *
      * @param int $id the kiosko id to read
      *
      * @return Kiosko the object instance
      */
    public function __construct($id = null)
    {
        $this->content_type_l10n_name = _('Cover');
        $this->kiosko_path = INSTANCE_MEDIA_PATH.'kiosko'.DS;

        parent::__construct($id);
    }

    /**
     * Creates a new kiosko from a data array
     *
     * @param array $data the kiosko data
     *
     * @return int the kiosko id
     **/
    public function create($data)
    {
        if ($this->exists($data['path'], $data['category'])) {
            //  throw new \Exception(_("There's other paper in this date & this category."));
        }

        // Check price
        if (!isset($data['price'])) {
            $data['price'] = 0;
        }
        //Check type
        if (!isset($data['type'])) {
            $data['type'] = 0;
        }

        parent::create($data);

        $sql  = "INSERT INTO kioskos (`pk_kiosko`, `name`, `path`, `date`, `price`, `type` )"
                ." VALUES (?,?,?,?,?,?)";

        $this->createThumb($data['name'], $data['path']);

        $values = array(
            $this->id, $data['name'], $data['path'],
            $data['date'], $data['price'], $data['type']
        );

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            throw new \Exception(_("Unable to save the cover data."));
        }

        return $this->id;
    }

    /**
     * Loads the kiosko data given an id
     *
     * @param int $id the kiosko id
     *
     * @return Kiosko the object instance
     **/
    public function read($id)
    {
        // If no valid id then return
        if (((int) $id) <= 0) return;

        try {
            $rs = getService('dbal_connection')->fetchAssoc(
                'SELECT * FROM contents LEFT JOIN contents_categories ON pk_content = pk_fk_content '
                .'LEFT JOIN kioskos ON pk_content = pk_kiosko WHERE pk_content = ?',
                [ $id ]
            );

            if (!$rs) {
                return false;
            }
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return false;
        }

        $this->load($rs);

        return $this;
    }

    /**
     * Overloads the object properties with an array of the new ones
     *
     * @param array $properties the list of properties to load
     *
     * @return void
     **/
    public function load($properties)
    {
        if (array_key_exists('name', $properties)) {
            $properties['thumb_url'] = str_replace('.pdf', '.jpg', $properties['name']);
        }

        parent::load($properties);
    }

    /**
     * Updates the kiosko information given an array of data
     *
     * @param array $data the new data for the kiosko
     *
     * @return boolean true if the kiosko was updated
     **/
    public function update($data)
    {
        parent::update($data);

        $sql  = "UPDATE kioskos SET `name`=?, `date`=?, `price`=? WHERE pk_kiosko=?";
        $values = array($data['name'], $data['date'], $data['price'], $data['id']);

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            return false;
        }

        $this->category_name = $this->loadCategoryName($this->id);

        return true;
    }

    /**
     * Removes permanently the kiosko and its files
     *
     * @param int $id the kiosko id to remove
     *
     * @return boolean true if the kiosko was removed
     **/
    public function remove($id)
    {
        parent::remove($this->id);

        $sql = 'DELETE FROM kioskos WHERE pk_kiosko='.($this->id);

        $paperPdf      = $this->kiosko_path.$this->path.$this->name;
        $paperImage    = $this->kiosko_path.$this->path.preg_replace("/.pdf$/", ".jpg", $this->name);
        $bigPaperImage = $this->kiosko_path."650-".$this->path.preg_replace("/.pdf$/", ".jpg", $this->name);

        unlink($paperPdf);
        unlink($paperImage);
        unlink($bigPaperImage);

        if ($GLOBALS['application']->conn->Execute($sql) === false) {
            return false;
        }

        return true;
    }

    /**
     * Check if already exists a kiosko
     *
     * @param  string  $path_pdf the path to the kiosko file
     * @param  string  $category the category where is saved
     *
     * @return boolean
    */
    public function exists($path_pdf, $category)
    {
        $sql = 'SELECT count(`kioskos`.`pk_kiosko`) AS total
                FROM kioskos,contents_categories
                WHERE `contents_categories`.`pk_fk_content`=`kioskos`.`pk_kiosko`
                AND `kioskos`.`path`=?
                AND `contents_categories`.`pk_fk_content_category`=?';
        $rs = $GLOBALS['application']->conn->GetOne($sql, array($path_pdf, $category));

        return intval($rs) > 0;
    }

    /**
     * Creates the PDF thumbnail for the kiosko
     *
     * @param string $file_pdf the filename to the pdf file
     * @param string $path     the path to the pdf file
     *
     * @return void
     **/
    public function createThumb($file_pdf, $path)
    {
        $imageFileName = basename($file_pdf, ".pdf") . '.jpg';
        $tmpName = '/tmp/' . basename($file_pdf, ".pdf") . '.png';

        // Thumbnail first page (see [0])
        if (file_exists($this->kiosko_path.$path. $file_pdf)) {
            try {
                $imagick = new \Imagick($this->kiosko_path.$path.$file_pdf.'[0]');
                $imagick->setImageBackgroundColor('white');
                $imagick->thumbnailImage(650, 0);
                $imagick = $imagick->flattenImages();
                $imagick->setFormat('png');
                // First, save to PNG (*.pdf => /tmp/xxx.png)
                $imagick->writeImage($tmpName);
                // finally, save to jpg (/tmp/xxx.png => *.jpg)
                // to avoid problems with the image
                $imagick = new \Imagick($tmpName);

                $imagick->writeImage($this->kiosko_path.$path.'650-'.$imageFileName);

                $imagick->thumbnailImage(180, 0);
                // Write the new image to a file
                $imagick->writeImage($this->kiosko_path.$path.$imageFileName);

                //remove temp image
                unlink($tmpName);
            } catch (Exception $e) {
                // Nothing
            }
        }
    }

    /**
     * Returns the list of kioskos by months
     *
     * @return array
     **/
    public function getMonthsByYears()
    {
        $sql = "SELECT DISTINCT MONTH(date) as month, "
               ."YEAR(date) as year FROM `kioskos` ORDER BY year DESC, month DESC";
        $rs = $GLOBALS['application']->conn->Execute($sql);

        $items = null;
        while (!$rs->EOF) {
            $items[$rs->fields['year']][] = $rs->fields['month'];
            $rs->MoveNext();
        }

        return $items;
    }

    /**
     * Get all subscription elements/items
     *
     */
    public static function getSubscriptionItems()
    {
        $sql = 'SELECT `kioskos`.`pk_kiosko`, `kioskos`.`price` , `contents`.`title`
                FROM kioskos, contents
                WHERE `contents`.`pk_content`=`kioskos`.`pk_kiosko`
                AND `kioskos`.`type`= 1 AND `contents`.`available` =1';

        $rs = $GLOBALS['application']->conn->GetArray($sql);

        if (!$rs) {
            return false;
        }

        return $rs;
    }
}
