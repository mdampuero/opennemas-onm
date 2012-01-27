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
 * @version    SVN: $Id: simple_menu.class.php 28842 Mér Xuñ 22 16:37:26 2011 frandieguez $
 */
class SimpleMenu {

    private $menu = null;
    private $errors = null;

    /**
     * Initilizes the object from an XML file
     *
     * @param string $menuXMLFile   the path to the XML menu file
     *
     * @return void
     */
    public function __construct($menuXMLFile, $baseUrl = null) {

        $menu = simplexml_load_string($menuXMLFile);

        if (!isset($baseUrl)) {
            $baseUrl = SITE_URL_ADMIN;
        }
        $this->baseUrl = $baseUrl;

        // If there were errors while loading the menu store them
        // otherwise store the menu
        if (!$menu) {

            $errors =  "Failed loading XML of Menu\n";
            foreach(libxml_get_errors() as $error) {
                $errors .= "\t".$error->message."\n";
            }
            $this->errors = $errors;

        } else {
            $this->menu = $menu;
        }

    }



    /**
     * Returns the HTML for a given XML menu file
     *
     * @param array  $params     the params for this function
     *
     * @return string    the HTML for this menu
     */
    public function getHTML($params = array()) {

        if(is_null($this->errors)) {

            $html = "";
            foreach($this->menu as $menu) {

                // Check if the user can se this menu and module activated
                if  (
                    (!isset($menu['privilege']) || $this->checkAcl($menu['privilege']))
                    && (\Onm\Module\ModuleManager::isActivated((string)$menu['module_name']))
                    )
                {
                    $class = $this->getclass($menu['class']);
                    $html.= "<li {$class}>";
                    $html .= $this->getHref($menu['title'], 'menu_'.$menu['id'], $menu['link']);

                    // If there are elements in this submenu and user can see it, print them
                    if ( $menu->count() > 0 )
                    {

                        $html .= "<ul>";

                        foreach($menu as $submenu) {
                            if (
                                (!isset($submenu['privilege']) || $this->checkAcl($submenu['privilege']))
                                && (\Onm\Module\ModuleManager::isActivated((string)$submenu['module_name']))
                                )
                            {
                                if (($submenu['privilege']!='ONLY_MASTERS') || ($submenu['privilege']=='ONLY_MASTERS') && \Acl::isMaster() ) {
                                    $external = isset($submenu['target']);
                                    $class = $this->getclass($submenu['class']);
                                    $html.= "<li {$class}>";
                                        $html .= $this->getHref($submenu['title'], 'submenu_'.$submenu['id'], $submenu['link'], $external);
                                    $html.= "</li>";
                                }
                            }
                        }

                        $html .= "</ul>";
                    }

                    $html.= "</li>";
                }
            }
            $output = "<ul id='menu' class='clearfix'>".$html."</ul>";

            return $output;

        } else {
            return $this->errors;
        }

    }

    private function getClass($class)
    {
        if (isset($class) && !empty($class)) {
            return "class=\"{$class}\"";
        }

    }

    private function getHref($title, $id, $url, $external = false) {
        if (empty($title)
            && empty($url))
        {
            return;
        }
        if (preg_match("@#@",$url)) {
            $url = $url;
        }
        if (!preg_match("@^http@",$url) && !preg_match("@#@",$url)) {
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

    private  function checkAcl($privilege)
    {
        if(isset($privilege) && !is_null($privilege)) {
            $privs = explode(',', $privilege);
            $test = false;
            foreach($privs as $priv) {
                $test = $test || \Acl::check($priv);
            }

            return $test;
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
        // var_dump($value);die();
        switch ($element) {
            case 'submenu':
                $output []= $this->_renderSubMenu($element, $value, $last);
                break;

            case 'node':
                $output []= $this->_renderNode($element, $value, $last);
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
     * @author
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
            $class = $this->getclass($menu['class']);
            $html .= "<li {$class}>";
            $html .= $this->getHref($value['title'], 'menu_'.$value['id'], $value['link']);
            $html .= "<ul>".implode("\n", $output)."</ul>";
            $html .="</li>";
        }
        return $html;
    }

    /**
     * Function for rendering one menu node
     *
     * @return void
     * @author
     **/
    private function _renderNode($element, $value, $last)
    {
        $html = null;
        if (
            (!isset($value['privilege']) || $this->checkAcl($value['privilege']))
            && (\Onm\Module\ModuleManager::isActivated((string)$value['module_name']))
            )
        {
            if (($value['privilege']!='ONLY_MASTERS') || ($value['privilege']=='ONLY_MASTERS') && \Acl::isMaster() ) {
                $external = isset($value['target']);
                $class = $this->getclass($value['class']);
                $html.= "<li {$class}>";
                    $html .= $this->getHref($value['title'], 'submenu_'.$value['id'], $value['link'], $external);
                $html.= "</li>";
            }
        }

        return $html;

    }


    /**
     * Renders the menu
     *
     * @package    Onm
     * @subpackage Common
     * @author     me
     **/
    public function render($params = array())
    {

        if (isset($params['contents'])) {
            $this->contents = $params['contents'];
        }

        $output = '';
        foreach ($this->menu as $element => $value ) {
            $output []= $this->_renderElement($element, $value, false);
        }

        $menu = "<ul id='menu' class='clearfix'>".implode("\n", $output)."</ul>";
        if ($params['doctype']) {
            $menu = "<nav>".$menu."</nav>";
        }

        return $menu;
    }

}
