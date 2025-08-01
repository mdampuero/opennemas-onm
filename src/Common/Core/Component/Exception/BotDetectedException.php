<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Core\Component\Exception;

/**
 * Exception thrown when the current instance is not activated.
 */
class BotDetectedException extends \Exception
{
    /**
     * The redirection route.
     *
     * @var string
     */
    protected $route;

    /**
     * Initializes the exception.
     */
    public function __construct($route = '', $message = '')
    {
        $this->route = $route;

        parent::__construct($message, empty($route) ? 403 : 302);
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
