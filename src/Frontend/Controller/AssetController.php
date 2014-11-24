<?php
/**
 * Defines the frontend controller for the dynamic assets
 *
 * @package Frontend_Controllers
 **/
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Frontend\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Onm\Framework\Controller\Controller;
use Onm\Message as m;
use Onm\Settings as s;
use Imagine\Image\ImageInterface;

/**
 * Handles the actions for assets
 *
 * @package Backend_Controllers
 **/
class AssetController extends Controller
{
    /**
     * Description of the action
     *
     * @return Response the response object
     **/
    public function imageAction(Request $request)
    {
        $this->view = new \TemplateAdmin(TEMPLATE_ADMIN);
        $parameters = $request->query->get('parameters');
        $path       = realpath(SITE_PATH.'/'.$request->query->get('real_path'));

        $parameters = explode(',', urldecode($parameters));

        $method = array_shift($parameters);

        if (file_exists($path)) {
            $imagine = new \Imagine\Imagick\Imagine();

            $image = $imagine->open($path);
            $image->strip();

            $imageSize   = $image->getSize();
            $imageWidth  = $imageSize->getWidth();
            $imageHeight = $imageSize->getHeight();

            if ($method == 'crop') {
                $topX = $parameters[0];
                $topY = $parameters[1];

                $width  = $parameters[2];
                $height = $parameters[3];

                $image->crop(
                    new \Imagine\Image\Point($topX, $topY),
                    new \Imagine\Image\Box($width, $height)
                );
            } elseif ($method == 'thumbnail') {
                $width  = $parameters[0];
                $height = $parameters[1];

                if (isset($parameters[3]) && $parameters[3] == 'in') {
                    $mode = ImageInterface::THUMBNAIL_INSET;
                } else {
                    $mode = ImageInterface::THUMBNAIL_OUTBOUND;
                }

                $image = $image->thumbnail(
                    new \Imagine\Image\Box($width, $height, $mode)
                );
            } elseif ($method == 'zoomcrop') {
                $width         = $parameters[0];
                $height        = $parameters[1];
                // $verticalPos   = $parameters[2];
                // $horizontalPos = $parameters[3];
                $mode = ImageInterface::THUMBNAIL_OUTBOUND;

                if ($imageWidth >= $imageHeight) {
                    $widthResize = $height*$imageWidth/$imageHeight;
                    $heightResize = $height;
                    $topX = $widthResize/2 - $width/2;
                    $topY = 0;
                } else {
                    $widthResize = $width;
                    $heightResize = $width*$imageHeight/$imageWidth;
                    $topX = 0;
                    $topY = $heightResize/2 - $height/2;
                }
                if ($topX < 0) {
                    $topX = 0;
                }
                if ($topY < 0) {
                    $topY = 0;
                }

                $image = $image->resize(
                    new \Imagine\Image\Box($widthResize, $heightResize, $mode)
                )->crop(
                    new \Imagine\Image\Point($topX, $topY),
                    new \Imagine\Image\Box($width, $height)
                );
            } elseif ($method == 'clean') {
                // do nothing
            } else {
                $width  = $parameters[0];
                $height = $parameters[1];

                $image->resize(new \Imagine\Image\Box($width, $height));
            }

            $originalFormat = strtolower($image->getImagick()->getImageFormat());

            $contents = $image->get(
                $originalFormat,
                array(
                    'resolution-units' => \Imagine\Image\ImageInterface::RESOLUTION_PIXELSPERINCH,
                    'resolution-x'     => 72,
                    'resolution-y'     => 72,
                    'quality'          => 85,
                )
            );

            return new Response($contents, 200, array('Content-Type' => $originalFormat));
        } else {
            return new Response('', 404);
        }
        // var_dump($finalParameters, $path);die();
    }

    /**
     * Retrieves the styleSheet rules for the frontpage
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function customCssAction(Request $request)
    {
        $categoryName = $request->query->filter('category', 'home', FILTER_SANITIZE_STRING);
        $version      = $request->query->filter('cb', time(), FILTER_SANITIZE_STRING);

        $this->view = new \Template(TEMPLATE_USER);
        $this->view->setConfig('frontpages');

        $cacheID = 'custom_css|' . $categoryName.'|'.$version;
        if ($this->view->caching == 0
            || !$this->view->isCached('base/custom_css.tpl', $cacheID)
        ) {
            $cm                 = new \ContentManager;
            $ccm                = \ContentCategoryManager::get_instance();
            $currentCategoryId  = $ccm->get_id($categoryName);
            $contentsInHomepage = $cm->getContentsForHomepageOfCategory($currentCategoryId);
            //content_id | title_catID | serialize(font-family:;font-size:;color:)
            if (is_array($contentsInHomepage)) {
                $bgColor = 'bgcolor_'.$currentCategoryId;
                $titleColor = "title_".$currentCategoryId;

                $properties = [];
                foreach ($contentsInHomepage as &$content) {
                    $properties []= [$content->id, $bgColor];
                    $properties []= [$content->id, $titleColor];
                }
                $properties = \Content::getMultipleProperties($properties);

                foreach ($contentsInHomepage as &$content) {
                    foreach ($properties as $property) {
                        if ($property['fk_content'] == $content->id) {
                            if ($property['meta_name'] == $bgColor) {
                                $content->bgcolor = $property['meta_value'];
                            }
                            if ($property['meta_name'] == $titleColor) {
                                $content->title_props = $property['meta_value'];
                                if (!empty($content->title_props)) {
                                    $content->title_props = json_decode($content->title_props);
                                }
                            }
                        }
                    }
                }
            }

            // RenderColorMenu - ADDED RENDER COLOR MENU
            $currentCategory = (isset($categoryName) ? $categoryName : null);
            $configColor = s::get('site_color');
            $siteColor   = (!empty($configColor) ? '#'.$configColor : '#005689');

            // Styles to print each category's new
            $currentCategoryColor = '';

            $categories = $ccm->categories;

            $selectedCategories = array();
            foreach ($categories as &$category) {
                $commonCategoryNames = [
                    'photo', 'publicidad', 'album', 'opinion',
                    'comment', 'video', 'author', 'portada', 'unknown'
                ];

                if (in_array($category->name, $commonCategoryNames)) {
                    continue;
                }
                if (empty($category->color)) {
                    $category->color = $siteColor;
                } else {
                    if (!preg_match('@^#@', $category->color)) {
                        $category->color = '#'.$category->color;
                    }
                }

                if ($currentCategory == $category->name) {
                    $currentCategoryColor = $category->color;
                }

                $selectedCategories []= $category;
            }

            if ($currentCategory == 'home' || $currentCategory == null) {
                $currentCategoryColor = $siteColor;
            }

            $this->view->assign([
                'contents_frontpage'     => $contentsInHomepage,
                'categories'             => $selectedCategories,
                'current_category'       => $currentCategory,
                'site_color'             => $siteColor,
                'current_category_color' => $currentCategoryColor,
            ]);
        }

        return new Response(
            $this->renderView(
                'base/custom_css.tpl',
                array(
                    'cache_id' => $cacheID
                )
            ),
            200,
            array(
                'Expire'       => new \DateTime("+5 min"),
                'Content-Type' => 'text/css',
            )
        );
    }
}
