<?php

namespace Api\Helper\Cache;

class WidgetCacheHelper extends ContentCacheHelper
{
    /**
     * {@inheritdoc}
     */
    protected $defaultVarnishKeys = [
        '{{content_type_name}}-{{pk_content}}',
        'widget-not-found-{{pk_content}}',
        'widget-not-found-{{class}}'
    ];
}
