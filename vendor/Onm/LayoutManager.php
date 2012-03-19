<?php
/*
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Onm;
/**
 * Loads an xml file and tries to generate the frontpage manager.
 *
 * @package    Onm
 * @subpackage LayoutManager
 * @author     me
 **/
class LayoutManager
{

    /*
     * Initializes the LayoutManager from a xml file
     *
     * @param $xmlFile
     */
    public function __construct($xmlFile)
    {
        $this->layoutDoc = simplexml_load_file($xmlFile);
    }

    /*
     * Renders wrapper
     *
     * @param $element
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

    /*
     * Renders wrapper
     *
     * @param $elementType
     * @param $innerValues
     */
    public function renderWrapper($elementType, $innerValues, $isLast)
    {
        $output = array();
        $last = ($isLast)?" last":"";
        $output []= '<div class="wrapper clearfix span-'.$innerValues['width'].$last.'">';

        $total = count($innerValues->children());
        $position = 0;
        $last = false;
        foreach ($innerValues->children() as $elementTypeInner => $innerValuesInner ) {
            $position++;
            $last = ($total == $position);
            $output []= $this->renderElement($elementTypeInner, $innerValuesInner, $last);

        }
        $output []= '</div><!-- end wrapper -->';
        return implode("\n", $output);
    }

    /*
     * Renders wrapper
     *
     * @param $elementType
     * @param $innerValues
     */
    public function renderPlaceholder($elementType, $innerValues, $isLast)
    {
        $last = ($isLast)?" last":"";

        $description  = '';
        if (!empty($innerValues['description'])) {
            $description = '<div class="title">'.$innerValues['description'].'</div>';
        }
        if (!empty($innerValues['class'])) {
            $description = '<div class="title">'.$innerValues['description'].'</div>';
        }
        $output  =  '<div class="placeholder clearfix '.$innerValues['class'].' span-'.$innerValues['width'].$last.'" data-placeholder="'.$innerValues['name'].'">'
                    .$description
                    .'<div class="content">'
                    .$this->renderContentsForPlaceholder($innerValues['name'])
                    .'<!-- {placeholder-content-'.$innerValues['name']. '} --></div>'
                    .'</div><!-- end wrapper -->';
        return $output;
    }

    /*
     * Renders wrapper
     *
     * @param $elementType
     * @param $innerValues
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
        $output  =  '<div class="static clearfix '.$innerValues['class'].' span-'.$innerValues['width'].$last.'">'
                    .$description
                    .'</div><!-- end static -->';
        return $output;
    }


    /**
     * Returns the html for a given placeholder
     *
     * @return string
     **/
    public function renderContentsForPlaceholder($placeholderName)
    {
        if (isset($this->contents) && count($this->contents) > 0) {
            $output = '';
            foreach ($this->contents as $content) {
                if ($content->placeholder == $placeholderName) {
                    $contentTypeName = $content->content_type_name;
                    // TODO: Add logic here for delayed, in time or postponed elements
                    // that will be passed to the view
                    if (!empty($contentTypeName)) {
                        $this->tpl->assign('content',$content);
                        $this->tpl->assign('params',$this->params);
                        try {
                            $output .= $this->tpl->fetch(strtolower($content->content_type_name).'/content-provider/'.strtolower($content->content_type_name).".tpl")."\n";
                        } catch (\SmartyException $e) {
                            // Do nothing.
                        }
                    }
                }
            }
            return $output;
        }
    }

    /**
     * Renders the frontpage layout.
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
        foreach ($this->layoutDoc as $element => $value ) {
            $output []= $this->renderElement($element, $value, false);
        }
        return implode("\n", $output);
    }

}