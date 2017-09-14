<?php

class WidgetFactory
{
    /**
     * The template for the widget form.
     *
     * @var string
     */
    public $form = null;

    /**
     * The params for the widget.
     *
     * @var array
     */
    public $params = [];

    /**
     * Default TTL for widgets cache.
     *
     * @var integer
     */
    public $ttl = 1800;

    /**
     * The service container.
     *
     * @var ServiceContainer
     */
    protected $container;

    /**
     * Initializes the WidgetFactory object instance
     *
     * @param ServiceContainer $container The service container.
     * @param mixed            $content   The widget object.
     *
     * @return WidgetFactory The current instance.
     */
    public function __construct($container, $content = null)
    {
        $this->cachedId  = get_class($this);
        $this->container = $container;
        $this->content   = $content;
        $this->tpl       = $this->container->get('core.template');

        // TODO: Remove when no usage in widgets
        $this->cm  = new ContentManager();
        $this->ccm = ContentCategoryManager::get_instance();

        // Append the widget id to the cached id
        if ($this->content && $this->content->pk_content) {
            $this->cachedId .= '-' . $this->content->pk_content;
        }

        $this->tpl->caching       = 0;
        $this->tpl->force_compile = true;

        // Assign a random number to diferenciate instances of the same widget
        $this->tpl->assign('rnd_number', rand(5, 900));

        return $this;
    }

    /**
     * Returns the form for widget parameters.
     *
     * @return string The widget form.
     */
    public function getForm()
    {
        if (empty($this->form)) {
            return '';
        }

        return $this->tpl->fetch($this->form);
    }

    /**
     * Returns a parameter given its name.
     *
     * @param string $name   The parameter name.
     * @param string $defaul The default value.
     *
     * @return mixed The parameter value.
     */
    public function getParameter($name, $default = false)
    {
        if ($this->params === null
            || !is_array($this->params)
            || !array_key_exists($name, $this->params)
        ) {
            return $default;
        }

        return $this->params[$name];
    }

    /**
     * Parse the given parameters and merge them with the current parameters.
     *
     * @param mixed $params The widget parameters.
     */
    public function parseParams($params)
    {
        // Unserialize widget params
        if (!empty($this->content) && is_string($this->content->params)) {
            $this->content->params = unserialize($this->content->params);
        }

        // Merge parameters if they are a valid array
        if (!empty($this->content) && is_array($this->content->params)) {
            $this->params = array_merge($this->params, $this->content->params);
        }

        // Merge parameters if they are a valid array
        if (is_array($params)) {
            $this->params = array_merge($this->params, $params);
        }
    }
}
