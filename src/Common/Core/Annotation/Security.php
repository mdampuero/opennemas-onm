<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Core\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 *
 * The Security class represents an annotation to add role, permission and
 * extension related constraints to actions in controllers.
 */
class Security extends Annotation
{
}
