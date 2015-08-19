<?php

namespace Framework\ORM\Core;

class ChainElement
{
    /**
     * The next element in chain.
     *
     * @var ChainElement
     */
    private $_next;

    /**
     * Sets the next element in chain.
     *
     * @param ChainElement $element The next element.
     */
    public function add(ChainElement $element)
    {
        $last = $this;

        while($this->hasNext()) {
            $last = $this->next();
        }

        $last->setNext($element);
    }

    /**
     * Checks if there is another element in chain.
     *
     * @return boolean True if there is another element in chain. Otherwise,
     *                 return false.
     */
    public function hasNext()
    {
        return !empty($this->_next);
    }

    /**
     * Returns the next element in chain.
     *
     * @return ChainElement The next element in chain.
     */
    public function next()
    {
        return $this->_next;
    }

    /**
     * Changes the next element in chain.
     *
     * @param ChainElement $next The next element.
     */
    public function setNext(ChainElement $element)
    {
        $this->_next = $element;
    }
}
