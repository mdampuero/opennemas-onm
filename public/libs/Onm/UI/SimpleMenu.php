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
    public function __construct($menuXMLFile) {

        $menu = simplexml_load_string($menuXMLFile);

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

                    $html.= "<li>";
                    $html .= $this->getHref($menu['title'], $menu['link']);

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

                                $external = isset($submenu['target']);
                                $html.= "<li>";
                                    $html .= $this->getHref($submenu['title'],$submenu['link'], $external);
                                $html.= "</li>";
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

    private function getHref($title, $url, $external = false) {
        if (preg_match("@#@",$url)) {
            $url = $url;
        }
        if (!preg_match("@^http@",$url) && !preg_match("@#@",$url)) {
            $url = SITE_URL_ADMIN."/".$url;
        }

        $target = '';
        if ($external) {
            $target = "target=\"_blank\"";
        }

        $attrTitle = "title=\"".sprintf(_("Go to %s"), $title)."\"";

        return "<a href=\"$url\" $target $attrTitle>".$title."</a>";
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

}
