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

/**
 * The Theme class represents themes that change the Opennemas templates.
 */
class Theme extends Extension
{
    /**
     * Checks if categories can change the page menu in the current theme.
     *
     * @return bool True if acategories can change the menu. False otherwise.
     */
    public function canCategoriesChangeMenu() : bool
    {
        if (!empty($this->parameters)
            && array_key_exists('categories', $this->parameters)
            && array_key_exists('menu', $this->parameters['categories'])
        ) {
            return $this->parameters['categories']['menu'];
        }

        return false;
    }

    /**
     * Returns the list of avaliable menus on theme.
     *
     * @return array Array of menu positions.
     *
     */
    public function getMenus()
    {
        if (!array_key_exists('parameters', $this->data)
            || !array_key_exists('menus', $this->data['parameters'])) {
            return [];
        }

        return $this->data['parameters']['menus'];
    }

    /**
     * Returns the list of cuts for the theme.
     *
     * @param string $device The device type.
     *
     * @return array The cuts for the theme that matches the restrictions.
     *
     */
    public function getCuts($device = 'desktop')
    {
        if (!array_key_exists('parameters', $this->data)
            || !array_key_exists('cuts', $this->data['parameters'])) {
            return [];
        }

        $targetWidth = $this->data['parameters']['cuts'][$device]['width'];

        return array_filter($this->data['parameters']['cuts'], function ($item) use ($targetWidth) {
            return $item['width'] <= $targetWidth;
        });
    }

    /**
     * Returns the skin information basing on the skin id.
     *
     * @param string $id The skin id.
     *
     * @return array The skin information.
     */
    public function getSkin($id)
    {
        $skins = $this->getSkins();

        if (empty($skins)) {
            return null;
        }

        if (array_key_exists($id, $skins)) {
            return $skins[$id];
        }

        return $this->getDefaultSkin();
    }

    /**
     * Returns the list of available skings for the Theme.
     *
     * @return array The list of available styles.
     */
    public function getSkins()
    {
        if (!array_key_exists('parameters', $this->data)
            || !array_key_exists('skins', $this->data['parameters'])
        ) {
            return [];
        }

        foreach ($this->data['parameters']['skins'] as $key => &$value) {
            $value['internal_name'] = $key;
        }

        return $this->data['parameters']['skins'];
    }

    /**
     * Returns the number of suggested epp of the theme.
     *
     * @return mixed The number of suggested epp or null.
     */
    public function getSuggestedEpp()
    {
        return array_key_exists('suggested', $this->data['parameters'])
            ? $this->data['parameters']['suggested']['epp']
            : null;
    }

    /**
     * Returns a property for a skin.
     *
     * @param string $skin     The skin id.
     * @param string $property The property name.
     *
     * @return mixed The property value.
     */
    public function getSkinProperty($id, $property)
    {
        $skin = $this->getSkin($id);

        if (empty($skin)
            || !array_key_exists('params', $skin)
            || !is_array($skin['params'])
            || !array_key_exists($property, $skin['params'])
        ) {
            return null;
        }

        return $skin['params'][$property];
    }

    /**
     * Returns the list of types for categories.
     *
     * @return array The list of types for categories.
     */
    public function getTypesForCategories() : array
    {
        if (!empty($this->parameters)
            && array_key_exists('categories', $this->parameters)
            && array_key_exists('types', $this->parameters['categories'])
        ) {
            return $this->parameters['categories']['types'];
        }

        return [];
    }

    /**
     * Checks if the current theme is multirepo.
     *
     * @return bool True if the current theme is multirepo. False otherwise.
     */
    public function isMultiRepo() : bool
    {
        return $this->multirepo === true;
    }

    /**
     * Returns the default skin for the Theme.
     *
     * @return array The default skin.
     */
    protected function getDefaultSkin()
    {
        $skins = $this->getSkins();

        if (empty($skins)) {
            return null;
        }

        foreach ($skins as $value) {
            if (array_key_exists('default', $value) && $value['default']) {
                return $value;
            }
        }
    }
}
