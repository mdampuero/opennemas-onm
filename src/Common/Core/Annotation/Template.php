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
 * The Template class describes which template service has to be used
 * in the controller action.
 *
 * @Annotation
 */
class Template extends Annotation
{
    /**
     * The template filename.
     *
     * @var string
     */
    protected $file;

    /**
     * The template service name.
     *
     * @var string
     */
    protected $name;

    /**
     * Returns the template filename.
     *
     * @return string The template filename.
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Returns the template service name.
     *
     * @return string The template service name.
     */
    public function getName()
    {
        return $this->name;
    }
}
