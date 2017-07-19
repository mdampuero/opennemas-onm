<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Data\Adapter;

use Common\Data\Core\Adapter;

/**
 * The `GoogleAnalyticsAdapter` adapts a single-account Google Analytics
 * configuration to a multi-account Google Analytics configuration.
 */
class GoogleAnalyticsAdapter extends Adapter
{
    /**
     * {@inheritdoc}
     */
    public function adapt($item, $params = [])
    {
        if (!is_array($item)) {
            return [];
        }

        if (array_key_exists('api_key', $item)) {
            $item = [ $item ];
        }

        return $item;
    }
}
