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
     * Default ttl for varnish cache of the widget.
     *
     * @var string
     */
    protected $defaultTtl = '100d';

    /**
     * The service container.
     *
     * @var ServiceContainer
     */
    protected $container;

    /**
     * Flag to indicate if the widget is cacheable.
     *
     * @var boolean
     */
    protected $isCacheable = true;

    /**
     * Flag to indicate if the widget is static.
     *
     * @var boolean
     */
    protected $isStatic = false;

    /**
     * Flag to indicate if the widget have custom contents.
     *
     * @var boolean
     */
    protected $isCustom = false;

    /**
     * The default content type of the widget.
     *
     * @var string
     */
    protected $defaultType = 'article';

    /**
     * The properties of the current widget that are suitable to be in the x-tags array.
     *
     * @var array
     */
    protected $propertiesMap = [
        'category'  => 'category',
        'tag_name'  => 'tag',
        'fk_author' => 'author'
    ];

    /**
     * Initializes the WidgetFactory object instance
     *
     * @param ServiceContainer $container The service container.
     * @param mixed            $content   The widget object.
     *
     * @return WidgetFactory The current instance.
     */
    public function __construct($content = null)
    {
        $this->cachedId  = get_class($this);
        $this->container = getService('service_container');
        $this->content   = $content;
        $this->tpl       = $this->container->get('core.template');

        // TODO: Remove when no usage in widgets
        $this->cm = new ContentManager();

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
     * Returns the content for the widget.
     *
     * @return string The widget content.
     */
    public function render()
    {
        $this->tpl->assign($this->params);

        return $this->tpl->fetch($this->template);
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
        // Merge parameters if they are a valid array
        if (!empty($this->content) && is_array($this->content->params)) {
            $this->params = array_merge($this->params, $this->content->params);
        }

        // Merge parameters if they are a valid array
        if (is_array($params)) {
            $this->params = array_merge($this->params, $params);
        }
    }

    /**
     * Loads the array of parameters with all the data required to render the template.
     */
    public function hydrateShow()
    {
    }

    /**
     * Returns an array with the x-tags for the specific widget.
     *
     * @return array The array with the x-tags for the specific widget.
     */
    public function getXTags()
    {
        $xtags = [ sprintf('widget-%s', $this->content->pk_content) ];

        if ($this->isStatic()) {
            return $xtags;
        }
        if (!$this->isCustom) {
            if (!array_key_exists('content_type', $this->params)) {
                $xtags[] = sprintf('content_type_name-widget-%s', $this->defaultType);
            } else {
                $xtags[] = is_array($this->params['content_type'])
                    ? sprintf('content_type_name-widget-%s', $this->params['content_type']['slug'])
                    : sprintf('content_type_name-widget-%s', underscore($this->params['content_type']));
            }

            foreach ($this->propertiesMap as $key => $value) {
                if (!array_key_exists($key, $this->params) || empty($this->params[$key])) {
                    $xtags[] = sprintf('%s-widget-all', $value);
                    continue;
                }

                if (!is_array($this->params[$key])) {
                    $xtags[] = sprintf('%s-widget-%s', $value, $this->params[$key]);
                    continue;
                }

                foreach ($this->params[$key] as $param) {
                    $xtags[] = sprintf('%s-widget-%d', $value, $param);
                }
            }
        }

        foreach ($this->params['contents'] as $content) {
            $xtags[] = sprintf('%s-%d', $content->content_type_name, $content->pk_content);
        }

        return $xtags;
    }

    /**
     * Returns the x-cache-for header for the specific widget.
     *
     * @return string The x-cache-for value for the widget.
     */
    public function getXCacheFor()
    {
        // Return 100d by default if there is no contents.
        if ($this->isStatic() || empty($this->params['contents'])) {
            return $this->defaultTtl;
        }

        $endtimes = array_filter(array_map(function ($content) {
            return $content->endtime;
        }, $this->params['contents']));

        sort($endtimes);

        $endtime = array_shift($endtimes) ?? null;

        if (empty($endtime)) {
            return $this->defaultTtl;
        }

        return $endtime->format('Y-m-d H:i:s');
    }

    /**
     * Returns true if the widget doesn't depend on contents and is static, false otherwise.
     *
     * @return boolean True if the widget is static, false otherwise.
     */
    public function isStatic()
    {
        return $this->isStatic;
    }

    /**
     * Returns true if the widget is cacheable, false otherwise.
     *
     * @return boolean True if the widget is cacheable, false otherwise.
     */
    public function isCacheable()
    {
        return $this->isCacheable;
    }

    /**
     * Returns the list of categories ready to use in a category selector in
     * the widget form.
     *
     * @return array The list of categories.
     */
    protected function getCategories()
    {
        $context = $this->container->get('core.locale')->getContext();
        $this->container->get('core.locale')->setContext('frontend');

        $items = $this->container->get('api.service.category')->getList();
        $this->container->get('core.locale')->setContext($context);

        $categories = [ 0 => _('Select a category...') ];

        foreach ($items['items'] as $category) {
            $categories[$category->name] = $category->title;
        }

        return $categories;
    }
}
