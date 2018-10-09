<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Onm;

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
     * The template service.
     */
    protected $tpl;

    /**
     * Initializes the LayoutManager.
     *
     * @param Templating $templating The templating service.
     */
    public function __construct($templating)
    {
        $this->tpl = $templating->getBackendTemplate();
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
     * Loads a layout file.
     *
     * @param string $file The path to the file to load.
     */
    public function load($file)
    {
        if (file_exists($file)) {
            $this->layoutDoc = simplexml_load_file($file);
        }
    }

    /**
     * Renders a layout element
     *
     * @param string  $element the kind of element
     * @param array   $value the properties for this placeholder
     * @param boolean $last      true if this element will be last in a column
     *
     * @return string the HTML generated for a static placeholder
     */
    public function renderElement($element, $value, $last)
    {
        $output = [];
        switch ($element) {
            case 'wrapper':
                $output[] = $this->renderWrapper($element, $value, $last);
                break;
            case 'placeholder':
                $output[] = $this->renderPlaceholder($element, $value, $last);
                break;
            case 'static':
                $output[] = $this->renderStatic($element, $value, $last);
                break;
            default:
                break;
        }

        return implode("\n", $output);
    }

    /**
     * Renders a placeholder wrapper
     *
     * @param string  $elementType the kind of element
     * @param array   $innerValues the properties for this wrapper
     * @param boolean $isLast      true if this element will be last in a column
     *
     * @return string the HTML generated for a static placeholder
     */
    public function renderWrapper($elementType, $innerValues, $isLast)
    {
        $output   = [];
        $last     = ($isLast) ? ' last' : '';
        $output[] = '<div class="wrapper clearfix span-' .
            $innerValues['width'] . $last . '">';

        $total    = count($innerValues->children());
        $position = 0;
        $last     = false;
        foreach ($innerValues->children() as $elementTypeInner => $innerValuesInner) {
            $position++;
            $last     = ($total == $position);
            $output[] = $this->renderElement($elementTypeInner, $innerValuesInner, $last);
        }
        $output[] = '</div><!-- end wrapper -->';

        return implode("\n", $output);
    }

    /**
     * Renders a placeholder
     *
     * @param string  $elementType the kind of element
     * @param array   $innerValues the properties for this placeholder
     * @param boolean $isLast      true if this element will be last in a column
     *
     * @return string the HTML generated for a static placeholder
     */
    public function renderPlaceholder($elementType, $innerValues, $isLast)
    {
        $last        = ($isLast) ? ' last' : '';
        $description = '';
        $order       = 'normal';
        if (!empty($innerValues['description'])) {
            $description = '<div class="title">' . $innerValues['description'] . '</div>';
        }
        if (!empty($innerValues['class'])) {
            $description = '<div class="title">' . $innerValues['description'] . '</div>';
        }
        if (!empty($innerValues['order'])) {
            $order = $innerValues['order'];
        }
        $output =
            ' <div class="placeholder clearfix ' . $innerValues['class']
            . ' span-' . $innerValues['width'] . $last
            . '" data-placeholder="' . $innerValues['name'] . '">'
            . $description
            . '<div class="content">'
            . $this->renderContentsForPlaceholder($innerValues['name'], $order)
            . '<!-- {placeholder-content-' . $innerValues['name'] . '} --></div>'
            . '</div><!-- end wrapper -->';

        return $output;
    }

    /**
     * Renders a static placeholder
     *
     * @param string  $elementType the kind of element
     * @param array   $innerValues the properties for this placeholder
     * @param boolean $isLast      true if this element will be last in a column
     *
     * @return string the HTML generated for a static placeholder
     */
    public function renderStatic($elementType, $innerValues, $isLast)
    {
        $last        = ($isLast) ? ' last' : '';
        $description = '';
        if (!empty($innerValues['description'])) {
            $description = '<div class="title">' . $innerValues['description'] . '</div>';
        }
        if (!empty($innerValues['class'])) {
            $description = '<div class="title">' . $innerValues['description'] . '</div>';
        }
        $output = '<div class="static clearfix ' . $innerValues['class']
            . ' span-' . $innerValues['width'] . $last . '">'
            . $description
            . '</div><!-- end static -->';

        return $output;
    }

    /**
     * Returns the html for a given placeholder
     *
     * @param string $placeholderName the name of the placeholder
     * @param string $order           the order to sort the rendered contents
     *
     * @return string the final HTML for the rendered contents
     */
    public function renderContentsForPlaceholder($placeholderName, $order)
    {
        if (isset($this->contentPositionByPos) &&
            array_key_exists((string) $placeholderName, $this->contentPositionByPos)
        ) {
            $contentPositionsOrder = $this->orderContents(
                $this->contentPositionByPos[(string) $placeholderName],
                (string) $order
            );
            $output                = '';
            foreach ($contentPositionsOrder as $contentPosition) {
                $output .= $this->renderContent(
                    $this->contents[$contentPosition->pk_fk_content]
                );
            }

            return $output;
        }
    }

    /**
     * Sorts contents by one of its properties
     *
     * @param array  $contents the array of objects to sort
     * @param string $order the sort method
     *
     * @return array the sorted array of contents
     */
    public static function orderContents(&$contents, $order)
    {
        if ($order == 'date DESC') {
            $contents = \ContentManager::sortArrayofObjectsByProperty($contents, 'starttime');
            $contents = array_reverse($contents);
        } elseif ($order == 'date') {
            $contents = \ContentManager::sortArrayofObjectsByProperty($contents, 'starttime');
        }

        return $contents;
    }

    /**
     * Returns the html for a given content
     *
     * @param Content $content the content instance to render
     *
     * @return string the html for the content
     */
    private function renderContent($content)
    {
        $this->tpl->assign('content', $content);
        $this->tpl->assign('params', $this->params);
        try {
            $contentName = strtolower($content->content_type_name);

            return $this->tpl->fetch(
                $contentName . '/content-provider/' . $contentName . '.tpl'
            );
        } catch (\Exception $e) {
            error_log('Error in LayoutManager::renderContent: ' . $e->getMessage());
            return '';
        }
    }

    /**
     * Renders the frontpage layout.
     *
     * @param array $params the list of params to pass to the template
     *
     * @param array $params the params for rendering the layout
     *
     * @return string
     */
    public function render($params = [])
    {
        if (isset($params['contents'])) {
            $this->contents             = $params['contents'];
            $this->contentPositionByPos = $params['contentPositionByPos'];
            unset($params['contents']);
            unset($params['contentPositionByPos']);
        }

        $this->params = $params;

        $output = [];
        foreach ($this->layoutDoc as $element => $value) {
            $output[] = $this->renderElement($element, $value, false);
        }

        return implode("\n", $output);
    }
}
