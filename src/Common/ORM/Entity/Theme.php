<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\ORM\Entity;

use Common\ORM\Core\Entity;

/**
 * The Theme class represents themes that change the Opennemas templates.
 */
class Theme extends Extension
{
    public function getStyles()
    {
        if (array_key_exists('styles', $this->data['parameters'])) {
            return $this->data['parameters']['styles'];
        }
    }

    public function getDefaultStyle()
    {
        if (!array_key_exists('styles', $this->data['parameters'])) {
            return null;
        }

        $default = array_filter($this->data['parameters']['styles'], function ($el) {
            return (array_key_exists('default', $el) && $el['default'] == true);
        });

        return array_pop($default);
    }
}
