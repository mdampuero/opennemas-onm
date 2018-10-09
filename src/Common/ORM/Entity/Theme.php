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
            $default = array_shift($skins);
        }

        return $default;
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

        return $skins[$name];
    }

    /**
     * Returns the name of the currently sking given its name
     *
     * @param string $internalName the internal name of the skin
     *
     * @return string|null the skin name
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
     * Returns a property value defined for the current skin given the property name
     *
     * @param string $internalName the internal name of the skin to select
     * @param string $propertyName the property name of the skin to select
     *
     * @return string|null the property value
     * @author
     **/
    public function getCurrentSkinProperty($internalName, $propertyName)
    {
        $skin = $this->getCurrentSkin($internalName);

        if (!is_array($skin)
            || !array_key_exists('params', $skin)
            || !is_array($skin['params'])
            || !array_key_exists($propertyName, $skin['params'])
        ) {
            return null;
        }

        return $skin['params'][$propertyName];
    }
}
