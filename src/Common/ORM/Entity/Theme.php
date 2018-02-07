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
    public function getStyles()
    {
        if (array_key_exists('parameters', $this->data)
            && array_key_exists('styles', $this->data['parameters'])
        ) {
            return $this->data['parameters']['styles'];
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
    public function getDefaultStyle()
    {
        $styles = $this->getStyles();

        if (empty($styles)) {
            return null;
        }

        $default = array_filter($styles, function ($el) {
            return (array_key_exists('default', $el) && $el['default'] == true);
        });

        // If no default style just pick the first one
        if (empty($default)) {
            return array_shift($styles);
        }

        return array_pop($default);
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
    public function getCurrentStyle($name)
    {
        $styles = $this->getStyles();

        if (empty($styles)) {
            return null;
        }

        // Return the default style if the name doesnt exists
        if (!array_key_exists($name, $styles)) {
            return $this->getDefaultStyle();
        }

        return $styles[$name];
    }
}
