<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Frontend\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Common\Core\Controller\Controller;
use Onm\Settings as s;
use Imagine\Image\ImageInterface;

/**
 * Handles the actions for assets.
 */
class AssetController extends Controller
{
    /**
     * Description of the action.
     *
     * @return Response The response object.
     */
    public function imageAction(Request $request)
    {
        $parameters = $request->query->get('parameters');
        $parameters = explode(',', urldecode($parameters));
        $path       = realpath(SITE_PATH.'/'.$request->query->get('real_path'));
        $method     = array_shift($parameters);

        if (file_exists($path) && is_file($path)) {
            $imagine = new \Imagine\Imagick\Imagine();
            $image   = $imagine->open($path);

            $imageFormat = strtolower($image->getImagick()->getImageFormat());
            $imageSize   = $image->getSize();
            $imageWidth  = $imageSize->getWidth();
            $imageHeight = $imageSize->getHeight();

            if ($imageFormat == 'gif') {
                return new Response(
                    file_get_contents($path),
                    200,
                    array('Content-Type' => $imageFormat)
                );
            }

            $image->strip();

            switch ($method) {
                case 'crop':
                    $topX = $parameters[0];
                    $topY = $parameters[1];

                    $width  = $parameters[2];
                    $height = $parameters[3];

                    $image->crop(
                        new \Imagine\Image\Point($topX, $topY),
                        new \Imagine\Image\Box($width, $height)
                    );
                    break;
                case 'thumbnail':
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
                    break;
                case 'zoomcrop':
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
                    break;
                case 'clean':
                    # do nothing
                    break;

                default:
                    $width  = $parameters[0];
                    $height = $parameters[1];

                    $image->resize(new \Imagine\Image\Box($width, $height));
                    break;
            }

            $contents = $image->get(
                $imageFormat,
                array(
                    'resolution-units' => \Imagine\Image\ImageInterface::RESOLUTION_PIXELSPERINCH,
                    'resolution-x'     => 72,
                    'resolution-y'     => 72,
                    'quality'          => 85,
                )
            );

            return new Response($contents, 200, array('Content-Type' => $imageFormat));
        } else {
            return new Response('', 404);
        }
    }

    /**
     * Generates custom css for frontpages elements
     *
     * @param Request $request The request object
     *
     * @return Response the response object
     **/
    public function customCssFrontpageAction(Request $request)
    {
        $categoryName = $request->query->filter('category', 'home', FILTER_SANITIZE_STRING);

        $ccm                = \ContentCategoryManager::get_instance();
        $currentCategoryId  = $ccm->get_id($categoryName);

        $cm                 = new \ContentManager;
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
            $properties = \ContentManager::getMultipleProperties($properties);

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

        $response = '';

        // render
        if (count($contentsInHomepage) > 0) {
            $response .= "/**********************************************************\n"
                      ."   CSS for contents in frontpage of category $categoryName\n"
                      ." **********************************************************/\n";

            $response .= "@media(min-width:768px) {\n";
            foreach ($contentsInHomepage as $item) {
                // Background color
                if (!empty($item->bgcolor)) {
                    $response .= "#content-{$item->pk_content}.onm-new { "
                            ."background-color:{$item->bgcolor} !important; }\n";

                    $response .= "#content-{$item->pk_content}.colorize { "
                            ."padding:10px !important; border-radius:5px; }\n";
                }
                if (!empty($item->title_props)) {
                    $response .= "#content-{$item->pk_content} .custom-text, "
                                ."#content-{$item->pk_content} .title a, "
                                ."#content-{$item->pk_content} .nw-title a { ";

                    foreach ($item->title_props as $property => $value) {
                        if (!empty($value)) {
                            $response .= "{$property}:{$value}!important;";
                        }
                    }

                    $response .= "}\n";
                }
            }
            $response .= "}\n\n";
        }

        return new Response(
            $response,
            200,
            array(
                // 'Expire'       => new \DateTime("+5 min"),
                'Content-Type' => 'text/css',
                'x-instance'   => $this->get('core.instance')->internal_name,
                'x-tags'       => 'instance-'.$this->get('core.instance')->internal_name.',frontpagecss',
            )
        );
    }

    /**
     * Retrieves the styleSheet rules for the frontpage
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function globalCssAction(Request $request)
    {
        $this->view->setConfig('frontpages');

        $cacheID = 'css|global';

        if ($this->view->getCaching() === 0
            || !$this->view->isCached('base/custom_css.tpl', $cacheID)
        ) {
            $ccm = \ContentCategoryManager::get_instance();

            // RenderColorMenu
            $siteColor = '#005689';
            $configColor = s::get('site_color');
            if (!empty($configColor)) {
                if (!preg_match('@^#@', $configColor)) {
                    $siteColor = '#'.$configColor;
                } else {
                    $siteColor = $configColor;
                }
            }

            $selectedCategories = array();
            if (is_array($ccm->categories) && !empty($ccm->categories)) {
                foreach ($ccm->categories as &$category) {
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

                    $selectedCategories []= $category;
                }
            }

            $this->view->assign([
                'categories' => $selectedCategories,
                'site_color' => $siteColor,
            ]);
        }

        $coreCss   = $this->get('core.template.admin')
            ->fetch('css/global.tpl');
        $customCss = $this->renderView('base/custom_css.tpl');

        return new Response($coreCss . ' ' . $customCss, 200, [
            'Content-Type' => 'text/css',
            'x-instance'   => $this->get('core.instance')->internal_name,
            'x-tags'       => 'instance-'.$this->get('core.instance')->internal_name.',customcss',
        ]);
    }

    /**
     * Redirect requests to apple-touch-icon
     *
     * @param Request $request the request object
     *
     * @return Response The response object
     **/
    public function favicoAction(Request $request)
    {
        // Default favico
        $favicoUrl = '/assets/images/favicon.png';

        // Check if favico is defined on site
        $favicoFileName  = getService('setting_repository')->get('favico');
        $sectionSettings = getService('setting_repository')->get('section_settings');

        $allowLogo = false;
        if (is_array($sectionSettings) && array_key_exists('allowLogo', $sectionSettings)) {
            $allowLogo = $sectionSettings['allowLogo'];
        }

        if ($allowLogo && $favicoFileName) {
            $favicoUrl = MEDIA_URL . MEDIA_DIR . '/sections/' . rawurlencode($favicoFileName);
        }

        $favicoUrl =  realpath(SITE_PATH.'/'.$favicoUrl);

        return new Response(
            file_get_contents($favicoUrl),
            200,
            [ 'Content-Type' => 'image/' . pathinfo($favicoFileName, PATHINFO_EXTENSION) ]
        );
    }
}
