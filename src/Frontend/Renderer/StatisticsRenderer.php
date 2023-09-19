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

class StatisticsRenderer extends Renderer
{
    /**
     * The renderer configuration.
     *
     * @var array
     */
    protected $config = [];

    /**
     * Initializes the StatisticsRenderer.
     *
     * @param Container The service container.
     */
    public function __construct($container)
    {
        parent::__construct($container);
        $this->global   = $this->container->get('core.globals');
        $this->backend  = $this->container->get('core.template.admin');
        $this->frontend = $this->container->get('core.template.frontend');
    }

    /**
     * Renders analytics code giving the types.
     *
     * @param Array   $params       The array of parameters.
     * @param Content $content      The content on template.
     *
     * @return String The output with the all analytics code inserted.
     */
    public function render($content, $params)
    {
        $codeType = $this->getCodeType();
        $code     = '';

        foreach ($params['types'] as $type) {
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

        if (empty($params['output'])) {
            return $code;
        }

        if ($codeType == 'amp') {
            return preg_replace('@(<body.*?>)@', '${1}' . "\n" . $code, $params['output']);
        }

        return preg_replace('@(</head>)@', $code . '${1}', $params['output']);
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

        if (preg_match('@/newsletters/save-contents@', $uri)) {
            return 'image';
        }

        if (preg_match('@/rss/facebook-instant-articles@', $uri)) {
            return 'fia';
        }

        if (preg_match('@\.amp\.html@', $uri)) {
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

        return new $classPath($this->container);
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

    /**
     * Returns the customization for the extension.
     *
     * @param string $extension The extension to customize.
     *
     * @return string The customized extension.
     */
    protected function customizeExtension(string $extension)
    {
        $contentTypes = [
            'album',
            'blog',
            'event',
            'letter',
            'opinion',
            'poll',
            'video',
        ];

        $replacements = [
            'article'    => 'articulo',
            'frontpages' => 'home',
            'category'   => 'subhome',
            'album'      => 'galeria',
            'opinion'    => 'articulo_opinion',
            'blog'       => 'blogpost',
            'poll'       => 'encuesta'
        ];

        if (in_array($extension, $contentTypes)) {
            if (empty($this->variablesExtractor->get('contentId'))) {
                return 'subhome';
            }
        }

        if ($extension === 'frontpages') {
            $category = $this->variablesExtractor->get('categoryId');

            if (!empty($category)) {
                return 'subhome';
            }
        }

        return !empty($replacements[$extension]) ? $replacements[$extension] : $extension;
    }
}
