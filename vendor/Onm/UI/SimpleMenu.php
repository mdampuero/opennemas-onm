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
 * @author     Fran Dieguez <fran@openhost.es>
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

    private function _getClass($class)
    {
        if (isset($class) && !empty($class)) {

            return "class=\"{$class}\"";
        }
    }

    private function _getHref($title, $id, $url, $external = false)
    {
        if (empty($title) && empty($url)) {
            return;
        }
        if (preg_match("@#@", $url)) {
            $url = $url;
        }
        if (!preg_match("@^http@", $url) && !preg_match("@#@", $url)) {
            $url = $this->baseUrl."/".$url;
        }

        $target = '';
        if ($external) {
            $target = "target=\"_blank\"";
        }

        $attrTitle = "title=\"".sprintf(_("Go to %s"), $title)."\"";
        $attrId = "id=\"".sprintf(_("%s"), $id)."\"";

        return "<a href=\"$url\" $target $attrTitle $attrId>".$title."</a>";
    }

    private function _checkAcl($privilege)
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
    private function _renderElement($element, $value, $last)
    {
        $output =  array();
        switch ($element) {
            case 'submenu':
                $output []= $this->_renderSubMenu($element, $value, $last);
                break;

            case 'node':
                $output []= $this->_renderNode($value, $last);
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
    private function _renderSubMenu($element, $value, $last)
    {
        foreach ($value as $element => $submenuContent ) {
            $element = $this->_renderElement($element, $submenuContent, false);
            if (!empty($element)) {
                $output []= $element;
            }
        }

        if (count($output) > 0) {
            $class = $this->_getClass($value['class']);
            $html  = "<li {$class}>"
                   . $this->_getHref($value['title'],
                        'menu_'.$value['id'], $value['link'])
                   . "<ul>".implode("\n", $output)."</ul>"
                   . "</li>";
        }

        return $html;
    }

    /**
     * Function for rendering one menu node
     *
     * @return string the html content for a node
     **/
    private function _renderNode($value, $last)
    {
        $html = null;
        if ((!isset($value['privilege']) || $this->_checkAcl($value['privilege']))
            && (\Onm\Module\ModuleManager::isActivated((string)$value['module_name']))
        ) {
            if (($value['privilege']!='ONLY_MASTERS')
                || ($value['privilege']=='ONLY_MASTERS') && \Acl::isMaster()
            ) {
                $external = isset($value['target']);
                $class = $this->_getClass($value['class']);
                $html .= "<li {$class}>"
                      . $this->_getHref(
                            $value['title'], 'submenu_'.$value['id'],
                            $value['link'], $external
                        )
                      . "</li>";
            }
        }

        return $html;
    }

    /**
     * Renders an submenu
     *
     * @return string the html for the submenu
     **/
    private function _renderMenuComponent($submenu)
    {
        if ((!isset($submenu['privilege']) || $this->_checkAcl($submenu['privilege']))
            && (\Onm\Module\ModuleManager::isActivated((string)$submenu['module_name']))
        ) {
            if (($submenu['privilege']!='ONLY_MASTERS')
                || ($submenu['privilege']=='ONLY_MASTERS')
                && \Acl::isMaster()
            ) {
                $external = isset($submenu['target']);
                $class = $this->_getClass($submenu['class']);
                $html.= "<li {$class}>";
                    $html .= $this->_getHref(
                        $submenu['title'], 'submenu_'.$submenu['id'],
                        $submenu['link'], $external
                    );
                $html.= "</li>";
            }
        }

        return $html;
    }

    /**
     * Returns the HTML for a given XML menu file
     *
     * @param array $params the params for this function
     *
     * @return string the HTML for this menu
     */
    public function getHTML($params = array())
    {
        if (is_null($this->_errors)) {

            $html = "";
            foreach ($this->_menu as $menu) {

                // Check if the user can se this menu and module activated
                if (
                    (!isset($menu['privilege']) || $this->_checkAcl($menu['privilege']))
                    && (\Onm\Module\ModuleManager::isActivated((string)$menu['module_name']))
                ) {
                    $class = $this->_getClass($menu['class']);
                    $html .= "<li {$class}>"
                          . $this->_getHref($menu['title'], 'menu_'.$menu['id'], $menu['link']);

                    // If there are elements in this submenu and user can see it, print them
                    if ($menu->count() > 0) {

                        $html .= "<ul>";

                        foreach ($menu as $submenu) {
                            $this->_renderMenuComponent($submenu);
                        }

                        $html .= "</ul>";
                    }

                    $html.= "</li>";
                }
            }
            $output = "<ul id='menu' class='clearfix'>".$html."</ul>";

            return $output;

        } else {
            return $this->_errors;
        }
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
        foreach ($this->_menu as $element => $value ) {
            $output []= $this->_renderElement($element, $value, false);
        }

        $menu = "<ul id='menu' class='clearfix'>"
              . implode("\n", $output)."</ul>";
        if ($params['doctype']) {
            $menu = "<nav>".$menu."</nav>";
        }

        return $menu;
    }

}
