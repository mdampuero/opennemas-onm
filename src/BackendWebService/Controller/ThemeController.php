<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <onm-devs@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace BackendWebService\Controller;

use Onm\Framework\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ThemeController extends Controller
{
    /**
     * Returns the list of themes.
     *
     * @return JsonResponse The response object.
     */
    public function listAction()
    {
        $themes = [
            [
                'id'                => 'theme1',
                'name'              => 'Theme 1',
                'description'       => '<p>Long description for theme 1</p><p>Nunc. Mauris consequat, enim vitae venenatis sollicitudin, dolor orci bibendum enim, a sagittis nulla nunc quis elit. Phasellus augue. Nunc suscipit.</p><ul><li>Feature 1</li><li>Feature 2</li><li>Feature 3</li></ul><p>Nascetur ridiculus mus.</p><p>Aenean risus dui, volutpat non, posuere vitae, sollicitudin in, urna. Nam eget eros a enim pulvinar rhoncus. Cum sociis.</p>',
                'type'              => 'free',
                'screenshots'       => [ 1, 2, 3 ],
                'short_description' => 'Short description for theme 1',
            ],

            [
                'id'                => 'theme2',
                'name'              => 'Theme 2',
                'description'       => '<p>Long description for theme 2</p><p>Nunc. Mauris consequat, enim vitae venenatis sollicitudin, dolor orci bibendum enim, a sagittis nulla nunc quis elit. Phasellus augue. Nunc suscipit.</p><ul><li>Feature 1</li><li>Feature 2</li><li>Feature 3</li></ul><p>Nascetur ridiculus mus.</p><p>Aenean risus dui, volutpat non, posuere vitae, sollicitudin in, urna. Nam eget eros a enim pulvinar rhoncus. Cum sociis.</p>',
                'type'              => 'free',
                'screenshots'       => [ 1, 2, 3 ],
                'short_description' => 'Short description for theme 2',
            ],
            [
                'id'                => 'theme3',
                'name'              => 'Theme 3',
                'description'       => '<p>Long description for theme 3</p><p>Nunc. Mauris consequat, enim vitae venenatis sollicitudin, dolor orci bibendum enim, a sagittis nulla nunc quis elit. Phasellus augue. Nunc suscipit.</p><ul><li>Feature 1</li><li>Feature 2</li><li>Feature 3</li></ul><p>Nascetur ridiculus mus.</p><p>Aenean risus dui, volutpat non, posuere vitae, sollicitudin in, urna. Nam eget eros a enim pulvinar rhoncus. Cum sociis.</p>',
                'type'              => 'free',
                'screenshots'       => [ 1, 2, 3 ],
                'short_description' => 'Short description for theme 3',
            ]
        ];

        $exclusive = \Onm\Module\ModuleManager::getAvailableThemes();
        array_shift($exclusive);

        $myThemes  = [ 'theme1', 'theme2' ];
        $enabled   = [ 'theme1' ];

        return new JsonResponse(
            [
                'themes'    => $themes,
                'enabled'   => $enabled,
                'exclusive' => $exclusive,
                'myThemes'  => $myThemes
            ]
        );
    }
}
