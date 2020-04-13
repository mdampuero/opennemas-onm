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
    protected $backTpl;

    /**
     * The frontend template.
     *
     * @var Template
     */
    protected $frontTpl;

    /**
     * Initializes the StatisticsRenderer.
     *
     * @param GlobalVariables $global   The global variables.
     * @param Template        $backTpl  The backend template.
     * @param Template        $frontTpl The frontend template.
     */
    public function __construct($global, $backTpl, $frontTpl)
    {
        $this->global   = $global;
        $this->backTpl  = $backTpl;
        $this->frontTpl = $frontTpl;
    }

    /**
     * Renders analytics code giving the types.
     *
     * @param array   $types     The array of types to render.
     * @param String  $output    The html page.
     * @param Content $content      The content on template.
     *
     * @return String The output with the all analytics code inserted.
     */
    public function render($types, $content = null, $output = null)
    {
        $codeType = $this->getCodeType($output);
        $code     = '';

        foreach ($types as $type) {
            $renderer = $this->getRendererClass($type);

            if ($renderer->validate()) {
                try {
                    $code .= $this->backTpl->fetch(
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
    protected function getCodeType($output)
    {
        $uri = $this->global->getRequest()->getUri();

        if (preg_match('@/rss/facebook-instant-articles$@', $uri)) {
            return 'fia';
        }

        if (empty($output)) {
            return 'image';
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
     * @return mixed An instance of the specific renderer.
     */
    protected function getRendererClass($type)
    {
        $class     = $type . 'Renderer';
        $classPath = __NAMESPACE__ . '\\Statistics\\' . $class;

        return new $classPath($this->global, $this->backTpl, $this->frontTpl);
    }

    /**
     * Returns if valid configuration or not
     *
     * @return boolean true
     */
    protected function validate()
    {
        return true;
    }

    /**
     * Returns the needed parameters
     *
     * @return array []
     */
    protected function getParameters($content)
    {
        if (!empty($content)) {
            return [ 'title' => $content->title ];
        }

        return [];
    }
}
