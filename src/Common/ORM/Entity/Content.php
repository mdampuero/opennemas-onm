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
     * Returns the objects associated to an specific position
     *
     * @param string $name The position name
     *
     * @return array
     */
    public function getRelated($name)
    {
        $related = array_map(function ($el) {
            return $el['pk_content2'];
        }, array_filter($this->related_contents, function ($element) use ($name) {
            return $element['relationship'] == $name;
        }));

        if (count($related) == 1) {
            $related = array_pop($related);
        }

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
            return $element['relationship'] == $name;
        })) > 0;
    }

    /**
     * Returns true if a match time constraints, is available and is not in trash
     *
     * @return boolean true if is ready
     */
    public function isReadyForPublish()
    {
        return ($this->isInTime()
            && $this->content_status == 1
            && $this->in_litter == 0);
    }

    /**
     * Check if a content is in time for publishing
     *
     * @param string $now the current time
     *
     * @return boolean
     */
    protected function isInTime($now = null)
    {
        $now = new \DateTime($now);

        $dued = (
            !empty($this->endtime)
            && $now->getTimeStamp() > $this->endtime->getTimeStamp()
        );

        $postponed = (
            !empty($this->starttime)
            && $now->getTimeStamp() < $this->starttime->getTimeStamp()
        );

        return (!$dued && !$postponed);
    }
}
