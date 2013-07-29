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
     * Loads the kiosko data from an array into the object properties
     *
     * @param array $data the kiosko data
     *
     * @return Kiosko the kiosko object
     **/
    public function initialize($data)
    {
        $this->title     = $data['name'];
        $this->name      = $data['name'];
        $this->path      = $data['path'];
        $this->date      = $data['date'];
        $this->price     = $data['price'];
        $this->type      = $data['type'];
        $this->category  = $data['category'];
        $this->available = $data['available'];
        $this->metadata  = $data['metadata'];

        return $this;
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
            throw new \Exception(_("Unable to save the cover data into the database."));
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
        parent::read($id);

        $sql = 'SELECT pk_kiosko, name, path, date, price, type FROM kioskos WHERE pk_kiosko=?';

        $rs = $GLOBALS['application']->conn->Execute($sql, array($id));
        if (!$rs) {
            return null;
        }

        $this->load($rs->fields);

        return $this;
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
        if (isset($data['available']) and !isset($data['content_status'])) {
            $data['content_status'] = $data['available'];
        }

        $GLOBALS['application']->dispatch('onBeforeUpdate', $this);

        parent::update($data);

        $sql  = "UPDATE kioskos SET `date`=?, `price`=?"
                ." WHERE pk_kiosko=?";

        $values = array($data['date'], $data['price'], $data['id']);

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            return false;
        }

        $this->category_name = $this->loadCategoryName($this->id);
        $GLOBALS['application']->dispatch('onAfterUpdate', $this);

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
     * Sets the favorite flag
     *
     * @param int $status the final status of the favorite flag
     *
     * @return boolean true if the flag changed its status
     **/
    public function set_favorite($status)
    {
        if ($this->id == null) {
            return false;
        }

        parent::set_favorite($status);

        $sql = "UPDATE kioskos SET `favorite`=? WHERE pk_kiosko=?";
        $values = array($status, $this->id);

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            return false;
        }

        return true;
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
        $img_name = basename($file_pdf, ".pdf") . '.jpg';
        $tmp_name = '/tmp/' . basename($file_pdf, ".pdf") . '.png';

        // Thumbnail first page (see [0])
        if (file_exists($this->kiosko_path.$path. $file_pdf)) {
            try {

                $imagick = new \Imagick($this->kiosko_path.$path.$file_pdf.'[0]');
                $imagick->thumbnailImage(650, 0);
                // First, save to PNG (*.pdf => /tmp/xxx.png)
                $imagick->writeImage($tmp_name);
                // finally, save to jpg (/tmp/xxx.png => *.jpg)
                // to avoid problems with the image
                $imagick = new \Imagick($tmp_name);

                $imagick->writeImage($this->kiosko_path.$path.'650-'.$img_name);

                $imagick->thumbnailImage(180, 0);
                // Write the new image to a file
                $imagick->writeImage($this->kiosko_path.$path.$img_name);

                //remove temp image
                unlink($tmp_name);
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
    public function get_months_by_years()
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
