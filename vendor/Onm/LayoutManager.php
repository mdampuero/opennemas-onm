<?php
/**
 * Defines the LayoutManager class
 *
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  Onm
 * @subpackage LayoutManager
 */
namespace Onm;

/**
 * Loads an xml file and tries to generate the frontpage manager.
 *
 * @package    Onm
 * @subpackage LayoutManager
 **/
class LayoutManager
{
    /**
     * Initializes the LayoutManager from a xml file
     *
     * @param stringn $xmlFile the layout definition file
     */
    public function __construct($xmlFile)
    {
        $this->layoutDoc = simplexml_load_file($xmlFile);
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
        $output =  array();
        switch ($element) {
            case 'wrapper':
                $output []= $this->renderWrapper($element, $value, $last);
                break;
            case 'placeholder':
                $output []= $this->renderPlaceholder($element, $value, $last);
                break;
            case 'static':
                $output []= $this->renderStatic($element, $value, $last);
                break;
            default:
                # code...
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
        $output = array();
        $last = ($isLast)?" last":"";
        $output []= '<div class="wrapper clearfix span-'.$innerValues['width'].$last.'">';

        $total = count($innerValues->children());
        $position = 0;
        $last = false;
        foreach ($innerValues->children() as $elementTypeInner => $innerValuesInner) {
            $position++;
            $last = ($total == $position);
            $output []= $this->renderElement($elementTypeInner, $innerValuesInner, $last);

        }
        $output []= '</div><!-- end wrapper -->';

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
        $last = ($isLast)?" last":"";

        $description  = '';
        $order = 'normal';
        if (!empty($innerValues['description'])) {
            $description = '<div class="title">'.$innerValues['description'].'</div>';
        }
        if (!empty($innerValues['class'])) {
            $description = '<div class="title">'.$innerValues['description'].'</div>';
        }
        if (!empty($innerValues['order'])) {
            $order = $innerValues['order'];
        }
        $output  =
            '<div class="placeholder clearfix '.$innerValues['class']
            .' span-'.$innerValues['width'].$last
            .'" data-placeholder="'.$innerValues['name'].'">'
            .$description
            .'<div class="content">'
            .$this->renderContentsForPlaceholder($innerValues['name'], $order)
            .'<!-- {placeholder-content-'.$innerValues['name']. '} --></div>'
            .'</div><!-- end wrapper -->';

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
        $last = ($isLast)?" last":"";

        $description  = '';
        if (!empty($innerValues['description'])) {
            $description = '<div class="title">'.$innerValues['description'].'</div>';
        }
        if (!empty($innerValues['class'])) {
            $description = '<div class="title">'.$innerValues['description'].'</div>';
        }
        $output  =  '<div class="static clearfix '.$innerValues['class']
                    .' span-'.$innerValues['width'].$last.'">'
                    .$description
                    .'</div><!-- end static -->';

        return $output;
    }

    /**
     * Returns the html for a given placeholder
     *
     * @param string $placeholderName the name of the placeholder
     * @param string $order           the order to sort the rendered contents
     *
     * @return string the final HTML for the rendered contents
     **/
    public function renderContentsForPlaceholder($placeholderName, $order)
    {
        if (isset($this->contents) && count($this->contents) > 0) {
            $output = '';
            $filteredContents = array();
            foreach ($this->contents as $content) {
                if ($content->placeholder == $placeholderName) {
                    $contentTypeName = $content->content_type_name;
                    // TODO: Add logic here for delayed, in time or postponed elements
                    // that will be passed to the view
                    if (!empty($contentTypeName)) {
                        $filteredContents []= $content;
                    }
                }
            }

            $this->orderContents($filteredContents, (string) $order);

            foreach ($filteredContents as $content) {
                $output .= $this->renderContent($content);
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
     **/
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
     **/
    private function renderContent($content)
    {
        $this->tpl->assign('content', $content);
        $this->tpl->assign('params', $this->params);
        try {
            $contentName = strtolower($content->content_type_name);

            return $this->tpl->fetch($contentName.'/content-provider/'.$contentName.".tpl")."\n";
        } catch (\SmartyException $e) {
            return '';
        }
    }

    /**
     * Renders the frontpage layout.
     *
     * @param array $params the list of params to pass to the template
     *
     * @param array $params the params for rendering the layout
     **/
    public function render($params = array())
    {
        // For bost performance by sharing the same view instance througth
        // rendering process.
        if (array_key_exists('smarty', $params)) {
            $this->tpl = clone $params['smarty'];
        } else {
            $this->tpl = new \TemplateAdmin(TEMPLATE_ADMIN);
        }

        if (isset($params['contents'])) {
            $this->contents = $params['contents'];
            unset($params['contents']);
        }

        $this->params = $params;

        $output = '';
        foreach ($this->layoutDoc as $element => $value) {
            $output []= $this->renderElement($element, $value, false);
        }

        return implode("\n", $output);
    }
}
