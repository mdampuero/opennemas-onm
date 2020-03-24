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

class StatisticsRenderer
{
    /**
     * The request stack.
     *
     * @var RequestStack
     */
    protected $stack;

    /**
     * The global variables
     *
     * @var GlobalVariables
     */
    protected $global;

    /**
     * The template
     *
     * @var Template
     */
    protected $tpl;

    /**
     * The entity manager
     *
     * @var EntityManager
     */
    protected $em;

    /**
     * Initializes the StatisticsRenderer
     *
     * @param RequestStack    $stack  The request stack
     * @param GlobalVariables $global The global variables
     * @param Template        $tpl    The template
     * @param EntityManager   $em     The entity manager
     */
    public function __construct($stack, $global, $tpl, $em)
    {
        $this->stack  = $stack;
        $this->global = $global;
        $this->tpl    = $tpl;
        $this->em     = $em;
    }

    /**
     * Renders analytics code giving the types
     *
     * @param array $types     The array of types to render
     * @param bool  $imageOnly The flag to indicate if is imageOnly or not
     * @param String $output   The html page
     */
    public function render($types, $output, $imageOnly = false)
    {
        $method = $this->getCode($imageOnly);
        //TODO: Initialize the code with our default analytics code
        $code = '';

        foreach ($types as $type) {
            $renderer = $this->getRendererClass($type);

            if ($renderer->validate()) {
                $code .= $renderer->{$method}();
            }
        }

        if ($imageOnly) {
            return $code;
        }

        if ($method == 'getAmp') {
            return preg_replace('@(<body.*>)@', '${1}' . "\n" . $code, $output);
        }

        return preg_replace('@(</head>)@', $code . '${1}', $output);
    }

    /**
     * Return the specific method to call based on the request and imageOnly flag
     *
     * @param bool $imageOnly Image only flag
     */
    protected function getCode($imageOnly)
    {
        $uri = $this->stack->getCurrentRequest()->getUri();

        if ($imageOnly) {
            return 'getImage';
        }

        if (preg_match('@\.amp\.html$@', $uri)) {
            return 'getAmp';
        }

        return 'getScript';
    }

    /**
     * Returns an instance of the renderer passed as parammeter
     *
     * @param String $type The type of analytics to render
     *
     * @return mixed An instance of the specific renderer
     */
    protected function getRendererClass($type)
    {
        $class     = $type . 'Renderer';
        $classPath = __NAMESPACE__ . '\\Statistics\\' . $class;

        return new $classPath($this->stack, $this->global, $this->tpl, $this->em);
    }
}
