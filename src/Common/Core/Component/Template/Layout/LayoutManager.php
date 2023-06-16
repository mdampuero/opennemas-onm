<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Core\Component\Template\Layout;

use Common\Core\Component\Template\TemplateFactory;

/**
 * Manages and renders theme layouts.
 */
class LayoutManager
{
    /**
     * The default values for a layout.
     *
     * @var array
     */
    protected $defaultLayout = [
        'name' => 'Layout name',
        'menu' => 'frontpage'
    ];

    /**
     * The contents.
     *
     * @var array
     */
    protected $contents = [];

    /**
     * The current layout document.
     *
     * @var array
     */
    protected $layoutDoc = [];

    /**
     * The list of layouts
     *
     * @var array
     */
    protected $layouts = [];

    /**
     * The params.
     *
     * @var array
     */
    protected $params = [];

    /**
     * The path to load layouts from.
     *
     * @var ?string
     */
    protected $path = null;

    /**
     * The positions.
     *
     * @var array
     */
    protected $positions = [];

    /**
     * The TemplateFactory service.
     *
     * @var TemplateFactory
     */
    protected $view;

    /**
     * Initializes the LayoutManager.
     *
     * @param TemplateFactory $view The view service.
     */
    public function __construct(TemplateFactory $view)
    {
        $this->view = $view;
    }

    /**
     * Adds a layout to the list of layouts.
     *
     * @param string $name The layout name.
     * @param string $file The layout configuration.
     */
    public function addLayout($name, $layout)
    {
        $layout = array_merge($this->defaultLayout, $layout);

        $this->layouts[$name] = $layout;
    }

    /**
     * Adds a list of layouts to the list of layouts.
     *
     * @param string $name The layout name.
     * @param string $file The layout configuration.
     */
    public function addLayouts($layouts)
    {
        foreach ($layouts as $name => $layout) {
            $this->addLayout($name, $layout);
        }
    }

    /**
     * Returns the configuration for a layout
     *
     * @param string $name The layout name.
     *
     * @return array The layout configuration.
     */
    public function getLayout($name)
    {
        if (!array_key_exists($name, $this->layouts)) {
            return false;
        }

        return $this->layouts[$name];
    }

    /**
     * Returns the list of layouts.
     *
     * @return array The list of layouts.
     */
    public function getLayouts()
    {
        return $this->layouts;
    }

    /**
     * Renders the frontpage layout.
     *
     * @param array $params The list of params.
     *
     * @return string The generated HTML.
     */
    public function render(array $params = []) : string
    {
        if (isset($params['contents'])) {
            $this->contents  = $params['contents'];
            $this->positions = $params['contentPositionByPos'];
            unset($params['contents']);
            unset($params['contentPositionByPos']);
        }

        $this->params = $params;

        $output = [];
        foreach ($this->layoutDoc as $type => $value) {
            $output[] = $this->renderElement($type, $value, false);
        }

        return implode("\n", $output);
    }

    /**
     * Selects and loads a layout.
     *
     * @param string $name The layout name.
     *
     * @codeCoverageIgnore
     */
    public function selectLayout($name)
    {
        if (!array_key_exists($name, $this->layouts)) {
            $name = 'default';
        }

        $path = $this->path . '/' . $name . '.xml';

        if (file_exists($path)) {
            $this->layoutDoc = simplexml_load_file($path);
        }
    }

    /**
     * Changes the path to the layouts directory.
     *
     * @param string $path The path to load layouts from.
     */
    public function setPath(string $path) : void
    {
        $this->path = $path;
    }

    /**
     * Returns the html for a given content
     *
     * @param Content $content the content instance to render
     *
     * @return string the html for the content
     */
    protected function renderContent($content)
    {
        $tpl = $this->view->get('backend');

        $template = $content->content_type_name . '/content-provider/'
            . $content->content_type_name . '.tpl';

        return $tpl->fetch($template, [
            'content' => $content,
            'params'  => $this->params
        ]);
    }

    /**
     * Returns the HTML with the contents in a placeholder.
     *
     * @param string $name The placeholder name.
     *
     * @return string The generated HTML with the contents for the placeholder.
     */
    protected function renderContentsForPlaceholder(string $name) : string
    {
        $output = '';

        if (array_key_exists($name, $this->positions)) {
            foreach ($this->positions[$name] as $position) {
                if (array_key_exists($position->pk_fk_content, $this->contents)) {
                    if ($this->contents[$position->pk_fk_content] != null) {
                        $output .= $this->renderContent(
                            $this->contents[$position->pk_fk_content]
                        );
                    }
                }
            }
        }

        return $output;
    }

    /**
     * Renders a layout element
     *
     * @param string            $type   The plaholder type.
     * @param \SimpleXmlElement $value The XML element to render.
     *  @param bool             $isLast Whether the element is the last in the
     *                                  column.

     * @return string The generated HTML the element.
     */
    protected function renderElement(string $type, \SimpleXmlElement $value, bool $isLast) : string
    {
        $output = '';
        $method = 'render' . ucfirst($type);

        if (method_exists($this, $method)) {
            $output = $this->{$method}($value, $isLast);
        }

        return $output . "\n";
    }

    /**
     * Renders a placeholder.
     *
     * @param SimpleXmlElement $xml     The XML element to render.
     * @param bool             $isLast  Whether the element is the last in the
     *                                  column.
     *
     * @return string The generated HTML a placeholder.
     */
    protected function renderPlaceholder(\SimpleXmlElement $xml, bool $isLast) : string
    {
        $last        = $isLast ? ' last' : '';
        $description = '';

        if (!empty($xml['description'])) {
            $description = '<div class="title">' . $xml['description']
                . '</div>';
        }

        $output = '<div class="placeholder clearfix ' . $xml['class']
            . ' span-' . $xml['width'] . $last . '" data-placeholder="'
            . $xml['name'] . '">' . $description
            . '<div class="content">'
            . $this->renderContentsForPlaceholder($xml['name'])
            . '<!-- {placeholder-content-' . $xml['name'] . '} --></div>'
            . '</div><!-- end wrapper -->';

        return $output;
    }

    /**
     * Renders a static placeholder.
     *
     * @param \SimpleXmlElement $xml    The XML element to render.
     * @param bool              $isLast Whether the element is the last in the
     *                                  column.
     *
     * @return string The generated HTML for a static placeholder.
     */
    protected function renderStatic(\SimpleXmlElement $xml, bool $isLast) : string
    {
        $last        = $isLast ? ' last' : '';
        $description = '';
        $collapse = '';

        if (!empty($xml['description'])) {
            if (!empty($xml['collapse'])) {
                $collapse = '<a role="button" class="wrapper-collapse text-white" data-toggle="collapse" href="#' . $xml['collapse'] . '" aria-expanded="true" aria-controls="' . $xml['collapse'] . '">' . $xml['description'] . '</a>';
                $description = '<div class="title">' . $collapse . '</div>';
            } else {
                $description = '<div class="title">' . $xml['description'] . '</div>';
            }
        }

        return '<div class="static clearfix ' . $xml['class']
            . ' span-' . $xml['width'] . $last . '">'
            . $description
            . '</div><!-- end static -->';
    }

    /**
     * Renders a wrapper placeholder.
     *
     * @param \SimpleXmlElement $xml    The XML element to render.
     * @param bool              $isLast Whether the element is the last in the
     *                                  column.
     *
     * @return string The generated HTML for a wrapper placeholder.
     */
    protected function renderWrapper(\SimpleXmlElement $xml, bool $isLast) : string
    {
        $output = [];
        $last   = $isLast ? ' last' : '';

        if ($xml['type'] == 'wrapper-collapsable') {
            $output[] = '<div id="' . $xml['id'] . '" class="wrapper ' . $xml['type'] .' clearfix collapse in span-' .
                $xml['width'] . $last . '">';
        } else {
            $output[] = '<div class="wrapper clearfix span-' .
                $xml['width'] . $last . '">';
        }

        $total    = count($xml->children());
        $position = 0;

        foreach ($xml->children() as $type => $value) {
            $output[] = $this->renderElement($type, $value, $total == ++$position);
        }

        $output[] = '</div><!-- end wrapper -->';

        return implode("\n", $output);
    }
}
