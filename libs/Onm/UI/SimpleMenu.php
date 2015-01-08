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
     * The nesting level when traversing the menu
     *
     * @var int
     **/
    private $nestingLevel = 0;

    /**
     * Initilizes the object from an XML file
     *
     * @param string $menuArray the array with menu contents
     * @param string $baseUrl the base url for the links
     *
     * @return void
     */
    public function __construct($menuArray, $baseUrl = null)
    {
        if (!isset($baseUrl)) {
            $baseUrl = SITE_URL_ADMIN;
        }
        $this->baseUrl = $baseUrl;

        $this->menu = $menuArray;
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
        foreach ($this->menu as $element) {
            list($content, $isCurrent) = $this->renderElement($element);
            $output []= $content;
        }

        $menu = "<ul>".implode("", $output)."</ul>";

        return $menu;
    }

    /**
     * Renders an element
     *
     * @param string $element the element name
     * @param SimpleXMLElement $element the element to render
     * @param boolean $last whether this element is the last in the list
     *
     * @return string the generated HTML
     */
    private function renderElement($element)
    {
        if (in_array('separator', $element)) {
            return $this->renderSeparator();
        }

        $output = '';
        $submenuContent = '';
        $isCurrent = $isSubmenuCurrent = false;

        // Render submenu
        $hasSubmenu = array_key_exists('submenu', $element);
        if ($hasSubmenu) {
            $submenu = $element['submenu'];

            $submenuContent = [];
            foreach ($submenu as $subMenuElement) {
                list($content, $isSubmenuElementCurrent) = $this->renderElement($subMenuElement);
                $isSubmenuCurrent = $isSubmenuCurrent || $isSubmenuElementCurrent;

                $submenuContent []= $content;
            }

            $submenuContent = "<ul class='sub-menu'>".implode('', $submenuContent)."</ul>";
        }

        // Render node content
        if (\Onm\Module\ModuleManager::isActivated( $element['module_name'])
            && (!isset($element['privilege']) || $this->checkAcl($element['privilege']))
        ) {
            $isCurrent = preg_match("@^".preg_quote($element['link'])."@", $_SERVER['REQUEST_URI']);

            $classes = [];

            if (!empty($this->getClass($element['class']))) {
                $classes []= $this->getClass($element['class']);
            }

            if ($isCurrent || $isSubmenuCurrent) {
                $classes []= 'active';
            }

            $class = '';
            if (!empty($classes)) {
                $class = 'class ="'.implode(' ', $classes).'"';
            }

            $output = "<li {$class}>"
                    .$this->getHref($element, $hasSubmenu)
                    .$submenuContent
                    ."</li>";
        }

        return [$output, ($isCurrent || $isSubmenuCurrent)];
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
    private function getHref($element, $hasArrow)
    {
        $id       = 'submenu_'.$element['id'];
        $url      = $element['link'];
        $title    = $element['title'];
        $external = isset($element['target']);

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
        $attrId    = "id=\"".sprintf(_("%s"), $id)."\"";

        $arrow = '';
        if ($hasArrow) {
            $arrow = "<span class='arrow' ></span>";
        }

        $icon = '<i class="fa" ></i>';
        if (array_key_exists('icon', $element)) {
            $icon = '<i class="'.$element['icon'].'" ></i>';
        }

        return "<a href=\"$url\" $target $attrTitle $attrId $class $dataToggle>
                    $icon
                    <span class=\"title\">$title</span>
                    $arrow
                </a>";
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
     * undocumented function
     *
     * @return void
     * @author
     **/
    public function renderSeparator()
    {
        return '<span class="separator"></span>';
    }
}
