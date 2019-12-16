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
use Symfony\Component\HttpFoundation\File\File;

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
     * Returns the path where the file should be moved.
     *
     * @param File     $file The file to generate path to.
     * @param DateTime $date The date to generate the path from.
     *
     * @return string The path where the file should be moved.
     */
    public function generatePath(File $file, \DateTime $date) : string
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
     * {@inheritdoc}
     */
    protected function getPathForFile()
    {
        return $this->loader->getInstance()->getImagesShortPath();
    }
}
