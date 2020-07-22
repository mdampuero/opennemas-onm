<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
use Symfony\Component\Filesystem\Filesystem;

class Photo extends Content
{

    /**
     * Full path to the photo file
     *
     * @var string
     */
    public $path = null;

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
     * Initializes the Photo object instance given an id
     *
     * @param int $id the photo id to load
     *
     * @return Photo the photo object instance
     */
    public function __construct($id = null)
    {
        $this->content_type_l10n_name = _('Image');
        $this->content_type           = 8;
        $this->content_type_name      = 'photo';

        parent::__construct($id);
    }

    /**
     * Returns the photo relative path.
     *
     * @return string The photo relative path.
     */
    public function getRelativePath()
    {
        return preg_replace('@[/]+@', '/', $this->path);
    }
}
