<?php

use Api\Exception\GetItemException;

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
     * The name of the widget keys storage.
     *
     * @var string
     */
    protected $keySetName = 'Widget_Keys';

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
     * The parameter to cache.
     *
     * @var string
     */
    protected $toCache = 'contents';

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

        // Append request language if hasMultilanguage
        if ($this->container->get('core.instance')->hasMultilanguage()) {
            $this->cachedId .= '-' . $this->container->get('core.locale')
                ->getRequestLocaleShort('frontend') . '-';
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
            $contentTypes = [ $this->defaultType ];
            if (array_key_exists('content_type', $this->params)) {
                $contentTypes = is_array($this->params['content_type'])
                    ? $this->params['content_type']
                    : [ $this->params['content_type'] ];
            }

            foreach ($contentTypes as $type) {
                $xtags[] = sprintf('content_type_name-widget-%s', underscore($type));
            }

            foreach ($this->propertiesMap as $key => $value) {
                $xtags = array_merge($xtags, $this->getXTagFor($key, $value));
            }
        }

        foreach ($this->params['contents'] as $content) {
            $xtags[] = sprintf('%s-%d', $content->content_type_name, $content->pk_content);
        }

        return $xtags;
    }

    /**
     * Returns an array with the x-tags for the specific property to map.
     *
     * @return array The array with the x-tags for the specific property.
     */
    protected function getXTagFor($key, $value)
    {
        if (!array_key_exists($key, $this->params) || empty($this->params[$key])) {
            return [ sprintf('%s-widget-all', $value) ];
        }

        if (!is_array($this->params[$key])) {
            return [ sprintf('%s-widget-%s', $value, $this->params[$key]) ];
        }

        $xtags = [];
        foreach ($this->params[$key] as $param) {
            $xtags[] = sprintf('%s-widget-%d', $value, $param);
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
        if ($this->isStatic()) {
            return $this->defaultTtl;
        }

        $endtime = null;

        if (!empty($this->params['contents'])) {
            $endtimes = array_filter(array_map(function ($content) {
                return $content->endtime;
            }, $this->params['contents']));

            sort($endtimes);

            $endtime = array_shift($endtimes) ?? null;
        }

        if (!empty($endtime)) {
            $endtime = $endtime->format('Y-m-d H:i:s');
        }

        $starttime = $this->getStarttimeByFilters();
        $dates     = array_filter([ $starttime, $endtime ]);
        if (!empty($dates)) {
            $now  = new \DateTime();
            $end  = new \DateTime(min($dates));
            $time = $end->getTimestamp() - $now->getTimestamp() - 2;

            $this->container->get('cache.connection.instance')
                ->set($this->cachedId, $this->params[$this->toCache], $time);

            return min($dates);
        }

        return $this->defaultTtl;
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
     * Inserts the key of the widget in the set of widgets caches.
     */
    public function saveKey()
    {
        $cache = $this->container->get('cache.connection.instance');

        $cache->addMemberToSet($this->keySetName, $this->cachedId);
    }

    /**
     * Returns the min starttime or null for the contents that match the filters of the widget.
     *
     * @return string The min starttime in the format Y-m-d H:i:s.
     */
    protected function getStarttimeByFilters()
    {
        if ($this->isStatic || !$this->isCacheable || $this->isCustom) {
            return null;
        }

        $replacements = [
            'category' => 'category_id',
            'author'   => 'fk_author'
        ];

        $oql = sprintf(
            'content_status = 1 and in_litter != 1 and ' .
            '(starttime !is null and starttime > "%s") and ',
            date('Y-m-d H:i:s')
        );

        $contentTypes = [ $this->defaultType ];
        if (array_key_exists('content_type', $this->params)) {
            $contentTypes = is_array($this->params['content_type']) ?
                $this->params['content_type'] :
                [ $this->params['content_type'] ];
        }

        foreach ($contentTypes as $i => $contentType) {
            $oql .= $i === array_key_first($contentTypes) ? '( ' : '';
            $oql .= $i === array_key_last($contentTypes)
                ? sprintf('content_type_name = "%s") ', $contentType)
                : sprintf('content_type_name = "%s" or ', $contentType);
        }

        $filters = array_intersect_key(array_flip($this->propertiesMap), $replacements);

        $oql .= $this->getOqlForFilters($filters, $replacements);
        $oql .= 'order by starttime asc limit 1';

        try {
            $content = $this->container->get('api.service.content')->getItemBy($oql);
            return $content->starttime->format('Y-m-d H:i:s');
        } catch (GetItemException $e) {
            return null;
        }
    }

    /**
     * Returns the oql snippet for filters
     *
     * @return string The oql snippet.
     */
    protected function getOqlForFilters($filters, $replacements)
    {
        $oql = '';
        foreach ($filters as $key => $value) {
            if (empty($this->params[$value])) {
                continue;
            }

            if (!is_array($this->params[$value])) {
                $oql .= sprintf('and %s = %s ', $replacements[$key], $this->params[$value]);
                continue;
            }

            if (is_array($this->params[$value]) && !empty($this->params[$value])) {
                $oql .= sprintf('and %s in[%s] ', $replacements[$key], implode(',', $this->params[$value]));
            }
        }

        return $oql;
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
