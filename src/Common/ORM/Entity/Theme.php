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
    /**
     * Returns the list of available styles for the Theme.
     * If no available styles it returns an empty array.
     *
     * @return array|null the list of available styles
     **/
    public function getSkins()
    {
        if (array_key_exists('parameters', $this->data)
            && array_key_exists('skins', $this->data['parameters'])
        ) {
            foreach ($this->data['parameters']['skins'] as $key => &$value) {
                $value['internal_name'] = $key;
            }

            return $this->data['parameters']['skins'];
        }

        return [];
    }

    /**
     * Returns the default style for the Theme.
     * If no default style it returns the first one.
     * If no styles it returns null.
     *
     * @return array|null the defeault style
     **/
    public function getDefaultSkin()
    {
        $skins = $this->getSkins();

        if (empty($skins)) {
            return null;
        }

        $default = null;
        foreach ($skins as $key => &$value) {
            if (array_key_exists('default', $value)
                && $value['default'] == true
            ) {
                return $value;
            }
        }


        // If no default style just pick the first one
        if (empty($default)) {
            return array_shift($skins);
        }
    }

    /**
     * Returns the style information given its name.
     * If the given name is not valid it returns the default style.
     * If no styles it returns null.
     *
     * @param string $name the style name
     *
     * @return array|null the defeault style
     **/
    public function getCurrentSkin($name)
    {
        $skins = $this->getSkins();

        if (empty($skins)) {
            return null;
        }

        // Return the default style if the name doesnt exists
        if (!array_key_exists($name, $skins)) {
            return $this->getDefaultSkin();
        }

        $skins['internal_name'] = $name;

        return $skins[$name];
    }

    /**
     * undocumented function
     *
     * @return void
     * @author
     **/
    public function getCurrentSkinName($internalName)
    {
        $skin = $this->getCurrentSkin($internalName);

        if (!is_array($skin)
            || !array_key_exists('internal_name', $skin)
        ) {
            return null;
        }

        return $skin['internal_name'];
    }

    /**
     * undocumented function
     *
     * @return void
     * @author
     **/
    public function getCurrentSkinProperty($internalName, $property)
    {
        $skin = $this->getCurrentSkin($internalName);

        if (!is_array($skin)
            || !array_key_exists('params', $skin)
            || !is_array($skin['params'])
            || !array_key_exists($property, $skin['params'])
        ) {
            return null;
        }

        return $skin['params'][$property];
    }
}
