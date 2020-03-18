<?php

namespace Common\ORM\Core\Validation;

interface Validable
{
    /**
     * Returns the current class name without namespace.
     *
     * @return string The current class name without namespace.
     */
    public function getClassName();

    /**
     * Returns the data to validate.
     *
     * @return array The data to validate.
     */
    public function getData();
}
