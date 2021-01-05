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
use Common\Core\Component\Loader\InstanceLoader;
use Framework\Component\MIME\MimeTypeTool;
use Symfony\Component\Filesystem\Filesystem;

class ImageHelper extends FileHelper
{
    /**
     * The image processor service.
     *
     * @var Processor
     */
    protected $processor;

    /**
     * Initalializes the ImageHelper.
     *
     * @param InstanceLoader $loader    The InstanceLoader service.
     * @param string         $publicDir The server public directory.
     * @param Processor      $processor The image processor service.
     */
    public function __construct(InstanceLoader $loader, string $publicDir, Processor $processor)
    {
        $this->fs        = new Filesystem();
        $this->loader    = $loader;
        $this->processor = $processor;
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
     * @param \SplFileInfo     $file The file to generate path to.
     * @param DateTime $date The date to generate the path from.
     *
     * @return string The path where the file should be moved.
     */
    public function generatePath(\SplFileInfo $file, \DateTime $date) : string
    {
        return preg_replace('/\/+/', '/', sprintf(
            '%s/%s/%s/%s%s.%s',
            $this->publicDir,
            $this->getPathForFile(),
            $date->format('Y/m/d'),
            $date->format('YmdHis'),
            substr(gettimeofday()['usec'], 0, 5),
            $this->getExtension($file)
        ));
    }

    /**
     * @codeCoverageIgnore
     *
     * Returns the extension for a file.
     *
     * @param \SplFileInfo $file The file to return extension for.
     *
     * @return string The file extension.
     */
    public function getExtension(\SplFileInfo $file) : string
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

        $information = [
            'height' => $this->processor->getHeight(),
            'size'   => $this->processor->getSize() / 1024,
            'width'  => $this->processor->getWidth()
        ];

        $description = $this->processor->getDescription();
        if (!empty($description)) {
            $information['description'] = $description;
        }

        return $information;
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
     * Optimizes the file in the provided path.
     *
     * @param string $path The path to the file to optimize.
     */
    public function optimize(string $path) : void
    {
        $this->processor->open($path)->optimize()->save($path);
    }

    /**
     * @codeCoverageIgnore
     *
     * Removes an image basing on the path.
     *
     * @param string $path The path to the image to remove.
     */
    public function remove(string $path) : void
    {
        $path = preg_replace('/\/+/', '/', sprintf(
            '%s/%s/%s',
            $this->publicDir,
            $this->loader->getInstance()->getMediaShortPath(),
            $path
        ));

        if ($this->fs->exists($path) && is_file($path)) {
            $this->fs->remove($path);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function getPathForFile()
    {
        return $this->loader->getInstance()->getImagesShortPath();
    }
}
