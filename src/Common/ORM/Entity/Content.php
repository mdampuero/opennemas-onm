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
 * The Content class represents a content.
 */
class Content extends Entity
{
    /**
     * Returns true if content has objects associated to an specific position
     *
     * @param string $name The position name
     *
     * @return boolean
     **/
    public function hasRelated($name)
    {
        return count(array_filter($this->related_contents, function ($element) {
            return $element->relationship == $name;
        })) > 0;
    }

    /**
     * Returns the objects associated to an specific position
     *
     * @param string $name The position name
     *
     * @return array
     */
    public function getRelated($name)
    {
        return array_filter($this->related, function ($element) {
            return $element->relationship == $name;
        });
    }
}
