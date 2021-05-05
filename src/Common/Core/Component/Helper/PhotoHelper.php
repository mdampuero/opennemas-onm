<?php

namespace Common\Core\Component\Helper;

use Common\Model\Entity\Instance;
use Framework\Component\MIME\MimeTypeTool;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Router;

/**
 * Helper class to retrieve photo data.
 */
class PhotoHelper
{
    /**
     * The content helper.
     *
     * @var ContentHelper
     */
    protected $contentHelper;

    /**
     * The instance.
     *
     * @var Instance
     */
    protected $instance;

    /**
     * The router component.
     *
     * @var Router
     */
    protected $router;

    /**
     * The url generator helper.
     *
     * @var UrlGeneratorHelper
     */
    protected $ugh;

    /**
     * Initializes the photo helper
     *
     * @param ContentHelper      $contentHelper The content helper.
     * @param Instance           $instance      The current instance.
     * @param Router             $router        The router component.
     * @param UrlGeneratorHelper $ugh           The url generator helper.
     */
    public function __construct($contentHelper, $instance, $router, $ugh)
    {
        $this->contentHelper = $contentHelper;
        $this->instance      = $instance;
        $this->router        = $router;
        $this->ugh           = $ugh;
    }

    /**
     * Returns the URL for a photo.
     *
     * @param Content $item      The photo to generate path for.
     * @param string  $transform The transform to apply.
     * @param array   $params    The list of parameters for the transform.
     * @param bool    $absolute  Wheter to generate an absolute URL.
     *
     * @return string The URL for the image.
     */
    public function getPhotoPath($item, string $transform = null, array $params = [], $absolute = false)
    {
        if (is_string($item) || empty($item)) {
            return $item;
        }

        $item = $this->contentHelper->getContent($item);

        if (empty($item)) {
            return null;
        }

        $url = $this->ugh->generate($item);

        // Do not transform if empty or external photo
        if (empty($transform) || preg_match('/^https?.*/', $url)) {
            if (!preg_match('/^https?.*/', $url) && $absolute) {
                $url = $this->ugh->generate($item, [ 'absolute' => true ]);
            }

            return $url;
        }

        $absolute = $absolute
            ? UrlGeneratorInterface::ABSOLUTE_URL
            : UrlGeneratorInterface::ABSOLUTE_PATH;

        return $this->router->generate('asset_image', [
            'params' => implode(',', array_merge([ $transform ], $params)),
            'path'   => $url
        ], $absolute);
    }

    /**
     * Returns the size for the provided photo.
     *
     * @param Content $item The photo to get property from.
     *
     * @return string The photo size.
     */
    public function getPhotoSize($item = null) : ?string
    {
        $value = $this->contentHelper->getProperty($this->contentHelper->getContent($item), 'size');

        return !empty($value) ? $value : null;
    }

    /**
     * Returns the width for the provided photo.
     *
     * @param Content $item The photo to get property from.
     *
     * @return string The photo width.
     */
    public function getPhotoWidth($item = null) : ?string
    {
        $value = $this->contentHelper->getProperty($this->contentHelper->getContent($item), 'width');

        return !empty($value) ? $value : null;
    }

    /**
     * Returns the height for the provided photo.
     *
     * @param Content $item The photo to get property from.
     *
     * @return string The photo height.
     */
    public function getPhotoHeight($item = null) : ?string
    {
        $value = $this->contentHelper->getProperty($this->contentHelper->getContent($item), 'height');

        return !empty($value) ? $value : null;
    }

    /**
     * Returns the height for the provided photo.
     *
     * @param Content $item The photo to get property from.
     *
     * @return string The photo height.
     */
    public function getPhotoMimeType($item = null) : ?string
    {
        $path = $this->getPhotoPath($this->contentHelper->getContent($item));

        if (!preg_match('/^http?.*/', $path)) {
            $path = $this->instance->getBaseUrl() . $path;
        }

        $value = MimeTypeTool::getMimeType($path);

        return !empty($value) ? $value : null;
    }

    /**
     * Returns if the provided photo has size or not.
     *
     * @param mixed $item The photo to get size from.
     *
     * @return boolean True if the photo has size. False otherwise.
     */
    public function hasPhotoSize($item = null)
    {
        return !empty($this->getPhotoSize($item));
    }
}
