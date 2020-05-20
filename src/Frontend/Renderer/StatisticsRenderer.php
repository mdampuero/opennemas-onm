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
     * The renderer configuration.
     *
     * @var array
     */
    protected $config = [];

    /**
     * The global variables.
     *
     * @var GlobalVariables
     */
    protected $global;

    /**
     * The admin template.
     *
     * @var Template
     */
    protected $backend;

    /**
     * The frontend template.
     *
     * @var Template
     */
    protected $frontend;

    /**
     * Initializes the StatisticsRenderer.
     *
     * @param GlobalVariables $global   The global variables.
     * @param Template        $backend  The backend template.
     * @param Template        $frontend The frontend template.
     */
    public function __construct($global, $backend, $frontend)
    {
        $this->global   = $global;
        $this->backend  = $backend;
        $this->frontend = $frontend;
    }

    /**
     * Renders analytics code giving the types.
     *
     * @param array   $types        The array of types to render.
     * @param String  $output       The html page.
     * @param Content $content      The content on template.
     *
     * @return String The output with the all analytics code inserted.
     */
    public function render($types, $content = null, $output = null)
    {
        $codeType = $this->getCodeType();
        $code     = '';

        foreach ($types as $type) {
            $renderer = $this->getRendererClass($type);

            if ($renderer->validate()) {
                try {
                    $code .= $this->backend->fetch(
                        'statistics/helpers/' . strtolower($type) . '/' . $codeType . '.tpl',
                        $renderer->getParameters($content)
                    );
                } catch (\Exception $e) {
                    continue;
                }
            }
        }

        if ($codeType == 'fia') {
            return '<figure class="op-tracker"><iframe>' . $code . '</iframe></figure>';
        }

        if (empty($output)) {
            return $code;
        }

        if ($codeType == 'amp') {
            return preg_replace('@(<body.*?>)@', '${1}' . "\n" . $code, $output);
        }

        return preg_replace('@(</head>)@', $code . '${1}', $output);
    }

    /**
     * Return the specific method to call based on the request and imageOnly flag.
     *
     * @param bool $imageOnly Image only flag.
     *
     * @return String The code type needed: image, amp or script.
     */
    protected function getCodeType()
    {
        $request = $this->global->getRequest();

        if (empty($request)) {
            return 'image';
        }

        $uri = $request->getUri();

        if (preg_match('@/newsletters/save-contents$@', $uri)) {
            return 'image';
        }

        if (preg_match('@/rss/facebook-instant-articles$@', $uri)) {
            return 'fia';
        }

        if (preg_match('@\.amp\.html$@', $uri)) {
            return 'amp';
        }

        return 'script';
    }

    /**
     * Returns an instance of the renderer passed as parammeter.
     *
     * @param String $type The type of analytics to render.
     *
     * @return StatisticsRenderer An instance of the specific renderer.
     */
    protected function getRendererClass($type)
    {
        $class     = $type . 'Renderer';
        $classPath = __NAMESPACE__ . '\\Statistics\\' . $class;

        return new $classPath($this->global, $this->backend, $this->frontend);
    }

    /**
     * Checks if the renderer configuration is valid.
     *
     * @return boolean True if the configuration is valid. False otherwise.
     */
    protected function validate()
    {
        return true;
    }

    /**
     * Returns the list of parameters for the current renderer.
     *
     * @param Content $content The returned for the current request.
     *
     * @return array The list of parameters.
     */
    protected function getParameters($content = null)
    {
        if (!empty($content)) {
            return [ 'title' => $content->title ];
        }

        return [];
    }
}
