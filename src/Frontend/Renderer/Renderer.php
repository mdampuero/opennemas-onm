<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Frontend\Renderer;

abstract class Renderer
{
    /**
     * The service container.
     *
     * @var Container
     */
    private $container;

    /**
     * Initializes the Renderer.
     *
     * @param Container The service container.
     */
    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * Renders a content on frontend.
     *
     * @param mixed   The content.
     * @param Array   The array of parameters.
     *
     * @return String The html of the content.
     */
    abstract public function render($content, $params);
}
