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

use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

class Renderer
{
    /**
     * The service container.
     *
     * @var Container
     */
    protected $container;

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
    public function render($content, $params)
    {
        if (!empty($params['types'])) {
            return $this->container->get('frontend.renderer.statistics')->render($content, $params);
        }

        try {
            return $this->container->get('frontend.renderer.'
                . strtolower($content->content_type_name))->render($content, $params);
        } catch (ServiceNotFoundException $e) {
            return $this->container->get('frontend.renderer.content')->render($content, $params);
        }
    }
}
