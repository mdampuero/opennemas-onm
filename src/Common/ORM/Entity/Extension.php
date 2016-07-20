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
 * The Extension class represents extensions to extend the features of the
 * Opennemas core.
 */
class Extension extends Entity
{
    /**
     * Returns the extension price.
     *
     * @param string $type The price type.
     *
     * @return float The extension price.
     */
    public function getPrice($type = 'monthly')
    {
        if (empty($this->price)) {
            return 0;
        }

        $prices = array_filter($this->price, function ($a) use ($type) {
            return $a['type'] === $type;
        });

        if (count($prices)) {
            return (float) $prices[0]['value'];
        }

        return (float) $this->price[0]['value'];
    }
}
