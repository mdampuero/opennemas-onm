<?php
/**
 * SimpleMEnu
 *
 * @category Onm
 * @package Onm_UI
 * @subpackage Menu
 * @copyright Copyright (c) 2005-2010 OpenHost S.L. http://www.openhost.es)
 * @license http://framework.zend.com/license
 * @version    $Id: simple_menu.class.php 1 2011-02-23 01:37:48Z frandieguez $
 */
class SimpleMenu {

    private $menu = null;
    private $errors = null;

    /**
    * Short description
    *
    * @param type $paramName[, explanation of the variable]
    * @return type[, explanation]
    * @throws ExceptionClass [description]
    */
    public function __construct($menuXML) {

        $menu = simplexml_load_string($menuXML);

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
    * Short description
    *
    * @param type $paramName[, explanation of the variable]
    * @return type[, explanation]
    * @throws ExceptionClass [description]
    */
    public function getHTML($params = array()) {

        if(is_null($this->errors)) {

            $html = "";
            foreach($this->menu as $menu) {

                // Check if the user can se this menu
                if (!isset($menu['privilege'])
                   || $this->checkAcl($menu['privilege']))
                {

                    $html.= "<li>";
                    $html .= $this->getHref($menu['title'], $menu['link']);

                    // If there are elements in this submenu and user can see it, print them
                    if ( $menu->count() > 0
                        && (!isset($menu['privilege']) || $this->checkAcl($menu['privilege']))
                        )
                    {

                        $html .= "<ul>";

                        foreach($menu as $submenu) {
                            $html.= "<li>";
                                $html .= $this->getHref($submenu['title'],$submenu['link']);
                            $html.= "</li>";
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

    private function getHref($title, $url) {
        return "<a href=\"".SITE_URL_ADMIN."/$url\">".$title."</a>";
    }

    private  function checkAcl($privilege)
    {
        if(isset($privilege) && !is_null($privilege)) {
            $privs = explode(',', $privilege);
            $test = false;
            foreach($privs as $priv) {
                $test = $test || Acl::check($priv);
            }

            return $test;
        }

        return true;
    }

}
