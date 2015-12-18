<?php
/**
 * This file is part of the Onm package.
 *
 * (c) OpenHost S.L. <onm-devs@openhost.es>
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
    $bag = getService('core.service.assetic.asset_bag');

    if ($repeat) { // Opening tag (first call only)
        $filters = [];

        if (!empty($params['filters'])) {
            $filters = explode(',', preg_replace('/\s+/', '', $params['filters']));
        }

        if (!empty($params['src'])) {
            foreach (explode(',', $params['src']) as $src) {
                $bag->addScript(trim($src), $filters);
            }
        }
    }

    if (!empty($content)) {
        $bag->addLiteralScript($content);
    }
}
