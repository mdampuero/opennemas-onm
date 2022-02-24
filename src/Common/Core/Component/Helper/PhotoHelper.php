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
     * The theme of the instance.
     *
     * @var Theme
     */
    protected $theme;

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
     * @param Theme              $theme         The theme of the instance.
     * @param UrlGeneratorHelper $ugh           The url generator helper.
     */
    public function __construct($contentHelper, $instance, $router, $theme, $ugh)
    {
        $this->contentHelper = $contentHelper;
        $this->instance      = $instance;
        $this->router        = $router;
        $this->theme         = $theme;
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

        // Added for external contents.
        if (!empty($item->externalPath) && preg_match('/^https?.*/', $item->externalPath)) {
            return $item->externalPath;
        }

        $item = $this->contentHelper->getContent($item, 'Photo');

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
        $value = $this->contentHelper->getProperty(
            $this->contentHelper->getContent($item, 'Photo'),
            'size'
        );

        return !empty($value) ? $value : null;
    }

    /**
     * Returns the srcset of the provided photo path.
     *
     * @param integer $width The width of the transformation or null.
     *
     * @return string The srcset to show.
     */
    public function getPhotoSizes($device = 'desktop')
    {
        $sizes = '';
        $cuts  = $this->theme->getCuts($device);

        if (empty($cuts)) {
            return '';
        }

        $widths = array_map(function ($item) {
            return $item['width'];
        }, $cuts);

        $last = array_pop($widths);

        foreach ($widths as $width) {
            $sizes .= sprintf('(max-width: %dpx) %dpx, ', $width, $width);
        }

        return $sizes . sprintf('%dpx', $last);
    }

    public function getSrcSetAndSizesFromImagePath($imagePath, $width)
    {
        $srcSets       = [];
        $availableCuts = [];
        $cuts          = $this->theme->getCuts();

        foreach ($cuts as $device => $cut) {
            $availableCuts[$device] = $cut;

            $srcSets[] = $this->router->generate('asset_image', [
                'params' => implode(
                    ',',
                    array_merge([ 'thumbnail' ], [ $cut['width'], $cut['height'], 'center', 'center' ])
                ),
                'path'   => $imagePath
            ], false) . ' ' . $cut['width'] . 'w';

            if ($cut['width'] >= $width) {
                break;
            }
        }

        $last = array_slice($availableCuts, -1, 1, true);

        return [ 'srcset' => implode(',', $srcSets), 'sizes' => $this->getPhotoSizes(key($last)) ];
    }

    /**
     * Returns the srcset of the provided photo path.
     *
     * @param string  $photo     The photo path.
     * @param string  $transform The name of the transformation to apply.
     * @param string  $device    The device type.
     *
     * @return string The srcset to show.
     */
    public function getPhotoSrcSet($photo, $transform = 'thumbnail', $device = 'desktop')
    {
        $srcSet = '';
        $cuts   = $this->theme->getCuts($device);

        if (empty($cuts) || empty($photo)) {
            return '';
        }

        $last = array_pop($cuts);

        foreach ($cuts as $key => $value) {
            $srcSet .= $this->getPhotoPath(
                $photo,
                $transform,
                [ $value['width'], $value['height'], 'center', 'center' ]
            ) . ' ' . $value['width'] . 'w, ';
        }

        return $srcSet . $this->getPhotoPath(
            $photo,
            $transform,
            [$last['width'], $last['height'], 'center', 'center']
        ) . ' ' . $last['width'] . 'w';
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
        $value = $this->contentHelper->getProperty(
            $this->contentHelper->getContent($item, 'Photo'),
            'width'
        );

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
        $value = $this->contentHelper->getProperty(
            $this->contentHelper->getContent($item, 'Photo'),
            'height'
        );

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
        $path = $this->getPhotoPath($this->contentHelper->getContent($item, 'Photo'));

        if (!preg_match('/^http?.*/', $path)) {
            $path = $this->instance->getBaseUrl() . $path;
        }

        $value = MimeTypeTool::getMimeType($path);

        return !empty($value) ? $value : null;
    }

    /**
     * Returns true if the item has photo path.
     *
     * @param mixed $item The photo to get path from.
     *
     * @return boolean True if the photo has path. False otherwise.
     */
    public function hasPhotoPath($item = null)
    {
        return !empty($this->getPhotoPath($item));
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

    /**
     * Returns if the provided data has sizes for the specific device type.
     *
     * @param string $device The device to get the sizes for.
     *
     * @return bool Wether the photo has sizes or not.
     */
    public function hasPhotoSizes($device = 'desktop')
    {
        return !empty($this->getPhotoSizes($device));
    }

    /**
     * Returns if the provided photo has srcset for the specific device type.
     *
     * @param string  $photo     The photo to get the srcset from.
     * @param string  $transform The name of the transformation to apply.
     * @param string  $device    The device to get the srcset for.
     *
     * @return bool Wether the photo has srcset or not.
     */
    public function hasPhotoSrcSet($photo, $transform, $device = 'desktop')
    {
        return !empty($this->getPhotoSrcSet($photo, $transform, $device));
    }
}
