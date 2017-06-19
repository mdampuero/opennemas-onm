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
 * The `LocaleAdapter` adapts deprecated and separated values for languages and
 * timezone and merges them into a single locale variable.
 */
class LocaleAdapter extends Adapter
{
    /**
     * {@inheritdoc}
     */
    public function adapt($item, $params = [])
    {
        if (!is_null($item)
            && is_array($item)
            && array_key_exists('backend', $item)
            && array_key_exists('timezone', $item)
        ) {
            return $item;
        }

        if (!is_array($item)) {
            $item = [];
        }

        $settings = $this->container->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get([ 'site_language', 'time_zone' ], [ 'en', 'UTC' ]);

        if (!array_key_exists('backend', $item)) {
            $item['backend'] = $settings['site_language'];
        }

        if (!array_key_exists('timezone', $item)) {
            $item['timezone'] = $settings['time_zone'];

            if (is_numeric($settings['time_zone'])) {
                $timezones        = \DateTimeZone::listIdentifiers();
                $item['timezone'] = $timezones[(int) $settings['time_zone']];
            }
        }

        return $item;
    }
}
