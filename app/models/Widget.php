<?php
/**
 * Defines the Widget class
 *
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package    Model
 *
 **/

/**
 * Handles all the CRUD actions over widgets.
 *
 * @package    Model
 *
 **/
class Widget extends Content
{
    /**
     * The widget id
     *
     * @var int
     **/
    public $pk_widget = null;

    /**
     * The content of the widget
     *
     * @var string
     **/
    public $content = null;

    /**
     * The type of widget
     *
     * @var
     **/
    public $renderlet = null;

    /**
     * Intitilizes the object instance
     *
     * @param int $id the Widget id
     */
    public function __construct($id = null)
    {
        $this->content_type_l10n_name = _('Widget');

        parent::__construct($id);
    }

    /**
     * Load properties into this instance
     *
     * @param array $properties Array properties
     */
    public function load($properties)
    {
        if (is_array($properties)) {
            foreach ($properties as $k => $v) {

                if (!is_numeric($k)) {
                    $this->{$k} = $v;
                }
            }
        } elseif (is_object($properties)) {
            $properties = get_object_vars($properties);
            foreach ($properties as $k => $v) {

                if (!is_numeric($k)) {
                    $this->{$k} = $v;
                }
            }
        }
        $this->id = $this->pk_widget;
    }

    /**
     * Creates a new widget from a data array
     *
     * @param array $data the widget data
     *
     * @return boolean true if the widget was created
     **/
    public function create($data)
    {
        $data['category'] = 0;

        // Start transaction
        $GLOBALS['application']->conn->BeginTrans();
        parent::create($data);

        if ($data['renderlet'] != 'html' && $data['renderlet'] != 'smarty') {
            $data['content'] = strip_tags($data['content']);
        }

        $rs = $GLOBALS['application']->conn->Execute(
            'INSERT INTO widgets (`pk_widget`, `content`, `renderlet`) VALUES (?, ?, ?)',
            array($this->id, $data['content'], $data['renderlet'])
        );

        if ($rs === false) {
            $GLOBALS['application']->conn->RollbackTrans();

            return false;
        }

        $GLOBALS['application']->conn->CommitTrans();

        return true;
    }

    /**
     * Read, get a specific object
     *
     * @param  int    $id Object ID
     *
     * @return Widget Return instance to chaining method
     */
    public function read($id)
    {
        parent::read($id);

        $this->id = $id;

        $rs = $GLOBALS['application']->conn->Execute(
            "SELECT * FROM `widgets` WHERE `pk_widget`=?",
            array($id)
        );

        if ($rs === false) {
            return null;
        }
        $this->loadAllContentProperties();

        $this->load($rs->fields);
    }

    /**
     * Updates the widget information
     *
     * @param  array $data Array values
     *
     * @return boolean true if all went well
     */
    public function update($data)
    {
        $data['category'] = 0;

        parent::update($data);

        $sql = "UPDATE `widgets` SET `content`=?, `renderlet`=? WHERE `pk_widget`=?";

        if ($data['renderlet'] != 'html'  && $data['renderlet'] != 'smarty') {
            $data['content'] = strip_tags($data['content']);
        }
        $values = array($data['content'], $data['renderlet'], $data['id']);

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {

            return false;
        }

        return true;
    }

    /**
     * Delete permanently a widget given its id
     *
     * @param int $id Identifier
     * @param int $editor the user id
     *
     * @return boolean true if the widget was removed
     */
    public function remove($id, $editor = null)
    {
        $sql = "DELETE FROM `widgets` WHERE `pk_widget`=?";
        parent::remove($id); // Delete from database, don't use trash

        $values = array($id);

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            return false;
        }

        return true;
    }

    /**
     * Read, get a specific object
     *
     * @param  int    $content the widget name
     *
     * @return Widget Return instance to chaining method
     */
    public function readIntelligentFromName($content)
    {
        $sqlSearchWidget = "SELECT * FROM `widgets` WHERE `content`=?";
        $rs = $GLOBALS['application']->conn->Execute(
            $sqlSearchWidget,
            $content
        );

        if ($rs === false) {
            return null;
        }
        $id = $rs->fields['pk_widget'];
        parent::read($id);
        $this->id = array($id);
        $sql      = "SELECT * FROM `widgets` WHERE `pk_widget`=?";
        $values   = array($id);
        $this->loadAllContentProperties();

        $rs     = $GLOBALS['application']->conn->Execute($sql, $values);
        if ($rs === false) {
            return null;
        }
        $this->load($rs->fields);
        $this->id = array($id);
    }

    /**
     * Returns the list of all the available widgets
     *
     * @return array the list of available widgets
     **/
    public static function getAllInteligentWidgets()
    {
        $paths = array();
        $paths[] = realpath(TEMPLATE_USER_PATH . '/tpl' . '/widgets') . '/';
        $instanceManager = getService('instance_manager');
        $baseTheme = $instanceManager->current_instance->theme->getParentTheme();

        if (!empty($baseTheme)) {
             $paths[] = SITE_PATH.DS.'themes'.DS.$baseTheme.DS.'tpl/widgets/';
        }
        $paths[] = SITE_PATH.'themes'.DS.'base'.DS.'tpl/widgets/';

        $allWidgets = array();

        foreach ($paths as $path) {
            if (is_dir($path) && $path != '/') {
                $objects = scandir($path);
                foreach ($objects as $object) {
                    if ($object != "." && $object != "..") {
                        if (preg_match('@^widget_(.)*\.tpl$@', $object)) {
                            $objectWords = explode('_', substr($object, 7, -10));
                            $name = '';
                            foreach ($objectWords as $word) {
                                $name .= ucfirst($word);
                            }
                            if (!in_array($name, $allWidgets)) {
                                $allWidgets[] .=  $name;
                            }
                        }
                    }
                }
            }
        }

        return $allWidgets;
    }

    /**
     * Renders the widget given a set of params
     *
     * @param array $params a list of params to pass to the widget rendering
     *
     * @return string the generated HTML
     **/
    public function render($params = null)
    {
        switch ($this->renderlet) {
            case 'html':
                $content = $this->content;

                break;
            case 'smarty':
                $content = $this->renderletSmarty($params);

                break;
            case 'intelligentwidget':
                $content = $this->renderletIntelligentWidget($params);

                break;
            default:
                $content = '';

                break;
        }

        return "<div class=\"widget\">" .$content. "</div>";
    }

    /**
     * Renders a HTML wiget
     *
     * @return string the generated HTML
     *
     * @see resource.string.php Smarty plugin
     * @see resource.widget.php Smarty plugin
     */
    private function renderletSmarty()
    {
        Template::$registry['widget'][$this->pk_widget] = $this->content;
        $resource = 'string:' . $this->content;
        $wgtTpl = new Template(TEMPLATE_USER);

        // no caching
        $wgtTpl->caching = 0;
        $wgtTpl->force_compile = true;
        $output = $wgtTpl->fetch($resource);

        return $output;
    }

    /**
     * Renders an intelligent wiget
     *
     * @param array $params parameters for rendering the widget
     *
     * @return string the generated HTML
     **/
    private function renderletIntelligentWidget($params = null)
    {
        $paths = array();
        $paths[] = realpath(TEMPLATE_USER_PATH . '/tpl' . '/widgets') . '/';
        $instanceManager = getService('instance_manager');
        $baseTheme = $instanceManager->current_instance->theme->getParentTheme();

        if (!empty($baseTheme)) {
             $paths[] = SITE_PATH.DS.'themes'.DS.$baseTheme.DS.'tpl/widgets/';
        }
        $paths[] = SITE_PATH.'themes'.DS.'base'.DS.'tpl/widgets/';

        $className = 'Widget' . $this->content;
        $filename = strtolower($className);

        foreach ($paths as $path) {
            if ($path != '/') {
                ini_set('include_path', get_include_path() . PATH_SEPARATOR . $path);
                if (file_exists($path . '/' . $filename . '.class.php')) {
                    require_once $path . '/' . $filename . '.class.php';
                    break;
                } else {
                    $filename = strtolower(
                        preg_replace('/([a-z])([A-Z])/', '$1_$2', $className)
                    );

                    if (file_exists($path . '/' . $filename . '.class.php')) {
                        require_once $path . '/' . $filename . '.class.php';
                        break;
                    }
                }
            }
        }

        try {
            if (class_exists($className)) {

                $er = getService('entity_repository');
                $widget = $er->find('Widget', $this->id);

                $class = new $className($widget);
            } else {
                throw new Exception('', 1);

            }
        } catch (Exception $e) {
            return sprintf(_("Widget %s not available"), $this->content);
        }

        return $class->render($params);
    }
}
