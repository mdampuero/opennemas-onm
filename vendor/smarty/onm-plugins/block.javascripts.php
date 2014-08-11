<?php

/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Process the JS files in the src attribute with Assetic.
 *
 * @param  array                    $params   Array of parameters.
 * @param  string                   $content  Current HTML to return.
 * @param  Smarty_Internal_Template $template Current template
 * @param  boolean                  $repeat   Current extension call number.
 *
 * @return string Result HTML.
 */
function smarty_block_javascripts($params, $content, $template, &$repeat)
{
    // In debug mode, we have to be able to loop a certain number of times, so we use a static counter
    static $count;
    static $assetsUrls;

    $am = getService('javascript_manager');
    $am->initConfiguration($params);

    $config = $am->getConfiguration();

    if ($repeat) { // Opening tag (first call only)
        $filters = array();

        if (array_key_exists('filters', $params)) {
            foreach (explode(',', $params['filters']) as $filter) {
                $filters[] = trim($filter);
            }
        }

        $srcs = array();
        foreach (explode(',', $params['src']) as $src) {
            $srcs[] = trim($src);
        }

        $am->initFilters($filters);
        $am->initAssets($srcs);
        $am->initFactory();

        $assetsUrls = array_reverse($am->writeAssets());
        $count      = count($assetsUrls);

        $template->assign($config['asset_url'], $assetsUrls[$count - 1]);
    } else { // Closing tag
        if (isset($content) && $am->debug()) {

            $count--;

            if ($count > 0) {
                $template->assign($config['asset_url'], $assetsUrls[$count - 1]);
            }

            $repeat = $count > 0;

        }

        return $content;
    }
}
