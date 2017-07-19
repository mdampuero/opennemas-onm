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
 * The `LogoEnabledAdapter` adapts the flag to enable/disable site logo to use
 * the new setting name instead of deprecated setting name.
 */
class LogoEnabledAdapter extends Adapter
{
    /**
     * {@inheritdoc}
     */
    public function adapt($item, $params = [])
    {
        if (!is_null($item)) {
            return (int) $item;
        }

        $settings = $this->container->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('section_settings');

        if (!empty($settings) && array_key_exists('allowLogo', $settings)) {
            return (int) $settings['allowLogo'];
        }

        return 0;
    }
}
