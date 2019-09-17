<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Core\Component\Helper;

use Common\ORM\Entity\Instance;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Filesystem\Filesystem;

abstract class FileHelper
{
    /**
     * The Filesystem component.
     *
     * @var Filesystem
     */
    protected $fs;

    /**
     * The current instance.
     *
     * @var instance
     */
    protected $instance;

    /**
     * The server public directory.
     *
     * @var string
     */
    protected $publicDir;

    /**
     * Initalializes the FileHelper.
     *
     * @param Instance  $instance  The current instance.
     * @param string    $publicDir The server public directory.
     */
    public function __construct(Instance $instance, string $publicDir)
    {
        $this->fs        = new Filesystem();
        $this->instance  = $instance;
        $this->publicDir = $publicDir;
    }

    /**
     * Checks if a file exists.
     *
     * @param string $path The path to file.
     *
     * @return bool True if the file exists. False otherwise.
     */
    public function exists(string $path) : bool
    {
        return $this->fs->exists($path);
    }

    /**
     * Returns the path where the file should be moved.
     *
     * @param File   $file The file to generate path to.
     * @param string $date The date to generate the path from.
     *
     * @return string The path where the file should be moved.
     */
    public function generatePath(File $file, ?string $date = null) : string
    {
        $date = new \Datetime($date);

        return preg_replace('/\/+/', '/', sprintf(
            '%s/%s/%s/%s',
            $this->publicDir,
            $this->getPathForFile(),
            $date->format('Y/m/d'),
            $file->getClientOriginalName()
        ));
    }

    /**
     * Returns the relative path for the file ready to use in data model.
     *
     * @param File   $file The file to generate path to.
     * @param string $date The date to generate the path from.
     *
     * @return string The relative path ready to use in data model.
     */
    public function generateRelativePath(File $file, ?string $date = null) : string
    {
        return str_replace(preg_replace('/\/+/', '/', sprintf(
            '%s/%s',
            $this->publicDir,
            $this->getPathForFile()
        )), '', $this->generatePath($file, $date));
    }

    /**
     * Moves the file to the target path.
     *
     * @param File   $file   The file to move.
     * @param string $target The path where file will be moved.
     * @param bool   $copy   Whether to copy the file.
     *
     * @return string The target path.
     */
    public function move(File $file, string $target, bool $copy = false) : void
    {
        $name      = basename($target);
        $directory = str_replace($name, '', $target);

        if ($copy) {
            $this->fs->copy($file->getRealPath(), $target);
            return;
        }

        $file->move($directory, $name);
    }

    /**
     * @codeCoverageIgnore
     *
     * Removes a file basing on the path.
     *
     * @param string $path The path to the file to remove.
     */
    public function remove(string $path) : void
    {
        $path = preg_replace('/\/+/', '/', sprintf(
            '%s/%s%s',
            $this->publicDir,
            $this->getPathForFile(),
            $path
        ));

        if ($this->fs->exists($path) && is_file($path)) {
            $this->fs->remove($path);
        }
    }

    /**
     * Returns the path where file should be saved basing on the current
     * instance and the type of the content the file is linked to.
     *
     * @return string The path where file should be saved to.
     */
    abstract protected function getPathForFile();
}
