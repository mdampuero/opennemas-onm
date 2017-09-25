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
 */

/**
 * Handles all the CRUD actions over widgets.
 *
 * @package    Model
 *
 */
class Widget extends Content
{
    /**
     * The widget id
     *
     * @var int
     */
    public $pk_widget = null;

    /**
     * The content of the widget
     *
     * @var string
     */
    public $content = null;

    /**
     * The type of widget
     *
     * @var
     */
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
     * Creates a new widget from a data array
     *
     * @param array $data the widget data
     *
     * @return boolean true if the widget was created
     */
    public function create($data)
    {
        $data['category'] = 0;

        if ($data['renderlet'] != 'html' && $data['renderlet'] != 'smarty') {
            $data['content'] = strip_tags($data['content']);
        }

        $conn = getService('dbal_connection');

        try {
            $conn->beginTransaction();
            parent::create($data);

            $conn->insert(
                'widgets',
                [
                    'pk_widget' => $this->id,
                    'content' => $data['content'],
                    'renderlet' => $data['renderlet'],
                ]
            );
            $conn->commit();

            return true;
        } catch (\Exception $e) {
            $conn->rollback();
            error_log($e->getMessage());
            return false;
        }
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
        // If no valid id then return
        if (((int) $id) <= 0) {
            return;
        }

        try {
            $rs = getService('dbal_connection')->fetchAssoc(
                'SELECT * FROM contents '
                . 'LEFT JOIN widgets ON pk_content = pk_widget WHERE pk_content = ?',
                [ $id ]
            );

            if (!$rs) {
                return false;
            }

            $this->load($rs);
            $this->loadAllContentProperties();

            return $this;
        } catch (\Exception $e) {
            return false;
        }
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

        if ($data['renderlet'] != 'html' && $data['renderlet'] != 'smarty') {
            $data['content'] = strip_tags($data['content']);
        }

        $conn = getService('dbal_connection');
        try {
            $conn->beginTransaction();
            parent::update($data);

            $conn->update(
                'widgets',
                [
                    'content'   => $data['content'],
                    'renderlet' => $data['renderlet'],
                ],
                [ 'pk_widget' => $data['id'] ]
            );
            $conn->commit();

            return true;
        } catch (\Exception $e) {
            $conn->rollback();
            error_log($e->getMessage());
            return false;
        }
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
        try {
            if (!parent::remove($id)) {
                return false;
            }

            getService('dbal_connection')->delete('widgets', [ 'pk_widget' => $id ]);

            return true;
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * Renders the widget given a set of params
     *
     * @param array $params a list of params to pass to the widget rendering
     *
     * @return string the generated HTML
     */
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

        return "<div class=\"widget\">" . $content . "</div>";
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
        $resource = 'string:' . $this->content;
        $wgtTpl   = getService('core.template');

        // no caching
        $wgtTpl->caching       = 0;
        $wgtTpl->force_compile = true;

        $output = $wgtTpl->fetch($resource, [ 'widget' => $this->content ]);

        return $output;
    }

    /**
     * Renders an intelligent wiget
     *
     * @param array $params parameters for rendering the widget
     *
     * @return string the generated HTML
     */
    private function renderletIntelligentWidget($params = null)
    {
        $class = $this->factoryWidget($params);

        if (is_null($class)) {
            return sprintf(_("Widget %s not available"), $this->content);
        }

        return $class->render($params);
    }

    /**
     * Returns an instance for a widget
     *
     * @param array $params parameters for rendering the widget
     *
     * @return Object the widget instance
     */
    public function factoryWidget($params = null)
    {
        getService('widget_repository')->loadWidget($this->content);

        $class = 'Widget' . $this->content;

        if (!class_exists($class)) {
            return null;
        }

        $class = new $class($this);

        $class->parseParams($params);

        return $class;
    }
}
