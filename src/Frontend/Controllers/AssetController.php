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

use Symfony\Component\HttpFoundation\Request;
use Onm\Framework\Controller\Controller;
use Onm\Message as m;
use Onm\Settings as s;

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

        $finalParameters = array();
        $parameters = explode('x', $parameters);
        foreach ($parameters as &$parameter) {
            $pars = explode('-', $parameter);
            $finalParameters[$pars[0]] = $pars[1];
        }

        $path = realpath(SITE_PATH.'/'.$request->query->get('real_path') .'.'.$request->query->get('_format'));
        $hash = substr(md5($parameters.$path), 0, 2);

// var_dump($hash);die();

//         if ($finalParameters['hash'] !== $hash) {
//             die('Me cago en tu puta madre hickajer de los webos');
//         }


        if (file_exists($path)) {
            $imagine = new \Imagine\Imagick\Imagine();

            $image = $imagine->open($path);

            $imageSize = $image->getSize();

            $topX = $imageSize->getWidth() / 2 - $finalParameters['w']/2;
            $topY = $imageSize->getHeight() / 2 - $finalParameters['h']/2;

            if (array_key_exists('crop', $finalParameters)
                && $finalParameters['crop'] == 1
            ) {
                $image->crop(
                    new \Imagine\Image\Point($topX, $topY),
                    new \Imagine\Image\Box($finalParameters['w'], $finalParameters['h'])
                );
            } elseif (array_key_exists('thumb', $finalParameters)) {
                $image->thumbnail(
                    new \Imagine\Image\Box($finalParameters['w'], $finalParameters['h'])
                );
            } else {
                $image
                    ->resize(new \Imagine\Image\Box($finalParameters['w'], $finalParameters['h']));
            }

            $blob = $image->show(
                'jpg',
                array(
                    'resolution-units' => \Imagine\Image\ImageInterface::RESOLUTION_PIXELSPERINCH,
                    'resolution-x'     => 300,
                    'resolution-y'     => 300,
                    'quality' => 100,
                )
            );

            die();

        }
        var_dump($finalParameters, $path);die();
    }
}
