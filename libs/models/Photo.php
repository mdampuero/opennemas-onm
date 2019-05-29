<?php
/**
 * Contains the Photo class definition
 *
 * This file is part of the Onm package.
 *
 * (c)  Fran Dieguez <fran@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package Model
 */
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;

/**
 * Photo class
 *
 * @package Model
 */
class Photo extends Content
{
    /**
     * Photo id
     *
     * @var int
     */
    public $pk_photo = null;

    /**
     * File name of the photo
     *
     * @var string
     */
    public $name = null;

    /**
     * Full path to the photo file
     *
     * @var string
     */
    public $path_file = null;

    /**
     * The size of the image
     *
     * @var int
     */
    public $size = null;

    /**
     * The width of the image
     *
     * @var int
     */
    public $width = null;

    /**
     * The height of the image
     *
     * @var int
     */
    public $height = null;

    /**
     * The copyright of the image
     *
     * @var string
     */
    public $author_name = null;

    /**
     * The photo information.
     *
     * @var string
     */
    public $infor = null;

    /**
     * Initializes the Photo object instance given an id
     *
     * @param int $id the photo id to load
     *
     * @return Photo the photo object instance
     */
    public function __construct($id = null)
    {
        $this->content_type_l10n_name = _('Image');

        parent::__construct($id);
    }

    /**
     * Overloads the object properties with an array of the new ones
     *
     * @param array $properties the list of properties to load
     */
    public function load($properties)
    {
        parent::load($properties);

        $this->pk_photo  = $properties['pk_photo'];
        $this->name      = $properties['name'];
        $this->path_file = $properties['path_file'];

        if (!empty($properties['path_file'])) {
            $this->path_img = $properties['path_file'] . DS . $properties['name'];
        }

        $this->size        = $properties['size'];
        $this->width       = $properties['width'];
        $this->height      = $properties['height'];
        $this->author_name = $properties['author_name'];
        $this->address     = $properties['address'];
        $this->type_img    = pathinfo($this->name, PATHINFO_EXTENSION);

        if (!empty($properties['address'])) {
            $positions = explode(',', $properties['address']);
            if (is_array($positions)
                && array_key_exists(0, $positions)
                && array_key_exists(1, $positions)
            ) {
                $this->latlong = [
                    'lat' => $positions[0],
                    'long' => $positions[1],
                ];
            }
        }
    }

    /**
     * Returns an instance of the Photo object given a photo id
     *
     * @param int $id the photo id to load
     *
     * @return Photo the photo object
     */
    public function read($id)
    {
        if ((int) $id <= 0) {
            return;
        }

        try {
            $rs = getService('dbal_connection')->fetchAssoc(
                'SELECT * FROM contents LEFT JOIN photos ON pk_content = pk_photo WHERE pk_content=?',
                [ $id ]
            );

            if (!$rs) {
                return false;
            }
        } catch (\Exception $e) {
            getService('error.log')->error($e->getMessage());
            return false;
        }

        $this->load($rs);

        return $this;
    }

    /**
     * Creates a new photo given an array of information
     *
     * @param array $data the photo information
     *
     * @return int the photo id
     * @return boolean false if the photo was not created
     */
    public function create($data)
    {
        $data['content_status'] = 1;

        parent::create($data);

        getService('dbal_connection')->insert('photos', [
            'pk_photo'    => $this->id,
            'name'        => $data['name'],
            'path_file'   => $data['path_file'],
            'size'        => $data['size'],
            'width'       => $data['width'],
            'height'      => $data['height'],
            'author_name' => $data['author_name'] ?? null
        ]);

        return $this->id;
    }

    /**
     * Creates a photo basing on a file and optional photo information.
     *
     * @param string $path The path to the file.
     * @param array  $data The photo information.
     * @param bool   $copy Whether to move or copy the file.
     *
     * @return int The photo id.
     */
    public function createFromLocalFile(string $path, array $data = [], bool $copy = false) : int
    {
        $ih   = getService('core.helper.image');
        $date = new \DateTime($data['created'] ?? null);

        $file     = new File($path);
        $path     = $ih->generatePath($file, $date->format('Y-m-d H:i:s'));
        $filename = basename($path);

        $ih->move($file, $path, $copy);

        if ($ih->isOptimizable($path)) {
            $ih->optimize($path);
        }

        $data = array_merge([
            'changed'        => $date->format('Y-m-d H:i:s'),
            'content_status' => 1,
            'created'        => $date->format('Y-m-d H:i:s'),
            'name'           => $filename,
            'path_file'      => $date->format('/Y/m/d/'),
            'title'          => $filename,
        ], $data, $ih->getInformation($path));

        return $this->create($data);
    }

    /**
     * Updates the photo object given an array with information
     *
     * @param array $data the new photo information
     *
     * @return boolean true if the photo was updated properly
     */
    public function update($data)
    {
        try {
            parent::update($data);

            getService('dbal_connection')->update('photos', [
                'name'        => $this->name,
                'path_file'   => $this->path_file,
                'size'        => $this->size,
                'width'       => (int) $this->width,
                'height'      => (int) $this->height,
                'author_name' => $data['author_name'],
                'address'     => $data['address'],
            ], [ 'pk_photo' => (int) $data['id'] ]);

            return true;
        } catch (\Exception $e) {
            getService('error.log')->error($e->getMessage());

            return false;
        }
    }

    /**
     * Removes a photo given its id.
     *
     * @param int $id the photo id to delete
     *
     * @return boolean true if the photo was deleted
     */
    public function remove($id)
    {
        //$path = getService('service_container')->getParameter('core.paths.public')
            //. getService('core.instance')->getImagesShortPath()
            //. $this->getRelativePath();

        //$fs = new Filesystem();

        //if ($fs->exists($path)) {
            //$fs->remove($path);
        //}

        parent::remove($id);

        getService('dbal_connection')->delete('photos', [ 'pk_photo' => $id ]);
    }

    /**
     * Returns the photo relative path.
     *
     * @return string The photo relative path.
     */
    public function getRelativePath()
    {
        return $this->path_file . $this->name;
    }
}
