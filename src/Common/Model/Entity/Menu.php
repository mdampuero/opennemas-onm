<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Model\Entity;

use Opennemas\Orm\Core\Entity;

class Menu extends Entity
{
    /**
     * The list of common l10n supported keys.
     *
     * @var array
     */
    protected static $l10nKeys = [
        'link_name', 'title', 'menu_items'
    ];

    /**
     * Gets the value of the property from the raw data array.
     *
     * @param string $name The property name.
     *
     * @return mixed The property value.
     */
    public function &__get($name)
    {
        switch ($name) {
            case 'id':
                $value = $this->pk_menu;
                break;
            default:
                $value = parent::__get($name);
                break;
        }

        return $value;
    }

     /**
     * Returns the list of l10n keys.
     *
     * @return array The list of l10n keys.
     */
    public function getL10nKeys()
    {
        return static::$l10nKeys;
    }
}
