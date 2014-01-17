<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Onm\Templating;

/**
*
*/
class Templating
{
    /**
     * The container that this class will use to fetch services
     *
     * @var string
     **/
    public $container;

    /**
     * The frontend template engine
     *
     * @var string
     **/
    private $frontendTemplateEngine;

    /**
     * The backend template engine
     *
     * @var string
     **/
    private $backendTemplateEngine;

    /**
     * The manage template engine
     *
     * @var string
     **/
    private $managerTemplateEngine;

    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * Bridge method calls to the proper Template engine
     *
     * @return void
     * @author
     **/
    public function __call($method, $params)
    {
        $bundleName = $this->getBundleName();

        if ($method == 'fetch' && $this->container->has('debug.stopwatch')) {
            $stopwatch = $this->container->get('debug.stopwatch');
            $stopwatch->start("template ({$bundleName} {$params[0]})");
        }

        $template = $this->getTemplateObject($bundleName);

        $response = call_user_func_array(array($template, $method), $params);

        if ($method == 'fetch' && $this->container->has('debug.stopwatch')) {
            $stopwatch = $this->container->get('debug.stopwatch');
            $stopwatch->stop("template ({$bundleName} {$params[0]})");
        }

        return $response;
    }

    /**
     * Returns the bundle name from the matched controller
     *
     * @return void
     **/
    public function getBundleName()
    {
        $controller = $this->container->get('request')->get('_controller');
        $controllerNameParts = explode('\\', $controller);

        return  $controllerNameParts[0];
    }

    /**
     * Returns the proper Template object for a given module name
     *
     * @return void
     * @author
     **/
    public function getTemplateObject($module)
    {
        if ($module == 'Manager') {
            if (!isset($this->managerTemplateEngine)) {
                $this->managerTemplateEngine = new \TemplateManager();
            }
            $template = $this->managerTemplateEngine;
        } elseif ($module == 'Backend') {
            if (!isset($this->backendTemplateEngine)) {
                $this->backendTemplateEngine = new \TemplateAdmin();
            }
            $template = $this->backendTemplateEngine;
        } else {
            if (!isset($this->frontendTemplateEngine)) {
                $this->frontendTemplateEngine = new \Template(TEMPLATE_USER);
            }
            $template = $this->frontendTemplateEngine;
        }

        return $template;
    }
}
