<?php

/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Backend\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 *
 * Check if module is activated for accessing an controller
 */
class CheckModuleAccess extends Annotation
{
    public $module;

    /**
     * Gets the value of module.
     *
     * @return mixed
     */
    public function getModule()
    {
        return $this->module;
    }
}