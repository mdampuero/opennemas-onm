<?php
/*
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Onm\UI;

/**
 * Class for generate a menu from XML file, with support for ACLs system.
 *
 * @package    Onm
 * @subpackage UI
 */
class SimpleMenu
{

    private $_menu = null;
    private $_errors = null;

    /**
     * Initilizes the object from an XML file
     *
     * @param string $menuXMLFile the path to the XML menu file
     *
     * @return void
     */
    public function __construct($menuXMLFile, $baseUrl = null)
    {

        $menu = simplexml_load_string($menuXMLFile);

        if (!isset($baseUrl)) {
            $baseUrl = SITE_URL_ADMIN;
        }
        $this->baseUrl = $baseUrl;

        // If there were errors while loading the menu store them
        // otherwise store the menu
        if (!$menu) {

            $errors =  "Failed loading XML of Menu\n";
            foreach (libxml_get_errors() as $error) {
                $errors .= "\t".$error->message."\n";
            }
            $this->_errors = $errors;

        } else {
            $this->_menu = $menu;
        }

    }

    private function getClass($class)
    {
        if (isset($class) && !empty($class)) {

            return "class=\"{$class}\"";
        }
    }

    private function getHref($title, $id, $url, $external = false)
    {
        if (empty($title) && empty($url)) {
            return;
        }
        if (preg_match("@#@", $url)) {
            $url = $url;
        }
        $url = htmlentities($url);

        $target = '';
        if ($external) {
            $target = "target=\"_blank\"";
        }

        $attrTitle = "title=\"".sprintf(_("Go to %s"), $title)."\"";
        $attrId = "id=\"".sprintf(_("%s"), $id)."\"";

        return "<a href=\"$url\" $target $attrTitle $attrId>".$title."</a>";
    }

    private function checkAcl($privilege)
    {
        if (isset($privilege) && !is_null($privilege)) {
            $privileges = explode(',', $privilege);
            $hasAccess = false;
            foreach ($privileges as $priv) {
                $hasAccess = $hasAccess || \Acl::check($priv);
            }

            return $hasAccess;
        }

        return true;
    }

    /*
     * Renders wrapper
     *
     * @param $element
     */
    private function renderElement($element, $value, $last)
    {
        $output =  array();
        switch ($element) {
            case 'submenu':
                $output []= $this->renderSubMenu($element, $value);

                break;
            case 'node':
                $output []= $this->renderNode($value);

                break;
            default:
                # code...
                break;
        }

        return implode("\n", $output);
    }

    /**
     * Recursive function to render a SubMenu and its contents
     *
     * @return void
     **/
    private function renderSubMenu($element, $value)
    {
        foreach ($value as $element => $submenuContent) {
            $element = $this->renderElement($element, $submenuContent, false);
            if (!empty($element)) {
                $output []= $element;
            }
        }

        if (count($output) > 0) {
            $class = $this->getClass($value['class']);
            $html  = "<li {$class}>"
                    .$this->getHref(
                        $value['title'],
                        'menu_'.$value['id'],
                        $value['link']
                    )
                    . "<ul>".implode("", $output)."</ul>"
                    . "</li>";
        }

        return $html;
    }

    /**
     * Function for rendering one menu node
     *
     * @return string the html content for a node
     **/
    private function renderNode($value)
    {
        $html = null;
        if ((!isset($value['privilege']) || $this->checkAcl($value['privilege']))
            && (\Onm\Module\ModuleManager::isActivated((string) $value['module_name']))
        ) {
            if (($value['privilege']!='ONLY_MASTERS')
                || ($value['privilege']=='ONLY_MASTERS') && \Acl::isMaster()
            ) {
                $external = isset($value['target']);
                $class = $this->getClass($value['class']);
                $html .= "<li {$class}>"
                        .$this->getHref(
                            $value['title'],
                            'submenu_'.$value['id'],
                            $value['link'],
                            $external
                        )
                        ."</li>";
            }
        }

        return $html;
    }

    /**
     * Renders the menu
     *
     * @return string the final html content for the menu
     **/
    public function render($params = array())
    {
        if (isset($params['contents'])) {
            $this->contents = $params['contents'];
        }

        $output = '';
        foreach ($this->_menu as $element => $value) {
            $output []= $this->renderElement($element, $value, false);
        }

        $menu = "<ul id='menu' class='clearfix'>"
              . implode("", $output)."</ul>";
        if ($params['doctype']) {
            $menu = "<nav>".$menu."</nav>";
        }

        return $menu;
    }
}

