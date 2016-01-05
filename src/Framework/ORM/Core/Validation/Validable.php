<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <onm-devs@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\ORM\Core\Validation;

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

    /**
     * Returns the parent class name without namespace.
     *
     * @return string The parent class name without namespace.
     */
    public function getParentClassName();
}
