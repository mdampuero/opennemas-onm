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
class BotDetector extends Annotation
{
    /**
     * The template service name.
     *
     * @var string
     */
    protected $bot;

    /**
     * The redirection route.
     *
     * @var string
     */
    protected $route;

    /**
     * Returns the template service name.
     *
     * @return string The template service name.
     */
    public function getBot()
    {
        return $this->bot;
    }

    /**
     * Returns the redirection route.
     *
     * @return string The redirection route.
     */
    public function getRoute()
    {
        return $this->route;
    }
}
