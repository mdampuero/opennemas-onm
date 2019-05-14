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

use Common\Core\Component\Image\Processor;
use Common\ORM\Entity\Instance;
use Framework\Component\MIME\MimeTypeTool;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Filesystem\Filesystem;

class ImageHelper
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
     * The image processor service.
     *
     * @var Processor
     */
    protected $processor;

    /**
     * The server public directory.
     *
     * @var string
     */
    protected $publicDir;

    /**
     * Initalializes the ImageHelper.
     *
     * @param Instance  $instance  The current instance.
     * @param Processor $processor The image processor service.
     * @param string    $publicDir The server public directory.
     */
    public function __construct(Instance $instance, Processor $processor, string $publicDir)
    {
        $this->fs        = new Filesystem();
        $this->instance  = $instance;
        $this->processor = $processor;
        $this->publicDir = $publicDir;
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
            '%s/%s/%s/%s%s.%s',
            $this->publicDir,
            $this->instance->getImagesShortPath(),
            $date->format('Y/m/d'),
            $date->format('YmdHis'),
            substr(gettimeofday()['usec'], 0, 5),
            $this->getExtension($file)
        ));
    }

    /**
     * Returns the description for the image.
     *
     * @return string The image description.
     */
    public function getDescription() : string
    {
        return $this->processor->getDescription();
    }

    /**
     * @codeCoverageIgnore
     *
     * Returns the extension for a file.
     *
     * @param File $file The file to return extension for.
     *
     * @return string The file extension.
     */
    public function getExtension(File $file) : string
    {
        return MimeTypeTool::getExtension($file);
    }

    /**
     * Returns the imformation for the image in the provided path.
     *
     * @param string $path The path to the image.
     *
     * @return array The image information.
     */
    public function getInformation(string $path) : array
    {
        $this->processor->open($path);

        return [
            'height' => $this->processor->getHeight(),
            'size'   => $this->processor->getSize() / 1024,
            'width'  => $this->processor->getWidth()
        ];
    }

    /**
     * Checks if the file is optimizable basing on the file path.
     *
     * @param string $path The file path.
     *
     * @return bool True if the file is optimizable. False otherwise.
     */
    public function isOptimizable(string $path) : bool
    {
        $file = new \SplFileInfo($path);

        return $file->getExtension() !== 'swf';
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
     * Optimizes the file in the provided path.
     *
     * @param string $path The path to the file to optimize.
     */
    public function optimize(string $path) : void
    {
        $this->processor->open($path)->optimize()->save($path);
    }
}
