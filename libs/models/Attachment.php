<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class Attachment extends Content
{
    /**
     * The attachment id
     *
     * @var int
     */
    public $pk_attachment = null;

    /**
     * The attachemnt title
     *
     * @var
     */
    public $title = null;

    /**
     * The relative path to the file
     *
     * @var
     */
    public $path = null;

    /**
     * The category Id
     */
    public $category = null;

    /**
     * Proxy handler for the object cache
     *
     * @var MethodCacheManager
     */
    public $cache = null;

    /**
     * Constructor for the Attachment class
     *
     * @param  integer $id the id of the Attachment
     */
    public function __construct($id = null)
    {
        $this->content_type_l10n_name = _('File');
        $this->content_type           = 3;
        $this->content_type_name      = 'attachment';

        parent::__construct($id);
    }

    /**
     * Magic function for getting uninitilized object properties.
     *
     * @param string $name the name of the property to get.
     *
     * @return mixed the value for the property
     */
    public function __get($name)
    {
        switch ($name) {
            case 'slug':
                return \Onm\StringUtils::generateSlug($this->title);

            case 'file_path':
                return getService('core.instance')->getFilesShortPath();

            default:
                return parent::__get($name);
        }
    }

    /**
     * Fetches information from one attachment given an id
     *
     * @param integer $id the id of the attachment we want to get information
     *
     * @return null|boolean|Attachment
     */
    public function read($id)
    {
        // If no valid id then return
        if (((int) $id) <= 0) {
            return null;
        }

        try {
            $rs = getService('dbal_connection')->fetchAssoc(
                'SELECT * FROM contents LEFT JOIN content_category ON pk_content = content_id '
                . 'LEFT JOIN attachments ON pk_content = pk_attachment WHERE pk_content = ?',
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
     * Creates a new attachment from the given data
     *
     * @param array $data the data for create the new Attachment
     *
     * @return boolean if it is true all went well, if it is false something went wrong
     */
    public function create($data)
    {
        $dirDate = date("/Y/m/d/");

        if ($this->exists($data['path']) && !array_key_exists('no_path', $data)) {
            return false;
        }

        $data['category'] = $data['category_id'];

        // all the data is ready to save into the database,
        // so create the general entry for this content
        parent::create($data);

        // now save all the specific information into the attachment table
        try {
            $rs = getService('dbal_connection')->insert('attachments', [
                'pk_attachment' => $this->id,
                'title'         => $data['title'],
                'path'          => $data['path'],
                'category'      => $data['category']
            ]);

            if (!$rs) {
                return false;
            }
        } catch (\Exception $e) {
            getService('error.log')->error($e->getMessage());

            return false;
        }

        // Check if exist thumbnail for this PDF
        if (preg_match('/\.pdf$/', $data['path'])) {
            $media_path = $this->file_path . DS . FILE_DIR . $dirDate;
            $imageName  = basename($data['path'], ".pdf") . '.jpg';

            // Remove existent thumbnail for PDF
            if (file_exists($media_path . '/' . $imageName)) {
                unlink($media_path . '/' . $imageName);
            }
        }

        return $this->id;
    }

    /**
     * Updates the information for one attachment given an array of data
     *
     * @param array $data the array of data for the attachment
     *
     * @return boolean
     */
    public function update($data)
    {
        $data['category'] = $data['category_id'];

        parent::update($data);

        try {
            $this->serializeL10nKeys($data);

            getService('dbal_connection')->update('attachments', [
                'title'    => $data['title'],
                'path'     => $data['path'],
                'category' => $data['category']
            ], [ 'pk_attachment' => (int) $data['id'] ]);

            return true;
        } catch (\Exception $e) {
            getService('error.log')->error($e->getMessage());

            return false;
        }
    }

    /**
     * Removes permanently the attachment given its id
     *
     * @param int $id the attachement id for delete
     *
     * @return boolean
     */
    public function remove($id)
    {
        // If no valid id then return
        if (((int) $id) <= 0) {
            return false;
        }

        $filename = getService('service_container')->getParameter('core.paths.public')
            . getService('core.instance')->getFilesShortPath()
            . $this->path;

        try {
            parent::remove($id);

            getService('dbal_connection')->delete(
                'attachments',
                [ 'pk_attachment' => $id ]
            );
        } catch (\Exception $e) {
            return false;
        }

        if (file_exists($filename) && !is_dir($filename)) {
            unlink($filename);
        }

        return true;
    }

    /**
     * Check if an attachment already exists
     *
     * @param  string  $path the path to check
     * @param  string  $category the category where to check
     *
     * @return boolean
    */
    public function exists($path)
    {
        try {
            $rs = getService('dbal_connection')->fetchColumn(
                'SELECT count(*) AS total FROM attachments WHERE `path`=?',
                [ $path ]
            );

            return intval($rs) > 0;
        } catch (\Exception $e) {
            getService('error.log')->error($e->getMessage());

            return false;
        }
    }

    /**
     *  This method return the path of the file system for this file
     */
    public function getFileSystemPath()
    {
        return $this->file_path . DS . $this->path;
    }

    public function getRelativePath()
    {
        return $this->path;
    }
}
