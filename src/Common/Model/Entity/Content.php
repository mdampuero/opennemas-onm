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

/**
 * The Content class represents a content.
 */
class Content extends Entity
{
    /**
     * The list of common l10n supported keys.
     *
     * @var array
     */
    protected static $l10nKeys = [ 'body', 'description', 'slug', 'title' ];

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
                $value = $this->pk_content;
                break;
            default:
                $value = parent::__get($name);
                break;
        }

        return $value;
    }

    /**
     * Checks if a property exists.
     *
     * @param string $name The property name.
     *
     * @return boolean True if the property exists. False otherwise.
     */
    public function __isset($name)
    {
        return parent::__isset($name) || !empty($this->__get($name));
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

    /**
     * Returns one media object to an specific position name
     *
     * @param string $name The position name
     *
     * @return array
     */
    public function getMedia($name)
    {
        $media = $this->getRelated($name);

        return array_pop($media);
    }

    /**
     * Returns a list of objects associated to an specific position
     *
     * @param string $name The position name
     *
     * @return array
     */
    public function getRelated($name)
    {
        $related = array_map(function ($el) {
            return $el['target_id'];
        }, array_filter($this->related_contents, function ($element) use ($name) {
            return $element['type'] == $name;
        }));

        return $related;
    }

    /**
     * Returns true if content has objects associated to an specific position
     *
     * @param string $name The position name
     *
     * @return boolean
     **/
    public function hasRelated($name)
    {
        return count(array_filter($this->related_contents, function ($element) use ($name) {
            return $element['type'] == $name;
        })) > 0;
    }
}
