<?php

class WidgetFactory
{
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
     * Initializes the WidgetFactory object instance
     *
     * @param mixed   $content The widget to initializate factory from.
     * @param boolean $useDB   Whether to use the database.
     *
     * @return WidgetFactory The current instance.
     */
    public function __construct($content = null, $useDB = true)
    {
        if ($useDB) {
            $this->cm = new ContentManager();
            $this->ccm = ContentCategoryManager::get_instance();
        }

        $this->cachedId = get_class($this);

        $this->content = $content;

        // Append the widget id to the cached id
        if ($this->content && $this->content->pk_content) {
            $this->cachedId .= '-' . $this->content->pk_content;
        }

        $this->tpl = new Template(TEMPLATE_USER);

        $this->tpl->caching       = 0;
        $this->tpl->force_compile = true;

        // Assign a random number to diferenciate instances of the same widget
        $this->tpl->assign('rnd_number', rand(5, 900));

        return $this;
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
        if (is_string($this->content->params)) {
            $this->content->params = unserialize($this->content->params);
        }

        // Merge parameters if they are a valid array
        if (is_array($this->content->params)) {
            $this->params = array_merge($this->params, $this->content->params);
        }

        // Parse parameters from template
        if (is_string($params)) {
            $params = explode(',', $params);
        }

        // Merge parameters if they are a valid array
        if (is_array($params)) {
            $this->params = array_merge($this->params, $params);
        }

        foreach ($this->params as $key => $param) {
            if (is_string($param) && strpos($param, ',') !== false) {
                $this->params[$key] = explode(',', $param);
            }
        }
    }
}
