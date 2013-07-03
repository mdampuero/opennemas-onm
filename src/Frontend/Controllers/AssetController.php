<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Frontend\Controllers;

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
     * Common code for all the actions
     *
     * @return void
     **/
    public function init()
    {
        $this->view = new \TemplateAdmin(TEMPLATE_ADMIN);
    }

    /**
     * Description of the action
     *
     * @return Response the response object
     **/
    public function imageAction(Request $request)
    {
        $parameters = $request->query->get('parameters');
        $path       = realpath(SITE_PATH.'/'.$request->query->get('real_path'));

        $finalParameters = array();
        $parameters      = explode(',', urldecode($parameters));

        $method = array_shift($parameters);

        // $hash = substr(md5($parameters.$path), 0, 2);

        // var_dump($hash);die();

        // if ($finalParameters['hash'] !== $hash) {
        //     die('Me cago en tu puta madre hickajer de los webos');
        // }

        if (file_exists($path)) {
            $imagine = new \Imagine\Imagick\Imagine();

            $image = $imagine->open($path);

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
            } else {

                $width  = $parameters[0];
                $height = $parameters[1];

                $image->resize(new \Imagine\Image\Box($width, $height));
            }

            $originalFormat = strtolower($image->getImagick()->getImageFormat());

            $blob = $image->show(
                $originalFormat,
                array(
                    'resolution-units' => \Imagine\Image\ImageInterface::RESOLUTION_PIXELSPERINCH,
                    'resolution-x'     => 300,
                    'resolution-y'     => 300,
                    'quality'          => 85,
                )
            );

            die();

        } else {
            return new Response('', 404);
        }
        // var_dump($finalParameters, $path);die();
    }
}
