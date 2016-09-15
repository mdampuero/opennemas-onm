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
     * Returns information about the extension.
     *
     * @param string $lang The language name in short format.
     *
     * @return string The information about the extension.
     */
    public function getAbout($lang = 'en')
    {
        return $this->getPropertyByLanguage('about', $lang);
    }

    /**
     * Returns the extension description.
     *
     * @param string $lang The language name in short format.
     *
     * @return string The extension description.
     */
    public function getDescription($lang = 'en')
    {
        return $this->getPropertyByLanguage('body', $lang);
    }

    /**
     * Returns the extension name.
     *
     * @param string $lang The language name in short format.
     *
     * @return string The extension name.
     */
    public function getName($lang = 'en')
    {
        return $this->getPropertyByLanguage('name', $lang);
    }

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

    /**
     * Returns the value of the property for a language.
     *
     * @param string $property The property name.
     * @param string $lang     The language name.
     *
     * @return string The property value.
     */
    protected function getPropertyByLanguage($property, $lang = 'en')
    {
        if (empty($this->{$property})) {
            return '';
        }

        if (array_key_exists($lang, $this->{$property})) {
            return $this->{$property}[$lang];
        }

        if (array_key_exists('en', $this->{$property})) {
            return $this->{$property}[$lang];
        }

        return array_shift(array_values($this->{$property}));
    }
}
