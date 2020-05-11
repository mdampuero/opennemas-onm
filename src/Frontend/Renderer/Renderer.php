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

use Content;

class Renderer
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
     * @param mixed The content.
     * @param Array   The array of parameters.
     *
     * @return String The html of the content.
     */
    public function render($content, $params)
    {
        return $this->getRendererClass($content)->render($content, $params);
    }

    /**
     * Return the Renderer based on the type of content.
     *
     * @param Content The content.
     *
     * @return mixed The specific renderer.
     */
    protected function getRendererClass($content)
    {
        $class     = get_class($content) . 'Renderer';
        $classPath = __NAMESPACE__ . '\\' . get_class($content) . '\\' . $class;

        if (class_exists($classPath)) {
            return new $classPath($this->container);
        }

        return $this->container->get('frontend.renderer.content');
    }
}
