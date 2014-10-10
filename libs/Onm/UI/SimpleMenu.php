<?php
/**
 * Defines the Onm\Ui\SimpleMenu class
 *
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package    Onm_UI
 */
namespace Onm\UI;

use Onm\Security\Acl;

/**
 * Class for generate a menu from XML file, with support for ACLs system.
 *
 * @package    Onm_UI
 */
class SimpleMenu
{
    /**
     * The menu to render
     *
     * @var array
     **/
    private $menu         = null;

    /**
     * Errors while parsing the menu
     *
     * @var array
     **/
    private $errors       = null;

    /**
     * The nesting level when traversing the menu
     *
     * @var int
     **/
    private $nestingLevel = 0;

    /**
     * Initilizes the object from an XML file
     *
     * @param string $menuXMLFile the path to the XML menu file
     * @param string $baseUrl the base url for the links
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
            $this->errors = $errors;

        } else {
            $this->menu = $menu;
        }

    }

    /**
     * Returns the class HTML property given a set of properties
     *
     * @param string $class the element class
     * @param boolean $dropdown whether if the element is a dropdown element
     *
     * @return string the HTML generated
     **/
    private function getClass($class, $dropdown = false)
    {
        if (isset($class) && !empty($class) || $dropdown) {
            if ($dropdown) {
                if ($this->nestingLevel > 1) {
                    $dropdownClass = ' dropdown-submenu';
                } else {
                    $dropdownClass = ' dropdown';
                }
            }

            return "class=\"{$class}{$dropdownClass}\"";
        }
    }

    /**
     * Returns the <a> tag for an element given a set of properties
     *
     * @param string $title the title property
     * @param string $id    the id property
     * @param string $url   the link url
     * @param boolean $external whether this link is external
     * @param boolean $toggle whether if it is a toggle element
     *
     * @return string the HTML generated
     **/
    private function getHref($title, $id, $url, $external = false, $toggle = false)
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

        if ($toggle) {
            $class = 'class="dropdown-toggle"';
            $dataToggle = 'data-toggle="dropdown"';
        }

        $attrTitle = "title=\"".sprintf(_("Go to %s"), $title)."\"";
        $attrId = "id=\"".sprintf(_("%s"), $id)."\"";

        return "<a href=\"$url\" $target $attrTitle $attrId $class $dataToggle>".$title."</a>";
    }

    /**
     * Checks if the user has access to this element
     *
     * @param string $privilege the menu element privilege
     *
     * @return boolean true if the user has access
     **/
    private function checkAcl($privilege)
    {
        if (isset($privilege) && !is_null($privilege)) {
            $privileges = explode(',', $privilege);
            $hasAccess = false;
            foreach ($privileges as $priv) {
                $hasAccess = $hasAccess || Acl::checkPrivileges($priv);
            }

            return $hasAccess;
        }

        return true;
    }

    /**
     * Renders an element
     *
     * @param string $element the element name
     * @param SimpleXMLElement $value the element to render
     * @param boolean $last whether this element is the last in the list
     *
     * @return string the generated HTML
     */
    private function renderElement($element, $value)
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
     * @param string $element the element name
     * @param SimpleXMLElement $value the element to render
     *
     * @return string the generated HTML
     **/
    private function renderSubMenu($element, $value)
    {
        $this->nestingLevel++;
        $hasSubmenu = false;
        foreach ($value as $element => $submenuContent) {
            $element = $this->renderElement($element, $submenuContent);
            if (!empty($element)) {
                $hasSubmenu = true;
                $output []= $element;
            }
        }

        if ($hasSubmenu) {
            $class = $this->getClass($value['class'], $hasSubmenu);

            if ($this->nestingLevel > 1) {
                $dropdownClass = ' dropdown-submenu';
                $dropdownClassUl = ' dropdown-menu';
            } else {
                $dropdownClass = ' dropdown-menu';
                $dropdownClassUl = ' dropdown-menu';
            }

            $html  = "<li {$class}>"
                    .$this->getHref(
                        $value['title'],
                        'menu_'.$value['id'],
                        $value['link'],
                        false,
                        $hasSubmenu
                    )
                    . "<ul class='".$dropdownClassUl."'>".implode("", $output)."</ul>"
                    . "</li>";
        }
        $this->nestingLevel--;

        return $html;
    }

    /**
     * Function for rendering one menu node
     *
     * @param SimpleXMLElement $value the node to render
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
                || ($value['privilege']=='ONLY_MASTERS') && Acl::isMaster()
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
     * Renders the menu given a set of params
     *
     * @param array $params the list of params used to render the menu
     *
     * @return string the final html content for the menu
     **/
    public function render($params = array())
    {
        if (isset($params['contents'])) {
            $this->contents = $params['contents'];
        }

        $output = '';
        foreach ($this->menu as $element => $value) {
            $output []= $this->renderElement($element, $value);
        }

        $menu = "<ul id='menu' class='nav'>"
              . implode("", $output)."</ul>";
        // if ($params['doctype']) {
        //     $menu = "<nav>".$menu."</nav>";
        // }

        return $menu;
    }
}
